<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Label Fail Pelajar — Kotak {{ $kotak }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; background: #fff; }
        .label-card { width: 105mm; min-height: 74mm; margin: 10mm auto; border: 2px solid #660000; padding: 8mm; }
        .label-title { font-size: 13pt; font-weight: bold; color: #660000; text-align: center; border-bottom: 1px solid #660000; padding-bottom: 6px; margin-bottom: 8px; }
        .row { display: flex; margin-bottom: 5px; }
        .row .lbl { font-weight: bold; width: 45mm; font-size: 10pt; }
        .row .val { font-size: 10pt; flex: 1; }
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
        $years = $pemisahans->map(fn($p) => [
            $p->fail->tarikh_pertama?->year,
            $p->fail->tarikh_akhir?->year,
        ])->flatten()->filter()->sort();
        $tahun = $years->isNotEmpty() ? $years->first() . '/' . $years->last() : '—';
        $jumlahFail = $pemisahans->count();
        $kotakPadded = str_pad($kotak, 3, '0', STR_PAD_LEFT);
    @endphp

    <div class="label-card">
        <div class="label-title">LABEL FAIL PELAJAR</div>

        <div class="row">
            <span class="lbl">No. Kotak:</span>
            <span class="val">{{ $kotakPadded }}</span>
        </div>
        <div class="row">
            <span class="lbl">Fakulti:</span>
            <span class="val">{{ $user->fakulti_bahagian ?? '—' }}</span>
        </div>
        <div class="row">
            <span class="lbl">Tahun:</span>
            <span class="val">{{ $tahun }}</span>
        </div>
        <div class="row">
            <span class="lbl">Jumlah Fail:</span>
            <span class="val">{{ $jumlahFail }}</span>
        </div>
    </div>
</body>
</html>
