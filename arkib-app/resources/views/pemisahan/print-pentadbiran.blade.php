<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Label Fail Pentadbiran — Kotak {{ $kotak }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; background: #fff; }
        .label-card { width: 148mm; min-height: 105mm; margin: 10mm auto; border: 2px solid #660000; padding: 10mm; }
        .label-title { font-size: 14pt; font-weight: bold; color: #660000; text-align: center; border-bottom: 1px solid #660000; padding-bottom: 6px; margin-bottom: 10px; }
        .row { display: flex; margin-bottom: 6px; }
        .row .lbl { font-weight: bold; width: 55mm; font-size: 10pt; }
        .row .val { font-size: 10pt; flex: 1; }
        table.fails { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9pt; }
        table.fails th { background: #660000; color: white; padding: 4px 6px; text-align: left; }
        table.fails td { padding: 3px 6px; border-bottom: 1px solid #ddd; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin:10px;">
        <button onclick="window.print()" style="padding:8px 16px;background:#660000;color:white;border:none;border-radius:4px;cursor:pointer;">Cetak</button>
        <button onclick="window.close()" style="margin-left:8px;padding:8px 16px;background:#666;color:white;border:none;border-radius:4px;cursor:pointer;">Tutup</button>
    </div>

    @php
        $firstFail = $pemisahans->first()?->fail;
        $years = $pemisahans->map(fn($p) => [
            $p->fail->tarikh_pertama?->year,
            $p->fail->tarikh_akhir?->year,
        ])->flatten()->filter()->sort();
        $tahunRekod = $years->isNotEmpty() ? $years->first() . '/' . $years->last() : '—';
        $jumlahFail = $pemisahans->count();
    @endphp

    <div class="label-card">
        <div class="label-title">LABEL FAIL PENTADBIRAN</div>

        <div class="row">
            <span class="lbl">No. Kotak:</span>
            <span class="val">{{ $kotak }}</span>
        </div>
        <div class="row">
            <span class="lbl">Fakulti/Bahagian:</span>
            <span class="val">{{ $user->fakultiBahagian?->nama ?? '—' }}</span>
        </div>
        <div class="row">
            <span class="lbl">Cawangan:</span>
            <span class="val">{{ $user->cawangan ?? '—' }}</span>
        </div>
        <div class="row">
            <span class="lbl">Tahun Rekod:</span>
            <span class="val">{{ $tahunRekod }}</span>
        </div>
        <div class="row">
            <span class="lbl">Jumlah Fail:</span>
            <span class="val">{{ $jumlahFail }}</span>
        </div>

        <table class="fails">
            <thead>
                <tr>
                    <th>Bil. Fail</th>
                    <th>No. Fail</th>
                    <th>Tajuk Fail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemisahans as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->fail->noRujukan->no_rujukan_full }}</td>
                    <td>{{ $p->fail->noRujukan->perkara }} — Jilid {{ $p->fail->jilid }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
