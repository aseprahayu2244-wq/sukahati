<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPM_{{ Str::slug($bpm->nomor_dokumen) }}_{{ Str::slug($bpm->nama_bpm) }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            margin: 0;
            padding: 20px;
            font-size: 11pt;
            line-height: 1.4;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-box {
            width: 65px;
            height: 65px;
        }

        .company-title {
            font-size: 15pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .company-sub {
            font-size: 8.5pt;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 3px 0;
        }

        .company-addr {
            font-size: 8pt;
            color: #64748b;
            margin: 0;
        }

        .doc-badge {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #cbd5e1;
        }

        .meta-table td {
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            font-size: 9.5pt;
        }

        .meta-label {
            background: #f8fafc;
            font-weight: 700;
            color: #475569;
            width: 25%;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }

        .section-header {
            font-size: 10pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 4px;
            margin: 24px 0 10px 0;
        }

        .raci-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }

        .raci-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .raci-table th {
            background: #f1f5f9;
            border: 1px solid #94a3b8;
            padding: 8px 6px;
            font-size: 8.5pt;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
        }

        .raci-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 9pt;
            vertical-align: middle;
        }

        .level-tag {
            display: inline-block;
            font-size: 7.5pt;
            font-weight: 800;
            padding: 2px 5px;
            border-radius: 3px;
            border: 1px solid #cbd5e1;
            margin-right: 4px;
        }

        .level-L1 {
            background: #e0e7ff;
            color: #3730a3;
            border-color: #c7d2fe;
            font-weight: 800;
        }

        .level-L2 {
            background: #e0f2fe;
            color: #0369a1;
            border-color: #bae6fd;
            font-weight: 700;
        }

        .level-L3 {
            background: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
            font-weight: 600;
        }

        .row-l1 {
            background: #f8fafc;
            font-weight: 700;
        }

        .indent-l1 { padding-left: 8px; }
        .indent-l2 { padding-left: 26px; }
        .indent-l3 { padding-left: 48px; }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .sign-table td {
            width: 50%;
            text-align: center;
            border: 1px solid #cbd5e1;
            padding: 12px;
            vertical-align: top;
        }

        .no-print-bar {
            background: #0f172a;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-print {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 9pt;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- On screen action bar (hidden during print) -->
    <div class="no-print-bar">
        <div>
            <strong>Pratinjau Cetak / Ekspor PDF BPM</strong>
            <span style="font-size: 8.5pt; opacity: 0.8; margin-left: 8px;">Gunakan menu cetak browser atau Save as PDF.</span>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">Cetak Dokumen Sekarang</button>
        </div>
    </div>

    <!-- Kop Surat Resmi -->
    <table class="header-table">
        <tr>
            <td style="width: 75px;">
                <img src="{{ asset('images/logo-sukahati.png') }}" class="logo-box" alt="Logo">
            </td>
            <td>
                <h1 class="company-title">CV. SUKAHATI PRATAMA</h1>
                <p class="company-sub">Standard Operating Procedure & Business Process Management</p>
                <p class="company-addr">Jl. Sukahati No. 123, Cibinong, Bogor &bull; Telp: (021) 87654321</p>
            </td>
            <td style="text-align: right; width: 220px;">
                <div class="doc-badge">DOKUMEN RESMI BPM</div>
                <div style="font-size: 9pt; font-family: monospace; font-weight: bold; margin-top: 4px;">{{ $bpm->nomor_dokumen }}</div>
                <div style="font-size: 8.5pt; color: #64748b;">Versi: {{ $bpm->version }}</div>
            </td>
        </tr>
    </table>

    <!-- Meta Information -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nama BPM</td>
            <td colspan="3" style="font-weight: bold; font-size: 11pt;">{{ $bpm->nama_bpm }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nomor Dokumen</td>
            <td style="font-family: monospace; font-weight: bold;">{{ $bpm->nomor_dokumen }}</td>
            <td class="meta-label">Tanggal Efektif</td>
            <td>{{ $bpm->tanggal_efektif ? $bpm->tanggal_efektif->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Direktorat (BOD)</td>
            <td>{{ $bpm->bod?->name ?? '-' }}</td>
            <td class="meta-label">Departemen</td>
            <td>{{ $bpm->department?->name ?? '-' }}</td>
        </tr>
        @if($bpm->deskripsi)
        <tr>
            <td class="meta-label">Deskripsi Proses</td>
            <td colspan="3">{{ $bpm->deskripsi }}</td>
        </tr>
        @endif
    </table>

    <!-- Section 1: Business Process Hierarchy & RACI Table -->
    @if($bpm->isBuilder())
        <div class="section-header">
            BAGIAN I &bull; TAHAPAN PROSES BISNIS (L1, L2, L3) & MATRIKS RACI
        </div>

        <table class="raci-table">
            <thead>
                <tr>
                    <th style="text-align: left; width: 44%;">Level & Uraian Aktivitas</th>
                    <th style="width: 14%;">Responsible (R)</th>
                    <th style="width: 14%;">Accountable (A)</th>
                    <th style="width: 14%;">Consulted (C)</th>
                    <th style="width: 14%;">Informed (I)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bpm->steps as $step)
                    @php
                        $indentClass = match($step->level) {
                            'L1' => 'indent-l1 row-l1',
                            'L2' => 'indent-l2',
                            'L3' => 'indent-l3',
                            default => 'indent-l1',
                        };
                        $arrow = match($step->level) {
                            'L1' => '',
                            'L2' => '↳ ',
                            'L3' => '↳ ↳ ',
                            default => '',
                        };
                    @endphp
                    <tr class="{{ $step->level === 'L1' ? 'row-l1' : '' }}">
                        <td class="{{ $indentClass }}">
                            <span style="color: #94a3b8; font-family: monospace;">{{ $arrow }}</span>
                            <span class="level-tag level-{{ $step->level }}">{{ $step->level }}</span>
                            {{ $step->activity_description }}
                        </td>
                        <td style="text-align: center; font-size: 8.5pt;">
                            {{ $step->responsible->pluck('position.name')->join(', ') ?: '-' }}
                        </td>
                        <td style="text-align: center; font-size: 8.5pt; font-weight: bold;">
                            {{ $step->accountable->pluck('position.name')->join(', ') ?: '-' }}
                        </td>
                        <td style="text-align: center; font-size: 8.5pt;">
                            {{ $step->consulted->pluck('position.name')->join(', ') ?: '-' }}
                        </td>
                        <td style="text-align: center; font-size: 8.5pt;">
                            {{ $step->informed->pluck('position.name')->join(', ') ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                            Belum ada tahapan proses.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="font-size: 8pt; color: #64748b; font-style: italic; margin-bottom: 20px;">
            Catatan RACI: R = Responsible (Pelaksana), A = Accountable (Penanggung Jawab Akhir), C = Consulted (Pihak Konsultasi), I = Informed (Pihak Penerima Informasi).
        </div>
    @else
        <div class="section-header">
            BAGIAN I &bull; BERKAS DOKUMEN PROSES BISNIS
        </div>
        <div style="border: 1px solid #cbd5e1; padding: 15px; text-align: center; background: #f8fafc; font-size: 9.5pt;">
            @foreach($bpm->attachments as $att)
                <strong>{{ $att->file_name }}</strong> ({{ $att->formatted_size }})
            @endforeach
        </div>
    @endif

    <!-- Section 2: Lembar Pengesahan -->
    <div class="section-header">
        BAGIAN II &bull; LEMBAR PENGESAHAN DOKUMEN
    </div>

    <table class="sign-table">
        <tr>
            <td>
                <div style="font-size: 8pt; font-weight: 700; color: #64748b; text-transform: uppercase;">Diajukan Oleh,</div>
                <div style="font-size: 8.5pt; color: #94a3b8; margin-top: 2px;">Admin Penyusun BPM</div>
                <div style="height: 60px;"></div>
                <div style="font-weight: 800; text-decoration: underline; font-size: 10pt;">
                    {{ $bpm->creator?->name ?? 'Admin Departemen' }}
                </div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                    Tanggal: {{ $bpm->submitted_at ? $bpm->submitted_at->format('d/m/Y') : '-' }}
                </div>
            </td>
            <td>
                <div style="font-size: 8pt; font-weight: 700; color: #64748b; text-transform: uppercase;">Disetujui & Disahkan Oleh,</div>
                <div style="font-size: 8.5pt; color: #94a3b8; margin-top: 2px;">Super Admin / Direksi</div>
                <div style="height: 60px;"></div>
                <div style="font-weight: 800; text-decoration: underline; font-size: 10pt;">
                    {{ $bpm->approver?->name ?? '( Belum Disetujui )' }}
                </div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                    Tanggal: {{ $bpm->approved_at ? $bpm->approved_at->format('d/m/Y') : '-' }}
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

