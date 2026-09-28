<x-app-layout>
    <x-slot name="headerTitle">Preview: {{ $document->title }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('documents.index', $document->type) }}" class="hover:underline">{{ strtoupper($document->type) }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Preview Dokumen</span>
    </x-slot>

    <!-- Top Action Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <button onclick="history.back()" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </button>
            <h1 class="text-lg font-bold text-slate-900 truncate max-w-md">{{ $document->title }}</h1>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('documents.download', $document->id) }}" class="inline-flex items-center space-x-2 text-xs font-bold px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md shadow-emerald-500/20 transition-all-200">
                <i class="fa-solid fa-download"></i>
                <span>Unduh File ({{ formatBytes($document->file_size) }})</span>
            </a>

            @if(auth()->check() && auth()->user()->isKetua())
                <a href="{{ route('documents.edit', $document->id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-md transition-all-200">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Edit</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content: Viewer + Metadata -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Viewer Container (3 columns) -->
        <div class="lg:col-span-3 bg-slate-900 rounded-3xl overflow-hidden shadow-xl border border-slate-800 flex flex-col min-h-[600px]">
            <div class="bg-slate-950 px-6 py-3 border-b border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center space-x-2">
                    <i class="fa-solid fa-eye text-blue-400"></i>
                    <span>Tampilan Berkas {{ strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION)) }}</span>
                </span>
                <span>{{ $document->file_name }}</span>
            </div>

            <div class="flex-1 flex items-center justify-center p-4 bg-slate-900">
                @php
                    $ext = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
                    $fileUrl = asset('storage/' . $document->file_path);
                @endphp

                @if(in_array($ext, ['pdf']))
                    <!-- PDF Viewer -->
                    <iframe src="{{ $fileUrl }}" class="w-full h-full min-h-[650px] rounded-xl border-0"></iframe>

                @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                    <!-- Image Viewer -->
                    <div class="text-center p-4">
                        <img src="{{ $fileUrl }}" alt="{{ $document->title }}" class="max-h-[650px] max-w-full rounded-xl object-contain mx-auto shadow-2xl">
                    </div>

                @elseif(in_array($ext, ['mp4', 'webm', 'ogg']))
                    <!-- Video Player -->
                    <video controls class="max-h-[600px] w-full rounded-xl shadow-2xl">
                        <source src="{{ $fileUrl }}" type="{{ $document->file_type }}">
                        Browser Anda tidak mendukung pemutaran video.
                    </video>

                @elseif(in_array($ext, ['mp3', 'wav']))
                    <!-- Audio Player -->
                    <div class="text-center p-8 bg-slate-800 rounded-2xl border border-slate-700">
                        <i class="fa-solid fa-music text-5xl text-blue-400 mb-4"></i>
                        <audio controls class="w-full max-w-md mx-auto">
                            <source src="{{ $fileUrl }}" type="{{ $document->file_type }}">
                        </audio>
                    </div>

                @elseif(in_array($ext, ['csv', 'xlsx', 'xls']))
                    <!-- Excel & CSV Interactive Table Viewer (SheetJS) -->
                    <div class="w-full h-full flex flex-col bg-slate-900 rounded-2xl overflow-hidden min-h-[650px]">
                        <!-- Sheet Selector Toolbar -->
                        <div class="bg-slate-950 p-3 border-b border-slate-800 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 text-xs font-bold rounded-lg border border-emerald-400/30 flex items-center space-x-1.5">
                                    <i class="fa-solid fa-table text-xs"></i>
                                    <span>Tabel {{ strtoupper($ext) }}</span>
                                </span>
                                <span id="excel-status" class="text-xs text-slate-400">Memuat data lembar kerja...</span>
                            </div>
                            <div id="sheet-tabs" class="flex flex-wrap items-center gap-1.5"></div>
                        </div>

                        <!-- Table Scroll Container -->
                        <div id="excel-table-container" class="flex-1 overflow-auto bg-slate-950/50 p-4 min-h-[550px] flex items-center justify-center">
                            <div id="excel-loading" class="text-center py-12">
                                <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-400 mb-3"></i>
                                <p class="text-xs text-slate-400">Membaca dan menyusun data berkas {{ strtoupper($ext) }}...</p>
                            </div>
                            <div id="excel-table-output" class="w-full hidden"></div>
                        </div>
                    </div>

                @elseif(in_array($ext, ['docx']))
                    <!-- Word Document Viewer (Mammoth.js) -->
                    <div class="w-full h-full flex flex-col bg-slate-900 rounded-2xl overflow-hidden min-h-[650px]">
                        <div class="bg-slate-950 p-3 border-b border-slate-800 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-1 bg-blue-500/20 text-blue-400 text-xs font-bold rounded-lg border border-blue-400/30 flex items-center space-x-1.5">
                                    <i class="fa-solid fa-file-word text-xs"></i>
                                    <span>Dokumen Word (.DOCX)</span>
                                </span>
                                <span id="docx-status" class="text-xs text-slate-400">Memuat teks dokumen...</span>
                            </div>
                        </div>

                        <!-- Word Virtual Page Container -->
                        <div class="flex-1 overflow-auto bg-slate-950/70 p-4 sm:p-8 min-h-[550px]">
                            <div id="docx-loading" class="text-center py-16">
                                <i class="fa-solid fa-spinner fa-spin text-3xl text-blue-400 mb-3"></i>
                                <p class="text-xs text-slate-400">Mengonversi format dokumen Word ke pratinjau...</p>
                            </div>
                            <div id="docx-content" class="hidden bg-white text-slate-900 p-8 sm:p-12 rounded-2xl shadow-2xl max-w-4xl mx-auto prose prose-slate"></div>
                        </div>
                    </div>

                @elseif(in_array($ext, ['txt']))
                    <!-- Text File Viewer -->
                    <div class="w-full h-full flex flex-col bg-slate-900 rounded-2xl overflow-hidden min-h-[600px]">
                        <div class="bg-slate-950 p-3 border-b border-slate-800 flex items-center space-x-2">
                            <span class="px-2.5 py-1 bg-slate-800 text-slate-300 text-xs font-bold rounded-lg border border-slate-700">
                                <i class="fa-solid fa-file-lines mr-1"></i> Teks (.TXT)
                            </span>
                        </div>
                        <div class="flex-1 overflow-auto p-6 bg-slate-950/80">
                            <pre id="txt-content" class="text-xs text-slate-200 font-mono whitespace-pre-wrap leading-relaxed">Memuat konten teks...</pre>
                        </div>
                    </div>

                @elseif(in_array($ext, ['pptx', 'ppt']))
                    <!-- PowerPoint Presentation Card -->
                    <div class="text-center py-16 px-6 max-w-lg mx-auto">
                        <div class="w-24 h-24 rounded-3xl bg-amber-500/15 text-amber-500 flex items-center justify-center text-5xl mx-auto mb-5 border border-amber-500/30 shadow-xl shadow-amber-500/10">
                            <i class="fa-solid fa-file-powerpoint"></i>
                        </div>
                        <span class="inline-block px-3 py-1 bg-amber-500/20 text-amber-400 text-xs font-bold rounded-full mb-3 border border-amber-500/30">
                            Slide Presentasi Microsoft PowerPoint
                        </span>
                        <h3 class="text-lg font-bold text-white mb-2 leading-snug">{{ $document->file_name }}</h3>
                        <p class="text-xs text-slate-300 mb-6 leading-relaxed max-w-md mx-auto">
                            Format berkas slide presentasi PowerPoint (<span class="text-amber-400 font-semibold">.{{ $ext }}</span>) memerlukan aplikasi Microsoft PowerPoint atau pembaca presentasi untuk menampilkan efek animasi dan tata letak slide.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a href="{{ route('documents.download', $document->id) }}" class="inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-lg shadow-amber-600/30 transition-all-200 w-full sm:w-auto">
                                <i class="fa-solid fa-download"></i>
                                <span>Unduh & Buka di PowerPoint</span>
                            </a>
                        </div>
                    </div>

                @else
                    <!-- Fallback Other Document Card -->
                    <div class="text-center py-16 px-6 max-w-md mx-auto">
                        <div class="w-20 h-20 rounded-3xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-4xl mx-auto mb-4 border border-blue-500/20">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-1">{{ $document->file_name }}</h3>
                        <p class="text-xs text-slate-400 mb-6">Format file .{{ $ext }} dapat diunduh untuk dibuka pada perangkat Anda.</p>
                        <a href="{{ route('documents.download', $document->id) }}" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-lg transition-all-200">
                            <i class="fa-solid fa-download"></i>
                            <span>Unduh File Sekarang</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Metadata (1 column, Sticky saat scroll) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6 flex flex-col justify-between lg:sticky lg:top-24 h-fit">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3 flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    <span>Informasi Dokumen</span>
                </h3>

                <div class="space-y-4 text-xs">
                    <div>
                        <p class="text-slate-400 font-semibold">Judul Dokumen</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $document->title }}</p>
                    </div>

                    <div>
                        <p class="text-slate-400 font-semibold">Kategori Dokumen</p>
                        <span class="inline-block font-bold text-[10px] px-2.5 py-1 rounded-full uppercase mt-1
                            {{ $document->type === 'sop' ? 'bg-emerald-100 text-emerald-700' : ($document->type === 'job' ? 'bg-amber-100 text-amber-700' : ($document->type === 'job_analysis' ? 'bg-purple-100 text-purple-700' : 'bg-rose-100 text-rose-700')) }}">
                            {{ strtoupper(str_replace('_', ' ', $document->type)) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-slate-400 font-semibold">Divisi Organisasi</p>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $document->division->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-slate-400 font-semibold">Jabatan Terkait</p>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $document->position->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-slate-400 font-semibold">Deskripsi</p>
                        <p class="text-slate-600 mt-0.5 leading-relaxed">{{ $document->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nama File:</span>
                            <span class="font-semibold text-slate-700 truncate max-w-[140px]">{{ $document->file_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Ukuran File:</span>
                            <span class="font-semibold text-slate-700">{{ formatBytes($document->file_size) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Pengunggah:</span>
                            <span class="font-semibold text-slate-700">{{ $document->uploader->name ?? 'System' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Waktu Upload:</span>
                            <span class="font-semibold text-slate-700">{{ $document->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Button -->
            <a href="{{ route('documents.download', $document->id) }}" class="w-full text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs py-3 rounded-xl shadow-lg shadow-emerald-500/20 transition-all-200 block">
                <i class="fa-solid fa-download mr-1"></i> Download Berkas
            </a>
        </div>

    </div>

    <!-- Client-side Document Renderers (SheetJS for Excel/CSV, Mammoth for DOCX, Fetch for TXT) -->
    @if(in_array($ext, ['csv', 'xlsx', 'xls']))
        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
        <style>
            #excel-table-output table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
                background-color: #ffffff;
                color: #1e293b;
                border-radius: 12px;
            }
            #excel-table-output th, #excel-table-output td {
                padding: 10px 14px;
                border: 1px solid #e2e8f0;
                white-space: nowrap;
            }
            #excel-table-output tr:first-child, #excel-table-output th {
                background-color: #f8fafc;
                font-weight: 700;
                color: #0f172a;
                position: sticky;
                top: 0;
                z-index: 10;
                border-bottom: 2px solid #cbd5e1;
            }
            #excel-table-output tr:nth-child(even) {
                background-color: #f8fafc;
            }
            #excel-table-output tr:hover {
                background-color: #eff6ff;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const fileUrl = "{{ $fileUrl }}";
                const statusEl = document.getElementById('excel-status');
                const loadingEl = document.getElementById('excel-loading');
                const outputEl = document.getElementById('excel-table-output');
                const sheetTabsEl = document.getElementById('sheet-tabs');

                fetch(fileUrl)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mengunduh berkas');
                        return response.arrayBuffer();
                    })
                    .then(data => {
                        const workbook = XLSX.read(data, { type: 'array' });
                        const sheetNames = workbook.SheetNames;
                        
                        if (!sheetNames || sheetNames.length === 0) {
                            throw new Error('Tidak ada lembar kerja yang ditemukan.');
                        }

                        function renderSheet(sheetName) {
                            const worksheet = workbook.Sheets[sheetName];
                            const html = XLSX.utils.sheet_to_html(worksheet, { id: 'rendered-excel-table', editable: false });
                            outputEl.innerHTML = html;
                            loadingEl.classList.add('hidden');
                            outputEl.classList.remove('hidden');
                            statusEl.innerText = `${sheetName} (${worksheet['!ref'] || 'Kosong'})`;

                            // Highlight active tab
                            document.querySelectorAll('.sheet-tab-btn').forEach(btn => {
                                if (btn.getAttribute('data-sheet') === sheetName) {
                                    btn.className = 'sheet-tab-btn px-3 py-1 bg-emerald-600 text-white text-xs font-bold rounded-lg shadow-sm';
                                } else {
                                    btn.className = 'sheet-tab-btn px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-lg transition-all';
                                }
                            });
                        }

                        // Create tab buttons if more than 1 sheet
                        sheetTabsEl.innerHTML = '';
                        sheetNames.forEach((name, index) => {
                            const btn = document.createElement('button');
                            btn.innerText = name;
                            btn.setAttribute('data-sheet', name);
                            btn.className = index === 0 
                                ? 'sheet-tab-btn px-3 py-1 bg-emerald-600 text-white text-xs font-bold rounded-lg shadow-sm'
                                : 'sheet-tab-btn px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-lg transition-all';
                            btn.onclick = () => renderSheet(name);
                            sheetTabsEl.appendChild(btn);
                        });

                        // Initial render of first sheet
                        renderSheet(sheetNames[0]);
                    })
                    .catch(err => {
                        console.error('SheetJS Error:', err);
                        loadingEl.innerHTML = `
                            <div class="text-rose-400 p-6 text-center">
                                <i class="fa-solid fa-triangle-exclamation text-3xl mb-2"></i>
                                <p class="text-sm font-semibold">Gagal memuat pratinjau tabel</p>
                                <p class="text-xs text-slate-400 mt-1">${err.message || 'File mungkin dilindungi kata sandi atau format tidak sesuai.'}</p>
                            </div>
                        `;
                        statusEl.innerText = 'Error';
                    });
            });
        </script>

    @elseif(in_array($ext, ['docx']))
        <script src="https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const fileUrl = "{{ $fileUrl }}";
                const statusEl = document.getElementById('docx-status');
                const loadingEl = document.getElementById('docx-loading');
                const contentEl = document.getElementById('docx-content');

                fetch(fileUrl)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mengunduh berkas');
                        return response.arrayBuffer();
                    })
                    .then(arrayBuffer => mammoth.convertToHtml({ arrayBuffer: arrayBuffer }))
                    .then(result => {
                        loadingEl.classList.add('hidden');
                        contentEl.innerHTML = result.value || '<p class="text-slate-400 italic">Dokumen Word tidak memiliki teks.</p>';
                        contentEl.classList.remove('hidden');
                        statusEl.innerText = 'Selesai dimuat';
                    })
                    .catch(err => {
                        console.error('Mammoth Error:', err);
                        loadingEl.innerHTML = `
                            <div class="text-rose-400 p-6 text-center">
                                <i class="fa-solid fa-triangle-exclamation text-3xl mb-2"></i>
                                <p class="text-sm font-semibold">Gagal mengonversi pratinjau dokumen Word</p>
                                <p class="text-xs text-slate-400 mt-1">${err.message || 'Format dokumen tidak didukung.'}</p>
                            </div>
                        `;
                        statusEl.innerText = 'Error';
                    });
            });
        </script>

    @elseif(in_array($ext, ['txt']))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const fileUrl = "{{ $fileUrl }}";
                const contentEl = document.getElementById('txt-content');
                fetch(fileUrl)
                    .then(res => res.text())
                    .then(text => {
                        contentEl.innerText = text;
                    })
                    .catch(err => {
                        contentEl.innerText = 'Gagal memuat isi teks: ' + err.message;
                    });
            });
        </script>
    @endif
</x-app-layout>
