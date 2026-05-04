<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\NoRujukan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $fakulti = Auth::user()->fakultiBahagian;
        return view('no-rujukan.create', compact('fakulti'));
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
                    ->where('fakulti_bahagian_id', Auth::user()->fakulti_bahagian_id)
                ),
            ],
            'perkara' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:255'],
        ]);

        $validated['deskripsi'] = strtoupper(trim($request->deskripsi));
        $validated['fakulti_bahagian_id'] = Auth::user()->fakulti_bahagian_id;

        $noRujukan = NoRujukan::create($validated);

        History::log(
            'Daftar No. Rujukan',
            $noRujukan->no_rujukan_full . ' — ' . $noRujukan->perkara,
            'no_rujukan',
            $noRujukan->id,
        );

        return redirect()->route('no-rujukan.index')->with('success', 'No. Rujukan berjaya didaftarkan.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:no_rujukan,id'],
        ]);

        $toDelete = NoRujukan::whereIn('id', $request->ids)->get();

        NoRujukan::whereIn('id', $request->ids)->delete();

        foreach ($toDelete as $nr) {
            History::log(
                'Padam No. Rujukan',
                $nr->no_rujukan_full,
                'no_rujukan',
                $nr->id,
            );
        }

        return redirect()->route('no-rujukan.index')->with('success', 'No. Rujukan berjaya dipadam.');
    }

    public function xlsxTemplate(): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('template_no_rujukan');

        $headers = ['siri', 'kampus', 'kod_bahagian', 'nombor_fail', 'perkara', 'deskripsi'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $col++;
        }
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', '100');
        $sheet->setCellValue('B2', 'UiTM');
        $sheet->setCellValue('C2', 'INFO');
        $sheet->setCellValue('D2', '1/1');
        $sheet->setCellValue('E2', 'PENTADBIRAN - AM');
        $sheet->setCellValue('F2', 'KETERANGAN AM');

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
        $expectedHeaders = ['siri', 'kampus', 'kod_bahagian', 'nombor_fail', 'perkara', 'deskripsi'];

        $headers = array_map(fn($v) => is_string($v) ? trim($v) : $v, array_slice($data[0] ?? [], 0, 6));
        if ($headers !== $expectedHeaders) {
            return back()->withErrors(['file' => 'Format tidak sah. Header mesti: ' . implode(', ', $expectedHeaders)]);
        }

        $rowErrors = [];
        $successCount = 0;

        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) continue;

            $rowNum = $i + 1;
            $row = array_pad(array_slice($row, 0, 6), 6, '');
            [$siri, $kampus, $kodBahagian, $nomborFail, $perkara, $deskripsi] = $row;

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
                'fakulti_bahagian_id' => Auth::user()->fakulti_bahagian_id,
            ]);
            $successCount++;
        }

        if (!empty($rowErrors)) {
            if ($successCount > 0) {
                History::log('Import Batch No. Rujukan', "{$successCount} rekod diimport");
            }
            return back()->with('csv_success', $successCount)->withErrors(['csv_rows' => implode("\n", $rowErrors)]);
        }

        History::log('Import Batch No. Rujukan', "{$successCount} rekod diimport");

        return redirect()->route('no-rujukan.index')->with('success', "{$successCount} rekod berjaya diimport.");
    }
}
