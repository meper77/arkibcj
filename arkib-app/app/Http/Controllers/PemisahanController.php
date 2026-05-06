<?php

namespace App\Http\Controllers;

use App\Models\Fail;
use App\Models\History;
use App\Models\Pemisahan;
use App\Services\DocTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PemisahanController extends Controller
{
    public function index(): View
    {
        $pemisahans = Pemisahan::with(['fail.noRujukan'])
            ->whereHas('fail', fn($q) => $q->whereNotNull('kotak')
                ->whereNotNull('tarikh_akhir')
                ->whereNotNull('tarikh_tutup'))
            ->orderBy('fail_id')
            ->get()
            ->groupBy(fn($p) => $p->fail->kotak);

        $allPemisahans = Pemisahan::with(['fail.noRujukan'])
            ->whereHas('fail', fn($q) => $q->whereNull('kotak')
                ->whereNotNull('tarikh_akhir')
                ->whereNotNull('tarikh_tutup'))
            ->orderBy('fail_id')
            ->get();

        $fakulti = Auth::user()?->fakultiBahagian;

        return view('pemisahan.index', compact('pemisahans', 'allPemisahans', 'fakulti'));
    }

    public function edit(Pemisahan $pemisahan): View
    {
        $pemisahan->load('fail.noRujukan');
        return view('pemisahan.edit', compact('pemisahan'));
    }

    public function update(Request $request, Pemisahan $pemisahan): RedirectResponse
    {
        $request->validate([
            'tarikh_pemisahan' => ['required', 'date'],
            'tujuan_pemisahan' => ['required', 'string', 'max:500'],
        ]);

        $pemisahan->update([
            'tarikh_pemisahan' => $request->tarikh_pemisahan,
            'tujuan_pemisahan' => $request->tujuan_pemisahan,
            'person_in_charge' => Auth::user()->name,
        ]);

        $pemisahan->load('fail.noRujukan');
        $label = ($pemisahan->fail?->noRujukan?->no_rujukan_full ?? '') . ' Jld.' . ($pemisahan->fail?->jilid ?? '');
        History::log('Kemaskini Pemisahan', trim($label), 'pemisahan', $pemisahan->id);

        return redirect()->route('pemisahan.index')->with('success', 'Rekod pemisahan berjaya dikemaskini.');
    }

    private function pemisahansForKotak(string $kotak)
    {
        return Pemisahan::with(['fail.noRujukan'])
            ->whereHas('fail', fn($q) => $q->where('kotak', $kotak))
            ->get()
            ->sortBy([
                fn($a, $b) => strcmp(
                    (string) ($a->fail?->noRujukan?->no_rujukan_full ?? ''),
                    (string) ($b->fail?->noRujukan?->no_rujukan_full ?? '')
                ),
                fn($a, $b) => ((int) ($a->fail?->jilid ?? 0)) <=> ((int) ($b->fail?->jilid ?? 0)),
            ])
            ->values();
    }

    public function printPemisahan(Request $request): BinaryFileResponse
    {
        $request->validate(['kotak' => ['required', 'string']]);
        $pemisahans = $this->pemisahansForKotak($request->kotak);
        $tmp = (new DocTemplateService())->buildPemisahan($request->kotak, Auth::user(), $pemisahans);
        return response()->download($tmp, 'borangPemisahanRekod_kotak' . $request->kotak . '.docx')->deleteFileAfterSend();
    }

    public function printPentadbiran(Request $request): BinaryFileResponse
    {
        $request->validate(['kotak' => ['required', 'string']]);
        $pemisahans = $this->pemisahansForKotak($request->kotak);
        $tmp = (new DocTemplateService())->buildLabelPentadbiran($request->kotak, Auth::user(), $pemisahans);
        return response()->download($tmp, 'labelFailPentadbiran_kotak' . $request->kotak . '.docx')->deleteFileAfterSend();
    }

    public function printStaf(Request $request): BinaryFileResponse
    {
        $request->validate(['kotak' => ['required', 'string']]);
        $pemisahans = $this->pemisahansForKotak($request->kotak);
        $tmp = (new DocTemplateService())->buildLabelStaf($request->kotak, Auth::user(), $pemisahans);
        return response()->download($tmp, 'labelFailStaf_kotak' . $request->kotak . '.docx')->deleteFileAfterSend();
    }

    public function printPelajar(Request $request): BinaryFileResponse
    {
        $request->validate(['kotak' => ['required', 'string']]);
        $pemisahans = $this->pemisahansForKotak($request->kotak);
        $tmp = (new DocTemplateService())->buildLabelPelajar($request->kotak, Auth::user(), $pemisahans);
        return response()->download($tmp, 'labelFailPelajar_kotak' . $request->kotak . '.docx')->deleteFileAfterSend();
    }
}
