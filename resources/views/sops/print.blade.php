<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sop->nomor_dokumen }} - {{ $sop->nama_sop }}</title>
    
    @unless($isPdf ?? false)
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Times+New+Roman&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @endunless

    <style>
        /* ================================================================
           SOP PDF & PRINT TEMPLATE — Standar Dokumen Resmi A4
           Font: Times New Roman | Kertas: A4 (210mm x 297mm)
        ================================================================ */

        @page {
            size: A4 portrait;
            margin: 18mm 18mm 20mm 22mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            background: {{ ($isPdf ?? false) ? '#ffffff' : '#e2e8f0' }};
            margin: 0;
            padding: {{ ($isPdf ?? false) ? '0' : '24px 16px' }};
            font-size: 11pt;
            line-height: 1.45;
        }

        @if($isPdf ?? false)
        /* Garis tepi halaman profesional berulang di setiap halaman A4 */
        .pdf-page-frame {
            position: fixed;
            top: -8mm;
            left: -10mm;
            right: -8mm;
            bottom: -10mm;
            border: 1.5pt solid #000000;
            pointer-events: none;
            z-index: -1;
        }

        .paper-container {
            width: 100%;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        @else
        /* Tampilan pratinjau kertas A4 di browser */
        .paper-container {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 18mm 18mm 20mm 22mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #cbd5e1;
            position: relative;
        }
        @endif

        /* ---- Topbar tombol (hanya di HTML view, bukan PDF) ---- */
        .no-print-bar {
            max-width: 210mm;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1e293b;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .btn-print {
            background: #10b981;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 12px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-print:hover { background: #059669; }

        .btn-close {
            background: rgba(255,255,255,0.15);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
        }

        /* ================================================================
           HEADER TABEL RESMI SOP
        ================================================================ */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5pt solid #000000;
            margin-bottom: 14px;
            table-layout: fixed;
        }

        .header-table td {
            border: 1pt solid #000000;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .header-logo {
            width: 20%;
            text-align: center;
            padding: 6px;
        }

        .header-logo img {
            max-width: 72px;
            max-height: 58px;
        }

        .header-title-box {
            width: 46%;
            text-align: center;
            padding: 6px 10px;
        }

        .company-name {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.3px;
        }

        .sop-title {
            font-size: 12.5pt;
            font-weight: bold;
            margin-top: 2px;
            color: #000000;
            text-transform: uppercase;
        }

        .sop-name {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
        }

        .meta-cell {
            width: 34%;
            vertical-align: top;
            padding: 0 !important;
        }

        .meta-inner {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-inner td {
            border: 0;
            border-top: 1pt solid #000000;
            padding: 3px 6px;
            font-size: 9pt;
            vertical-align: middle;
            line-height: 1.3;
        }

        .meta-inner tr:first-child td {
            border-top: 0;
        }

        .meta-label {
            font-weight: bold;
            background: #f3f4f6;
            white-space: nowrap;
            width: 44%;
            border-right: 1pt solid #000000 !important;
        }

        /* ================================================================
           SECTION HEADER & BODY
        ================================================================ */
        .section-header {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 6px;
            padding: 4px 8px;
            background: #f3f4f6;
            border: 1pt solid #000000;
            border-left: 3.5pt solid #000000;
            page-break-after: avoid;
        }

        .section-body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 12px;
            padding: 0 2px;
            white-space: pre-line;
            line-height: 1.45;
            word-break: break-word;
            overflow-wrap: anywhere;
            color: #000000;
        }

        /* ================================================================
           TABEL KONTEN (Catatan Revisi, Lampiran)
        ================================================================ */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 12px;
            font-family: 'Times New Roman', Times, serif;
        }

        .content-table th {
            border: 1pt solid #000000;
            padding: 5px 8px;
            background: #f3f4f6;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9.5pt;
        }

        .content-table td {
            border: 1pt solid #000000;
            padding: 5px 8px;
            vertical-align: top;
            line-height: 1.4;
        }

        /* ================================================================
           TANDA TANGAN
        ================================================================ */
        .signature-table {
            width: 100%;
            margin-top: 16px;
            margin-bottom: 12px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            border: 1pt solid #000000;
            text-align: center;
            vertical-align: top;
            padding: 8px 12px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 4px;
        }

        .signature-space {
            height: 55px;
        }

        .signer-name {
            font-weight: bold;
            font-size: 10.5pt;
            color: #000000;
            text-decoration: underline;
        }

        .signer-role {
            font-size: 9.5pt;
            color: #111827;
            margin-top: 2px;
        }

        .signer-date {
            font-size: 8.5pt;
            color: #374151;
            margin-top: 2px;
        }

        /* ================================================================
           FLOWCHART & LAMPIRAN GAMBAR
        ================================================================ */
        .flowchart-box {
            text-align: center;
            margin: 8px 0 12px 0;
            page-break-inside: avoid;
        }

        .flowchart-img {
            max-width: 100%;
            max-height: 225mm;
            height: auto;
            display: block;
            margin: 0 auto;
            border: 1pt solid #000000;
        }

        .attachment-preview-item {
            margin-top: 12px;
            page-break-inside: avoid;
            text-align: center;
        }

        .attachment-preview-caption {
            font-size: 10pt;
            font-weight: bold;
            text-align: left;
            margin-bottom: 4px;
        }

        .attachment-preview-img {
            max-width: 100%;
            max-height: 210mm;
            height: auto;
            border: 1pt solid #94a3b8;
            display: block;
            margin: 0 auto;
        }

        /* ================================================================
           FOOTER (hanya untuk print browser, PDF pakai canvas page_text)
        ================================================================ */
        .page-footer {
            margin-top: 16px;
            padding-top: 6px;
            border-top: 1pt solid #000000;
            font-size: 8.5pt;
            color: #333333;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }

            .paper-container {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                width: 100%;
                min-height: auto;
            }

            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    @if($isPdf ?? false)
        <div class="pdf-page-frame"></div>
    @endif

    @unless($isPdf ?? false)
    <!-- Non-Print Controls -->
    <div class="no-print-bar">
        <div>
            <strong>SIMA HRD &bull; Download Dokumen SOP (PDF)</strong>
            <span style="font-size: 11px; opacity: 0.8; margin-left: 8px;">(Format standar A4)</span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('sops.download', $sop->id) }}" class="btn-print" style="text-decoration: none;">
                <i class="fa-solid fa-file-arrow-down"></i> Unduh File PDF Langsung
            </a>
            <button onclick="window.print()" class="btn-print" style="background: #3b82f6;">
                <i class="fa-solid fa-print"></i> Cetak / Print
            </button>
            <a href="{{ route('sops.show', $sop->id) }}" class="btn-close">Kembali</a>
        </div>
    </div>
    @endunless

    <!-- Printable Paper Sheet -->
    <div class="paper-container">

        <!-- Official Header Table -->
        <table class="header-table">
            <tr>
                <td class="header-logo" rowspan="2">
                    @php
                        $logoPath = public_path('images/logo-sukahati.png');
                        $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
                    @endphp
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" alt="Logo CV. Sukahati Pratama">
                    @else
                        <div style="font-size:8pt; font-weight:bold;">CV. SUKAHATI<br>PRATAMA</div>
                    @endif
                </td>
                <td class="header-title-box">
                    <div class="company-name">CV. SUKAHATI PRATAMA</div>
                    <div class="sop-title">STANDARD OPERATING PROCEDURE</div>
                </td>
                <td class="meta-cell" rowspan="2">
                    <table class="meta-inner">
                        <tr>
                            <td class="meta-label">No. Dokumen</td>
                            <td><strong>{{ $sop->nomor_dokumen }}</strong></td>
                        </tr>
                        <tr>
                            <td class="meta-label">No. Revisi</td>
                            <td>{{ $sop->version }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Tgl. Efektif</td>
                            <td>{{ $sop->tanggal_efektif ? $sop->tanggal_efektif->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Departemen</td>
                            <td>{{ $sop->department?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Status</td>
                            <td>{{ $sop->getStatusLabel() }}</td>
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

        <!-- 4. CATATAN REVISI & PENGESAHAN -->
        <div class="section-header">4. CATATAN REVISI</div>
        <table class="content-table" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 14%;">Rev</th>
                    <th style="width: 24%;">Tanggal Efektif</th>
                    <th>Alasan Revisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sop->revisionNotes as $n)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $n->nomor_revisi }}</td>
                        <td style="text-align: center;">{{ $n->tanggal_revisi ? $n->tanggal_revisi->format('d F Y') : '-' }}</td>
                        <td>{{ $n->keterangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b;">Tidak ada catatan revisi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signatures Table (menyatu rapi di bawah Catatan Revisi sesuai format perusahaan) -->
        <table class="signature-table" style="margin-top: 0;">
            <tr>
                <td style="border-top: 0;">
                    <div class="signature-label">Dibuat oleh,</div>
                    <div class="signature-space"></div>
                    <div class="signer-name">{{ $sop->creator?->name ?? 'Admin HRD' }}</div>
                    <div class="signer-role">{{ $sop->creator?->jabatan ?: ($sop->creator?->getRoleLabel() ?? 'Admin') }}</div>
                    
                </td>
                <td style="border-top: 0;">
                    <div class="signature-label">Disetujui oleh,</div>
                    <div class="signature-space"></div>
                    <div class="signer-name">{{ $sop->approver?->name ?? 'Management / Super Admin' }}</div>
                    <div class="signer-role">{{ $sop->approver?->jabatan ?: ($sop->approver?->getRoleLabel() ?? 'Super Administrator') }}</div>
                </td>
            </tr>
        </table>

        <!-- 5. ATURAN UMUM -->
        <div class="section-header">5. ATURAN UMUM</div>
        <div class="section-body">{{ $sop->aturan_umum }}</div>

        <!-- 6. FLOW CHART -->
        @if($sop->flowchart)
            <div class="section-header">6. FLOW CHART</div>
            @if($sop->flowchart->isImage())
                @php
                    $fcSrc = $sop->flowchart->preview_url;
                    if ($sop->flowchart->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($sop->flowchart->file_path)) {
                        $fcMime = $sop->flowchart->mime_type ?? 'image/png';
                        $fcSrc = 'data:' . $fcMime . ';base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($sop->flowchart->file_path));
                    }
                @endphp
                <div class="flowchart-box">
                    <img src="{{ $fcSrc }}" class="flowchart-img" alt="Flowchart">
                </div>
            @else
                <p style="font-size: 10.5pt; color: #111827; font-style: italic; margin: 6px 0 12px 0;">
                    * Diagram alir terlampir dalam berkas PDF: <strong>{{ $sop->flowchart->nama_file }}</strong>
                </p>
            @endif
        @endif

        <!-- 7. LAMPIRAN -->
        @if($sop->regularAttachments->count() > 0)
            <div class="section-header">7. LAMPIRAN</div>
            <table class="content-table">
               
              
            </table>

            @foreach($sop->regularAttachments as $idx => $att)
                @if($att->isImage() && $att->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($att->file_path))
                    @php
                        $attMime = $att->mime_type ?? 'image/png';
                        $attSrc = 'data:' . $attMime . ';base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($att->file_path));
                    @endphp
                    <div class="attachment-preview-item">
                        <div class="attachment-preview-caption">Lampiran {{ $idx + 1 }}. {{ $att->nama_file }}</div>
                        <img src="{{ $attSrc }}" class="attachment-preview-img" alt="{{ $att->nama_file }}">
                    </div>
                @endif
            @endforeach
        @endif

        @unless($isPdf ?? false)
        <!-- Footer (browser print preview) -->
        <div class="page-footer">
            <span>Dokumen ini dicetak dari Sistem SIMA HRD &mdash; CV. Sukahati Pratama</span>
            <span>{{ $sop->nomor_dokumen }} &bull; Versi {{ $sop->version }}</span>
        </div>
        @endunless

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

