<?php

namespace App\Http\Controllers;

use App\Models\AvailableFakultiBahagian;
use App\Models\Fail;
use App\Models\History;
use App\Models\NoRujukan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AvailableFakultiBahagianController extends Controller
{
    public function index()
    {
        return redirect()->route('users.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:available_fakulti_bahagian,nama'],
        ], [], [
            'nama' => 'Fakulti/Bahagian',
        ]);

        $fakulti = AvailableFakultiBahagian::create([
            'nama' => strtoupper(trim($request->nama)),
        ]);

        History::log('Tambah Fakulti/Bahagian', $fakulti->nama, 'fakulti', $fakulti->id, null);

        return back()->with('success', 'Fakulti/Bahagian berjaya ditambah.');
    }

    public function updatePermissions(Request $request, AvailableFakultiBahagian $fakulti): RedirectResponse
    {
        $fakulti->fill([
            'additional_space_1' => $request->boolean('additional_space_1'),
            'additional_space_2' => $request->boolean('additional_space_2'),
            'additional_cawangan' => $request->boolean('additional_cawangan'),
            'fail_am' => $request->boolean('fail_am'),
            'fail_sulit' => $request->boolean('fail_sulit'),
            'fail_pelajar' => $request->boolean('fail_pelajar'),
            'fail_staff' => $request->boolean('fail_staff'),
            'fail_akademik' => $request->boolean('fail_akademik'),
            'fail_pentadbiran' => $request->boolean('fail_pentadbiran'),
            'student_id' => $request->boolean('student_id'),
            'borang_pemisahan' => $request->boolean('borang_pemisahan'),
            'label_pentadbiran' => $request->boolean('label_pentadbiran'),
            'label_staff' => $request->boolean('label_staff'),
            'label_pelajar' => $request->boolean('label_pelajar'),
        ]);
        $fakulti->save();

        History::log('Kemaskini Tetapan Fakulti', $fakulti->nama, 'fakulti', $fakulti->id, $fakulti->id);

        return back()->with('success', 'Tetapan kebenaran berjaya dikemaskini.');
    }

    public function destroy(AvailableFakultiBahagian $fakulti): RedirectResponse
    {
        $hasUser = User::where('fakulti_bahagian_id', $fakulti->id)->exists();
        $hasNoRujukan = NoRujukan::withoutGlobalScopes()->where('fakulti_bahagian_id', $fakulti->id)->exists();
        $hasFail = Fail::withoutGlobalScopes()->where('fakulti_bahagian_id', $fakulti->id)->exists();

        if ($hasUser || $hasNoRujukan || $hasFail) {
            return back()->withErrors(['fakulti' => 'Tidak boleh padam: fakulti/bahagian ini masih dirujuk oleh pengguna atau rekod.']);
        }

        $nama = $fakulti->nama;
        $id = $fakulti->id;
        $fakulti->delete();

        History::log('Padam Fakulti/Bahagian', $nama, 'fakulti', $id, null);

        return back()->with('success', 'Fakulti/Bahagian berjaya dipadam.');
    }
}
