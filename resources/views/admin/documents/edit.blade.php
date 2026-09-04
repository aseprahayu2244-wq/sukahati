<x-app-layout>
    <x-slot name="headerTitle">Edit Dokumen: {{ $document->title }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Edit Dokumen</span>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Form Edit Dokumen HRD</h1>
                <p class="text-xs text-slate-500 mt-0.5">Ubah informasi atau ganti berkas file dokumen</p>
            </div>
        </div>

        <form action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Document Category -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori Dokumen <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                        <input type="radio" name="type" value="sop" {{ $document->type === 'sop' ? 'checked' : '' }} required class="sr-only">
                        <i class="fa-solid fa-file-contract text-2xl text-emerald-500 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">SOP</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                        <input type="radio" name="type" value="job" {{ $document->type === 'job' ? 'checked' : '' }} required class="sr-only">
                        <i class="fa-solid fa-user-gear text-2xl text-amber-500 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">Job Description</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                        <input type="radio" name="type" value="job_analysis" {{ $document->type === 'job_analysis' ? 'checked' : '' }} required class="sr-only">
                        <i class="fa-solid fa-magnifying-glass-chart text-2xl text-purple-500 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">Job Analysis</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-2xl p-3 flex flex-col items-center justify-center text-center transition-all-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                        <input type="radio" name="type" value="bpmn" {{ $document->type === 'bpmn' ? 'checked' : '' }} required class="sr-only">
                        <i class="fa-solid fa-diagram-project text-2xl text-rose-500 mb-1"></i>
                        <span class="text-xs font-bold text-slate-800">BPMN Diagram</span>
                    </label>
                </div>
            </div>

            <!-- Division & Position Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Divisi <span class="text-rose-500">*</span></label>
                    <select name="division_id" id="divisionSelect" required onchange="loadPositions(this.value)" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ (string)$document->division_id === (string)$div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jabatan <span class="text-rose-500">*</span></label>
                    <select name="position_id" id="positionSelect" required class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Memuat Jabatan --</option>
                    </select>
                </div>
            </div>

            <!-- Title Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Dokumen <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title', $document->title) }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Description Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Dokumen</label>
                <textarea name="description" rows="3" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $document->description) }}</textarea>
            </div>

            <!-- Optional File Replace -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ganti Berkas File (Biarkan kosong jika tidak diganti)</label>
                <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50">
                    <p class="text-xs text-slate-600 mb-2">Berkas saat ini: <span class="font-bold text-blue-600">{{ $document->file_name }}</span> ({{ formatBytes($document->file_size) }})</p>
                    <input type="file" name="file" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="history.back()" class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-all-200">
                    Batal
                </button>
                <button type="submit" class="px-8 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-amber-500/20 transition-all-200">
                    <i class="fa-solid fa-check mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Script for Dynamic Position Loading -->
    <script>
        const initialDivision = "{{ $document->division_id }}";
        const initialPosition = "{{ $document->position_id }}";

        function loadPositions(divisionId) {
            const select = document.getElementById('positionSelect');
            select.innerHTML = '<option value="">Memuat jabatan...</option>';
            
            if (!divisionId) {
                select.innerHTML = '<option value="">-- Pilih Divisi Terlebih Dahulu --</option>';
                return;
            }

            fetch(`/api/divisions/${divisionId}/positions`)
                .then(res => res.json())
                .then(data => {
                    select.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                    data.forEach(pos => {
                        const selected = (initialPosition && String(initialPosition) === String(pos.id)) ? 'selected' : '';
                        select.innerHTML += `<option value="${pos.id}" ${selected}>${pos.name}</option>`;
                    });
                })
                .catch(() => {
                    select.innerHTML = '<option value="">Gagal memuat jabatan</option>';
                });
        }

        if (initialDivision) {
            loadPositions(initialDivision);
        }
    </script>
</x-app-layout>
