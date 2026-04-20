<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;

/**
 * Merge data into the /res/*.docx templates that ship with the Arkib app.
 *
 * Each template has been pre-tokenised (see /res/*.original.docx for untouched
 * copies) with ${placeholder} tokens matching the field names used below. For
 * the tabular reports (pemisahan, pelupusan) a single data-row remains in the
 * template; TemplateProcessor::cloneRow() is used to replicate it per record.
 *
 * Each build* method returns an absolute path to a temp .docx file ready to be
 * streamed to the browser.
 */
class DocTemplateService
{
    private function templatePath(string $filename): string
    {
        return base_path('../res/' . $filename);
    }

    private function newProcessor(string $filename): TemplateProcessor
    {
        return new TemplateProcessor($this->templatePath($filename));
    }

    private function saveTmp(TemplateProcessor $tp, string $prefix): string
    {
        $tmp = tempnam(sys_get_temp_dir(), $prefix . '_') . '.docx';
        $tp->saveAs($tmp);
        return $tmp;
    }

    private function kampusLine($user): string
    {
        return trim(
            ($user->kampus ?? '')
            . ($user->cawangan ? ' — ' . $user->cawangan : '')
            . ($user->fakulti_bahagian ? ' — ' . $user->fakulti_bahagian : '')
        );
    }

    private function tahunRange($items, callable $extractFail): string
    {
        $years = collect();
        foreach ($items as $it) {
            $fail = $extractFail($it);
            if ($fail && $fail->tarikh_pertama) $years->push($fail->tarikh_pertama->year);
            if ($fail && $fail->tarikh_akhir)   $years->push($fail->tarikh_akhir->year);
        }
        $years = $years->filter()->sort()->values();
        return $years->isNotEmpty() ? $years->first() . '/' . $years->last() : '-';
    }

    /**
     * Legacy resolver kept for back-compat. Copies the template so callers that
     * still rely on the old API keep working.
     */
    public function resolve(string $filename, array $data = []): string
    {
        $sourcePath = $this->templatePath($filename);
        $docxVariant = $this->templatePath(pathinfo($filename, PATHINFO_FILENAME) . '.docx');
        $templatePath = file_exists($docxVariant) ? $docxVariant : $sourcePath;
        $ext = pathinfo($templatePath, PATHINFO_EXTENSION);
        $tmpPath = tempnam(sys_get_temp_dir(), 'arkib_') . '.' . $ext;
        if (file_exists($templatePath)) {
            copy($templatePath, $tmpPath);
        }
        return $tmpPath;
    }

    /* ------------------------------------------------------------------ */
    /* Pemisahan                                                          */
    /* ------------------------------------------------------------------ */

    /**
     * Borang Pemisahan Rekod — template tokens:
     *   ${kampus} ${kotak} ${tarikh} ${user} ${tujuan}
     *   Row: ${bil} ${no_fail} ${tajuk} ${tarikh_pemisahan}
     *        ${dibuka} ${ditutup} ${tujuan_row} ${no_kotak}
     *
     * @param string $kotak
     * @param \App\Models\User $user
     * @param \Illuminate\Support\Collection $pemisahans eager-loaded with fail.noRujukan
     */
    public function buildPemisahan(string $kotak, $user, $pemisahans): string
    {
        $tp = $this->newProcessor('borangPemisahanRekod.docx');
        $first = $pemisahans->first();

        $tp->setValue('kampus', $this->kampusLine($user));
        $tp->setValue('kotak', (string) $kotak);
        $tp->setValue('tarikh', optional($first?->tarikh_pemisahan)->format('d/m/Y') ?? now()->format('d/m/Y'));
        $tp->setValue('user', (string) ($user->name ?? ''));
        $tp->setValue('tujuan', (string) ($first?->tujuan_pemisahan ?? ''));

        $rows = max(1, $pemisahans->count());
        $tp->cloneRow('bil', $rows);

        $i = 0;
        foreach ($pemisahans as $p) {
            $i++;
            $tp->setValue('bil#' . $i, (string) $i);
            $tp->setValue('no_fail#' . $i, (string) $p->fail->noRujukan->no_rujukan_full);
            $tp->setValue('tajuk#' . $i, $p->fail->noRujukan->perkara . ' — Jilid ' . $p->fail->jilid);
            $tp->setValue('tarikh_pemisahan#' . $i, optional($p->tarikh_pemisahan)->format('d/m/Y') ?? '-');
            $tp->setValue('dibuka#' . $i, optional($p->fail->tarikh_pertama)->format('d/m/Y') ?? '-');
            $tp->setValue('ditutup#' . $i, optional($p->fail->tarikh_akhir)->format('d/m/Y') ?? '-');
            $tp->setValue('tujuan_row#' . $i, (string) ($p->tujuan_pemisahan ?? ''));
            $tp->setValue('no_kotak#' . $i, (string) ($p->fail->kotak ?? '-'));
        }
        // If no rows, blank out placeholders in the single cloned row.
        if ($pemisahans->isEmpty()) {
            foreach (['bil','no_fail','tajuk','tarikh_pemisahan','dibuka','ditutup','tujuan_row','no_kotak'] as $k) {
                $tp->setValue($k . '#1', '');
            }
        }

        return $this->saveTmp($tp, 'pemisahan');
    }

    /* ------------------------------------------------------------------ */
    /* Label helpers                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Fills a fail list for the Pentadbiran/Staf labels. The template has
     * a fixed number of dotted-line slots (${fail1}..${failN}); we fill
     * the first N with fail labels and blank any unused slots.
     */
    private function fillFailList(TemplateProcessor $tp, $pemisahans, int $maxFailTokens): void
    {
        $i = 0;
        foreach ($pemisahans as $p) {
            $i++;
            if ($i > $maxFailTokens) break;
            $label = $p->fail->noRujukan->no_rujukan_full
                . ' — ' . $p->fail->noRujukan->perkara
                . ' (Jilid ' . $p->fail->jilid . ')';
            $tp->setValue('fail' . $i, $label);
        }
        for ($j = $i + 1; $j <= $maxFailTokens; $j++) {
            $tp->setValue('fail' . $j, '');
        }
    }

    /**
     * labelFailPentadbiranLatest.docx — tokens injected:
     *   ${no_penerimaan} ${nilai_kaji} ${kotak} ${fakulti} ${cawangan}
     *   ${tahun} ${jumlah_fail} ${fail1..fail8} ${user} ${tarikh}
     * (8 dotted-line slots; PYB signature only — the BAU signature row is
     *  left as manual-fill underscores per template design.)
     */
    public function buildLabelPentadbiran(string $kotak, $user, $pemisahans): string
    {
        $tp = $this->newProcessor('labelFailPentadbiranLatest.docx');
        $tp->setValue('kotak', str_pad($kotak, 3, '0', STR_PAD_LEFT));
        $tp->setValue('fakulti', (string) ($user->fakulti_bahagian ?? '-'));
        $tp->setValue('cawangan', (string) ($user->cawangan ?? '-'));
        $tp->setValue('tahun', $this->tahunRange($pemisahans, fn($p) => $p->fail));
        $tp->setValue('jumlah_fail', (string) $pemisahans->count());
        $tp->setValue('no_penerimaan', '-');
        $tp->setValue('nilai_kaji', '-');
        $tp->setValue('user', (string) ($user->name ?? ''));
        $tp->setValue('tarikh', now()->format('d/m/Y'));
        $this->fillFailList($tp, $pemisahans, 8);
        return $this->saveTmp($tp, 'label_pentadbiran');
    }

    /**
     * labelFailStafLatest.docx — tokens injected:
     *   ${no_penerimaan} ${kotak} ${fakulti} ${cawangan} ${tahun}
     *   ${jumlah_fail} ${fail1..fail9} ${tarikh} ${user} ${tarikh2}
     * (9 dotted-line slots. ${tarikh} = Tarikh Penerimaan Rekod;
     *  ${tarikh2} = Disusun-Oleh tarikh — both set to today by default.)
     */
    public function buildLabelStaf(string $kotak, $user, $pemisahans): string
    {
        $tp = $this->newProcessor('labelFailStafLatest.docx');
        $today = now()->format('d/m/Y');
        $tp->setValue('kotak', str_pad($kotak, 3, '0', STR_PAD_LEFT));
        $tp->setValue('fakulti', (string) ($user->fakulti_bahagian ?? '-'));
        $tp->setValue('cawangan', (string) ($user->cawangan ?? '-'));
        $tp->setValue('tahun', $this->tahunRange($pemisahans, fn($p) => $p->fail));
        $tp->setValue('jumlah_fail', (string) $pemisahans->count());
        $tp->setValue('no_penerimaan', '-');
        $tp->setValue('tarikh', $today);
        $tp->setValue('tarikh2', $today);
        $tp->setValue('user', (string) ($user->name ?? ''));
        $this->fillFailList($tp, $pemisahans, 9);
        return $this->saveTmp($tp, 'label_staf');
    }

    /**
     * labelFailPelajarLatest.docx — tokens injected:
     *   ${fakulti} ${abjad} ${tahun} ${jumlah_fail}
     *   ${kotak1} ${kotak2} ${kotak3}  (3-digit box row)
     *   ${nama} ${tarikh}
     */
    public function buildLabelPelajar(string $kotak, $user, $pemisahans): string
    {
        $tp = $this->newProcessor('labelFailPelajarLatest.docx');
        $tahun = $this->tahunRange($pemisahans, fn($p) => $p->fail);
        $kotakPadded = str_pad($kotak, 3, '0', STR_PAD_LEFT);

        $first = $pemisahans->first();
        $nama = $first ? ($first->fail->noRujukan->perkara ?? '-') : '-';
        $abjad = $first ? strtoupper(substr((string) ($first->fail->noRujukan->perkara ?? ''), 0, 1)) : '-';

        $tp->setValue('fakulti', (string) ($user->fakulti_bahagian ?? '-'));
        $tp->setValue('abjad', $abjad);
        $tp->setValue('tahun', $tahun);
        $tp->setValue('jumlah_fail', (string) $pemisahans->count());
        $tp->setValue('nama', $nama);
        $tp->setValue('tarikh', now()->format('d/m/Y'));
        // Three single-digit boxes for the kotak number
        $tp->setValue('kotak1', (string) substr($kotakPadded, 0, 1));
        $tp->setValue('kotak2', (string) substr($kotakPadded, 1, 1));
        $tp->setValue('kotak3', (string) substr($kotakPadded, 2, 1));

        return $this->saveTmp($tp, 'label_pelajar');
    }

    /* ------------------------------------------------------------------ */
    /* Pelupusan                                                          */
    /* ------------------------------------------------------------------ */

    /**
     * Borang Pelupusan Rekod — template tokens:
     *   ${fakulti} ${tarikh} ${tahun} ${jumlah_fail} ${kotak} ${jenis_bahan}
     *   Row: ${bil} ${tajuk} ${tahun_row} ${jumlah_row} ${bil_folio} ${catatan}
     *
     * @param string $kotak
     * @param \App\Models\User $user
     * @param \Illuminate\Support\Collection $pelupusans eager-loaded with pemisahan.fail.noRujukan
     */
    public function buildPelupusan(string $kotak, $user, $pelupusans): string
    {
        $tp = $this->newProcessor('borangPelupusanRekod.docx');
        $tahun = $this->tahunRange($pelupusans, fn($p) => $p->pemisahan?->fail);

        $tp->setValue('fakulti', trim(
            ($user->fakulti_bahagian ?? '')
            . ($user->cawangan ? ' — ' . $user->cawangan : '')
        ));
        $tp->setValue('tarikh', now()->format('d/m/Y'));
        $tp->setValue('tahun', $tahun);
        $tp->setValue('jumlah_fail', (string) $pelupusans->count());
        $tp->setValue('kotak', (string) $kotak);
        $tp->setValue('jenis_bahan', 'Rekod Pentadbiran');

        $rows = max(1, $pelupusans->count());
        $tp->cloneRow('bil', $rows);

        $i = 0;
        foreach ($pelupusans as $p) {
            $i++;
            $fail = $p->pemisahan?->fail;
            if ($fail) {
                $tajuk = ($fail->noRujukan->no_rujukan_full ?? '') . ' — ' . ($fail->noRujukan->perkara ?? '') . ' (Jilid ' . ($fail->jilid ?? '') . ')';
                $tahunRow = optional($fail->tarikh_pertama)->format('Y') ?? '-';
            } else {
                $tajuk = (string) ($p->tajuk_fail ?? '-');
                $tahunRow = optional($p->lupus_at)->format('Y') ?? '-';
            }
            $tp->setValue('bil#' . $i, (string) $i);
            $tp->setValue('tajuk#' . $i, $tajuk);
            $tp->setValue('tahun_row#' . $i, $tahunRow);
            $tp->setValue('jumlah_row#' . $i, '1');
            $tp->setValue('bil_folio#' . $i, '-');
            $tp->setValue('catatan#' . $i, (string) ($p->person_in_charge ?? ''));
        }
        if ($pelupusans->isEmpty()) {
            foreach (['bil','tajuk','tahun_row','jumlah_row','bil_folio','catatan'] as $k) {
                $tp->setValue($k . '#1', '');
            }
        }

        return $this->saveTmp($tp, 'pelupusan');
    }
}
