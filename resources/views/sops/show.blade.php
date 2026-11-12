<x-app-layout>
    <x-slot name="headerTitle">{{ $sop->nama_sop }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        @if(auth()->user()->canManageDocuments())
            <a href="{{ route('sops.index') }}" class="hover:underline">Workflow SOP</a>
        @else
            <a href="{{ route('documents.index', 'sop') }}" class="hover:underline">Direktori SOP</a>
            @if($sop->bod && $sop->department)
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <a href="{{ route('documents.positions', ['sop', $sop->bod_id, $sop->department_id]) }}" class="hover:underline">{{ $sop->department->name }}</a>
            @endif
        @endif
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold truncate max-w-sm">{{ $sop->nama_sop }}</span>
    </x-slot>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8 relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $sop->getStatusBadgeClass() }}">
                        <i class="fa-solid {{ $sop->getStatusIcon() }} text-[11px]"></i>
                        <span>{{ $sop->getStatusLabel() }}</span>
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        Versi {{ $sop->version }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fa-solid fa-layer-group mr-1 text-[11px]"></i>
                        {{ $sop->department?->name ?? 'Departemen' }}
                    </span>
                    @if($sop->bod)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <i class="fa-solid fa-building mr-1 text-[11px]"></i>
                            BOD: {{ $sop->bod->name }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">
                    {{ $sop->nama_sop }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 font-medium">
                    <span class="flex items-center space-x-1.5 font-mono bg-slate-100 px-2.5 py-1 rounded-lg text-slate-700 font-bold">
                        <i class="fa-solid fa-barcode text-slate-400"></i>
                        <span>{{ $sop->nomor_dokumen }}</span>
                    </span>
                    <span class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-calendar-day text-slate-400"></i>
                        <span>Tgl Efektif: <strong class="text-slate-700">{{ $sop->tanggal_efektif ? $sop->tanggal_efektif->format('d M Y') : '-' }}</strong></span>
                    </span>
                    <span class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-user-pen text-slate-400"></i>
                        <span>Dibuat oleh: <strong class="text-slate-700">{{ $sop->creator?->name ?? 'Admin' }}</strong></span>
                    </span>
                    @if($sop->isApproved())
                        <span class="flex items-center space-x-1.5 text-emerald-700 font-semibold">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Disetujui oleh: {{ $sop->approver?->name ?? 'Super Admin' }} ({{ $sop->approved_at ? $sop->approved_at->format('d M Y') : '' }})</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('sops.print', $sop->id) }}" target="_blank" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all-200 shadow-sm">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    <span>Download PDF</span>
                </a>

                @if($sop->canBeEditedBy(auth()->user()))
                    <a href="{{ route('sops.edit', $sop->id) }}" class="inline-flex items-center space-x-2 bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold text-xs px-4 py-2.5 rounded-xl transition-all-200 shadow-sm shadow-amber-500/20">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>{{ $sop->isRejected() ? 'Revisi SOP' : 'Edit SOP' }}</span>
                    </a>
                @endif

                @if($sop->canBeDeletedBy(auth()->user()))
                    <form method="POST" action="{{ route('sops.destroy', $sop->id) }}" onsubmit="return confirmDelete(event, 'Dokumen SOP ini akan dihapus secara permanen!', 'Hapus SOP?');">
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
    @if($sop->isRejected())
        <div class="mb-8 p-6 rounded-3xl bg-rose-50 border-2 border-rose-200 text-rose-900 shadow-sm">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-xl shrink-0 shadow-md shadow-rose-600/30">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 bg-rose-200/80 text-rose-800 rounded-full">
                            Pemberitahuan Penolakan SOP
                        </span>
                        <span class="text-xs text-rose-700 font-medium">
                            Tanggal: {{ $sop->rejected_at ? $sop->rejected_at->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-rose-950 mb-1">
                        Catatan Evaluasi & Alasan Penolakan dari Super Admin:
                    </h2>
                    <div class="bg-white/80 p-4 rounded-xl border border-rose-200 text-xs sm:text-sm text-rose-900 leading-relaxed font-medium mt-2 italic shadow-inner">
                        "{{ $sop->latestRejectionReason?->alasan_penolakan ?? 'Perbaiki bagian yang belum sesuai ketentuan.' }}"
                    </div>
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 pt-2 text-xs text-rose-800">
                        <span>Diperiksa oleh: <strong>{{ $sop->latestRejectionReason?->reviewer?->name ?? 'Super Admin' }}</strong></span>
                        @if($sop->canBeEditedBy(auth()->user()))
                            <a href="{{ route('sops.edit', $sop->id) }}" class="inline-flex items-center space-x-2 bg-rose-600 hover:bg-rose-700 text-white font-bold px-4 py-2 rounded-xl transition-all shadow-sm shadow-rose-600/30">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Revisi & Ajukan Kembali SOP Ini</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Super Admin Examination Action Bar (When Pending) -->
    @if(auth()->user()->isSuperAdmin() && $sop->isPending())
        <div class="mb-8 p-6 rounded-3xl bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 shadow-md">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0 shadow-md shadow-amber-500/30 animate-pulse">
                        <i class="fa-solid fa-stamp"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Pemeriksaan Pengajuan SOP</h2>
                        <p class="text-xs text-slate-600 mt-0.5">Periksa seluruh isi SOP, klausul, flowchart, dan lampiran di bawah ini sebelum memberikan keputusan.</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Tombol TOLAK -->
                    <button type="button" onclick="openRejectModal()" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all-200 shadow-md shadow-rose-600/20 flex items-center space-x-2">
                        <i class="fa-solid fa-ban"></i>
                        <span>TOLAK SOP</span>
                    </button>

                    <!-- Tombol SETUJUI -->
                    <button type="button" onclick="confirmApproval()" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold transition-all-200 shadow-md shadow-emerald-600/30 flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>SETUJUI & PUBLIKASIKAN</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden Approval Form -->
        <form id="approvalForm" method="POST" action="{{ route('sops.approve', $sop->id) }}" style="display:none;">
            @csrf
        </form>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Main SOP Clauses & Flowchart -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- 1. TUJUAN -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex items-center space-x-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">1</div>
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">TUJUAN</h2>
                </div>
                <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                    {{ $sop->tujuan }}
                </div>
            </div>

            <!-- 2. RUANG LINGKUP -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex items-center space-x-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">2</div>
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">RUANG LINGKUP</h2>
                </div>
                <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                    {{ $sop->ruang_lingkup }}
                </div>
            </div>

            <!-- 3. DEFINISI -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex items-center space-x-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">3</div>
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">DEFINISI</h2>
                </div>
                <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                    {{ $sop->definisi }}
                </div>
            </div>

            <!-- 4. CATATAN REVISI -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex items-center space-x-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">4</div>
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">CATATAN REVISI</h2>
                </div>
                
                <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                                <th class="py-2.5 px-4 w-32">Nomor Revisi</th>
                                <th class="py-2.5 px-4 w-40">Tanggal Revisi</th>
                                <th class="py-2.5 px-4">Keterangan Perubahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($sop->revisionNotes as $note)
                                <tr>
                                    <td class="py-2.5 px-4 font-mono font-bold text-blue-600">{{ $note->nomor_revisi }}</td>
                                    <td class="py-2.5 px-4">{{ $note->tanggal_revisi ? $note->tanggal_revisi->format('d M Y') : '-' }}</td>
                                    <td class="py-2.5 px-4">{{ $note->keterangan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-3 px-4 text-center text-slate-400">Tidak ada catatan revisi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. ATURAN UMUM -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex items-center space-x-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">5</div>
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">ATURAN UMUM</h2>
                </div>
                <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                    {{ $sop->aturan_umum }}
                </div>
            </div>

            <!-- 6. FLOWCHART SECTION -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold">6</div>
                        <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">DIAGRAM ALIR (FLOWCHART)</h2>
                    </div>

                    @if($sop->flowchart)
                        <div class="flex items-center space-x-2">
                            <a href="{{ $sop->flowchart->url }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold transition-all-200">
                                <i class="fa-solid fa-download"></i>
                                <span>Unduh</span>
                            </a>
                            <a href="{{ $sop->flowchart->preview_url }}" target="_blank" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all-200">
                                <i class="fa-solid fa-up-right-from-square"></i>
                                <span>Buka Penuh</span>
                            </a>
                        </div>
                    @endif
                </div>

                @if($sop->flowchart)
                    @if($sop->flowchart->isImage())
                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-center">
                            <img src="{{ $sop->flowchart->preview_url }}" alt="Flowchart {{ $sop->nama_sop }}" class="max-h-[600px] w-auto mx-auto object-contain rounded-xl shadow-sm">
                        </div>
                    @else
                        <div class="p-8 text-center bg-purple-50/50 rounded-2xl border border-purple-200">
                            <i class="fa-solid fa-file-pdf text-6xl text-rose-500 mb-3"></i>
                            <h3 class="text-sm font-bold text-slate-800">{{ $sop->flowchart->nama_file }}</h3>
                            <p class="text-xs text-slate-500 mt-1">Berkas format PDF &bull; {{ $sop->flowchart->formatted_size }}</p>
                            <div class="mt-4 flex items-center justify-center space-x-3">
                                <a href="{{ $sop->flowchart->preview_url }}" target="_blank" class="inline-flex items-center space-x-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow-sm">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Tinjau PDF Flowchart</span>
                                </a>
                                <a href="{{ $sop->flowchart->url }}" class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-xl">
                                    <i class="fa-solid fa-download"></i>
                                    <span>Unduh File</span>
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                        <i class="fa-solid fa-diagram-project text-4xl text-slate-300 mb-2"></i>
                        <p class="text-xs text-slate-500 font-medium">Belum ada diagram alir (Flowchart) yang diunggah untuk dokumen ini.</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 1 Col: Attachments, Version History, & Rejection History -->
        <div class="space-y-8">
            
            <!-- LAMPIRAN BERKAS -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-2.5">
                        <i class="fa-solid fa-paperclip text-amber-500 text-base"></i>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">LAMPIRAN DOKUMEN</h2>
                    </div>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                        {{ $sop->regularAttachments->count() }} Berkas
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($sop->regularAttachments as $att)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100/80 border border-slate-200 text-xs transition-colors group">
                            <div class="flex items-center space-x-3 min-w-0 pr-2">
                                <i class="fa-solid {{ $att->getFileIconClass() }} text-lg shrink-0"></i>
                                <div class="truncate">
                                    <p class="font-bold text-slate-800 truncate">{{ $att->nama_file }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $att->formatted_size }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1 shrink-0">
                                <a href="{{ $att->preview_url }}" target="_blank" title="Tinjau Berkas" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ $att->url }}" title="Unduh Berkas" class="p-2 text-slate-400 hover:text-emerald-600 transition-colors">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            <i class="fa-regular fa-folder-open text-2xl mb-1.5 block"></i>
                            <span>Tidak ada berkas lampiran tambahan</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- RIWAYAT PENOLAKAN (IF ANY) -->
            @if($sop->rejectionReasons->count() > 0)
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                    <div class="flex items-center space-x-2.5 pb-4 mb-4 border-b border-slate-100">
                        <i class="fa-solid fa-clock-rotate-left text-rose-500 text-base"></i>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">RIWAYAT PENOLAKAN</h2>
                    </div>

                    <div class="space-y-3">
                        @foreach($sop->rejectionReasons as $rej)
                            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200 text-xs">
                                <div class="flex items-center justify-between text-[11px] text-rose-700 font-semibold mb-1">
                                    <span>Reviewer: <strong>{{ $rej->reviewer?->name ?? 'Super Admin' }}</strong></span>
                                    <span>{{ $rej->created_at ? $rej->created_at->format('d/m/Y H:i') : '' }}</span>
                                </div>
                                <p class="text-rose-950 italic leading-relaxed">
                                    "{{ $rej->alasan_penolakan }}"
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- RIWAYAT VERSI DOKUMEN -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center space-x-2.5 pb-4 mb-4 border-b border-slate-100">
                    <i class="fa-solid fa-timeline text-blue-500 text-base"></i>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">RIWAYAT VERSI SNAPSHOT</h2>
                </div>

                <div class="relative pl-6 space-y-4 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($sop->versions as $v)
                        <div class="relative">
                            <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full bg-blue-600 border-2 border-white shadow-sm"></div>
                            <div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-900">Versi {{ $v->version_number }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $v->created_at ? $v->created_at->format('d M Y, H:i') : '-' }}</span>
                                </div>
                                <p class="text-[11px] text-slate-600 mt-0.5">{{ $v->change_description ?? 'Pembaruan data' }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Oleh: {{ $v->user?->name ?? 'Sistem' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada riwayat versi tercatat</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- Reject Modal for Super Admin -->
    @if(auth()->user()->isSuperAdmin() && $sop->isPending())
        <div id="rejectModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in-95 duration-200">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Tolak Pengajuan SOP</h2>
                        <p class="text-xs text-slate-500">Berikan catatan alasan penolakan secara spesifik dan jelas.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('sops.reject', $sop->id) }}" id="rejectForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="alasan_penolakan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Alasan Penolakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alasan_penolakan" 
                                  name="alasan_penolakan" 
                                  rows="5" 
                                  required 
                                  placeholder="Contoh: Bagian ruang lingkup belum menjelaskan batasan proses kerja secara lengkap. Flowchart juga belum sesuai dengan prosedur yang dijelaskan. Silakan revisi kembali."
                                  class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl p-3.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:bg-white"></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Alasan ini wajib diisi dan akan dikirimkan sebagai notifikasi revisi ke Admin.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/30">
                            Konfirmasi Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openRejectModal() {
                document.getElementById('rejectModal').classList.remove('hidden');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
            }

            function confirmApproval() {
                Swal.fire({
                    title: 'Setujui & Publikasikan SOP?',
                    text: 'SOP "{{ $sop->nama_sop }}" akan resmi disetujui, dipublikasikan, dan dapat diakses oleh seluruh pengguna SIMA HRD.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Setujui Sekarang',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('approvalForm').submit();
                    }
                });
            }
        </script>
    @endif

</x-app-layout>

