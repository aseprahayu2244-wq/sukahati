<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sop->nomor_dokumen }} - {{ $sop->nama_sop }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Times+New+Roman&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #1e293b;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
            font-size: 12pt;
            line-height: 1.6;
        }

        .paper-container {
            background: white;
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 2.5cm 2cm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            box-sizing: border-box;
        }

        .no-print-bar {
            max-width: 210mm;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1e293b;
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
        }

        .btn-print {
            background: #10b981;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print:hover { background: #059669; }

        .btn-close {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            text-decoration: none;
        }

        /* Official SOP Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #0f172a;
            margin-bottom: 24px;
        }

        .header-table td, .header-table th {
            border: 1px solid #0f172a;
            padding: 8px 12px;
            vertical-align: middle;
        }

        .header-logo {
            width: 90px;
            text-align: center;
            padding: 6px;
        }

        .header-logo img {
            max-width: 75px;
            max-height: 60px;
            object-contain: contain;
        }

        .header-title-box {
            text-align: center;
        }

        .company-name {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .sop-title {
            font-size: 14pt;
            font-weight: 800;
            margin-top: 4px;
            color: #1e40af;
            text-transform: uppercase;
        }

        .sop-name {
            font-size: 12pt;
            font-weight: 700;
            margin-top: 2px;
            color: #334155;
        }

        .meta-table {
            font-size: 9pt;
            width: 220px;
        }

        .meta-table td {
            padding: 4px 8px;
        }

        /* Section Titles */
        .section-header {
            background: #f1f5f9;
            border-left: 4px solid #1e40af;
            padding: 6px 12px;
            font-weight: 800;
            font-size: 11pt;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 8px;
            page-break-after: avoid;
        }

        .section-body {
            font-size: 10pt;
            text-align: justify;
            margin-bottom: 16px;
            white-space: pre-line;
            line-height: 1.6;
        }

        /* Tables */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 16px;
        }

        .content-table th, .content-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
        }

        .content-table th {
            background: #f8fafc;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Signatures Block */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px 20px;
        }

        .signature-box {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 16px;
            background: #f8fafc;
        }

        .signature-space {
            height: 70px;
        }

        .signer-name {
            font-weight: 800;
            font-size: 10.5pt;
            color: #0f172a;
            text-decoration: underline;
        }

        .signer-role {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* Flowchart */
        .flowchart-img {
            max-width: 100%;
            max-height: 500px;
            display: block;
            margin: 10px auto;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .paper-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }

            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Non-Print Controls -->
    <div class="no-print-bar">
        <div>
            <strong>SIMA HRD &bull; Download Dokumen SOP (PDF)</strong>
            <span style="font-size: 11px; opacity: 0.8; margin-left: 8px;">(Format standar A4)</span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button onclick="window.print()" class="btn-print">
                <i class="fa-solid fa-file-arrow-down"></i> Download PDF
            </button>
            <a href="{{ route('sops.show', $sop->id) }}" class="btn-close">Kembali</a>
        </div>
    </div>

    <!-- Printable Paper Sheet -->
    <div class="paper-container">

        <!-- Official Header Table -->
        <table class="header-table">
            <tr>
                <td class="header-logo" rowspan="2">
                    <img src="{{ asset('images/logo-sukahati.png') }}" alt="Logo">
                </td>
                <td class="header-title-box">
                    <div class="company-name">CV. SUKAHATI PRATAMA</div>
                    <div class="sop-title">STANDARD OPERATING PROCEDURE</div>
                </td>
                <td rowspan="2" style="padding: 0; vertical-align: top;">
                    <table class="meta-table" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold; background: #f8fafc; width: 45%;">No. Dokumen</td>
                            <td>: <strong>{{ $sop->nomor_dokumen }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; background: #f8fafc;">No. Revisi</td>
                            <td>: {{ $sop->version }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; background: #f8fafc;">Tgl. Efektif</td>
                            <td>: {{ $sop->tanggal_efektif ? $sop->tanggal_efektif->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; background: #f8fafc;">Departemen</td>
                            <td>: {{ $sop->department?->name ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="header-title-box">
                    <div class="sop-name">{{ $sop->nama_sop }}</div>
                </td>
            </tr>
        </table>

        <!-- 1. TUJUAN -->
        <div class="section-header">1. TUJUAN</div>
        <div class="section-body">{{ $sop->tujuan }}</div>

        <!-- 2. RUANG LINGKUP -->
        <div class="section-header">2. RUANG LINGKUP</div>
        <div class="section-body">{{ $sop->ruang_lingkup }}</div>

        <!-- 3. DEFINISI -->
        <div class="section-header">3. DEFINISI</div>
        <div class="section-body">{{ $sop->definisi }}</div>

        <!-- 4. CATATAN REVISI -->
        <div class="section-header">4. CATATAN REVISI</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Nomor Revisi</th>
                    <th style="width: 25%;">Tanggal Revisi</th>
                    <th>Keterangan Perubahan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sop->revisionNotes as $n)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $n->nomor_revisi }}</td>
                        <td style="text-align: center;">{{ $n->tanggal_revisi ? $n->tanggal_revisi->format('d/m/Y') : '-' }}</td>
                        <td>{{ $n->keterangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #94a3b8;">Tidak ada catatan revisi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 5. ATURAN UMUM -->
        <div class="section-header">5. ATURAN UMUM</div>
        <div class="section-body">{{ $sop->aturan_umum }}</div>

        <!-- 6. FLOWCHART -->
        @if($sop->flowchart)
            <div class="section-header" style="page-break-before: auto;">6. DIAGRAM ALIR (FLOWCHART)</div>
            @if($sop->flowchart->isImage())
                <img src="{{ $sop->flowchart->preview_url }}" class="flowchart-img" alt="Flowchart">
            @else
                <p style="font-size: 10pt; color: #475569; font-style: italic;">
                    * Diagram alir terlampir dalam berkas PDF: <strong>{{ $sop->flowchart->nama_file }}</strong>
                </p>
            @endif
        @endif

        <!-- 7. LAMPIRAN -->
        @if($sop->regularAttachments->count() > 0)
            <div class="section-header">7. LAMPIRAN PENDUKUNG</div>
            <table class="content-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th>Nama Berkas Lampiran</th>
                        <th style="width: 20%;">Ukuran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sop->regularAttachments as $idx => $att)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>{{ $att->nama_file }}</td>
                            <td>{{ $att->formatted_size }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Signatures Table -->
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-box">
                        <div style="font-weight: bold; font-size: 10pt; color: #475569; text-transform: uppercase;">Dibuat Oleh:</div>
                        <div class="signature-space"></div>
                        <div class="signer-name">{{ $sop->creator?->name ?? 'Admin HRD' }}</div>
                        <div class="signer-role">{{ $sop->creator?->getRoleLabel() ?? 'Admin' }}</div>
                        <div style="font-size: 8pt; color: #94a3b8; margin-top: 4px;">
                            Tgl: {{ $sop->created_at ? $sop->created_at->format('d/m/Y') : '-' }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        <div style="font-weight: bold; font-size: 10pt; color: #475569; text-transform: uppercase;">Disetujui Oleh:</div>
                        <div class="signature-space"></div>
                        <div class="signer-name">{{ $sop->approver?->name ?? 'Management / Super Admin' }}</div>
                        <div class="signer-role">Super Administrator</div>
                        <div style="font-size: 8pt; color: #94a3b8; margin-top: 4px;">
                            Tgl: {{ $sop->approved_at ? $sop->approved_at->format('d/m/Y') : ($sop->isApproved() ? $sop->updated_at->format('d/m/Y') : 'MENUNGGU PERSETUJUAN') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

    </div>

    <script>
        // Auto-trigger browser print/save dialog when page loads
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 600);
        };
    </script>

</body>
</html>

