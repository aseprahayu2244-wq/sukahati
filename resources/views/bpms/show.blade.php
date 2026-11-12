<x-app-layout>
    <x-slot name="headerTitle">{{ $bpm->nama_bpm }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        @if(auth()->user()->canManageDocuments())
            <a href="{{ route('bpms.index') }}" class="hover:underline">Workflow BPM</a>
        @else
            <a href="{{ route('documents.index', 'bpm') }}" class="hover:underline">Direktori BPM</a>
            @if($bpm->bod && $bpm->department)
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <a href="{{ route('documents.positions', ['bpm', $bpm->bod_id, $bpm->department_id]) }}" class="hover:underline">{{ $bpm->department->name }}</a>
            @endif
        @endif
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold truncate max-w-sm">{{ $bpm->nama_bpm }}</span>
    </x-slot>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8 relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $bpm->getStatusBadgeClass() }}">
                        <i class="fa-solid {{ $bpm->getStatusIcon() }} text-[11px]"></i>
                        <span>{{ $bpm->getStatusLabel() }}</span>
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        Versi {{ $bpm->version }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <i class="fa-solid {{ $bpm->isBuilder() ? 'fa-wand-magic-sparkles' : 'fa-cloud-arrow-up' }} mr-1 text-[11px]"></i>
                        {{ $bpm->isBuilder() ? 'BPM Builder' : 'Upload Berkas' }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fa-solid fa-layer-group mr-1 text-[11px]"></i>
                        {{ $bpm->department?->name ?? 'Departemen' }}
                    </span>
                    @if($bpm->bod)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                            <i class="fa-solid fa-building mr-1 text-[11px]"></i>
                            BOD: {{ $bpm->bod->name }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">
                    {{ $bpm->nama_bpm }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 font-medium">
                    <span class="flex items-center space-x-1.5 font-mono bg-slate-100 px-2.5 py-1 rounded-lg text-slate-700 font-bold">
                        <i class="fa-solid fa-barcode text-slate-400"></i>
                        <span>{{ $bpm->nomor_dokumen }}</span>
                    </span>
                    <span class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-calendar-day text-slate-400"></i>
                        <span>Tgl Efektif: <strong class="text-slate-700">{{ $bpm->tanggal_efektif ? $bpm->tanggal_efektif->format('d M Y') : '-' }}</strong></span>
                    </span>
                    <span class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-user-pen text-slate-400"></i>
                        <span>Dibuat oleh: <strong class="text-slate-700">{{ $bpm->creator?->name ?? 'Admin' }}</strong></span>
                    </span>
                    @if($bpm->isApproved())
                        <span class="flex items-center space-x-1.5 text-emerald-700 font-semibold">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Disetujui oleh: {{ $bpm->approver?->name ?? 'Super Admin' }} ({{ $bpm->approved_at ? $bpm->approved_at->format('d M Y') : '' }})</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('bpms.print', $bpm->id) }}" target="_blank" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all-200 shadow-sm">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    <span>Cetak / PDF</span>
                </a>

                <a href="{{ route('bpms.preview', $bpm->id) }}" class="inline-flex items-center space-x-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs px-4 py-2.5 rounded-xl transition-all-200">
                    <i class="fa-solid fa-eye"></i>
                    <span>Preview Resmi</span>
                </a>

                @if($bpm->canBeEditedBy(auth()->user()))
                    <a href="{{ route('bpms.edit', $bpm->id) }}" class="inline-flex items-center space-x-2 bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold text-xs px-4 py-2.5 rounded-xl transition-all-200 shadow-sm shadow-amber-500/20">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>{{ $bpm->isRejected() ? 'Revisi BPM' : 'Edit BPM' }}</span>
                    </a>
                @endif

                @if($bpm->canBeDeletedBy(auth()->user()))
                    <form method="POST" action="{{ route('bpms.destroy', $bpm->id) }}" onsubmit="return confirmDelete(event, 'Dokumen BPM ini akan dihapus secara permanen!', 'Hapus BPM?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center space-x-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs px-3.5 py-2.5 rounded-xl transition-all-200">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Hapus</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
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
                            Pemberitahuan Penolakan BPM
                        </span>
                        <span class="text-xs text-rose-700 font-medium">
                            Tanggal: {{ $bpm->rejected_at ? $bpm->rejected_at->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-rose-950 mb-1">
                        Catatan Evaluasi & Alasan Penolakan dari Super Admin:
                    </h2>
                    <div class="bg-white/80 p-4 rounded-xl border border-rose-200 text-xs sm:text-sm text-rose-900 leading-relaxed font-medium mt-2 italic shadow-inner">
                        "{{ $bpm->latestRejection->rejection_reason }}"
                    </div>

                    @if($bpm->canBeEditedBy(auth()->user()))
                        <div class="mt-4 pt-3 border-t border-rose-200 flex items-center justify-between">
                            <span class="text-xs text-rose-700">Silakan perbaiki BPM sesuai arahan dan ajukan kembali.</span>
                            <a href="{{ route('bpms.edit', $bpm->id) }}" class="inline-flex items-center space-x-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all-200 shadow-sm">
                                <i class="fa-solid fa-wrench"></i>
                                <span>Revisi & Ajukan Kembali BPM</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Description Card if available -->
    @if($bpm->deskripsi)
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-2">Deskripsi / Ruang Lingkup Proses</h2>
            <div class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                {{ $bpm->deskripsi }}
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- CONTENT DISPLAY: BPM BUILDER TABLE OR UPLOADED DOCUMENT -->
    <!-- ========================================================= -->
    @if($bpm->isBuilder())
        <!-- BPM Builder Hierarchy & RACI Matrix -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-indigo-500/20">
                        <i class="fa-solid fa-table-list"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Hierarki Proses Bisnis & Matriks RACI</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tingkatan proses L1, L2, L3 dan pembagian tanggung jawab per aktivitas.</p>
                    </div>
                </div>

                <!-- RACI Legend -->
                <div class="flex items-center space-x-2 text-[11px] bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-slate-600">
                    <span class="font-bold text-indigo-600">R:</span> Responsible &bull;
                    <span class="font-bold text-amber-600">A:</span> Accountable &bull;
                    <span class="font-bold text-sky-600">C:</span> Consulted &bull;
                    <span class="font-bold text-emerald-600">I:</span> Informed
                </div>
            </div>

            <!-- Table View -->
            <div class="overflow-x-auto border border-slate-200 rounded-2xl shadow-inner">
                <table class="w-full text-left text-xs text-slate-700 min-w-[850px]">
                    <thead class="bg-slate-100/90 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4 min-w-[320px]">Level / Aktivitas</th>
                            <th class="py-3.5 px-4 w-48 text-indigo-700">Responsible (R)</th>
                            <th class="py-3.5 px-4 w-44 text-amber-700">Accountable (A)</th>
                            <th class="py-3.5 px-4 w-44 text-sky-700">Consulted (C)</th>
                            <th class="py-3.5 px-4 w-44 text-emerald-700">Informed (I)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($bpm->steps as $step)
                            @php
                                $indentClass = match($step->level) {
                                    'L1' => 'pl-4 font-bold bg-slate-50/50',
                                    'L2' => 'pl-10 font-semibold',
                                    'L3' => 'pl-16',
                                    default => 'pl-4',
                                };
                                $arrow = match($step->level) {
                                    'L1' => '',
                                    'L2' => '↳ ',
                                    'L3' => '↳ ↳ ',
                                    default => '',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 {{ $indentClass }}">
                                    <div class="flex items-center space-x-2">
                                        @if($arrow)
                                            <span class="text-slate-400 font-mono text-xs select-none">{{ $arrow }}</span>
                                        @endif
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded border {{ $step->getLevelBadgeClass() }}">
                                            {{ $step->level }}
                                        </span>
                                        <span class="text-slate-900 text-xs">{{ $step->activity_description }}</span>
                                    </div>
                                </td>
                                <!-- Responsible -->
                                <td class="py-3 px-4">
                                    @if($step->responsible->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($step->responsible as $r)
                                                <span class="text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded">
                                                    {{ $r->position?->name ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-300 italic">-</span>
                                    @endif
                                </td>
                                <!-- Accountable -->
                                <td class="py-3 px-4">
                                    @if($step->accountable->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($step->accountable as $a)
                                                <span class="text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 rounded">
                                                    {{ $a->position?->name ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-300 italic">-</span>
                                    @endif
                                </td>
                                <!-- Consulted -->
                                <td class="py-3 px-4">
                                    @if($step->consulted->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($step->consulted as $c)
                                                <span class="text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200 px-2 py-0.5 rounded">
                                                    {{ $c->position?->name ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-300 italic">-</span>
                                    @endif
                                </td>
                                <!-- Informed -->
                                <td class="py-3 px-4">
                                    @if($step->informed->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($step->informed as $i)
                                                <span class="text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded">
                                                    {{ $i->position?->name ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-300 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 italic">
                                    Belum ada tahapan proses yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Uploaded Document View -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
            <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold shadow-sm shadow-rose-500/20">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Berkas Dokumen BPM</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Dokumen BPM yang diunggah dalam format berkas PDF atau gambar.</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($bpm->attachments as $att)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200 gap-3">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-lg text-rose-500 shadow-sm shrink-0">
                                <i class="fa-solid {{ $att->isPdf() ? 'fa-file-pdf' : 'fa-file-image text-blue-500' }}"></i>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800">{{ $att->file_name }}</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Ukuran: {{ $att->formatted_size }} &bull; Diunggah oleh: {{ $att->uploader?->name ?? 'Admin' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 shrink-0">
                            <a href="{{ route('bpms.attachments.preview', [$bpm->id, $att->id]) }}" target="_blank" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition-colors">
                                <i class="fa-solid fa-eye mr-1"></i>
                                <span>Lihat Berkas</span>
                            </a>
                            <a href="{{ route('bpms.attachments.download', [$bpm->id, $att->id]) }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                                <i class="fa-solid fa-download mr-1"></i>
                                <span>Unduh</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic text-center py-4">Tidak ada berkas yang terunggah.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- SUPER ADMIN DECISION PANEL (Approve / Reject) -->
    <!-- ========================================================= -->
    @if($bpm->canBeApprovedBy(auth()->user()))
        <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center space-x-2 bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs px-3 py-1 rounded-full font-semibold mb-2">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i>
                        <span>Panel Otorisasi Super Admin</span>
                    </div>
                    <h3 class="text-xl font-extrabold text-white">Evaluasi & Keputusan Pengajuan BPM</h3>
                    <p class="text-xs text-slate-300 mt-1 max-w-xl">
                        Periksa kelayakan susunan hierarki L1-L3 dan tanggung jawab RACI di atas. Berikan persetujuan untuk mempublikasikan BPM secara resmi, atau tolak dengan menyertakan instruksi revisi.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Reject Button triggers modal -->
                    <button type="button" onclick="openRejectModal()" class="px-5 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition-all-200 flex items-center space-x-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span>Tolak BPM (Revisi)</span>
                    </button>

                    <!-- Approve Form -->
                    <form method="POST" action="{{ route('bpms.approve', $bpm->id) }}" onsubmit="return confirmApproval(event)">
                        @csrf
                        <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition-all-200 flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Setujui & Publikasikan BPM</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rejection Modal -->
        <div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Formulir Penolakan BPM</h3>
                            <p class="text-[11px] text-slate-400">Instruksi perbaikan bagi Admin pengaju</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('bpms.reject', $bpm->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="alasan_penolakan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Alasan Penolakan / Catatan Revisi <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alasan_penolakan" name="alasan_penolakan" rows="4" required 
                                  placeholder="Contoh: Susunan level BPM belum sesuai. Aktivitas seleksi administrasi perlu ditempatkan sebagai L3 dari subproses seleksi. Kolom Accountable juga perlu diperjelas..." 
                                  class="w-full bg-slate-50 text-slate-900 text-xs rounded-xl p-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Alasan penolakan akan dikirimkan langsung ke Admin pengaju melalui notifikasi.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-2">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all-200">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all-200 shadow-sm shadow-rose-600/30">
                            Konfirmasi Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- RIWAYAT PENOLAKAN & REVISI -->
    <!-- ========================================================= -->
    @if($bpm->rejections->count() > 0 || $bpm->versions->count() > 0)
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                <span>Riwayat Revisi & Penolakan Dokumen</span>
            </h2>

            <div class="space-y-4">
                @foreach($bpm->versions as $v)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-900">Versi {{ $v->version_number }}</span>
                            <span class="text-slate-400 text-[11px]">{{ $v->created_at ? $v->created_at->format('d M Y, H:i') : '' }}</span>
                        </div>
                        <p class="text-slate-600">{{ $v->change_description ?? 'Pembaruan data BPM.' }}</p>
                        <p class="text-[10px] text-slate-400 mt-1">Diubah oleh: {{ $v->user?->name ?? 'Sistem' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <script>
        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function confirmApproval(event) {
            event.preventDefault();
            const form = event.target;

            Swal.fire({
                title: 'Setujui Dokumen BPM?',
                text: 'BPM ini akan dipublikasikan secara resmi ke seluruh pengguna dan direktori organisasi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Setujui & Publikasikan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
    </script>
</x-app-layout>

