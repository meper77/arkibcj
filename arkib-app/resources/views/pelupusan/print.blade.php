<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Borang Pelupusan Rekod — Kotak {{ $kotak }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; background: #fff; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm 15mm; }
        h1 { font-size: 14pt; font-weight: bold; text-align: center; margin-bottom: 6px; }
        h2 { font-size: 11pt; text-align: center; margin-bottom: 16px; }
        .header-block { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #660000; padding-bottom: 10px; }
        .logo-text { font-size: 16pt; font-weight: bold; color: #660000; }
        .meta-table { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
        .meta-table td { padding: 4px 8px; font-size: 10pt; vertical-align: top; }
        .meta-table .label { font-weight: bold; width: 55%; }
        table.records { width: 100%; border-collapse: collapse; margin-top: 12px; table-layout: fixed; }
        table.records th { background-color: #660000; color: white; padding: 6px 8px; font-size: 10pt; text-align: left; border: 1px solid #660000; }
        table.records td { padding: 5px 8px; font-size: 10pt; border: 1px solid #ccc; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
        table.records col.bil { width: 7%; }
        table.records col.no-fail { width: 22%; }
        table.records col.tajuk { width: 36%; }
        table.records col.tarikh { width: 14%; }
        table.records col.pic { width: 21%; }
        table.records td.nowrap { white-space: nowrap; }
        .sign-section { margin-top: 40px; display: flex; justify-content: space-between; }
        .sign-box { width: 45%; }
        .sign-line { border-top: 1px solid #000; margin-top: 40px; }
        .sign-label { font-size: 9pt; margin-top: 4px; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { width: 100%; padding: 10mm; }
            .no-print { display: none; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            .sign-section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="no-print" style="margin-bottom:12px;">
            <button onclick="window.print()" style="padding:8px 16px;background:#660000;color:white;border:none;border-radius:4px;cursor:pointer;font-size:12px;">Cetak</button>
            <button onclick="window.close()" style="margin-left:8px;padding:8px 16px;background:#666;color:white;border:none;border-radius:4px;cursor:pointer;font-size:12px;">Tutup</button>
        </div>

        <div class="header-block">
            <div class="logo-text">UiTM</div>
            <h1>BORANG PELUPUSAN REKOD</h1>
            <h2>Universiti Teknologi MARA</h2>
        </div>

        @php
            $firstP = $pelupusans->first();
            $years = $pelupusans->map(fn($p) => [
                $p->pemisahan->fail->tarikh_pertama?->year,
                $p->pemisahan->fail->tarikh_akhir?->year,
            ])->flatten()->filter()->sort();
            $tahun = $years->isNotEmpty() ? $years->first() . '/' . $years->last() : '—';
            $jumlah = $pelupusans->count();
        @endphp

        <table class="meta-table">
            <tr>
                <td class="label">Fakulti / Bahagian / Pusat / Unit / Cawangan:</td>
                <td>{{ $user->fakultiBahagian?->nama ?? '' }}{{ $user->cawangan ? ' — ' . $user->cawangan : '' }}</td>
            </tr>
            <tr>
                <td class="label">Tarikh Permohonan:</td>
                <td>{{ now()->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">No. Kotak:</td>
                <td>{{ $kotak }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Rekod:</td>
                <td>{{ $tahun }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Fail:</td>
                <td>{{ $jumlah }}</td>
            </tr>
        </table>

        <table class="records">
            <colgroup>
                <col class="bil">
                <col class="no-fail">
                <col class="tajuk">
                <col class="tarikh">
                <col class="pic">
            </colgroup>
            <thead>
                <tr>
                    <th>BIL.</th>
                    <th>NO. FAIL</th>
                    <th>TAJUK FAIL</th>
                    <th>TARIKH LUPUS</th>
                    <th>PERSON IN CHARGE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pelupusans as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->pemisahan->fail->noRujukan->no_rujukan_full }}</td>
                    <td>{{ $p->pemisahan->fail->noRujukan->perkara }} — Jilid {{ $p->pemisahan->fail->jilid }}</td>
                    <td class="nowrap">{{ $p->lupus_at?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $p->person_in_charge ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="sign-section">
            <div class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-label">Disediakan oleh:<br>{{ $user->name }}<br>{{ $user->position ?? '' }}</div>
            </div>
            <div class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-label">Disahkan oleh:<br>&nbsp;<br>&nbsp;</div>
            </div>
        </div>
    </div>
</body>
</html>
