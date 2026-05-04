<?php

namespace App\Http\Controllers;

use App\Models\Fail;
use App\Models\History;
use App\Models\Pelupusan;
use App\Models\Pemisahan;
use App\Services\DocTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PelupusanController extends Controller
{
    public function index(): View
    {
        // Pending: pelupusan rows not yet disposed. Group by fail.kotak.
        $pending = Pelupusan::with(['pemisahan.fail.noRujukan'])
            ->whereNull('lupus_at')
            ->whereHas('pemisahan', fn($q) => $q->whereNotNull('tarikh_pemisahan')
                ->whereNotNull('tujuan_pemisahan'))
            ->whereHas('pemisahan.fail')
            ->orderBy('created_at')
            ->get()
            ->filter(fn($p) => $p->pemisahan && $p->pemisahan->fail)
            ->groupBy(fn($p) => $p->pemisahan->fail->kotak ?? '-');

        // Disposed: read snapshot columns from pelupusan itself since the
        // underlying Fail row has been deleted.
        $afterLupus = Pelupusan::whereNotNull('lupus_at')
            ->orderBy('lupus_at')
            ->get()
            ->groupBy(fn($p) => $p->kotak ?? '-');

        return view('pelupusan.index', compact('pending', 'afterLupus'));
    }

    public function updateStatus(Request $request, Pelupusan $pelupusan): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:PENDING,APPROVE,DECLINE'],
        ]);

        $pelupusan->update(['status' => $request->status]);

        History::log('Kemaskini Status Pelupusan', 'Status: ' . $request->status, 'pelupusan', $pelupusan->id);

        return redirect()->route('pelupusan.index')->with('success', 'Status berjaya dikemaskini.');
    }

    public function updateKotakStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'kotak' => ['required', 'string'],
            'status' => ['required', 'in:PENDING,APPROVE,DECLINE'],
        ]);

        Pelupusan::whereNull('lupus_at')
            ->whereHas('pemisahan.fail', fn($q) => $q->where('kotak', $request->kotak))
            ->update(['status' => $request->status]);

        History::log('Kemaskini Status Kotak Pelupusan', 'Kotak ' . $request->kotak . ' — Status: ' . $request->status, 'pelupusan');

        return redirect()->route('pelupusan.index')->with('success', 'Status kotak berjaya dikemaskini.');
    }

    public function lupus(Pelupusan $pelupusan): RedirectResponse
    {
        if ($pelupusan->status !== 'APPROVE') {
            return back()->withErrors(['lupus' => 'Hanya rekod berstatus APPROVE boleh dilupuskan.']);
        }

        $pelupusan->update([
            'lupus_at' => now(),
            'person_in_charge' => Auth::user()->name,
        ]);

        History::log('Lupus Rekod', $pelupusan->tajuk_fail ?: ('Pelupusan #' . $pelupusan->id), 'pelupusan', $pelupusan->id);

        return redirect()->route('pelupusan.index')->with('success', 'Rekod berjaya dilupuskan.');
    }

    /**
     * Dispose every pending pelupusan row belonging to the given kotak:
     * snapshot kotak + tajuk_fail, mark status=LUPUS, then delete the
     * underlying Fail rows so they disappear from the Fail page. The
     * pelupusan row has its pemisahan_id detached first (SET NULL FK) so
     * cascade-delete does not wipe out the historical pelupusan record.
     */
    public function lupusKotak(Request $request): RedirectResponse
    {
        $request->validate([
            'kotak' => ['required', 'string'],
        ]);

        $kotak = $request->kotak;
        $disposedCount = 0;

        DB::transaction(function () use ($kotak, &$disposedCount) {
            $pelupusans = Pelupusan::with(['pemisahan.fail' => function ($q) {
                    $q->withoutGlobalScopes()->with('noRujukan');
                }])
                ->whereNull('lupus_at')
                ->whereHas('pemisahan.fail', fn($q) => $q->withoutGlobalScopes()->where('kotak', $kotak))
                ->get();

            $userName = Auth::user()->name ?? null;
            $failIds = [];

            foreach ($pelupusans as $p) {
                $fail = $p->pemisahan?->fail;
                if (! $fail) {
                    continue;
                }

                $tajuk = trim(
                    ($fail->noRujukan->perkara ?? '') .
                    ($fail->jilid ? ' — Jilid ' . $fail->jilid : '')
                );

                $p->update([
                    'kotak' => $fail->kotak,
                    'tajuk_fail' => $tajuk,
                    'no_rujukan_id' => $fail->no_rujukan_id,
                    'jilid' => $fail->jilid,
                    'fakulti_bahagian_id' => $fail->fakulti_bahagian_id,
                    'status' => 'LUPUS',
                    'lupus_at' => now(),
                    'person_in_charge' => $userName,
                    'pemisahan_id' => null,
                ]);

                $failIds[] = $fail->id;
            }

            if (! empty($failIds)) {
                Fail::withoutGlobalScopes()->whereIn('id', $failIds)->delete();
            }
            $disposedCount = count($failIds);
        });

        History::log('Lupus Kotak', 'Kotak ' . $kotak . ' — ' . $disposedCount . ' rekod', 'pelupusan');

        return redirect()->route('pelupusan.index')
            ->with('success', 'Semua fail dalam kotak ' . $kotak . ' telah dilupuskan.');
    }

    public function destroySelepasKotak(Request $request): RedirectResponse
    {
        $request->validate([
            'kotak' => ['required', 'string'],
        ]);

        Pelupusan::whereNotNull('lupus_at')
            ->where('kotak', $request->kotak)
            ->delete();

        History::log('Padam Selepas Pelupusan', 'Kotak ' . $request->kotak, 'pelupusan');

        return redirect()->route('pelupusan.index')
            ->with('success', 'Rekod selepas pelupusan bagi kotak ' . $request->kotak . ' telah dipadam.');
    }

    public function printPelupusan(Request $request): BinaryFileResponse
    {
        $request->validate([
            'kotak' => ['required', 'string'],
        ]);

        // Only include records that have been disposed (selepas pelupusan).
        $pelupusans = Pelupusan::with(['pemisahan.fail.noRujukan'])
            ->whereNotNull('lupus_at')
            ->where(function ($q) use ($request) {
                $q->where('kotak', $request->kotak)
                  ->orWhereHas('pemisahan.fail', fn($qq) => $qq->where('kotak', $request->kotak));
            })
            ->orderBy('lupus_at')
            ->get();

        $tmp = (new DocTemplateService())->buildPelupusan(
            $request->kotak,
            Auth::user(),
            $pelupusans,
        );

        return response()->download($tmp, 'borangPelupusanRekod_kotak' . $request->kotak . '.docx')->deleteFileAfterSend();
    }
}
