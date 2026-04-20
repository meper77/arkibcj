<?php

namespace App\Http\Controllers;

use App\Models\NoRujukan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoRujukanController extends Controller
{
    public function index(): View
    {
        $noRujukans = NoRujukan::orderBy('siri')->get();
        return view('no-rujukan.index', compact('noRujukans'));
    }

    public function create(): View
    {
        return view('no-rujukan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siri' => ['required', 'integer', 'min:1'],
            'kampus' => ['required', 'string', 'max:255'],
            'kod_bahagian' => ['required', 'string', 'max:255'],
            'nombor_fail' => [
                'required',
                'string',
                'max:255',
                Rule::unique('no_rujukan')->where(fn($q) => $q
                    ->where('siri', $request->siri)
                    ->where('kampus', $request->kampus)
                    ->where('kod_bahagian', $request->kod_bahagian)
                ),
            ],
            'perkara' => ['required', 'string', 'max:255'],
            'additional_space' => ['boolean'],
        ]);

        $validated['additional_space'] = $request->boolean('additional_space');

        NoRujukan::create($validated);

        return redirect()->route('no-rujukan.index')->with('success', 'No. Rujukan berjaya didaftarkan.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:no_rujukan,id'],
        ]);

        NoRujukan::whereIn('id', $request->ids)->delete();

        return redirect()->route('no-rujukan.index')->with('success', 'No. Rujukan berjaya dipadam.');
    }

    public function csvTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_no_rujukan.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['siri', 'kampus', 'kod_bahagian', 'nombor_fail', 'perkara', 'additional_space']);
            fputcsv($handle, ['100', 'UiTM', 'INFO', '1/1', 'PENTADBIRAN - AM', '0']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function csvImport(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $headers = fgetcsv($handle);
        $expectedHeaders = ['siri', 'kampus', 'kod_bahagian', 'nombor_fail', 'perkara', 'additional_space'];

        if ($headers !== $expectedHeaders) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'Format CSV tidak sah. Header mesti: ' . implode(', ', $expectedHeaders)]);
        }

        $rowErrors = [];
        $rowNum = 1;
        $successCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (count($row) !== 6) {
                $rowErrors[] = "Baris {$rowNum}: Bilangan lajur tidak betul (perlu 6).";
                continue;
            }

            [$siri, $kampus, $kodBahagian, $nomborFail, $perkara, $additionalSpace] = $row;

            $errors = [];
            if (!is_numeric($siri) || (int)$siri < 1) {
                $errors[] = 'siri mesti nombor positif';
            }
            if (empty(trim($kampus))) {
                $errors[] = 'kampus diperlukan';
            }
            if (empty(trim($kodBahagian))) {
                $errors[] = 'kod_bahagian diperlukan';
            }
            if (empty(trim($nomborFail))) {
                $errors[] = 'nombor_fail diperlukan';
            }
            if (empty(trim($perkara))) {
                $errors[] = 'perkara diperlukan';
            }

            if (empty($errors)) {
                $exists = NoRujukan::where('siri', (int)$siri)
                    ->where('kampus', trim($kampus))
                    ->where('kod_bahagian', trim($kodBahagian))
                    ->where('nombor_fail', trim($nomborFail))
                    ->exists();
                if ($exists) {
                    $errors[] = 'rekod dengan kombinasi siri, kampus, kod_bahagian, nombor_fail ini sudah wujud';
                }
            }

            if (!empty($errors)) {
                $rowErrors[] = "Baris {$rowNum}: " . implode(', ', $errors);
                continue;
            }

            NoRujukan::create([
                'siri' => (int)$siri,
                'kampus' => trim($kampus),
                'kod_bahagian' => trim($kodBahagian),
                'nombor_fail' => trim($nomborFail),
                'perkara' => trim($perkara),
                'additional_space' => (bool)(int)$additionalSpace,
            ]);
            $successCount++;
        }

        fclose($handle);

        if (!empty($rowErrors)) {
            return back()->with('csv_success', $successCount)->withErrors(['csv_rows' => implode("\n", $rowErrors)]);
        }

        return redirect()->route('no-rujukan.index')->with('success', "{$successCount} rekod berjaya diimport.");
    }
}
