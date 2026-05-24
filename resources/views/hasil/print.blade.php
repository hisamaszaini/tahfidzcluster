<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Akhir Clustering - TahfidzCluster</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #0f172a;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header-container {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .logo-img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-right: 15px;
        }
        .header-text {
            flex-grow: 1;
            text-align: center;
            margin-right: 10px;
        }
        .header-text h1 {
            font-size: 15pt;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header-text h2 {
            font-size: 12pt;
            margin: 0 0 4px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-text p {
            font-size: 9pt;
            margin: 0;
            color: #334155;
        }
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 10pt;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .signature-container {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 220px;
        }
        .signature-space {
            height: 65px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            body {
                padding: 0;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>
    <!-- Official Kop Surat Header -->
    <div class="header-container">
        <img class="logo-img" src="{{ asset('logo.png') }}" alt="Logo">
        <div class="header-text">
            <h1>Pondok Pesantren Tahfidz Al-Qur'an Karangmojo Balong Ponorogo</h1>
            <h2>Laporan Hasil Akhir Analisis Clustering Santri</h2>
            <p>Alamat: Jl. Agung Dukuh Blender, RT.01/RW.02, Desa Karangmojo, Kec. Balong, Kab. Ponorogo, Jawa Timur</p>
        </div>
    </div>
    
    <!-- Document Title -->
    <div class="doc-title">Laporan Pengelompokan Karakteristik Tahfidz Santri</div>
    
    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Santri</th>
                <th style="width: 15%;">Hafalan</th>
                <th style="width: 15%;">Murojaah</th>
                <th style="width: 15%;">Tahsin</th>
                <th style="width: 10%;">Rata-Rata</th>
                <th style="width: 15%;">Kluster</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hasils as $hasil)
                @php
                    $hafalan = 0;
                    $murojaah = 0;
                    $tahsin = 0;
                    foreach($hasil->santri->nilai as $nilai) {
                        if($nilai->kriteria_id == 1) $hafalan = $nilai->nilai;
                        if($nilai->kriteria_id == 2) $murojaah = $nilai->nilai;
                        if($nilai->kriteria_id == 3) $tahsin = $nilai->nilai;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td style="color: #0f172a;">{{ $hasil->santri->nama }}</td>
                    <td class="text-center" style="color: #334155;">{{ $hafalan }}</td>
                    <td class="text-center" style="color: #334155;">{{ $murojaah }}</td>
                    <td class="text-center" style="color: #334155;">{{ $tahsin }}</td>
                    <td class="text-center" style="color: #0f172a;">{{ $hasil->skor_rata }}</td>
                    <td class="text-center">
                        @php
                            $color = $hasil->kluster === 'Sangat Baik' ? '#047857' : ($hasil->kluster === 'Baik' ? '#4338ca' : '#b45309');
                        @endphp
                        <span {!! 'style="color: ' . $color . ';"' !!}>{{ $hasil->kluster }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>
</body>
</html>
