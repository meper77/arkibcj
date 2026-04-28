<?php

namespace App\Http\Controllers;

use App\Models\NoRujukan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            'deskripsi' => ['required', 'string', 'max:255'],
            'additional_space' => ['boolean'],
        ]);

        $validated['additional_space'] = $request->boolean('additional_space');
        $validated['deskripsi'] = strtoupper(trim($request->deskripsi));

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

    public function xlsxTemplate(): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('template_no_rujukan');

        $headers = ['siri', 'kampus', 'kod_bahagian', 'nombor_fail', 'perkara', 'deskripsi', 'additional_space'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $col++;
        }
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', '100');
        $sheet->setCellValue('B2', 'UiTM');
        $sheet->setCellValue('C2', 'INFO');
        $sheet->setCellValue('D2', '1/1');
        $sheet->setCellValue('E2', 'PENTADBIRAN - AM');
        $sheet->setCellValue('F2', 'KETERANGAN AM');
        $sheet->setCellValue('G2', '0');

        $tmp = tempnam(sys_get_temp_dir(), 'tmpl_norujukan_') . '.xlsx';
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tmp);

        return response()->download($tmp, 'template-no-rujukan.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    public function xlsxImport(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $file = $request->file('file');
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Gagal membaca fail Excel: ' . $e->getMessage()]);
        }

        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $expectedHeaders = ['siri', 'kampus', 'kod_bahagian', 'nombor_fail', 'perkara', 'deskripsi', 'additional_space'];

        $headers = array_map(fn($v) => is_string($v) ? trim($v) : $v, array_slice($data[0] ?? [], 0, 7));
        if ($headers !== $expectedHeaders) {
            return back()->withErrors(['file' => 'Format tidak sah. Header mesti: ' . implode(', ', $expectedHeaders)]);
        }

        $rowErrors = [];
        $successCount = 0;

        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) continue;

            $rowNum = $i + 1;
            $row = array_pad(array_slice($row, 0, 7), 7, '');
            [$siri, $kampus, $kodBahagian, $nomborFail, $perkara, $deskripsi, $additionalSpace] = $row;

            $errors = [];
            if (!is_numeric($siri) || (int)$siri < 1) {
                $errors[] = 'siri mesti nombor positif';
            }
            if (empty(trim((string)$kampus))) {
                $errors[] = 'kampus diperlukan';
            }
            if (empty(trim((string)$kodBahagian))) {
                $errors[] = 'kod_bahagian diperlukan';
            }
            if (empty(trim((string)$nomborFail))) {
                $errors[] = 'nombor_fail diperlukan';
            }
            if (empty(trim((string)$perkara))) {
                $errors[] = 'perkara diperlukan';
            }
            if (empty(trim((string)$deskripsi))) {
                $errors[] = 'deskripsi diperlukan';
            }

            if (empty($errors)) {
                $exists = NoRujukan::where('siri', (int)$siri)
                    ->where('kampus', trim((string)$kampus))
                    ->where('kod_bahagian', trim((string)$kodBahagian))
                    ->where('nombor_fail', trim((string)$nomborFail))
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
                'kampus' => trim((string)$kampus),
                'kod_bahagian' => trim((string)$kodBahagian),
                'nombor_fail' => trim((string)$nomborFail),
                'perkara' => trim((string)$perkara),
                'deskripsi' => strtoupper(trim((string)$deskripsi)),
                'additional_space' => (bool)(int)$additionalSpace,
            ]);
            $successCount++;
        }

        if (!empty($rowErrors)) {
            return back()->with('csv_success', $successCount)->withErrors(['csv_rows' => implode("\n", $rowErrors)]);
        }

        return redirect()->route('no-rujukan.index')->with('success', "{$successCount} rekod berjaya diimport.");
    }
}
