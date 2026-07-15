<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi – {{ $selectedDate->isoFormat('D MMMM Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background: #fff;
            padding: 30px 40px;
            font-size: 13px;
        }

        /* Header */
        .print-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #059669;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .print-header .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .print-header img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }
        .print-header h1 {
            font-size: 20px;
            font-weight: 800;
            color: #065f46;
            letter-spacing: -0.5px;
        }
        .print-header .subtitle {
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .print-header .date-badge {
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            border-radius: 10px;
            padding: 8px 16px;
            text-align: right;
        }
        .print-header .date-badge .label {
            font-size: 10px;
            color: #059669;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .print-header .date-badge .value {
            font-size: 14px;
            font-weight: 800;
            color: #064e3b;
        }

        /* Stats row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .stat-card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 14px 16px;
            background: #f8fafc;
        }
        .stat-card.green {
            background: #ecfdf5;
            border-color: #6ee7b7;
        }
        .stat-card.red {
            background: #fef2f2;
            border-color: #fca5a5;
        }
        .stat-card.gradient {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
        }
        .stat-card .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .stat-card.gradient .label { color: #a7f3d0; }
        .stat-card .value {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
        }
        .stat-card.green .value { color: #059669; }
        .stat-card.red .value { color: #dc2626; }
        .stat-card.gradient .value { color: #fff; }

        /* Table */
        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        thead tr {
            background: #f1fdf7;
        }
        thead th {
            padding: 9px 12px;
            text-align: left;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #059669;
            border-bottom: 2px solid #6ee7b7;
        }
        tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        tbody td {
            padding: 9px 12px;
            font-size: 12px;
            color: #334155;
        }
        .badge-hadir {
            display: inline-block;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #6ee7b7;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-absent {
            display: inline-block;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Belum Absen */
        .belum-section {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .belum-section .title {
            font-size: 11px;
            font-weight: 800;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .belum-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .belum-item {
            background: #fff;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 8px 12px;
        }
        .belum-item .name { font-weight: 700; font-size: 12px; color: #1e293b; }
        .belum-item .nim { font-size: 10px; color: #94a3b8; }

        /* Signature Area */
        .signature-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 32px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-box .role {
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 60px;
        }
        .signature-box .name-line {
            border-top: 2px solid #334155;
            padding-top: 6px;
            font-size: 13px;
            font-weight: 800;
            color: #1e293b;
        }

        /* Footer */
        .print-footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }

        @media print {
            body { padding: 20px 28px; }
            .no-print { display: none !important; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>

    {{-- Print Button (screen only) --}}
    <div class="no-print" style="margin-bottom:20px; display:flex; gap:10px;">
        <button onclick="window.print()" style="background:#059669;color:#fff;border:none;padding:10px 22px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
        <button onclick="window.history.back()" style="background:#f1f5f9;color:#475569;border:none;padding:10px 22px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;">
            ← Kembali
        </button>
    </div>

    {{-- Header --}}
    <div class="print-header">
        <div class="logo-area">
            <img src="{{ asset('logo_sirnaraja.png') }}" alt="Logo KKN Sirnaraja">
            <div>
                <h1>Absensi KKN Sirnaraja</h1>
                <div class="subtitle">Program KKN – Desa Sirnaraja</div>
                <div class="subtitle">Rekap Kehadiran Harian Mahasiswa</div>
            </div>
        </div>
        <div class="date-badge">
            <div class="label">Tanggal Rekap</div>
            <div class="value">{{ $selectedDate->isoFormat('D MMMM Y') }}</div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="label">Total Anggota</div>
            <div class="value">{{ $totalMembers }}</div>
        </div>
        <div class="stat-card green">
            <div class="label">Hadir</div>
            <div class="value">{{ $hadirCount }}</div>
        </div>
        <div class="stat-card red">
            <div class="label">Tidak Hadir</div>
            <div class="value">{{ $tidakHadirCount }}</div>
        </div>
        <div class="stat-card gradient">
            <div class="label">% Kehadiran</div>
            <div class="value">{{ $persentase }}%</div>
        </div>
    </div>

    {{-- Attendance table --}}
    <div class="section-title">Daftar Kehadiran</div>
    @if($attendances->isEmpty())
        <p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px;">Tidak ada data absensi pada tanggal ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:35px">No</th>
                    <th>Nama Lengkap</th>
                    <th>NIM</th>
                    <th>Jam Masuk</th>
                    <th>Lokasi</th>
                    <th style="text-align:center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $i => $a)
                <tr>
                    <td style="color:#94a3b8;font-weight:700;">{{ $i + 1 }}</td>
                    <td style="font-weight:700;">{{ $a->user->name }}</td>
                    <td style="font-family:monospace;color:#64748b;">{{ $a->user->nim ?? '-' }}</td>
                    <td style="font-weight:600;">{{ $a->check_in_at->format('H:i') }} WIB</td>
                    <td>{{ $a->location->name ?? 'GPS Posko' }}</td>
                    <td style="text-align:center;"><span class="badge-hadir">{{ $a->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Belum Absen --}}
    @if($belumAbsen->isNotEmpty())
    <div class="belum-section">
        <div class="title">⚠ Anggota Belum Absen ({{ $belumAbsen->count() }} orang)</div>
        <div class="belum-grid">
            @foreach($belumAbsen as $u)
            <div class="belum-item">
                <div class="name">{{ $u->name }}</div>
                <div class="nim">{{ $u->nim ?? 'NIM tidak tersedia' }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Signature --}}
    <div class="signature-row">
        <div class="signature-box">
            <div class="role">Koordinator Kelompok KKN</div>
            <div class="name-line">( _______________________________ )</div>
        </div>
        <div class="signature-box">
            <div class="role">Dosen Pembimbing Lapangan (DPL)</div>
            <div class="name-line">( _______________________________ )</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="print-footer">
        Dicetak oleh Sistem Absensi KKN Sirnaraja &bull; {{ now()->isoFormat('D MMMM Y, HH:mm') }} WIB &bull; Data bersifat resmi
    </div>

</body>
</html>
