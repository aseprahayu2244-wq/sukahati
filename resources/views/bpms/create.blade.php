<x-app-layout>
    <x-slot name="headerTitle">Pembuatan Dokumen BPM</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('bpms.index') }}" class="hover:underline">Workflow BPM</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Buat Dokumen BPM</span>
    </x-slot>

    <!-- Top Info -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Formulir BPM Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pilih metode pembuatan: gunakan <strong>BPM Builder</strong> untuk menyusun hierarki L1-L3 & RACI interaktif, atau <strong>Upload Berkas</strong> untuk mengunggah dokumen PDF/Gambar.</p>
        </div>
        <a href="{{ route('bpms.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200 self-start md:self-auto">
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
    <form id="bpmForm" method="POST" action="{{ route('bpms.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <input type="hidden" name="action" id="formActionInput" value="submit">

        <!-- Method Switcher Tab (Builder vs Upload) -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Metode Dokumen:</span>
            </div>
            <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 text-xs font-bold">
                <label class="cursor-pointer">
                    <input type="radio" name="source_type" value="builder" {{ old('source_type', $mode) === 'builder' ? 'checked' : '' }} onchange="toggleSourceType('builder')" class="sr-only peer">
                    <span class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-600 transition-all-200">
                        <i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i>
                        <span>BPM Builder (L1-L3 & RACI)</span>
                    </span>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="source_type" value="upload" {{ old('source_type', $mode) === 'upload' ? 'checked' : '' }} onchange="toggleSourceType('upload')" class="sr-only peer">
                    <span class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-600 transition-all-200">
                        <i class="fa-solid fa-cloud-arrow-up text-rose-500"></i>
                        <span>Upload File Dokumen</span>
                    </span>
                </label>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN 1: INFORMASI UMUM BPM -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-indigo-500/20">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">1. Informasi Umum BPM</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Identitas dokumen Business Process Management.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Nama BPM -->
                <div class="md:col-span-2">
                    <label for="nama_bpm" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nama BPM <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama_bpm" name="nama_bpm" value="{{ old('nama_bpm') }}" 
                           placeholder="Contoh: Proses Rekrutmen Karyawan, Pengadaan Bahan Baku..." 
                           required class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- 2. Nomor Dokumen BPM -->
                <div>
                    <label for="nomor_dokumen" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nomor Dokumen BPM <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nomor_dokumen" name="nomor_dokumen" value="{{ old('nomor_dokumen') }}" 
                           placeholder="Contoh: BPM-HRD-001" 
                           required class="w-full bg-slate-50 text-slate-900 text-sm font-mono rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">Nomor dokumen harus unik dan belum pernah digunakan.</p>
                </div>

                <!-- 3. Tanggal Efektif -->
                <div>
                    <label for="tanggal_efektif" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Tanggal Efektif <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tanggal_efektif" name="tanggal_efektif" value="{{ old('tanggal_efektif', date('Y-m-d')) }}" 
                           required class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- 4. BOD -->
                <div>
                    <label for="bod_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        BOD (Board of Directors)
                    </label>
                    <select id="bod_id" name="bod_id" onchange="onBodChange(this.value)" 
                            class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih BOD (Opsional) --</option>
                        @foreach($bods as $bod)
                            <option value="{{ $bod->id }}" {{ old('bod_id') == $bod->id ? 'selected' : '' }}>
                                {{ $bod->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Departemen (Divisi) -->
                <div>
                    <label for="department_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Departemen <span class="text-rose-500">*</span>
                    </label>
                    <select id="department_id" name="department_id" required 
                            class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" 
                                    data-bod="{{ $dept->bod_id }}" 
                                    {{ old('department_id', $selectedDepartmentId) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} {{ $dept->bod ? '('.$dept->bod->name.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 6. Deskripsi BPM -->
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Deskripsi BPM <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" 
                              placeholder="Penjelasan ringkas mengenai tujuan dan ruang lingkup proses bisnis ini..." 
                              class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('deskripsi') }}</textarea>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN 2: AREA BPM BUILDER (L1, L2, L3 + RACI) -->
        <!-- ========================================================= -->
        <div id="builderSection" class="{{ old('source_type', $mode) === 'upload' ? 'hidden' : '' }} bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-indigo-500/20">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">2. BPM Builder (Hierarki & RACI)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tambah baris L1, L2, L3 secara bebas, ubah urutan (drag & drop), dan tentukan RACI untuk setiap aktivitas.</p>
                    </div>
                </div>

                <!-- RACI Legend Pill -->
                <div class="flex items-center space-x-2 text-[11px] bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-slate-600">
                    <span class="font-bold text-indigo-600">R:</span> Responsible &bull;
                    <span class="font-bold text-amber-600">A:</span> Accountable &bull;
                    <span class="font-bold text-sky-600">C:</span> Consulted &bull;
                    <span class="font-bold text-emerald-600">I:</span> Informed
                </div>
            </div>

            <!-- Builder Interactive Table -->
            <div class="overflow-x-auto border border-slate-200 rounded-2xl mb-5 shadow-inner">
                <table class="w-full text-left text-xs text-slate-700 min-w-[900px]">
                    <thead class="bg-slate-100/80 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 w-12 text-center">Urutan</th>
                            <th class="py-3 px-4 min-w-[280px]">Level & Uraian Aktivitas <span class="text-rose-500">*</span></th>
                            <th class="py-3 px-3 w-48 text-indigo-700">Responsible (R)</th>
                            <th class="py-3 px-3 w-44 text-amber-700">Accountable (A)</th>
                            <th class="py-3 px-3 w-44 text-sky-700">Consulted (C)</th>
                            <th class="py-3 px-3 w-44 text-emerald-700">Informed (I)</th>
                            <th class="py-3 px-3 w-16 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="builderTableBody" class="divide-y divide-slate-100 bg-white">
                        <!-- Rows dynamically rendered via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State Container if no rows -->
            <div id="builderEmptyState" class="hidden p-8 text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl mb-5">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto text-xl mb-3">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <h4 class="font-bold text-slate-800 text-sm">Belum Ada Tahapan Proses</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Gunakan tombol di bawah untuk menambahkan baris proses utama (L1), subproses (L2), atau aktivitas (L3).</p>
            </div>

            <!-- Add Buttons Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="addStepRow('L1')" class="inline-flex items-center space-x-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all-200 shadow-sm shadow-indigo-600/20">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Tambah L1 (Proses Utama)</span>
                    </button>
                    <button type="button" onclick="addStepRow('L2')" class="inline-flex items-center space-x-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold transition-all-200 shadow-sm shadow-sky-600/20">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Tambah L2 (Subproses)</span>
                    </button>
                    <button type="button" onclick="addStepRow('L3')" class="inline-flex items-center space-x-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all-200 shadow-sm shadow-emerald-600/20">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Tambah L3 (Aktivitas)</span>
                    </button>
                </div>

                <div class="text-[11px] text-slate-400">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Tip: Drag ikon titik untuk memindahkan posisi baris secara bebas.
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN 3: AREA UPLOAD FILE DOKUMEN (Jika metode Upload) -->
        <!-- ========================================================= -->
        <div id="uploadSection" class="{{ old('source_type', $mode) === 'upload' ? '' : 'hidden' }} bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-rose-500/20">
                    <i class="fa-solid fa-file-arrow-up"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">2. Unggah Berkas Dokumen BPM</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Unggah dokumen BPM dalam format PDF atau Gambar (maksimal 10MB).</p>
                </div>
            </div>

            <div class="p-8 border-2 border-dashed border-slate-300 hover:border-rose-400 rounded-2xl bg-slate-50/50 text-center transition-colors">
                <input type="file" id="bpm_file" name="bpm_file" accept=".pdf,.jpg,.jpeg,.png" onchange="previewUploadFile(event)" class="hidden">
                <label for="bpm_file" class="cursor-pointer flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mb-3 shadow-inner">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-800">Klik di sini untuk memilih berkas dokumen BPM</span>
                    <span class="text-xs text-slate-400 mt-1">Mendukung format PDF, PNG, JPG, JPEG (Maks. 10MB)</span>
                </label>

                <!-- Selected file info preview -->
                <div id="fileUploadPreview" class="hidden mt-4 inline-flex items-center space-x-3 p-3 rounded-xl bg-white border border-slate-200 shadow-sm text-xs text-slate-800">
                    <i class="fa-solid fa-file-pdf text-rose-500 text-lg"></i>
                    <span id="fileNameDisplay" class="font-bold"></span>
                    <button type="button" onclick="clearUploadFile()" class="text-slate-400 hover:text-rose-600 p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- ACTION BUTTONS -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('bpms.index') }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all-200 text-center">
                Batal
            </a>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Simpan Draft Button -->
                <button type="button" onclick="submitWithAction('draft')" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all-200 shadow-md flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Draft</span>
                </button>

                <!-- Ajukan BPM Button -->
                <button type="button" onclick="confirmSubmission()" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs font-extrabold transition-all-200 shadow-lg shadow-indigo-500/30 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ auth()->user()->isSuperAdmin() ? 'Terbitkan / Ajukan BPM' : 'Ajukan BPM ke Super Admin' }}</span>
                </button>
            </div>
        </div>
    </form>

    <!-- JavaScript Data and Controller for BPM Builder -->
    <script>
        // Available positions from backend for RACI dropdowns
        const availablePositions = @json($positions);

        let stepIndex = 0;

        function toggleSourceType(type) {
            const builderSec = document.getElementById('builderSection');
            const uploadSec = document.getElementById('uploadSection');
            if (type === 'builder') {
                builderSec.classList.remove('hidden');
                uploadSec.classList.add('hidden');
            } else {
                builderSec.classList.add('hidden');
                uploadSec.classList.remove('hidden');
            }
        }

        function onBodChange(bodId) {
            const deptSelect = document.getElementById('department_id');
            const options = deptSelect.querySelectorAll('option[data-bod]');
            
            options.forEach(opt => {
                if (!bodId || opt.getAttribute('data-bod') === bodId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
        }

        // Add a new row to the BPM Builder table
        function addStepRow(level, initialData = {}) {
            const tbody = document.getElementById('builderTableBody');
            const emptyState = document.getElementById('builderEmptyState');
            emptyState.classList.add('hidden');

            const id = 'step_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const currentIndex = stepIndex++;

            const indentClass = level === 'L1' ? 'pl-2' : (level === 'L2' ? 'pl-8' : 'pl-14');
            const badgeClass = level === 'L1' 
                ? 'bg-indigo-100 text-indigo-800 border-indigo-200' 
                : (level === 'L2' ? 'bg-sky-100 text-sky-800 border-sky-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200');
            const placeholder = level === 'L1' 
                ? 'Masukkan nama proses utama (Contoh: Proses Rekrutmen Karyawan)' 
                : (level === 'L2' ? 'Masukkan nama subproses (Contoh: Seleksi Administrasi)' : 'Masukkan rincian aktivitas (Contoh: Memeriksa kelengkapan berkas CV)');

            const arrowIcon = level === 'L2' ? '↳ ' : (level === 'L3' ? '↳ ↳ ' : '');

            // Build Options HTML
            let posOptionsHtml = '';
            availablePositions.forEach(p => {
                posOptionsHtml += `<option value="${p.id}">${p.name}</option>`;
            });

            const row = document.createElement('tr');
            row.id = id;
            row.className = 'step-row transition-colors hover:bg-slate-50/80';
            row.setAttribute('draggable', 'true');
            row.dataset.level = level;

            row.innerHTML = `
                <td class="py-3 px-3 text-center cursor-move text-slate-400 hover:text-slate-700 drag-handle" title="Tahan & geser untuk mengubah urutan">
                    <i class="fa-solid fa-grip-vertical"></i>
                </td>
                <td class="py-3 px-4 ${indentClass}">
                    <div class="flex items-center space-x-2">
                        <span class="shrink-0 text-slate-400 text-xs font-mono">${arrowIcon}</span>
                        <span class="shrink-0 text-[10px] font-extrabold px-2 py-0.5 rounded border ${badgeClass}">
                            ${level}
                        </span>
                        <input type="hidden" name="steps[${currentIndex}][level]" value="${level}">
                        <input type="text" name="steps[${currentIndex}][activity_description]" 
                               value="${initialData.activity_description || ''}" 
                               placeholder="${placeholder}" 
                               required 
                               class="w-full bg-slate-50 text-slate-900 text-xs font-medium rounded-lg px-3 py-2 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </td>
                <!-- RACI Responsible (Multi Select) -->
                <td class="py-3 px-3">
                    <select name="steps[${currentIndex}][raci_r][]" multiple size="2" class="w-full bg-slate-50 text-slate-800 text-[11px] rounded-lg px-2 py-1 border border-indigo-200 focus:outline-none focus:ring-1 focus:ring-indigo-500" title="Tekan Ctrl/Cmd untuk memilih beberapa jabatan">
                        ${posOptionsHtml}
                    </select>
                </td>
                <!-- RACI Accountable (Single Select) -->
                <td class="py-3 px-3">
                    <select name="steps[${currentIndex}][raci_a]" class="w-full bg-slate-50 text-slate-800 text-[11px] rounded-lg px-2 py-1 border border-amber-200 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">-- Pilih Jabatan --</option>
                        ${posOptionsHtml}
                    </select>
                </td>
                <!-- RACI Consulted (Multi Select) -->
                <td class="py-3 px-3">
                    <select name="steps[${currentIndex}][raci_c][]" multiple size="2" class="w-full bg-slate-50 text-slate-800 text-[11px] rounded-lg px-2 py-1 border border-sky-200 focus:outline-none focus:ring-1 focus:ring-sky-500" title="Tekan Ctrl/Cmd untuk memilih beberapa jabatan">
                        ${posOptionsHtml}
                    </select>
                </td>
                <!-- RACI Informed (Multi Select) -->
                <td class="py-3 px-3">
                    <select name="steps[${currentIndex}][raci_i][]" multiple size="2" class="w-full bg-slate-50 text-slate-800 text-[11px] rounded-lg px-2 py-1 border border-emerald-200 focus:outline-none focus:ring-1 focus:ring-emerald-500" title="Tekan Ctrl/Cmd untuk memilih beberapa jabatan">
                        ${posOptionsHtml}
                    </select>
                </td>
                <!-- Action Delete -->
                <td class="py-3 px-3 text-center">
                    <button type="button" onclick="removeStepRow('${id}')" title="Hapus Baris Ini" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(row);
            attachDragAndDrop(row);
        }

        // Delete row confirmation
        function removeStepRow(rowId) {
            Swal.fire({
                title: 'Hapus baris ini?',
                text: 'Baris level dan data RACI pada baris ini akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = document.getElementById(rowId);
                    if (row) {
                        row.remove();
                        checkEmptyBuilder();
                    }
                }
            });
        }

        function checkEmptyBuilder() {
            const rows = document.querySelectorAll('#builderTableBody .step-row');
            const emptyState = document.getElementById('builderEmptyState');
            if (rows.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        // Drag & Drop Sorting implementation
        let dragSrcEl = null;

        function attachDragAndDrop(row) {
            row.addEventListener('dragstart', function(e) {
                dragSrcEl = this;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.outerHTML);
                this.classList.add('opacity-40');
            });

            row.addEventListener('dragover', function(e) {
                if (e.preventDefault) e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                return false;
            });

            row.addEventListener('dragenter', function(e) {
                this.classList.add('bg-indigo-50/50');
            });

            row.addEventListener('dragleave', function(e) {
                this.classList.remove('bg-indigo-50/50');
            });

            row.addEventListener('drop', function(e) {
                if (e.stopPropagation) e.stopPropagation();
                if (dragSrcEl !== this) {
                    const tbody = document.getElementById('builderTableBody');
                    const allRows = Array.from(tbody.querySelectorAll('.step-row'));
                    const srcIndex = allRows.indexOf(dragSrcEl);
                    const targetIndex = allRows.indexOf(this);

                    if (srcIndex < targetIndex) {
                        tbody.insertBefore(dragSrcEl, this.nextSibling);
                    } else {
                        tbody.insertBefore(dragSrcEl, this);
                    }
                }
                return false;
            });

            row.addEventListener('dragend', function() {
                this.classList.remove('opacity-40');
                const allRows = document.querySelectorAll('#builderTableBody .step-row');
                allRows.forEach(r => r.classList.remove('bg-indigo-50/50'));
            });
        }

        // Upload Preview
        function previewUploadFile(event) {
            const input = event.target;
            const preview = document.getElementById('fileUploadPreview');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                fileNameDisplay.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
                preview.classList.remove('hidden');
            }
        }

        function clearUploadFile() {
            const input = document.getElementById('bpm_file');
            input.value = '';
            document.getElementById('fileUploadPreview').classList.add('hidden');
        }

        // Form Submission Handlers
        function submitWithAction(actionType) {
            document.getElementById('formActionInput').value = actionType;
            document.getElementById('bpmForm').submit();
        }

        function confirmSubmission() {
            const form = document.getElementById('bpmForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const sourceType = document.querySelector('input[name="source_type"]:checked')?.value || 'builder';
            if (sourceType === 'builder') {
                const rows = document.querySelectorAll('#builderTableBody .step-row');
                if (rows.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'BPM Builder Masih Kosong',
                        text: 'Silakan tambahkan minimal satu baris tahapan Level 1 (L1) sebelum mengajukan.',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }

                let hasL1 = false;
                rows.forEach(r => {
                    if (r.dataset.level === 'L1') hasL1 = true;
                });

                if (!hasL1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Level 1 (L1) Diperlukan',
                        text: 'BPM harus memiliki minimal satu baris Level 1 (Proses Utama).',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }
            }

            Swal.fire({
                title: 'Konfirmasi Pengajuan BPM',
                text: 'Apakah Anda yakin seluruh data BPM, hierarki proses, dan matriks RACI sudah lengkap dan siap diajukan untuk diperiksa?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Ajukan BPM Sekarang',
                cancelButtonText: 'Periksa Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formActionInput').value = 'submit';
                    form.submit();
                }
            });
        }

        // Initialize with default L1 row on page load
        document.addEventListener('DOMContentLoaded', function() {
            addStepRow('L1');
        });
    </script>
</x-app-layout>

