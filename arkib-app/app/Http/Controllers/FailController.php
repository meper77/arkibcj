<?php

namespace App\Http\Controllers;

use App\Models\Fail;
use App\Models\History;
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
    private static function jenisLabel(Fail $fail): string
    {
        if ($fail->jenis_fail === 'AM') {
            return 'AM';
        }
        return implode('-', array_filter(['SULIT', $fail->kategori, $fail->sub_kategori]));
    }

    public function index(Request $request): View
    {
        $fakulti = Auth::user()->fakultiBahagian;
        $jenis = $request->input('jenis');
        $kategori = $request->input('kategori');
        $sub = $request->input('sub');

        $query = Fail::with(['noRujukan', 'kertasBerhubung.noRujukan', 'studentIds'])
            ->orderBy('no_rujukan_id')
            ->orderBy('jilid');

        if ($jenis) {
            $query->where('jenis_fail', $jenis);
        }
        if ($kategori) {
            $query->where('kategori', $kategori);
        }
        if ($sub) {
            $query->where('sub_kategori', $sub);
        }

        $fails = $query->get();

        return view('fail.index', compact('fails', 'fakulti', 'jenis', 'kategori', 'sub'));
    }

    public function create(): View
    {
        $noRujukans = NoRujukan::orderBy('siri')->get();
        $fakulti = Auth::user()->fakultiBahagian;
        return view('fail.create', compact('noRujukans', 'fakulti'));
    }

    public function store(Request $request): RedirectResponse
    {
        $fakulti = Auth::user()->fakultiBahagian;

        $validated = $request->validate([
            'no_rujukan_id' => ['required', 'exists:no_rujukan,id'],
            'jilid' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('fail')->where(fn($q) => $q->where('no_rujukan_id', $request->no_rujukan_id)),
                function ($attribute, $value, $fail) use ($request) {
                    $exists = \App\Models\Pelupusan::whereNotNull('lupus_at')
                        ->where('no_rujukan_id', $request->no_rujukan_id)
                        ->where('jilid', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Jilid ini telah dilupuskan dan tidak boleh didaftarkan semula.');
                    }
                },
            ],
            'tarikh_pertama' => ['required', 'date'],
            'jenis_fail' => ['required', Rule::in(['AM', 'SULIT'])],
            'kategori' => ['nullable', Rule::in(['STAFF', 'PELAJAR'])],
            'sub_kategori' => ['nullable', Rule::in(['AKADEMIK', 'PENTADBIRAN'])],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'jenis_fail.in' => 'Jenis fail mesti AM atau SULIT.',
            'kategori.in' => 'Kategori mesti STAFF atau PELAJAR.',
            'sub_kategori.in' => 'Sub-kategori mesti AKADEMIK atau PENTADBIRAN.',
            'student_ids.*.regex' => 'Student ID mesti nombor sahaja.',
        ]);

        $jenis = $validated['jenis_fail'];
        $kategori = $validated['kategori'] ?? null;
        $sub = $validated['sub_kategori'] ?? null;
        $rawIds = $request->input('student_ids', []);
        $studentIds = array_values(array_unique(array_filter(array_map(fn($v) => trim((string)$v), is_array($rawIds) ? $rawIds : []))));

        // Branch validation
        if ($jenis === 'AM') {
            if (!$fakulti || !$fakulti->fail_am) {
                return back()->withErrors(['jenis_fail' => 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail AM.'])->withInput();
            }
            $kategori = null;
            $sub = null;
            $studentIds = [];
        } else { // SULIT
            if (!$fakulti || !$fakulti->fail_sulit) {
                return back()->withErrors(['jenis_fail' => 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail SULIT.'])->withInput();
            }
            if (!$kategori) {
                return back()->withErrors(['kategori' => 'Kategori diperlukan untuk Fail SULIT.'])->withInput();
            }
            if ($kategori === 'STAFF') {
                if (!$fakulti->fail_staff) {
                    return back()->withErrors(['kategori' => 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail STAFF.'])->withInput();
                }
                if (!$sub) {
                    return back()->withErrors(['sub_kategori' => 'Sub-kategori diperlukan untuk Fail STAFF.'])->withInput();
                }
                if ($sub === 'AKADEMIK' && !$fakulti->fail_akademik) {
                    return back()->withErrors(['sub_kategori' => 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail AKADEMIK.'])->withInput();
                }
                if ($sub === 'PENTADBIRAN' && !$fakulti->fail_pentadbiran) {
                    return back()->withErrors(['sub_kategori' => 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail PENTADBIRAN.'])->withInput();
                }
                $studentIds = [];
            } else { // PELAJAR
                if (!$fakulti->fail_pelajar) {
                    return back()->withErrors(['kategori' => 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail PELAJAR.'])->withInput();
                }
                $sub = null;
                if ($fakulti->student_id) {
                    if (empty($studentIds)) {
                        return back()->withErrors(['student_ids' => 'Sekurang-kurangnya satu Student ID diperlukan.'])->withInput();
                    }
                } else {
                    $studentIds = [];
                }
            }
        }

        unset($validated['student_ids']);
        $validated['person_in_charge'] = Auth::user()->name;
        $validated['fakulti_bahagian_id'] = Auth::user()->fakulti_bahagian_id;
        $validated['jenis_fail'] = $jenis;
        $validated['kategori'] = $kategori;
        $validated['sub_kategori'] = $sub;

        $fail = Fail::create($validated);

        foreach ($studentIds as $sid) {
            $fail->studentIds()->create(['student_id' => $sid]);
        }

        // Auto-create pemisahan record
        Pemisahan::create([
            'fail_id' => $fail->id,
            'fakulti_bahagian_id' => $fail->fakulti_bahagian_id,
            'person_in_charge' => Auth::user()->name,
        ]);

        $fail->load('noRujukan');
        $label = ($fail->noRujukan?->no_rujukan_full ?? '') . ' Jld.' . $fail->jilid;
        History::log('Daftar Fail', $label, 'fail', $fail->id);

        return redirect()->route('fail.index')->with('success', 'Fail berjaya didaftarkan.');
    }

    public function edit(Fail $fail): View
    {
        $fail->load('noRujukan', 'studentIds');
        $availableKertas = Fail::with('noRujukan')
            ->where('no_rujukan_id', $fail->no_rujukan_id)
            ->where('id', '!=', $fail->id)
            ->where('jilid', '!=', $fail->jilid)
            ->orderBy('jilid')
            ->get();
        $fakulti = Auth::user()->fakultiBahagian;
        $allowStudentId = $fail->jenis_fail === 'SULIT' && $fail->kategori === 'PELAJAR'
            && $fakulti && $fakulti->student_id;

        // Map kotak -> distinct file-type labels currently in that box (same fakulti,
        // excluding this fail) so the edit form can warn about type mismatches live.
        $kotakTypes = [];
        $boxFiles = Fail::where('fakulti_bahagian_id', $fail->fakulti_bahagian_id)
            ->where('id', '!=', $fail->id)
            ->whereNotNull('kotak')
            ->get(['kotak', 'jenis_fail', 'kategori', 'sub_kategori']);
        foreach ($boxFiles as $bf) {
            $label = self::jenisLabel($bf);
            $kotakTypes[$bf->kotak] ??= [];
            if (!in_array($label, $kotakTypes[$bf->kotak], true)) {
                $kotakTypes[$bf->kotak][] = $label;
            }
        }
        $currentType = self::jenisLabel($fail);

        return view('fail.edit', compact('fail', 'availableKertas', 'allowStudentId', 'kotakTypes', 'currentType'));
    }

    public function update(Request $request, Fail $fail): RedirectResponse
    {
        $request->validate([
            'tarikh_akhir' => ['nullable', 'date', 'after_or_equal:' . $fail->tarikh_pertama],
            'tarikh_tutup' => ['nullable', 'date'],
            'kotak' => [
                'nullable',
                'string',
                function ($attribute, $value, $failCb) use ($fail) {
                    if ($value === null || $value === '') return;
                    $disposed = \App\Models\Pelupusan::whereNotNull('lupus_at')
                        ->where('kotak', $value)
                        ->where('fakulti_bahagian_id', $fail->fakulti_bahagian_id)
                        ->exists();
                    if ($disposed) {
                        $failCb('No. Kotak ini telah dilupuskan dan tidak boleh digunakan semula.');
                        return;
                    }
                    // One box holds one file type only: every fail sharing a kotak
                    // must match on jenis_fail + kategori + sub_kategori.
                    $other = Fail::where('kotak', $value)
                        ->where('fakulti_bahagian_id', $fail->fakulti_bahagian_id)
                        ->where('id', '!=', $fail->id)
                        ->where(fn($q) => $q
                            ->where('jenis_fail', '!=', $fail->jenis_fail)
                            ->orWhereRaw('IFNULL(kategori, \'\') != ?', [$fail->kategori ?? ''])
                            ->orWhereRaw('IFNULL(sub_kategori, \'\') != ?', [$fail->sub_kategori ?? '']))
                        ->first();
                    if ($other) {
                        $existing = self::jenisLabel($other);
                        $current = self::jenisLabel($fail);
                        $failCb("No. Kotak ini mengandungi fail jenis {$existing}. Satu kotak hanya untuk satu jenis fail (kini: {$current}).");
                    }
                },
            ],
            'kertas_berhubung_id' => ['nullable', 'integer', Rule::exists('fail', 'id')->where(fn($q) => $q->where('no_rujukan_id', $fail->no_rujukan_id)->where('id', '!=', $fail->id))],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'student_ids.*.regex' => 'Student ID mesti nombor sahaja.',
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

        // Sync student IDs only when this fail is SULIT-PELAJAR and fakulti permits.
        $fakulti = Auth::user()->fakultiBahagian;
        $allowStudentId = $fail->jenis_fail === 'SULIT' && $fail->kategori === 'PELAJAR'
            && $fakulti && $fakulti->student_id;
        if ($allowStudentId) {
            $rawIds = $request->input('student_ids', []);
            $ids = array_values(array_unique(array_filter(array_map(fn($v) => trim((string)$v), is_array($rawIds) ? $rawIds : []))));
            $fail->studentIds()->delete();
            foreach ($ids as $sid) {
                $fail->studentIds()->create(['student_id' => $sid]);
            }
        }

        // If kotak was just assigned (was null, now has value), create pelupusan row if not exists
        if (!$hadKotakBefore && !is_null($newKotak)) {
            $pemisahan = $fail->pemisahan;
            if ($pemisahan && !$pemisahan->pelupusan()->exists()) {
                Pelupusan::create([
                    'pemisahan_id' => $pemisahan->id,
                    'fakulti_bahagian_id' => $fail->fakulti_bahagian_id,
                    'status' => 'PENDING',
                    'person_in_charge' => $pemisahan->person_in_charge ?? Auth::user()->name,
                ]);
            }
        }

        $fail->load('noRujukan');
        $label = ($fail->noRujukan?->no_rujukan_full ?? '') . ' Jld.' . $fail->jilid;
        History::log('Kemaskini Fail', $label, 'fail', $fail->id);

        return redirect()->route('fail.index')->with('success', 'Fail berjaya dikemaskini.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:fail,id'],
        ]);

        $toDelete = Fail::with('noRujukan')->whereIn('id', $request->ids)->get();
        $labels = $toDelete->map(fn($f) => ($f->noRujukan?->no_rujukan_full ?? '') . ' Jld.' . $f->jilid)->all();
        $count = $toDelete->count();

        // Drop pending (not-yet-disposed) pelupusan tied to these fails so the
        // cascade does not leave orphan rows. Disposed history (lupus_at set)
        // already has pemisahan_id detached and is preserved.
        $pemisahanIds = Pemisahan::whereIn('fail_id', $request->ids)->pluck('id');
        if ($pemisahanIds->isNotEmpty()) {
            Pelupusan::whereIn('pemisahan_id', $pemisahanIds)->whereNull('lupus_at')->delete();
        }

        Fail::whereIn('id', $request->ids)->delete();

        if ($count > 0) {
            History::log('Padam Fail', $count . ' rekod: ' . implode(', ', $labels), 'fail');
        }

        return redirect()->route('fail.index')->with('success', 'Fail berjaya dipadam.');
    }

    public function csvTemplate(): BinaryFileResponse
    {
        $noRujukans = NoRujukan::orderBy('siri')->get();
        $fakulti = Auth::user()->fakultiBahagian;

        $allowedJenis = [];
        if (!$fakulti || $fakulti->fail_am) {
            $allowedJenis[] = 'AM';
        }
        if (!$fakulti || $fakulti->fail_sulit) {
            if (!$fakulti || $fakulti->fail_pelajar) {
                $allowedJenis[] = 'SULIT-PELAJAR';
            }
            if (!$fakulti || $fakulti->fail_staff) {
                if (!$fakulti || $fakulti->fail_akademik) {
                    $allowedJenis[] = 'SULIT-STAFF-AKADEMIK';
                }
                if (!$fakulti || $fakulti->fail_pentadbiran) {
                    $allowedJenis[] = 'SULIT-STAFF-PENTADBIRAN';
                }
            }
        }
        $includeStudentId = !$fakulti || $fakulti->student_id;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('template_fail');

        // Header row
        $sheet->setCellValue('A1', 'noRujukan');
        $sheet->setCellValue('B1', 'jilid');
        $sheet->setCellValue('C1', 'tarikhPertama');
        $sheet->setCellValue('D1', 'jenisFail');
        if ($includeStudentId) {
            $sheet->setCellValue('E1', 'studentId');
        }

        // Style header
        $headerRange = $includeStudentId ? 'A1:E1' : 'A1:D1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(28);
        if ($includeStudentId) {
            $sheet->getColumnDimension('E')->setWidth(28);
            $sheet->getCell('E1')->setHyperlink(null);
            $sheet->getComment('E1')->getText()->createTextRun(
                'Student ID untuk SULIT-PELAJAR. Boleh isi lebih dari satu, asingkan dengan ; atau ,. Contoh: 0123456789;0987654321'
            );
        }

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

        // Data validation dropdown for jenisFail (column D), filtered by permission
        if (!empty($allowedJenis)) {
            $jenisOptions = '"' . implode(',', $allowedJenis) . '"';
            for ($r = 2; $r <= 501; $r++) {
                $jv = $sheet->getCell('D' . $r)->getDataValidation();
                $jv->setType(DataValidation::TYPE_LIST);
                $jv->setErrorStyle(DataValidation::STYLE_STOP);
                $jv->setAllowBlank(false);
                $jv->setShowDropDown(true);
                $jv->setShowErrorMessage(true);
                $jv->setErrorTitle('jenisFail tidak sah');
                $jv->setError('Sila pilih dari senarai jenisFail yang dibenarkan untuk fakulti/bahagian anda.');
                $jv->setFormula1($jenisOptions);
            }
        }

        // Seed sample row
        if ($noRujukans->count() > 0) {
            $sheet->setCellValue('A2', (string) $noRujukans->first()->no_rujukan_full);
            $sheet->setCellValue('B2', '1');
            $sheet->setCellValue('C2', '2024-01-01');
            $sheet->setCellValue('D2', $allowedJenis[0] ?? '');
            if ($includeStudentId) {
                $sheet->setCellValue('E2', '0123456789;0987654321');
            }
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
        $importFakulti = Auth::user()->fakultiBahagian;
        $expectedHeaders = ['noRujukan', 'jilid', 'tarikhPertama', 'jenisFail'];
        if (!$importFakulti || $importFakulti->student_id) {
            $expectedHeaders[] = 'studentId';
        }
        $colCount = count($expectedHeaders);

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
            $headers = array_map(fn($v) => is_string($v) ? trim($v) : $v, array_slice($data[0] ?? [], 0, $colCount));
            if ($headers !== $expectedHeaders) {
                return back()->withErrors(['csv_file' => 'Format tidak sah. Header mesti: ' . implode(', ', $expectedHeaders)]);
            }
            for ($i = 1; $i < count($data); $i++) {
                $r = $data[$i];
                // Skip fully empty rows
                if (count(array_filter($r, fn($v) => $v !== null && $v !== '')) === 0) continue;
                $rows[] = array_slice($r, 0, $colCount);
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

        $fakulti = Auth::user()->fakultiBahagian;

        $jenisMap = [
            'AM' => ['AM', null, null],
            'SULIT-PELAJAR' => ['SULIT', 'PELAJAR', null],
            'SULIT-STAFF-AKADEMIK' => ['SULIT', 'STAFF', 'AKADEMIK'],
            'SULIT-STAFF-PENTADBIRAN' => ['SULIT', 'STAFF', 'PENTADBIRAN'],
        ];

        foreach ($rows as $row) {
            $rowNum++;
            $row = array_pad(array_slice($row, 0, $colCount), $colCount, '');
            $noRujukanText = is_string($row[0]) ? trim($row[0]) : (string) $row[0];
            $jilid = $row[1];
            $tarikhPertama = $row[2];
            $jenisText = is_string($row[3]) ? strtoupper(trim($row[3])) : '';
            $studentIdText = $colCount >= 5 ? (is_string($row[4]) ? trim($row[4]) : (string) $row[4]) : '';

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

            $jenis = $kategori = $sub = null;
            if ($jenisText === '') {
                $errors[] = 'jenisFail diperlukan';
            } elseif (!isset($jenisMap[$jenisText])) {
                $errors[] = "jenisFail '{$jenisText}' tidak sah (AM, SULIT-PELAJAR, SULIT-STAFF-AKADEMIK, SULIT-STAFF-PENTADBIRAN)";
            } else {
                [$jenis, $kategori, $sub] = $jenisMap[$jenisText];
                if ($jenis === 'AM' && $fakulti && !$fakulti->fail_am) {
                    $errors[] = 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail AM';
                }
                if ($jenis === 'SULIT' && $fakulti && !$fakulti->fail_sulit) {
                    $errors[] = 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail SULIT';
                }
                if ($kategori === 'STAFF' && $fakulti && !$fakulti->fail_staff) {
                    $errors[] = 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail STAFF';
                }
                if ($kategori === 'PELAJAR' && $fakulti && !$fakulti->fail_pelajar) {
                    $errors[] = 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail PELAJAR';
                }
                if ($sub === 'AKADEMIK' && $fakulti && !$fakulti->fail_akademik) {
                    $errors[] = 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail AKADEMIK';
                }
                if ($sub === 'PENTADBIRAN' && $fakulti && !$fakulti->fail_pentadbiran) {
                    $errors[] = 'Fakulti/Bahagian anda tidak dibenarkan mendaftar Fail PENTADBIRAN';
                }
            }

            $studentIdsForRow = [];
            if ($jenis === 'SULIT' && $kategori === 'PELAJAR' && $fakulti && $fakulti->student_id) {
                if ($studentIdText === '') {
                    $errors[] = 'studentId diperlukan untuk SULIT-PELAJAR';
                } else {
                    $parts = preg_split('/[;,\s]+/', $studentIdText, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($parts as $p) {
                        if (!preg_match('/^\d+$/', $p)) {
                            $errors[] = "studentId '{$p}' mesti nombor sahaja";
                            break;
                        }
                        $studentIdsForRow[] = $p;
                    }
                    $studentIdsForRow = array_values(array_unique($studentIdsForRow));
                }
            }

            if (empty($errors) && $noRujukan) {
                $exists = Fail::where('no_rujukan_id', $noRujukan->id)->where('jilid', (int)$jilid)->exists();
                if ($exists) {
                    $errors[] = "jilid {$jilid} sudah wujud untuk noRujukan '{$noRujukanText}'";
                } else {
                    $disposed = \App\Models\Pelupusan::whereNotNull('lupus_at')
                        ->where('no_rujukan_id', $noRujukan->id)
                        ->where('jilid', (int)$jilid)
                        ->exists();
                    if ($disposed) {
                        $errors[] = "jilid {$jilid} untuk noRujukan '{$noRujukanText}' telah dilupuskan dan tidak boleh didaftarkan semula";
                    }
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
                'fakulti_bahagian_id' => Auth::user()->fakulti_bahagian_id,
                'jenis_fail' => $jenis,
                'kategori' => $kategori,
                'sub_kategori' => $sub,
            ]);

            foreach ($studentIdsForRow as $sid) {
                $fail->studentIds()->create(['student_id' => $sid]);
            }

            Pemisahan::create([
                'fail_id' => $fail->id,
                'fakulti_bahagian_id' => $fail->fakulti_bahagian_id,
                'person_in_charge' => Auth::user()->name,
            ]);

            $successCount++;
        }

        if (!empty($rowErrors)) {
            if ($successCount > 0) {
                History::log('Import Batch Fail', "{$successCount} rekod diimport");
            }
            return back()->with('csv_success', $successCount)->withErrors(['csv_rows' => implode("\n", $rowErrors)]);
        }

        History::log('Import Batch Fail', "{$successCount} rekod diimport");

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
