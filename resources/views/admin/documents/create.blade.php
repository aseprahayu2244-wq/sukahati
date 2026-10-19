<x-app-layout>
    <x-slot name="headerTitle">Upload Dokumen HRD Baru</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Upload Dokumen</span>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Form Unggah Dokumen HRD</h1>
                <p class="text-xs text-slate-500 mt-0.5">Semua format file didukung (PDF, Word, Excel, Gambar, Diagram BPMN, Zip, dll)</p>
            </div>
        </div>

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Document Category -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori Dokumen <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all-200 hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                        <input type="radio" name="type" value="sop" {{ old('type', $type ?? 'sop') === 'sop' ? 'checked' : '' }} required class="sr-only" onchange="togglePositionField()">
                        <i class="fa-solid fa-file-contract text-2xl text-emerald-500 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">SOP</span>
                        <span class="text-[10px] text-slate-400">BOD &rarr; Dept &rarr; File</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all-200 hover:border-amber-500 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50">
                        <input type="radio" name="type" value="job_desk" {{ old('type', $type ?? '') === 'job_desk' ? 'checked' : '' }} required class="sr-only" onchange="togglePositionField()">
                        <i class="fa-solid fa-user-gear text-2xl text-amber-500 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">Job Desk</span>
                        <span class="text-[10px] text-slate-400">BOD &rarr; Dept &rarr; Jabatan</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all-200 hover:border-purple-500 has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50/50">
                        <input type="radio" name="type" value="bpm" {{ old('type', $type ?? '') === 'bpm' ? 'checked' : '' }} required class="sr-only" onchange="togglePositionField()">
                        <i class="fa-solid fa-diagram-project text-2xl text-purple-500 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">BPM</span>
                        <span class="text-[10px] text-slate-400">BOD &rarr; Dept &rarr; File</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all-200 hover:border-rose-500 has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50/50">
                        <input type="radio" name="type" value="struktur" {{ old('type', $type ?? '') === 'struktur' ? 'checked' : '' }} required class="sr-only" onchange="togglePositionField()">
                        <i class="fa-solid fa-sitemap text-2xl text-rose-500 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">Struktur</span>
                        <span class="text-[10px] text-slate-400">BOD &rarr; Dept &rarr; File</span>
                    </label>
                </div>
            </div>

            <!-- Hierarchy: BOD, Departemen, Jabatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- BOD -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">BOD <span class="text-rose-500">*</span></label>
                    <select name="bod_id" id="bodSelect" required onchange="onBodChange(this.value)" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih BOD --</option>
                        @foreach($bods as $b)
                            <option value="{{ $b->id }}" {{ (string)old('bod_id', $bodId ?? '') === (string)$b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departemen -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Departemen <span class="text-rose-500">*</span></label>
                    <select name="division_id" id="departmentSelect" required onchange="onDepartmentChange(this.value)" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih BOD Terlebih Dahulu --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (string)old('division_id', $departmentId ?? '') === (string)$dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Jabatan (khusus Job Desk) -->
            <div id="positionContainer" class="{{ old('type', $type ?? 'sop') === 'job_desk' ? '' : 'hidden' }}">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Jabatan <span class="text-rose-500">* (Wajib untuk Job Desk)</span>
                </label>
                <select name="position_id" id="positionSelect" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Departemen Terlebih Dahulu --</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ (string)old('position_id', $positionId ?? '') === (string)$pos->id ? 'selected' : '' }}>
                            {{ $pos->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Title Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Dokumen <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Contoh: SOP Rekrutmen Karyawan Baru 2026" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Dokumen</label>
                <textarea name="description" rows="3" placeholder="Tuliskan keterangan mengenai dokumen ini..." class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <!-- File Upload Zone -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Berkas File <span class="text-rose-500">*</span></label>
                <div class="relative border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-3xl p-8 text-center bg-slate-50/50 hover:bg-blue-50/30 transition-all-200 group">
                    <input type="file" name="file" id="fileInput" required onchange="handleFileSelect(this)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div id="uploadPrompt" class="flex flex-col items-center justify-center pointer-events-none">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-3 shadow-inner group-hover:scale-110 transition-all-200">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Klik atau geser file ke area ini untuk upload</p>
                        <p class="text-xs text-slate-400 mt-1">Mendukung file dokumen PDF, Word, Excel, Gambar, Diagram BPMN, Zip dll (Maksimal 50MB)</p>
                    </div>
                    <div id="filePreview" class="hidden flex items-center justify-center space-x-3 pointer-events-none">
                        <i class="fa-solid fa-file text-2xl text-blue-600"></i>
                        <span id="fileName" class="text-sm font-bold text-slate-800"></span>
                        <span id="fileSize" class="text-xs text-slate-400 font-medium"></span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-between">
                <a href="{{ url()->previous() }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-all-200">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Batal & Kembali
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3.5 rounded-xl shadow-lg shadow-blue-500/25 transition-all-200 flex items-center space-x-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Upload Dokumen Sekarang</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function togglePositionField() {
            const selectedType = document.querySelector('input[name="type"]:checked').value;
            const container = document.getElementById('positionContainer');
            const select = document.getElementById('positionSelect');
            if (selectedType === 'job_desk') {
                container.classList.remove('hidden');
                select.required = true;
            } else {
                container.classList.add('hidden');
                select.required = false;
                select.value = '';
            }
        }

        async function onBodChange(bodId) {
            const deptSelect = document.getElementById('departmentSelect');
            const posSelect  = document.getElementById('positionSelect');
            deptSelect.innerHTML = '<option value="">Memuat data departemen...</option>';
            posSelect.innerHTML  = '<option value="">-- Pilih Departemen Terlebih Dahulu --</option>';

            if (!bodId) {
                deptSelect.innerHTML = '<option value="">-- Pilih BOD Terlebih Dahulu --</option>';
                return;
            }

            try {
                const res = await fetch(`/api/bods/${bodId}/departments`);
                const depts = await res.json();
                deptSelect.innerHTML = '<option value="">-- Pilih Departemen --</option>';
                depts.forEach(d => {
                    deptSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                });
            } catch (e) {
                deptSelect.innerHTML = '<option value="">Gagal memuat departemen</option>';
            }
        }

        async function onDepartmentChange(deptId) {
            const posSelect = document.getElementById('positionSelect');
            posSelect.innerHTML = '<option value="">Memuat data jabatan...</option>';

            if (!deptId) {
                posSelect.innerHTML = '<option value="">-- Pilih Departemen Terlebih Dahulu --</option>';
                return;
            }

            try {
                const res = await fetch(`/api/departments/${deptId}/positions`);
                const positions = await res.json();
                posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                positions.forEach(p => {
                    posSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            } catch (e) {
                posSelect.innerHTML = '<option value="">Gagal memuat jabatan</option>';
            }
        }

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = '(' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                document.getElementById('uploadPrompt').classList.add('hidden');
                document.getElementById('filePreview').classList.remove('hidden');
            }
        }

        // Run once on load
        document.addEventListener('DOMContentLoaded', togglePositionField);
    </script>
    @endpush
</x-app-layout>
