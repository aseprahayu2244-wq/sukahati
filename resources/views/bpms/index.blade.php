<x-app-layout>
    <x-slot name="headerTitle">Manajemen BPM & Persetujuan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('documents.index', 'bpm') }}" class="hover:underline">Direktori BPM</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Workflow & BPM Builder</span>
    </x-slot>

    <!-- Top Hero Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 opacity-10 flex items-center pr-10 pointer-events-none">
            <i class="fa-solid fa-network-wired text-9xl"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center space-x-2 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs px-3.5 py-1.5 rounded-full font-semibold mb-3 backdrop-blur-md">
                    <i class="fa-solid fa-diagram-project text-indigo-400"></i>
                    <span>BPM Builder & Workflow &bull; SIMA HRD</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-2">
                    Sistem Pengelolaan Business Process Management (BPM)
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Susun proses bisnis terstandarisasi dengan hierarki Level 1 (Proses Utama), Level 2 (Subproses), dan Level 3 (Aktivitas) serta matriks tanggung jawab RACI.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('documents.index', 'bpm') }}" class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl backdrop-blur-md transition-all-200 border border-white/10">
                    <i class="fa-solid fa-folder-tree text-amber-400"></i>
                    <span>Direktori Folder BOD</span>
                </a>

                @if(auth()->user()->canManageDocuments())
                <a href="{{ route('bpms.create', ['mode' => 'upload']) }}" class="inline-flex items-center space-x-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-rose-600/30 transition-all-200">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Upload BPM</span>
                </a>
                <a href="{{ route('bpms.create', ['mode' => 'builder']) }}" class="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-600/30 transition-all-200">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>BPM Builder</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Rejection Alert Reminder for Admin -->
    @if(auth()->user()->isAdmin() && ($counts['ditolak'] ?? 0) > 0 && $tab !== 'ditolak')
        <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-rose-900">Perhatian: Ada {{ $counts['ditolak'] }} Pengajuan BPM yang Memerlukan Revisi</h3>
                    <p class="text-xs text-rose-700 mt-0.5">Super Admin telah memberikan catatan perbaikan pada BPM Anda. Silakan periksa catatan dan ajukan kembali.</p>
                </div>
            </div>
            <a href="{{ route('bpms.index', ['tab' => 'ditolak']) }}" class="inline-flex items-center space-x-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2 rounded-xl shrink-0 transition-all-200 shadow-sm shadow-rose-600/30">
                <span>Lihat BPM Ditolak</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    @endif

    <!-- Pending Reminder for Super Admin -->
    @if(auth()->user()->isSuperAdmin() && ($counts['pengajuan'] ?? 0) > 0 && $tab !== 'pengajuan')
        <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5 animate-pulse">
                    <i class="fa-solid fa-clock text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-amber-900">Ada {{ $counts['pengajuan'] }} Pengajuan BPM Menunggu Pemeriksaan Anda</h3>
                    <p class="text-xs text-amber-700 mt-0.5">BPM baru dari Admin siap untuk diperiksa hierarki L1-L3 dan matriks RACI-nya.</p>
                </div>
            </div>
            <a href="{{ route('bpms.index', ['tab' => 'pengajuan']) }}" class="inline-flex items-center space-x-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2 rounded-xl shrink-0 transition-all-200 shadow-sm shadow-amber-600/30">
                <span>Periksa Pengajuan ({{ $counts['pengajuan'] }})</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    @endif

    <!-- Tabs & Filter Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm mb-6 p-4 sm:p-5">
        
        <!-- Navigation Tabs -->
        <div class="flex items-center space-x-2 border-b border-slate-200 pb-4 overflow-x-auto">
            
            <!-- Tab: BPM Resmi -->
            <a href="{{ route('bpms.index', array_merge(request()->except('page'), ['tab' => 'resmi'])) }}"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center space-x-2 shrink-0 transition-all-200 {{ $tab === 'resmi' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="fa-solid fa-certificate"></i>
                <span>BPM Resmi</span>
                <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full {{ $tab === 'resmi' ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600 font-semibold' }}">
                    {{ $counts['resmi'] }}
                </span>
            </a>

            @if(auth()->user()->canManageDocuments())
                <!-- Tab: Pengajuan BPM -->
                <a href="{{ route('bpms.index', array_merge(request()->except('page'), ['tab' => 'pengajuan'])) }}"
                   class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center space-x-2 shrink-0 transition-all-200 {{ $tab === 'pengajuan' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <span>Pengajuan BPM</span>
                    @if($counts['pengajuan'] > 0)
                        <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full {{ $tab === 'pengajuan' ? 'bg-amber-600 text-white font-extrabold' : 'bg-amber-100 text-amber-800 font-bold' }}">
                            {{ $counts['pengajuan'] }}
                        </span>
                    @endif
                </a>

                <!-- Tab: Draft -->
                <a href="{{ route('bpms.index', array_merge(request()->except('page'), ['tab' => 'draft'])) }}"
                   class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center space-x-2 shrink-0 transition-all-200 {{ $tab === 'draft' ? 'bg-slate-700 text-white shadow-md shadow-slate-700/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Draft</span>
                    <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full {{ $tab === 'draft' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 font-semibold' }}">
                        {{ $counts['draft'] }}
                    </span>
                </a>

                <!-- Tab: Ditolak -->
                <a href="{{ route('bpms.index', array_merge(request()->except('page'), ['tab' => 'ditolak'])) }}"
                   class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center space-x-2 shrink-0 transition-all-200 {{ $tab === 'ditolak' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>Ditolak / Revisi</span>
                    @if($counts['ditolak'] > 0)
                        <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full {{ $tab === 'ditolak' ? 'bg-rose-700 text-white font-extrabold' : 'bg-rose-100 text-rose-800 font-bold' }}">
                            {{ $counts['ditolak'] }}
                        </span>
                    @endif
                </a>
            @endif
        </div>

        <!-- Filter & Search Form -->
        <form method="GET" action="{{ route('bpms.index') }}" class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <!-- Search input -->
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama BPM, nomor dokumen..." 
                       class="w-full bg-slate-50 text-slate-800 placeholder-slate-400 text-xs rounded-xl pl-9 pr-3 py-2 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
            </div>

            <!-- Department Filter -->
            <div>
                <select name="department_id" onchange="this.form.submit()" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-3 py-2 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} {{ $dept->bod ? '('.$dept->bod->name.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Source Type Filter -->
            <div>
                <select name="source_type" onchange="this.form.submit()" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-3 py-2 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Metode (Builder & Upload)</option>
                    <option value="builder" {{ request('source_type') == 'builder' ? 'selected' : '' }}>BPM Builder</option>
                    <option value="upload" {{ request('source_type') == 'upload' ? 'selected' : '' }}>Upload Dokumen</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold transition-all-200 flex-1">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'department_id', 'source_type', 'bod_id']))
                    <a href="{{ route('bpms.index', ['tab' => $tab]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition-all-200" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- BPM Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($bpms as $bpm)
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-all-200 flex flex-col justify-between group">
                <div>
                    <!-- Badges -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="inline-flex items-center space-x-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full border {{ $bpm->getStatusBadgeClass() }}">
                            <i class="fa-solid {{ $bpm->getStatusIcon() }} text-[10px]"></i>
                            <span>{{ $bpm->getStatusLabel() }}</span>
                        </span>

                        <div class="flex items-center space-x-1.5">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                <i class="fa-solid {{ $bpm->isBuilder() ? 'fa-wand-magic-sparkles text-indigo-500' : 'fa-cloud-arrow-up text-rose-500' }} mr-0.5"></i>
                                {{ $bpm->isBuilder() ? 'Builder' : 'Upload' }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400 font-bold">v{{ $bpm->version }}</span>
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="font-extrabold text-slate-900 text-base group-hover:text-indigo-600 transition-colors line-clamp-2 mb-1">
                        <a href="{{ route('bpms.show', $bpm->id) }}">{{ $bpm->nama_bpm }}</a>
                    </h3>

                    <!-- Document Number & Department -->
                    <p class="text-[11px] font-mono font-bold text-slate-500 mb-2">{{ $bpm->nomor_dokumen }}</p>
                    
                    <div class="flex items-center space-x-1.5 text-xs text-slate-600 mb-3">
                        <i class="fa-solid fa-building text-slate-400 text-[11px]"></i>
                        <span class="font-semibold">{{ $bpm->department?->name ?? '-' }}</span>
                        @if($bpm->bod)
                            <span class="text-slate-400">&bull;</span>
                            <span class="text-slate-500">{{ $bpm->bod->name }}</span>
                        @endif
                    </div>

                    @if($bpm->deskripsi)
                        <p class="text-xs text-slate-600 line-clamp-2 mb-3">{{ Str::limit(strip_tags($bpm->deskripsi), 90) }}</p>
                    @endif

                    @if($bpm->isRejected() && $bpm->latestRejection)
                        <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-[11px] mb-3">
                            <p class="font-bold flex items-center space-x-1 mb-0.5 text-rose-800">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>Alasan Penolakan:</span>
                            </p>
                            <p class="italic line-clamp-2">"{{ $bpm->latestRejection->rejection_reason }}"</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Meta & Actions -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400">
                        {{ $bpm->tanggal_efektif ? $bpm->tanggal_efektif->format('d/m/Y') : '-' }}
                    </span>

                    <div class="flex items-center space-x-2">
                        @if($bpm->isApproved())
                            <a href="{{ route('bpms.print', $bpm->id) }}" target="_blank" title="Cetak / Download PDF" class="p-1.5 text-slate-400 hover:text-slate-700 transition-colors">
                                <i class="fa-solid fa-file-arrow-down"></i>
                            </a>
                        @endif

                        @if($bpm->canBeEditedBy(auth()->user()))
                            <a href="{{ route('bpms.edit', $bpm->id) }}" title="{{ $bpm->isRejected() ? 'Revisi BPM' : 'Edit BPM' }}" class="p-1.5 text-amber-600 hover:text-amber-800 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif

                        <a href="{{ route('bpms.show', $bpm->id) }}" class="font-bold text-indigo-600 hover:text-indigo-800 flex items-center space-x-1">
                            <span>Buka BPM</span>
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl border border-slate-200 p-12 text-center">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-4">
                    <i class="fa-solid fa-diagram-project"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Dokumen BPM</h3>
                <p class="text-xs text-slate-500 mt-1">Belum ada dokumen BPM dalam kategori tab ini.</p>
                @if(auth()->user()->canManageDocuments())
                    <div class="mt-5 flex items-center justify-center space-x-3">
                        <a href="{{ route('bpms.create', ['mode' => 'builder']) }}" class="inline-flex items-center space-x-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all-200">
                            <i class="fa-solid fa-plus"></i>
                            <span>Buat via BPM Builder</span>
                        </a>
                        <a href="{{ route('bpms.create', ['mode' => 'upload']) }}" class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all-200">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Upload Berkas BPM</span>
                        </a>
                    </div>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $bpms->links() }}
    </div>
</x-app-layout>

