<?php

namespace App\Http\Controllers;

use App\Models\Fail;
use App\Models\NoRujukan;
use App\Models\Pelupusan;
use App\Models\Pemisahan;
use App\Services\DocTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class FailController extends Controller
{
    public function index(): View
    {
        $fails = Fail::with(['noRujukan', 'kertasBerhubung.noRujukan'])->orderBy('no_rujukan_id')->orderBy('jilid')->get();
        return view('fail.index', compact('fails'));
    }

    public function create(): View
    {
        $noRujukans = NoRujukan::orderBy('siri')->get();
        return view('fail.create', compact('noRujukans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'no_rujukan_id' => ['required', 'exists:no_rujukan,id'],
            'jilid' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('fail')->where(fn($q) => $q->where('no_rujukan_id', $request->no_rujukan_id)),
            ],
            'tarikh_pertama' => ['required', 'date'],
        ]);

        $validated['person_in_charge'] = Auth::user()->name;

        $fail = Fail::create($validated);

        // Auto-create pemisahan record
        Pemisahan::create([
            'fail_id' => $fail->id,
            'person_in_charge' => Auth::user()->name,
        ]);

        return redirect()->route('fail.index')->with('success', 'Fail berjaya didaftarkan.');
    }

    public function edit(Fail $fail): View
    {
        $fail->load('noRujukan');
        $availableKertas = Fail::with('noRujukan')
            ->where('no_rujukan_id', $fail->no_rujukan_id)
            ->where('id', '!=', $fail->id)
            ->where('jilid', '!=', $fail->jilid)
            ->orderBy('jilid')
            ->get();
        return view('fail.edit', compact('fail', 'availableKertas'));
    }

    public function update(Request $request, Fail $fail): RedirectResponse
    {
        $request->validate([
            'tarikh_akhir' => ['nullable', 'date', 'after_or_equal:' . $fail->tarikh_pertama],
            'tarikh_tutup' => ['nullable', 'date'],
            'kotak' => ['nullable', 'string'],
            'kertas_berhubung_id' => ['nullable', 'integer', Rule::exists('fail', 'id')->where(fn($q) => $q->where('no_rujukan_id', $fail->no_rujukan_id)->where('id', '!=', $fail->id))],
        ]);

        $newKotak = $request->kotak ?: null;
        $hadKotakBefore = !is_null($fail->kotak);

        $fail->update([
            'tarikh_akhir' => $request->tarikh_akhir ?: null,
            'tarikh_tutup' => $request->tarikh_tutup ?: null,
            'kotak' => $newKotak,
            'kertas_berhubung_id' => $request->kertas_berhubung_id ?: null,
            'person_in_charge' => Auth::user()->name,
        ]);

        // If kotak was just assigned (was null, now has value), create pelupusan row if not exists
        if (!$hadKotakBefore && !is_null($newKotak)) {
            $pemisahan = $fail->pemisahan;
            if ($pemisahan && !$pemisahan->pelupusan()->exists()) {
                Pelupusan::create([
                    'pemisahan_id' => $pemisahan->id,
                    'status' => 'PENDING',
                    'person_in_charge' => $pemisahan->person_in_charge ?? Auth::user()->name,
                ]);
            }
        }

        return redirect()->route('fail.index')->with('success', 'Fail berjaya dikemaskini.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:fail,id'],
        ]);

        Fail::whereIn('id', $request->ids)->delete();

        return redirect()->route('fail.index')->with('success', 'Fail berjaya dipadam.');
    }

    public function csvTemplate(): BinaryFileResponse
    {
        $noRujukans = NoRujukan::orderBy('siri')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('template_fail');

        // Header row
        $sheet->setCellValue('A1', 'noRujukan');
        $sheet->setCellValue('B1', 'jilid');
        $sheet->setCellValue('C1', 'tarikhPertama');

        // Style header
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(18);

        // Lookup sheet: column A holds the visible no_rujukan_full text (this is
        // what the dropdown references), column B keeps the numeric id for
        // reference only, column C holds the perkara/title.
        $lookup = $spreadsheet->createSheet();
        $lookup->setTitle('no_rujukan_list');
        $lookup->setCellValue('A1', 'no_rujukan');
        $lookup->setCellValue('B1', 'id');
        $lookup->setCellValue('C1', 'perkara');
        $lookup->getStyle('A1:C1')->getFont()->setBold(true);

        $row = 2;
        foreach ($noRujukans as $nr) {
            $lookup->setCellValue('A' . $row, $nr->no_rujukan_full);
            $lookup->setCellValue('B' . $row, $nr->id);
            $lookup->setCellValue('C' . $row, $nr->perkara);
            $row++;
        }
        $lookup->getColumnDimension('A')->setWidth(40);
        $lookup->getColumnDimension('B')->setWidth(8);
        $lookup->getColumnDimension('C')->setWidth(40);

        // Data validation dropdown on column A of the main sheet. References
        // the lookup sheet's column A which holds the no_rujukan_full text.
        $lastRow = max(2, count($noRujukans) + 1);
        $formulaRange = "'no_rujukan_list'!\$A\$2:\$A\$" . $lastRow;

        for ($r = 2; $r <= 501; $r++) {
            $validation = $sheet->getCell('A' . $r)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('noRujukan tidak sah');
            $validation->setError('Sila pilih dari senarai (lihat tab no_rujukan_list).');
            $validation->setPromptTitle('Pilih noRujukan');
            $validation->setPrompt('Pilih no rujukan dari senarai.');
            $validation->setFormula1($formulaRange);
        }

        // Seed sample row
        if ($noRujukans->count() > 0) {
            $sheet->setCellValue('A2', (string) $noRujukans->first()->no_rujukan_full);
            $sheet->setCellValue('B2', '1');
            $sheet->setCellValue('C2', '2024-01-01');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $tmp = tempnam(sys_get_temp_dir(), 'tmpl_fail_') . '.xlsx';
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tmp);

        return response()->download($tmp, 'template_fail.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    public function csvImport(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        $file = $request->file('csv_file');
        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($file->getRealPath(), PATHINFO_EXTENSION));
        $expectedHeaders = ['noRujukan', 'jilid', 'tarikhPertama'];

        $rows = [];
        if (in_array($ext, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
            } catch (\Throwable $e) {
                return back()->withErrors(['csv_file' => 'Gagal membaca fail Excel: ' . $e->getMessage()]);
            }
            $sheet = $spreadsheet->getSheetByName('template_fail') ?? $spreadsheet->getSheet(0);
            $data = $sheet->toArray(null, true, true, false);
            // First row = headers
            $headers = array_map(fn($v) => is_string($v) ? trim($v) : $v, array_slice($data[0] ?? [], 0, 3));
            if ($headers !== $expectedHeaders) {
                return back()->withErrors(['csv_file' => 'Format tidak sah. Header mesti: ' . implode(', ', $expectedHeaders)]);
            }
            for ($i = 1; $i < count($data); $i++) {
                $r = $data[$i];
                // Skip fully empty rows
                if (count(array_filter($r, fn($v) => $v !== null && $v !== '')) === 0) continue;
                $rows[] = array_slice($r, 0, 3);
            }
        } else {
            $handle = fopen($file->getRealPath(), 'r');
            $headers = fgetcsv($handle);
            if ($headers !== $expectedHeaders) {
                fclose($handle);
                return back()->withErrors(['csv_file' => 'Format CSV tidak sah. Header mesti: ' . implode(', ', $expectedHeaders)]);
            }
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        $rowErrors = [];
        $rowNum = 1;
        $successCount = 0;

        foreach ($rows as $row) {
            $rowNum++;
            // Pad row to 3 columns
            $row = array_pad(array_slice($row, 0, 3), 3, '');
            [$noRujukanText, $jilid, $tarikhPertama] = $row;
            $noRujukanText = is_string($noRujukanText) ? trim($noRujukanText) : (string) $noRujukanText;

            // Normalize excel date values
            if ($tarikhPertama instanceof \DateTimeInterface) {
                $tarikhPertama = $tarikhPertama->format('Y-m-d');
            } elseif (is_numeric($tarikhPertama) && $tarikhPertama > 25569) {
                // Excel serial date
                try {
                    $tarikhPertama = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$tarikhPertama)->format('Y-m-d');
                } catch (\Throwable $e) {
                    // leave as-is
                }
            }
            $tarikhPertama = (string) $tarikhPertama;

            $errors = [];
            $noRujukan = null;
            if ($noRujukanText === '') {
                $errors[] = 'noRujukan diperlukan';
            } else {
                $noRujukan = NoRujukan::all()->first(fn($nr) => $nr->no_rujukan_full === $noRujukanText);
                if (!$noRujukan) {
                    $errors[] = "noRujukan '{$noRujukanText}' tidak dijumpai dalam senarai No. Rujukan";
                }
            }
            if (!is_numeric($jilid) || (int)$jilid < 1) {
                $errors[] = 'jilid mesti nombor positif';
            }
            if (empty(trim($tarikhPertama))) {
                $errors[] = 'tarikhPertama diperlukan';
            }

            if (empty($errors) && $noRujukan) {
                $exists = Fail::where('no_rujukan_id', $noRujukan->id)->where('jilid', (int)$jilid)->exists();
                if ($exists) {
                    $errors[] = "jilid {$jilid} sudah wujud untuk noRujukan '{$noRujukanText}'";
                }
            }

            if (!empty($errors)) {
                $rowErrors[] = "Baris {$rowNum}: " . implode(', ', $errors);
                continue;
            }

            $fail = Fail::create([
                'no_rujukan_id' => $noRujukan->id,
                'jilid' => (int)$jilid,
                'tarikh_pertama' => trim($tarikhPertama),
                'person_in_charge' => Auth::user()->name,
            ]);

            Pemisahan::create([
                'fail_id' => $fail->id,
                'person_in_charge' => Auth::user()->name,
            ]);

            $successCount++;
        }

        if (!empty($rowErrors)) {
            return back()->with('csv_success', $successCount)->withErrors(['csv_rows' => implode("\n", $rowErrors)]);
        }

        return redirect()->route('fail.index')->with('success', "{$successCount} rekod berjaya diimport.");
    }

    public function print(Request $request): BinaryFileResponse
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:fail,id'],
        ]);

        $fails = Fail::with('noRujukan')->whereIn('id', $request->ids)->get();
        $path = app(DocTemplateService::class)->buildPenilaian($fails);

        return response()->download($path, 'penilaian-fail.docx')->deleteFileAfterSend(true);
    }
}
