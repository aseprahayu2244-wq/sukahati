<x-app-layout>
    <x-slot name="headerTitle">{{ $sop->isRejected() ? 'Revisi Pengajuan SOP' : 'Edit Dokumen SOP' }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('sops.index') }}" class="hover:underline">Workflow SOP</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('sops.show', $sop->id) }}" class="hover:underline truncate max-w-xs">{{ $sop->nama_sop }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">{{ $sop->isRejected() ? 'Revisi & Ajukan Kembali' : 'Edit' }}</span>
    </x-slot>

    <!-- Rejection Alert Callout if Rejected -->
    @if($sop->isRejected())
        <div class="mb-8 p-6 rounded-3xl bg-rose-50 border-2 border-rose-300 shadow-md">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-xl shrink-0 shadow-md shadow-rose-600/30">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 bg-rose-200/80 text-rose-800 rounded-full">
                            Pengajuan Memerlukan Revisi
                        </span>
                        <span class="text-xs text-rose-700 font-medium">
                            Tanggal Penolakan: {{ $sop->rejected_at ? $sop->rejected_at->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-rose-950 mb-1">
                        Catatan Alasan Penolakan dari Super Admin ({{ $sop->latestRejectionReason?->reviewer?->name ?? 'Reviewer' }}):
                    </h2>
                    
                    <div class="bg-white/80 p-4 rounded-xl border border-rose-200 text-xs sm:text-sm text-rose-900 leading-relaxed font-medium mt-2 italic shadow-inner">
                        "{{ $sop->latestRejectionReason?->alasan_penolakan ?? 'Harap periksa dan lengkapi bagian yang belum sesuai ketentuan.' }}"
                    </div>

                    <p class="text-xs text-rose-700 mt-3">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Silakan perbaiki bagian-bagian yang disebutkan di atas. Setelah selesai, tekan <strong>Ajukan Kembali SOP</strong> agar masuk kembali ke antrean pemeriksaan Super Admin.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
            <div class="flex items-center space-x-2 font-bold text-rose-900 mb-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                <span>Terdapat kesalahan pada formulir:</span>
            </div>
            <ul class="list-disc pl-5 space-y-1 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <form id="sopEditForm" method="POST" action="{{ route('sops.update', $sop->id) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" id="formActionInput" value="submit">

        <!-- ========================================================= -->
        <!-- BAGIAN A: INFORMASI UMUM SOP -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-blue-500/20">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Bagian A: Informasi Umum SOP</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Versi Saat Ini: <strong class="text-slate-800">v{{ $sop->version }}</strong> &bull; Status: <strong class="text-slate-800">{{ $sop->getStatusLabel() }}</strong></p>
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
                           value="{{ old('nama_sop', $sop->nama_sop) }}" 
                           required
                           class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
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
                               value="{{ old('nomor_dokumen', $sop->nomor_dokumen) }}" 
                               required
                               class="w-full bg-slate-50 text-slate-800 font-mono text-sm rounded-xl pl-9 pr-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    </div>
                </div>

                <!-- 3. Departemen -->
                <div>
                    <label for="department_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        3. Departemen <span class="text-rose-500">*</span>
                    </label>
                    <select id="department_id" 
                            name="department_id" 
                            required
                            class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (old('department_id', $sop->department_id) == $dept->id) ? 'selected' : '' }}>
                                {{ $dept->name }} &bull; (BOD: {{ $dept->bod?->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
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
                               value="{{ old('tanggal_efektif', $sop->tanggal_efektif ? $sop->tanggal_efektif->format('Y-m-d') : date('Y-m-d')) }}" 
                               required
                               class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl pl-9 pr-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN B: ISI DOKUMEN SOP -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-emerald-500/20">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Bagian B: Isi Dokumen SOP</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Sesuaikan isi dan klausul dokumen berdasarkan arahan catatan evaluasi.</p>
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
                              class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl p-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">{{ old('tujuan', $sop->tujuan) }}</textarea>
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
                              class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl p-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">{{ old('ruang_lingkup', $sop->ruang_lingkup) }}</textarea>
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
                              class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl p-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">{{ old('definisi', $sop->definisi) }}</textarea>
                </div>

                <!-- 4. CATATAN REVISI -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                4. CATATAN REVISI
                            </label>
                            <p class="text-[11px] text-slate-400 mt-0.5">Catat riwayat dan versi perubahan yang baru Anda lakukan.</p>
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
                                @forelse($sop->revisionNotes as $idx => $note)
                                    <tr class="revision-row">
                                        <td class="p-2">
                                            <input type="text" name="revision_notes[{{ $idx }}][nomor_revisi]" value="{{ $note->nomor_revisi }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="date" name="revision_notes[{{ $idx }}][tanggal_revisi]" value="{{ $note->tanggal_revisi ? $note->tanggal_revisi->format('Y-m-d') : date('Y-m-d') }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="revision_notes[{{ $idx }}][keterangan]" value="{{ $note->keterangan }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeRevisionRow(this)" title="Hapus Baris" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="revision-row">
                                        <td class="p-2">
                                            <input type="text" name="revision_notes[0][nomor_revisi]" value="1.0" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200">
                                        </td>
                                        <td class="p-2">
                                            <input type="date" name="revision_notes[0][tanggal_revisi]" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="revision_notes[0][keterangan]" value="Penerbitan SOP Perdana" class="w-full bg-slate-50 text-slate-800 text-xs rounded-lg px-2.5 py-1.5 border border-slate-200">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeRevisionRow(this)" class="p-1.5 text-slate-400 hover:text-rose-600">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
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
                              class="w-full bg-slate-50 text-slate-800 text-sm rounded-xl p-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">{{ old('aturan_umum', $sop->aturan_umum) }}</textarea>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN C: FLOWCHART -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-purple-500/20">
                    <i class="fa-solid fa-diagram-project"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Bagian C: Diagram Alir (Flowchart)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Unggah berkas flowchart baru jika ingin mengganti flowchart saat ini.</p>
                </div>
            </div>

            <!-- Current Flowchart -->
            @if($sop->flowchart)
                <div class="mb-5 p-4 rounded-2xl bg-purple-50/60 border border-purple-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2 text-xs font-bold text-purple-900">
                            <i class="fa-solid fa-circle-check text-purple-600"></i>
                            <span>Flowchart Saat Ini: {{ $sop->flowchart->nama_file }} ({{ $sop->flowchart->formatted_size }})</span>
                        </div>
                        <a href="{{ $sop->flowchart->preview_url }}" target="_blank" class="text-xs text-purple-700 hover:text-purple-900 font-semibold flex items-center space-x-1">
                            <i class="fa-solid fa-up-right-from-square"></i>
                            <span>Buka di Tab Baru</span>
                        </a>
                    </div>

                    @if($sop->flowchart->isImage())
                        <div class="rounded-xl overflow-hidden border border-purple-200 max-h-72 flex items-center justify-center bg-white p-2">
                            <img src="{{ $sop->flowchart->preview_url }}" alt="Flowchart" class="max-h-64 object-contain">
                        </div>
                    @else
                        <div class="p-4 bg-white rounded-xl border border-purple-200 flex items-center space-x-3">
                            <i class="fa-solid fa-file-pdf text-3xl text-rose-500"></i>
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ $sop->flowchart->nama_file }}</p>
                                <p class="text-[11px] text-slate-500">Berkas format PDF</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Replace Flowchart Input -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    {{ $sop->flowchart ? 'Ganti Berkas Flowchart (Opsional)' : 'Unggah Berkas Flowchart' }}
                </label>
                
                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-purple-500 transition-colors bg-slate-50/50">
                    <div class="space-y-2 text-center">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-purple-400"></i>
                        <div class="flex text-xs text-slate-600 justify-center items-center">
                            <label for="flowchart" class="relative cursor-pointer bg-white rounded-md font-bold text-purple-600 hover:text-purple-500 px-2.5 py-1 border border-purple-200 shadow-sm">
                                <span>Pilih Berkas Baru</span>
                                <input id="flowchart" name="flowchart" type="file" accept=".png,.jpg,.jpeg,.pdf" class="sr-only">
                            </label>
                        </div>
                        <p class="text-[11px] text-slate-400">Biarkan kosong jika tidak ingin mengubah flowchart saat ini.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN D: LAMPIRAN -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-900 flex items-center justify-center text-lg font-bold shadow-sm shadow-amber-500/20">
                    <i class="fa-solid fa-paperclip"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Bagian D: Berkas Lampiran Tambahan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola berkas lampiran yang sudah ada atau tambahkan berkas pendukung baru.</p>
                </div>
            </div>

            <!-- Existing Attachments List -->
            @if($sop->regularAttachments->count() > 0)
                <div class="mb-5">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lampiran Tersimpan Saat Ini:</h3>
                    <div class="space-y-2">
                        @foreach($sop->regularAttachments as $att)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid {{ $att->getFileIconClass() }} text-base"></i>
                                    <div>
                                        <a href="{{ $att->url }}" target="_blank" class="font-bold text-slate-800 hover:text-blue-600 transition-colors">
                                            {{ $att->nama_file }}
                                        </a>
                                        <p class="text-[10px] text-slate-400">{{ $att->formatted_size }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <a href="{{ $att->url }}" target="_blank" class="p-1.5 text-slate-500 hover:text-blue-600" title="Unduh">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <button type="button" 
                                            onclick="confirmDeleteAttachment('{{ route('sops.attachments.delete', [$sop->id, $att->id]) }}', '{{ $att->nama_file }}')" 
                                            class="p-1.5 text-rose-500 hover:text-rose-700" 
                                            title="Hapus Lampiran Ini">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Add More Attachments -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Tambah Berkas Lampiran Baru
                </label>
                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-amber-500 transition-colors bg-slate-50/50">
                    <div class="space-y-2 text-center">
                        <i class="fa-solid fa-file-arrow-up text-3xl text-amber-400"></i>
                        <div class="flex text-xs text-slate-600 justify-center items-center">
                            <label for="lampiranInput" class="relative cursor-pointer bg-white rounded-md font-bold text-amber-700 hover:text-amber-600 px-2.5 py-1 border border-amber-200 shadow-sm">
                                <span>Pilih Berkas Lampiran</span>
                                <input id="lampiranInput" name="lampiran[]" type="file" multiple class="sr-only" onchange="handleMultipleAttachments(event)">
                            </label>
                        </div>
                        <p class="text-[11px] text-slate-400">Mendukung PDF, DOCX, XLSX, PNG, JPG (Maks. 20MB per berkas)</p>
                    </div>
                </div>

                <div id="attachmentListContainer" class="hidden mt-4 space-y-2">
                    <h4 class="text-xs font-bold text-slate-700">Lampiran Baru yang Akan Ditambahkan:</h4>
                    <div id="attachmentItemsList" class="space-y-2"></div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- BAGIAN E: TOMBOL FORMULIR -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-lg sticky bottom-4 z-20 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('sops.show', $sop->id) }}" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold text-center transition-all-200 flex items-center justify-center space-x-2">
                <i class="fa-solid fa-xmark"></i>
                <span>Batal</span>
            </a>

            <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
                <!-- Simpan Draft Button -->
                <button type="button" onclick="submitWithAction('draft')" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all-200 shadow-md flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan Draft</span>
                </button>

                <!-- Ajukan / Ajukan Kembali Button -->
                <button type="button" onclick="confirmResubmission()" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-extrabold transition-all-200 shadow-lg shadow-emerald-500/30 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>{{ $sop->isRejected() ? 'Ajukan Kembali SOP ke Super Admin' : 'Simpan & Ajukan SOP' }}</span>
                </button>
            </div>
        </div>

    </form>

    <!-- Delete Attachment Hidden Form -->
    <form id="deleteAttachmentForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        let revisionIndex = {{ $sop->revisionNotes->count() }};

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

        function handleMultipleAttachments(event) {
            const input = event.target;
            const container = document.getElementById('attachmentListContainer');
            const list = document.getElementById('attachmentItemsList');
            list.innerHTML = '';

            if (input.files && input.files.length > 0) {
                container.classList.remove('hidden');
                Array.from(input.files).forEach((file) => {
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
                        <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-semibold">Siap Ditambahkan</span>
                    `;
                    list.appendChild(item);
                });
            } else {
                container.classList.add('hidden');
            }
        }

        function confirmDeleteAttachment(deleteUrl, fileName) {
            Swal.fire({
                title: 'Hapus Lampiran Ini?',
                text: `Berkas "${fileName}" akan dihapus dari SOP ini.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteAttachmentForm');
                    form.action = deleteUrl;
                    form.submit();
                }
            });
        }

        function submitWithAction(actionType) {
            document.getElementById('formActionInput').value = actionType;
            document.getElementById('sopEditForm').submit();
        }

        function confirmResubmission() {
            const form = document.getElementById('sopEditForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: '{{ $sop->isRejected() ? "Ajukan Kembali SOP?" : "Konfirmasi Simpan SOP" }}',
                text: '{{ $sop->isRejected() ? "SOP yang telah direvisi akan dikirimkan kembali ke antrean pemeriksaan Super Admin." : "Apakah Anda yakin seluruh data yang diperbarui sudah benar?" }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Lanjutkan',
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

