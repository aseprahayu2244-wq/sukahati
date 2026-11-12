<x-app-layout>
    <x-slot name="headerTitle">Formulir Pembuatan & Pengajuan SOP</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('sops.index') }}" class="hover:underline">Workflow SOP</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Buat SOP Baru</span>
    </x-slot>

    <!-- Top Info -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Formulir SOP Digital</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi data informasi umum, klausul SOP, diagram alir flowchart, dan berkas lampiran pendukung.</p>
        </div>
        <a href="{{ route('sops.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200 self-start md:self-auto">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Error Summary Alerts -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
            <div class="flex items-center space-x-2 font-bold text-rose-900 mb-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                <span>Terdapat kesalahan pada isian formulir:</span>
            </div>
            <ul class="list-disc pl-5 space-y-1 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <form id="sopForm" method="POST" action="{{ route('sops.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <input type="hidden" name="action" id="formActionInput" value="submit">

        <!-- Progress Steps Indicator -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <a href="#sectionA" class="flex items-center space-x-3 p-3 rounded-xl bg-blue-50/80 border border-blue-200 text-blue-900 font-bold transition-all-200">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0">1</div>
                    <div>
                        <p class="leading-none text-[11px] text-blue-600 uppercase font-semibold">Bagian A</p>
                        <p class="mt-0.5 truncate font-bold">Informasi Umum</p>
                    </div>
                </a>
                <a href="#sectionB" class="flex items-center space-x-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition-all-200">
                    <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm shrink-0">2</div>
                    <div>
                        <p class="leading-none text-[11px] text-slate-400 uppercase font-semibold">Bagian B</p>
                        <p class="mt-0.5 truncate font-bold">Isi Dokumen SOP</p>
                    </div>
                </a>
                <a href="#sectionC" class="flex items-center space-x-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition-all-200">
                    <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm shrink-0">3</div>
                    <div>
                        <p class="leading-none text-[11px] text-slate-400 uppercase font-semibold">Bagian C</p>
                        <p class="mt-0.5 truncate font-bold">Flowchart</p>
                    </div>
                </a>
                <a href="#sectionD" class="flex items-center space-x-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition-all-200">
                    <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm shrink-0">4</div>
                    <div>
                        <p class="leading-none text-[11px] text-slate-400 uppercase font-semibold">Bagian D</p>
                        <p class="mt-0.5 truncate font-bold">Lampiran Berkas</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN A: INFORMASI UMUM SOP -->
        <!-- ========================================================= -->
        <div id="sectionA" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm scroll-mt-24">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-blue-500/20">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Bagian A: Informasi Umum SOP</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Identitas utama dokumen SOP yang akan dicatat dalam database resmi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Nama SOP -->
                <div class="md:col-span-2">
                    <label for="nama_sop" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        1. Nama SOP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_sop" 
                           name="nama_sop" 
                           value="{{ old('nama_sop') }}" 
                           placeholder="Contoh: Prosedur Penerimaan Karyawan Baru"
                           required
                           class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <p class="text-[11px] text-slate-400 mt-1.5">Tuliskan nama lengkap prosedur operasional standar secara jelas.</p>
                </div>

                <!-- 2. Nomor Dokumen -->
                <div>
                    <label for="nomor_dokumen" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        2. Nomor Dokumen <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-barcode"></i>
                        </div>
                        <input type="text" 
                               id="nomor_dokumen" 
                               name="nomor_dokumen" 
                               value="{{ old('nomor_dokumen') }}" 
                               placeholder="Contoh: SOP-HRD-001"
                               required
                               class="w-full bg-slate-50 text-slate-800 font-mono text-sm rounded-xl pl-9 pr-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Wajib unik dan belum pernah digunakan pada dokumen SOP lain.</p>
                </div>

                <!-- 3. Departemen -->
                <div>
                    <label for="department_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        3. Departemen <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="department_id" 
                                name="department_id" 
                                required
                                class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ (old('department_id', $selectedDepartmentId) == $dept->id) ? 'selected' : '' }}>
                                    {{ $dept->name }} &bull; (BOD: {{ $dept->bod?->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Departemen pemilik yang bertanggung jawab atas pelaksanaan SOP ini.</p>
                </div>

                <!-- 4. Tanggal Efektif -->
                <div>
                    <label for="tanggal_efektif" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        4. Tanggal Efektif <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <input type="date" 
                               id="tanggal_efektif" 
                               name="tanggal_efektif" 
                               value="{{ old('tanggal_efektif', date('Y-m-d')) }}" 
                               required
                               class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl pl-9 pr-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Tanggal berlakunya ketentuan SOP ini di lingkungan perusahaan.</p>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN B: ISI DOKUMEN SOP -->
        <!-- ========================================================= -->
        <div id="sectionB" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm scroll-mt-24">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-emerald-500/20">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Bagian B: Isi Dokumen SOP</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Uraian klausul, tujuan, batasan cakupan, definisi terminologi, dan ketentuan umum.</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- 1. TUJUAN -->
                <div>
                    <label for="tujuan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        1. TUJUAN <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="tujuan" 
                              name="tujuan" 
                              rows="4" 
                              required 
                              placeholder="Uraikan tujuan dan sasaran yang ingin dicapai dengan adanya prosedur operasional ini..."
                              class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl p-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">{{ old('tujuan') }}</textarea>
                </div>

                <!-- 2. RUANG LINGKUP -->
                <div>
                    <label for="ruang_lingkup" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        2. RUANG LINGKUP <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="ruang_lingkup" 
                              name="ruang_lingkup" 
                              rows="4" 
                              required 
                              placeholder="Uraikan batasan, pihak-pihak terkait, dan cakupan aktivitas operasional yang diatur..."
                              class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl p-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">{{ old('ruang_lingkup') }}</textarea>
                </div>

                <!-- 3. DEFINISI -->
                <div>
                    <label for="definisi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        3. DEFINISI <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="definisi" 
                              name="definisi" 
                              rows="4" 
                              required 
                              placeholder="Jelaskan istilah-istilah, singkatan, dan definisi teknis yang digunakan dalam SOP ini..."
                              class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl p-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">{{ old('definisi') }}</textarea>
                </div>

                <!-- 4. CATATAN REVISI (Tabel Dinamis) -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                4. CATATAN REVISI
                            </label>
                            <p class="text-[11px] text-slate-400 mt-0.5">Riwayat versi revisi dokumen. Anda dapat menambahkan baris baru sesuai kebutuhan.</p>
                        </div>
                        <button type="button" onclick="addRevisionRow()" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition-all-200">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Tambah Baris</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                                    <th class="py-2.5 px-3 w-32">Nomor Revisi</th>
                                    <th class="py-2.5 px-3 w-40">Tanggal Revisi</th>
                                    <th class="py-2.5 px-3">Keterangan Perubahan</th>
                                    <th class="py-2.5 px-3 text-center w-12">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="revisionTableBody" class="divide-y divide-slate-100">
                                <tr class="revision-row">
                                    <td class="p-2">
                                        <input type="text" name="revision_notes[0][nomor_revisi]" value="1.0" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="date" name="revision_notes[0][tanggal_revisi]" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="p-2">
                                        <input type="text" name="revision_notes[0][keterangan]" value="Penerbitan SOP Perdana" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="p-2 text-center">
                                        <button type="button" onclick="removeRevisionRow(this)" title="Hapus Baris" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. ATURAN UMUM -->
                <div class="pt-4 border-t border-slate-100">
                    <label for="aturan_umum" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        5. ATURAN UMUM <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="aturan_umum" 
                              name="aturan_umum" 
                              rows="5" 
                              required 
                              placeholder="Uraikan ketentuan umum, syarat kepatuhan, sanksi, dan instruksi wajib dalam pelaksanaan SOP ini..."
                              class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl p-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">{{ old('aturan_umum') }}</textarea>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN C: FLOWCHART -->
        <!-- ========================================================= -->
        <div id="sectionC" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm scroll-mt-24">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-purple-500/20">
                    <i class="fa-solid fa-diagram-project"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Bagian C: Diagram Alir (Flowchart)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Unggah gambar atau berkas PDF Flowchart yang menggambarkan alur proses kerja secara visual.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Upload Berkas Flowchart <span class="text-rose-500">*</span>
                </label>
                
                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-purple-500 transition-colors bg-slate-50/50">
                    <div class="space-y-2 text-center" id="flowchartUploadArea">
                        <i class="fa-solid fa-file-arrow-up text-4xl text-purple-400"></i>
                        <div class="flex text-xs text-slate-600 justify-center items-center">
                            <label for="flowchart" class="relative cursor-pointer bg-white rounded-md font-bold text-purple-600 hover:text-purple-500 px-2.5 py-1 border border-purple-200 shadow-sm">
                                <span>Pilih Berkas Flowchart</span>
                                <input id="flowchart" name="flowchart" type="file" accept=".png,.jpg,.jpeg,.pdf" class="sr-only" onchange="previewFlowchart(event)">
                            </label>
                            <span class="pl-2">atau tarik ke sini</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Mendukung format gambar (PNG, JPG, JPEG) atau PDF (Maks. 20MB)</p>
                    </div>
                </div>

                <!-- Preview Area -->
                <div id="flowchartPreviewContainer" class="hidden mt-4 p-4 rounded-2xl border border-purple-200 bg-purple-50/40">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2 text-xs font-bold text-purple-900">
                            <i class="fa-solid fa-circle-check text-purple-600"></i>
                            <span id="flowchartFileName">Nama File Flowchart</span>
                        </div>
                        <button type="button" onclick="clearFlowchart()" class="text-xs font-semibold text-rose-600 hover:text-rose-800 flex items-center space-x-1">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Ganti / Hapus</span>
                        </button>
                    </div>
                    <div id="flowchartImagePreview" class="rounded-xl overflow-hidden border border-slate-200 max-h-96 flex items-center justify-center bg-white p-2">
                        <!-- Image or PDF placeholder injected via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN D: LAMPIRAN (MULTIPLE FILE UPLOAD) -->
        <!-- ========================================================= -->
        <div id="sectionD" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm scroll-mt-24">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-900 flex items-center justify-center text-lg font-bold shadow-sm shadow-amber-500/20">
                    <i class="fa-solid fa-paperclip"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Bagian D: Lampiran Dokumen Tambahan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Unggah beberapa file lampiran seperti formulir kerja, template, referensi, atau regulasi terkait.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Upload Berkas Lampiran (Bisa Banyak File Sekaligus)
                </label>

                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-amber-500 transition-colors bg-slate-50/50">
                    <div class="space-y-2 text-center">
                        <i class="fa-solid fa-cloud-arrow-up text-4xl text-amber-400"></i>
                        <div class="flex text-xs text-slate-600 justify-center items-center">
                            <label for="lampiranInput" class="relative cursor-pointer bg-white rounded-md font-bold text-amber-700 hover:text-amber-600 px-2.5 py-1 border border-amber-200 shadow-sm">
                                <span>Pilih File Lampiran</span>
                                <input id="lampiranInput" name="lampiran[]" type="file" multiple class="sr-only" onchange="handleMultipleAttachments(event)">
                            </label>
                            <span class="pl-2">atau tarik ke area ini</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Mendukung PDF, DOCX, XLSX, PNG, JPG, ZIP (Maks. 20MB per file)</p>
                    </div>
                </div>

                <!-- Attachment List Container -->
                <div id="attachmentListContainer" class="hidden mt-4 space-y-2">
                    <h3 class="text-xs font-bold text-slate-700 mb-2">Daftar Lampiran yang Dipilih:</h3>
                    <div id="attachmentItemsList" class="space-y-2">
                        <!-- Dynamic items via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN E: TOMBOL FORMULIR -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-lg sticky bottom-4 z-20 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('sops.index') }}" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold text-center transition-all-200 flex items-center justify-center space-x-2">
                <i class="fa-solid fa-xmark"></i>
                <span>Batal</span>
            </a>

            <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
                <!-- Simpan Draft Button -->
                <button type="button" onclick="submitWithAction('draft')" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all-200 shadow-md flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Sebagai Draft</span>
                </button>

                <!-- Ajukan SOP Button -->
                <button type="button" onclick="confirmSubmission()" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-extrabold transition-all-200 shadow-lg shadow-emerald-500/30 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ auth()->user()->isSuperAdmin() ? 'Terbitkan / Ajukan SOP' : 'Ajukan SOP ke Super Admin' }}</span>
                </button>
            </div>
        </div>

    </form>

    <!-- Dynamic JavaScript Helper for Tables & File Previews -->
    <script>
        let revisionIndex = 1;

        function addRevisionRow() {
            const tbody = document.getElementById('revisionTableBody');
            const row = document.createElement('tr');
            row.className = 'revision-row';
            row.innerHTML = `
                <td class="p-2">
                    <input type="text" name="revision_notes[${revisionIndex}][nomor_revisi]" placeholder="Contoh: 1.1" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="p-2">
                    <input type="date" name="revision_notes[${revisionIndex}][tanggal_revisi]" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="p-2">
                    <input type="text" name="revision_notes[${revisionIndex}][keterangan]" placeholder="Keterangan perbaikan" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="p-2 text-center">
                    <button type="button" onclick="removeRevisionRow(this)" title="Hapus Baris" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            revisionIndex++;
        }

        function removeRevisionRow(btn) {
            const tbody = document.getElementById('revisionTableBody');
            if (tbody.querySelectorAll('.revision-row').length > 1) {
                btn.closest('.revision-row').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pemberitahuan',
                    text: 'Minimal harus ada satu baris catatan revisi!',
                    confirmButtonColor: '#3085d6'
                });
            }
        }

        // Flowchart Preview
        function previewFlowchart(event) {
            const input = event.target;
            const container = document.getElementById('flowchartPreviewContainer');
            const previewArea = document.getElementById('flowchartImagePreview');
            const fileNameElem = document.getElementById('flowchartFileName');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                fileNameElem.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                container.classList.remove('hidden');

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewArea.innerHTML = `<img src="${e.target.result}" alt="Preview Flowchart" class="max-h-80 object-contain rounded-lg">`;
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    previewArea.innerHTML = `
                        <div class="p-6 text-center">
                            <i class="fa-solid fa-file-pdf text-5xl text-rose-500 mb-2"></i>
                            <p class="font-bold text-slate-700 text-xs">File PDF Terpilih: ${file.name}</p>
                            <p class="text-[11px] text-slate-400 mt-1">Dokumen PDF Flowchart akan dapat diunduh/ditinjau setelah diajukan.</p>
                        </div>
                    `;
                }
            }
        }

        function clearFlowchart() {
            const input = document.getElementById('flowchart');
            input.value = '';
            document.getElementById('flowchartPreviewContainer').classList.add('hidden');
            document.getElementById('flowchartImagePreview').innerHTML = '';
        }

        // Multiple Attachments Handling
        function handleMultipleAttachments(event) {
            const input = event.target;
            const container = document.getElementById('attachmentListContainer');
            const list = document.getElementById('attachmentItemsList');
            list.innerHTML = '';

            if (input.files && input.files.length > 0) {
                container.classList.remove('hidden');
                Array.from(input.files).forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs';
                    item.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-file text-slate-500"></i>
                            <div>
                                <p class="font-bold text-slate-800">${file.name}</p>
                                <p class="text-[10px] text-slate-400">${(file.size / 1024).toFixed(1)} KB</p>
                            </div>
                        </div>
                        <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-semibold">Siap Diunggah</span>
                    `;
                    list.appendChild(item);
                });
            } else {
                container.classList.add('hidden');
            }
        }

        // Submit Action (Draft vs Submit)
        function submitWithAction(actionType) {
            document.getElementById('formActionInput').value = actionType;
            document.getElementById('sopForm').submit();
        }

        function confirmSubmission() {
            const form = document.getElementById('sopForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Pengajuan SOP',
                text: 'Apakah Anda yakin seluruh data SOP, Flowchart, dan Lampiran sudah lengkap dan siap diajukan untuk diperiksa?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Ajukan SOP Sekarang',
                cancelButtonText: 'Periksa Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formActionInput').value = 'submit';
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>

