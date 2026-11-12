<x-app-layout>
    <x-slot name="headerTitle">Revisi & Edit Dokumen BPM</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('bpms.index') }}" class="hover:underline">Workflow BPM</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('bpms.show', $bpm->id) }}" class="hover:underline">{{ $bpm->nama_bpm }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">{{ $bpm->isRejected() ? 'Revisi BPM' : 'Edit BPM' }}</span>
    </x-slot>

    <!-- Top Info -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border {{ $bpm->getStatusBadgeClass() }}">
                    {{ $bpm->getStatusLabel() }}
                </span>
                <span class="text-xs font-mono text-slate-500 font-bold">Versi {{ $bpm->version }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                {{ $bpm->isRejected() ? 'Revisi & Perbaikan BPM' : 'Edit Dokumen BPM' }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Lakukan perbaikan struktur hierarki L1-L3, uraian proses, dan matriks RACI sesuai arahan evaluasi.
            </p>
        </div>
        <a href="{{ route('bpms.show', $bpm->id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200 self-start md:self-auto">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Detail</span>
        </a>
    </div>

    <!-- Rejection Alert Callout if Rejected -->
    @if($bpm->isRejected() && $bpm->latestRejection)
        <div class="mb-8 p-6 rounded-3xl bg-rose-50 border-2 border-rose-200 text-rose-900 shadow-sm">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-xl shrink-0 shadow-md shadow-rose-600/30">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 bg-rose-200/80 text-rose-800 rounded-full">
                            Pemberitahuan Evaluasi Penolakan dari Super Admin
                        </span>
                        <span class="text-xs text-rose-700 font-medium">
                            Tanggal: {{ $bpm->rejected_at ? $bpm->rejected_at->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-rose-950 mb-1">
                        Catatan Evaluasi / Alasan Penolakan:
                    </h2>
                    <div class="bg-white/90 p-4 rounded-xl border border-rose-200 text-xs sm:text-sm text-rose-900 leading-relaxed font-medium mt-2 italic shadow-inner">
                        "{{ $bpm->latestRejection->rejection_reason }}"
                    </div>
                </div>
            </div>
        </div>
    @endif

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
    <form id="bpmForm" method="POST" action="{{ route('bpms.update', $bpm->id) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" id="formActionInput" value="submit">
        <input type="hidden" name="source_type" value="{{ $bpm->source_type }}">

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
                    <p class="text-xs text-slate-500 mt-0.5">Identitas utama dokumen Business Process Management.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Nama BPM -->
                <div class="md:col-span-2">
                    <label for="nama_bpm" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nama BPM <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama_bpm" name="nama_bpm" value="{{ old('nama_bpm', $bpm->nama_bpm) }}" 
                           required class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- 2. Nomor Dokumen BPM -->
                <div>
                    <label for="nomor_dokumen" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nomor Dokumen BPM <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nomor_dokumen" name="nomor_dokumen" value="{{ old('nomor_dokumen', $bpm->nomor_dokumen) }}" 
                           required class="w-full bg-slate-50 text-slate-900 text-sm font-mono rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- 3. Tanggal Efektif -->
                <div>
                    <label for="tanggal_efektif" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Tanggal Efektif <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tanggal_efektif" name="tanggal_efektif" value="{{ old('tanggal_efektif', $bpm->tanggal_efektif ? $bpm->tanggal_efektif->format('Y-m-d') : date('Y-m-d')) }}" 
                           required class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- 4. BOD -->
                <div>
                    <label for="bod_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        BOD (Board of Directors)
                    </label>
                    <select id="bod_id" name="bod_id" 
                            class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih BOD (Opsional) --</option>
                        @foreach($bods as $bod)
                            <option value="{{ $bod->id }}" {{ old('bod_id', $bpm->bod_id) == $bod->id ? 'selected' : '' }}>
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
                            <option value="{{ $dept->id }}" {{ old('department_id', $bpm->department_id) == $dept->id ? 'selected' : '' }}>
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
                              class="w-full bg-slate-50 text-slate-900 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('deskripsi', $bpm->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        @if($bpm->isBuilder())
            <!-- ========================================================= -->
            <!-- BAGIAN 2: AREA BPM BUILDER (L1, L2, L3 + RACI) -->
            <!-- ========================================================= -->
            <div id="builderSection" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-indigo-500/20">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">2. BPM Builder (Hierarki & RACI)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Tambah baris L1, L2, L3 secara bebas, ubah urutan (drag & drop), dan perbaiki RACI sesuai arahan.</p>
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
                            <!-- Prepopulated with existing steps -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State Container if no rows -->
                <div id="builderEmptyState" class="hidden p-8 text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto text-xl mb-3">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Belum Ada Tahapan Proses</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Gunakan tombol di bawah untuk menambahkan baris tahapan proses.</p>
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
        @else
            <!-- ========================================================= -->
            <!-- BAGIAN 2: AREA UPLOAD BERKAS -->
            <!-- ========================================================= -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-rose-500/20">
                        <i class="fa-solid fa-file-arrow-up"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">2. Berkas Dokumen BPM</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah berkas baru jika ada penggantian berkas dokumen.</p>
                    </div>
                </div>

                @if($bpm->attachments->count() > 0)
                    <div class="mb-4">
                        <p class="text-xs font-bold text-slate-700 mb-2">Berkas yang Saat Ini Terunggah:</p>
                        <div class="space-y-2">
                            @foreach($bpm->attachments as $att)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                    <div class="flex items-center space-x-3">
                                        <i class="fa-solid {{ $att->isPdf() ? 'fa-file-pdf text-rose-500' : 'fa-file-image text-blue-500' }} text-base"></i>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $att->file_name }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $att->formatted_size }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('bpms.attachments.preview', [$bpm->id, $att->id]) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-bold">
                                        Lihat File
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="p-6 border-2 border-dashed border-slate-300 hover:border-rose-400 rounded-2xl bg-slate-50/50 text-center transition-colors">
                    <input type="file" id="bpm_file" name="bpm_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="previewUploadFile(event)">
                    <label for="bpm_file" class="cursor-pointer flex flex-col items-center">
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mb-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-800">Pilih Berkas Baru (Opsional, mengganti berkas sebelumnya)</span>
                        <span class="text-[11px] text-slate-400 mt-0.5">PDF, PNG, JPG, JPEG (Maks. 10MB)</span>
                    </label>
                    <div id="fileUploadPreview" class="hidden mt-3 inline-flex items-center space-x-2 text-xs font-bold text-slate-700 bg-white px-3 py-1.5 rounded-lg border border-slate-200">
                        <span id="fileNameDisplay"></span>
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================================= -->
        <!-- ACTION BUTTONS -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('bpms.show', $bpm->id) }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all-200 text-center">
                Batal
            </a>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Simpan Draft Button -->
                <button type="button" onclick="submitWithAction('draft')" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all-200 shadow-md flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Sebagai Draft</span>
                </button>

                <!-- Ajukan Kembali Button -->
                <button type="button" onclick="confirmSubmission()" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs font-extrabold transition-all-200 shadow-lg shadow-indigo-500/30 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ $bpm->isRejected() ? 'Ajukan Kembali BPM (Revisi)' : (auth()->user()->isSuperAdmin() ? 'Simpan & Publikasikan' : 'Ajukan BPM ke Super Admin') }}</span>
                </button>
            </div>
        </div>
    </form>

    <script>
        const availablePositions = @json($positions);
        const existingSteps = @json($bpm->steps->map(fn($s) => [
            'level' => $s->level,
            'activity_description' => $s->activity_description,
            'raci_r' => $s->responsible->pluck('position_id')->toArray(),
            'raci_a' => $s->accountable->pluck('position_id')->first() ?? '',
            'raci_c' => $s->consulted->pluck('position_id')->toArray(),
            'raci_i' => $s->informed->pluck('position_id')->toArray(),
        ]));

        let stepIndex = 0;

        function addStepRow(level, initialData = {}) {
            const tbody = document.getElementById('builderTableBody');
            if (!tbody) return;
            const emptyState = document.getElementById('builderEmptyState');
            emptyState.classList.add('hidden');

            const id = 'step_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const currentIndex = stepIndex++;

            const indentClass = level === 'L1' ? 'pl-2' : (level === 'L2' ? 'pl-8' : 'pl-14');
            const badgeClass = level === 'L1' 
                ? 'bg-indigo-100 text-indigo-800 border-indigo-200' 
                : (level === 'L2' ? 'bg-sky-100 text-sky-800 border-sky-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200');
            const placeholder = level === 'L1' 
                ? 'Masukkan nama proses utama' 
                : (level === 'L2' ? 'Masukkan nama subproses' : 'Masukkan rincian aktivitas');

            const arrowIcon = level === 'L2' ? '↳ ' : (level === 'L3' ? '↳ ↳ ' : '');

            const selectedR = initialData.raci_r || [];
            const selectedA = initialData.raci_a || '';
            const selectedC = initialData.raci_c || [];
            const selectedI = initialData.raci_i || [];

            let posOptionsR = '';
            let posOptionsA = '<option value="">-- Pilih Jabatan --</option>';
            let posOptionsC = '';
            let posOptionsI = '';

            availablePositions.forEach(p => {
                const isR = selectedR.includes(p.id) ? 'selected' : '';
                const isA = (String(selectedA) === String(p.id)) ? 'selected' : '';
                const isC = selectedC.includes(p.id) ? 'selected' : '';
                const isI = selectedI.includes(p.id) ? 'selected' : '';

                posOptionsR += `<option value="${p.id}" ${isR}>${p.name}</option>`;
                posOptionsA += `<option value="${p.id}" ${isA}>${p.name}</option>`;
                posOptionsC += `<option value="${p.id}" ${isC}>${p.name}</option>`;
                posOptionsI += `<option value="${p.id}" ${isI}>${p.name}</option>`;
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
                               value="${initialData.activity_description ? initialData.activity_description.replace(/"/g, '&quot;') : ''}" 
                               placeholder="${placeholder}" 
                               required 
                               class="w-full bg-slate-50 text-slate-900 text-xs font-medium rounded-lg px-3 py-2 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </td>
                <td class="py-3 px-3">
                    <select name="steps[${currentIndex}][raci_r][]" multiple size="2" class="w-full bg-slate-50 text-slate-800 text-[11px] rounded-lg px-2 py-1 border border-indigo-200 focus:outline-none focus:ring-1 focus:ring-indigo-500" title="Tekan Ctrl/Cmd untuk memilih beberapa jabatan">
                        ${posOptionsR}
                    </select>
                </td>
                <td class="py-3 px-3">
                    <select name="steps[${currentIndex}][raci_a]" class="w-full bg-slate-50 text-slate-800 text-[11px] rounded-lg px-2 py-1 border border-amber-200 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        ${posOptionsA}
                    </select>
                </td>
                <td class="py-3 px-3">
                    <select name="steps[${currentIndex}][raci_c][]" multiple size="2" class="w-full bg-slate-50 text-slate-800 text-[11px] rounded-lg px-2 py-1 border border-sky-200 focus:outline-none focus:ring-1 focus:ring-sky-500" title="Tekan Ctrl/Cmd untuk memilih beberapa jabatan">
                        ${posOptionsC}
                    </select>
                </td>
                <td class="py-3 px-3">
                    <select name="steps[${currentIndex}][raci_i][]" multiple size="2" class="w-full bg-slate-50 text-slate-800 text-[11px] rounded-lg px-2 py-1 border border-emerald-200 focus:outline-none focus:ring-1 focus:ring-emerald-500" title="Tekan Ctrl/Cmd untuk memilih beberapa jabatan">
                        ${posOptionsI}
                    </select>
                </td>
                <td class="py-3 px-3 text-center">
                    <button type="button" onclick="removeStepRow('${id}')" title="Hapus Baris" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(row);
            attachDragAndDrop(row);
        }

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
            if (emptyState) {
                if (rows.length === 0) {
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                }
            }
        }

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

        function previewUploadFile(event) {
            const input = event.target;
            const preview = document.getElementById('fileUploadPreview');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                fileNameDisplay.textContent = input.files[0].name;
                preview.classList.remove('hidden');
            }
        }

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

            Swal.fire({
                title: 'Konfirmasi Pengajuan BPM',
                text: 'Apakah seluruh perbaikan susunan hierarki dan data RACI sudah selesai dan siap diajukan?',
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

        document.addEventListener('DOMContentLoaded', function() {
            if (existingSteps && existingSteps.length > 0) {
                existingSteps.forEach(s => {
                    addStepRow(s.level, s);
                });
            } else {
                addStepRow('L1');
            }
        });
    </script>
</x-app-layout>

