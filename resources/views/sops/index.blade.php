<x-app-layout>
    <x-slot name="headerTitle">Manajemen SOP & Persetujuan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('documents.index', 'sop') }}" class="hover:underline">Direktori SOP</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Workflow Pengajuan SOP</span>
    </x-slot>

    <!-- Top Hero Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-blue-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 opacity-10 flex items-center pr-10 pointer-events-none">
            <i class="fa-solid fa-file-signature text-9xl"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center space-x-2 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs px-3.5 py-1.5 rounded-full font-semibold mb-3 backdrop-blur-md">
                    <i class="fa-solid fa-clipboard-check text-emerald-400"></i>
                    <span>SOP Approval Workflow &bull; SIMA HRD</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-2">
                    Sistem Pengajuan & Persetujuan SOP
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Kelola seluruh siklus hidup Standard Operating Procedure (SOP) secara digital: pembuatan draft, pengajuan antar-departemen, pemeriksaan berjenjang, revisi, hingga penerbitan resmi.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('documents.index', 'sop') }}" class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl backdrop-blur-md transition-all-200 border border-white/10">
                    <i class="fa-solid fa-folder-tree text-amber-400"></i>
                    <span>Direktori Folder BOD</span>
                </a>

                @if(auth()->user()->canManageDocuments())
                <a href="{{ route('sops.create') }}" class="inline-flex items-center space-x-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-500/30 transition-all-200">
                    <i class="fa-solid fa-plus"></i>
                    <span>Buat / Ajukan SOP</span>
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
                    <h3 class="font-bold text-sm text-rose-900">Perhatian: Ada {{ $counts['ditolak'] }} Pengajuan SOP yang Memerlukan Revisi</h3>
                    <p class="text-xs text-rose-700 mt-0.5">Super Admin telah memberikan catatan perbaikan pada SOP Anda. Silakan periksa catatan dan ajukan kembali.</p>
                </div>
            </div>
            <a href="{{ route('sops.index', ['tab' => 'ditolak']) }}" class="inline-flex items-center space-x-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2 rounded-xl shrink-0 transition-all-200 shadow-sm shadow-rose-600/30">
                <span>Lihat SOP Ditolak</span>
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
                    <h3 class="font-bold text-sm text-amber-900">Ada {{ $counts['pengajuan'] }} Pengajuan SOP Menunggu Pemeriksaan Anda</h3>
                    <p class="text-xs text-amber-700 mt-0.5">SOP baru dari Admin siap untuk diperiksa, diverifikasi flowchart & lampirannya, serta diputuskan.</p>
                </div>
            </div>
            <a href="{{ route('sops.index', ['tab' => 'pengajuan']) }}" class="inline-flex items-center space-x-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2 rounded-xl shrink-0 transition-all-200 shadow-sm shadow-amber-600/30">
                <span>Periksa Pengajuan ({{ $counts['pengajuan'] }})</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    @endif

    <!-- Tabs & Filter Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm mb-6 p-4 sm:p-5">
        
        <!-- Navigation Tabs -->
        <div class="flex items-center space-x-2 border-b border-slate-200 pb-4 overflow-x-auto">
            
            <!-- Tab: SOP Resmi -->
            <a href="{{ route('sops.index', array_merge(request()->except('page'), ['tab' => 'resmi'])) }}"
               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center space-x-2 shrink-0 transition-all-200 {{ $tab === 'resmi' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <i class="fa-solid fa-certificate"></i>
                <span>SOP Resmi</span>
                <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full {{ $tab === 'resmi' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 font-semibold' }}">
                    {{ $counts['resmi'] }}
                </span>
            </a>

            @if(auth()->user()->canManageDocuments())
                <!-- Tab: Pengajuan SOP -->
                <a href="{{ route('sops.index', array_merge(request()->except('page'), ['tab' => 'pengajuan'])) }}"
                   class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center space-x-2 shrink-0 transition-all-200 {{ $tab === 'pengajuan' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i class="fa-solid fa-inbox"></i>
                    <span>{{ auth()->user()->isSuperAdmin() ? 'Pengajuan SOP' : 'Pengajuan Saya' }}</span>
                    @if(($counts['pengajuan'] ?? 0) > 0)
                        <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full bg-amber-600 text-white font-bold animate-pulse">
                            {{ $counts['pengajuan'] }}
                        </span>
                    @else
                        <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full {{ $tab === 'pengajuan' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                            0
                        </span>
                    @endif
                </a>

                <!-- Tab: Draft -->
                <a href="{{ route('sops.index', array_merge(request()->except('page'), ['tab' => 'draft'])) }}"
                   class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center space-x-2 shrink-0 transition-all-200 {{ $tab === 'draft' ? 'bg-slate-800 text-white shadow-md shadow-slate-800/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i class="fa-solid fa-pen-ruler"></i>
                    <span>Draft</span>
                    <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full {{ $tab === 'draft' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $counts['draft'] }}
                    </span>
                </a>

                <!-- Tab: Ditolak -->
                <a href="{{ route('sops.index', array_merge(request()->except('page'), ['tab' => 'ditolak'])) }}"
                   class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center space-x-2 shrink-0 transition-all-200 {{ $tab === 'ditolak' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>Ditolak</span>
                    @if(($counts['ditolak'] ?? 0) > 0)
                        <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full bg-rose-700 text-white font-bold">
                            {{ $counts['ditolak'] }}
                        </span>
                    @else
                        <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full {{ $tab === 'ditolak' ? 'bg-rose-700 text-white' : 'bg-slate-100 text-slate-600' }}">
                            0
                        </span>
                    @endif
                </a>
            @endif

        </div>

        <!-- Filter & Search Controls -->
        <form method="GET" action="{{ route('sops.index') }}" class="mt-4 flex flex-col md:flex-row items-center justify-between gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="flex-1 w-full flex flex-col sm:flex-row items-center gap-3">
                <!-- Search Keyword -->
                <div class="relative w-full sm:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama SOP / no. dokumen..."
                           class="w-full bg-slate-50 text-slate-800 placeholder-slate-400 text-xs rounded-xl pl-9 pr-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                </div>

                <!-- Department Filter -->
                <div class="w-full sm:w-60">
                    <select name="department_id" onchange="this.form.submit()" class="w-full bg-slate-50 text-slate-700 text-xs rounded-xl px-3 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->bod?->name ?? 'BOD' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit / Reset -->
                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition-all-200">
                        Filter
                    </button>
                    @if(request('search') || request('department_id'))
                        <a href="{{ route('sops.index', ['tab' => $tab]) }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition-all-200" title="Reset filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="text-xs text-slate-500 self-end sm:self-center font-medium">
                Menampilkan <span class="font-bold text-slate-800">{{ $sops->total() }}</span> SOP
            </div>
        </form>

    </div>

    <!-- Main Content Table / Grid -->
    @if($tab === 'pengajuan')
        <!-- Section: Tabel Pengajuan SOP -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            {{ auth()->user()->isSuperAdmin() ? 'Antrean Pengajuan SOP Masuk' : 'Pengajuan SOP Saya' }}
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">SOP berstatus Menunggu Persetujuan dari Super Admin</p>
                    </div>
                </div>
            </div>

            @if($sops->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4">Nama SOP</th>
                                <th class="py-3.5 px-4">No. Dokumen</th>
                                <th class="py-3.5 px-4">Departemen</th>
                                <th class="py-3.5 px-4">Pengaju</th>
                                <th class="py-3.5 px-4">Tanggal Pengajuan</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($sops as $index => $sop)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-semibold">
                                        {{ $sops->firstItem() + $index }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <a href="{{ route('sops.show', $sop->id) }}" class="hover:text-blue-600 transition-colors">
                                            {{ $sop->nama_sop }}
                                        </a>
                                        <div class="text-[11px] text-slate-400 font-normal">v{{ $sop->version }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-semibold text-slate-700">
                                        {{ $sop->nomor_dokumen }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $sop->department?->name ?? '-' }}
                                        </span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $sop->department?->bod?->name ?? '' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-800">{{ $sop->creator?->name ?? 'Admin' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $sop->creator?->email }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        <div>{{ $sop->submitted_at ? $sop->submitted_at->format('d M Y, H:i') : $sop->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $sop->submitted_at ? $sop->submitted_at->diffForHumans() : '' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[11px] font-bold {{ $sop->getStatusBadgeClass() }}">
                                            <i class="fa-solid {{ $sop->getStatusIcon() }} text-[10px]"></i>
                                            <span>{{ $sop->getStatusLabel() }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('sops.show', $sop->id) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all-200 shadow-sm shadow-blue-500/20">
                                            <i class="fa-solid fa-eye"></i>
                                            <span>{{ auth()->user()->isSuperAdmin() ? 'Periksa SOP' : 'Lihat Detail' }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-4">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Tidak Ada Antrean Pengajuan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada pengajuan SOP yang berstatus Menunggu Persetujuan.</p>
                </div>
            @endif
        </div>

    @elseif($tab === 'ditolak')
        <!-- Section: SOP Ditolak (Perlu Revisi) -->
        <div class="space-y-4 mb-6">
            @forelse($sops as $sop)
                <div class="bg-white rounded-2xl border border-rose-200/80 p-6 shadow-sm hover:shadow-md transition-all-200 relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-rose-500"></div>

                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200 flex items-center space-x-1">
                                    <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                    <span>Status: Ditolak</span>
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                    v{{ $sop->version }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700">
                                    {{ $sop->department?->name ?? 'Departemen' }}
                                </span>
                            </div>

                            <h2 class="text-lg font-bold text-slate-900 mb-1">
                                <a href="{{ route('sops.show', $sop->id) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $sop->nama_sop }}
                                </a>
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mb-4">No. Dokumen: {{ $sop->nomor_dokumen }}</p>

                            <!-- Catatan Penolakan Box -->
                            <div class="bg-rose-50/70 border border-rose-200 rounded-xl p-4 text-xs">
                                <div class="flex items-center space-x-2 text-rose-800 font-bold mb-1.5">
                                    <i class="fa-solid fa-comment-dots text-rose-600"></i>
                                    <span>Catatan Penolakan / Permintaan Revisi:</span>
                                </div>
                                <p class="text-rose-900 leading-relaxed italic">
                                    "{{ $sop->latestRejectionReason?->alasan_penolakan ?? 'Harap lakukan peninjauan kembali pada isi dokumen.' }}"
                                </p>
                                <div class="mt-2 pt-2 border-t border-rose-200/60 flex items-center justify-between text-[11px] text-rose-700 font-medium">
                                    <span>Diperiksa oleh: <strong class="text-rose-900">{{ $sop->latestRejectionReason?->reviewer?->name ?? 'Super Admin' }}</strong></span>
                                    <span>Tanggal: {{ $sop->rejected_at ? $sop->rejected_at->format('d M Y, H:i') : '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex lg:flex-col items-center justify-end gap-2.5 shrink-0">
                            <a href="{{ route('sops.show', $sop->id) }}" class="flex-1 lg:w-44 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl text-center transition-all-200 flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-eye"></i>
                                <span>Lihat Detail</span>
                            </a>

                            @if($sop->canBeEditedBy(auth()->user()))
                                <a href="{{ route('sops.edit', $sop->id) }}" class="flex-1 lg:w-44 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-900 text-xs font-bold rounded-xl text-center transition-all-200 shadow-sm shadow-amber-500/20 flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Revisi SOP</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-4">
                        <i class="fa-solid fa-check-double text-emerald-500"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Tidak Ada SOP Ditolak</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Semua pengajuan SOP berjalan lancar tanpa adanya penolakan.</p>
                </div>
            @endforelse
        </div>

    @else
        <!-- Section: SOP Grid Cards (Untuk Resmi & Draft) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @forelse($sops as $sop)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all-200 flex flex-col justify-between p-6 group">
                    <div>
                        <!-- Header badge & status -->
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[11px] font-bold {{ $sop->getStatusBadgeClass() }}">
                                <i class="fa-solid {{ $sop->getStatusIcon() }} text-[10px]"></i>
                                <span>{{ $sop->getStatusLabel() }}</span>
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-md">
                                v{{ $sop->version }}
                            </span>
                        </div>

                        <!-- Title & Doc No -->
                        <h3 class="font-bold text-slate-900 text-base group-hover:text-blue-600 transition-colors line-clamp-2">
                            <a href="{{ route('sops.show', $sop->id) }}">
                                {{ $sop->nama_sop }}
                            </a>
                        </h3>
                        <p class="text-xs font-mono text-slate-500 mt-1 mb-3">{{ $sop->nomor_dokumen }}</p>

                        <!-- Purpose snippet -->
                        <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed">
                            {{ Str::limit(strip_tags($sop->tujuan), 120) }}
                        </p>

                        <!-- Metadata Badges -->
                        <div class="space-y-1.5 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                            <div class="flex items-center justify-between">
                                <span class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-layer-group text-slate-400"></i>
                                    <span>Departemen:</span>
                                </span>
                                <span class="font-semibold text-slate-700">{{ $sop->department?->name ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-calendar-day text-slate-400"></i>
                                    <span>Tgl. Efektif:</span>
                                </span>
                                <span class="font-semibold text-slate-700">{{ $sop->tanggal_efektif ? $sop->tanggal_efektif->format('d M Y') : '-' }}</span>
                            </div>
                            @if($sop->isApproved())
                            <div class="flex items-center justify-between">
                                <span class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-user-check text-emerald-500"></i>
                                    <span>Disetujui Oleh:</span>
                                </span>
                                <span class="font-semibold text-emerald-700">{{ $sop->approver?->name ?? 'Super Admin' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('sops.show', $sop->id) }}" class="flex-1 py-2 px-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold text-center transition-all-200 flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-eye text-[11px]"></i>
                            <span>Buka SOP</span>
                        </a>

                        <a href="{{ route('sops.print', $sop->id) }}" target="_blank" title="Download PDF" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all-200">
                            <i class="fa-solid fa-file-arrow-down"></i>
                        </a>

                        @if($sop->canBeEditedBy(auth()->user()))
                            <a href="{{ route('sops.edit', $sop->id) }}" title="Edit SOP" class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 transition-all-200">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif

                        @if($sop->canBeDeletedBy(auth()->user()))
                            <form method="POST" action="{{ route('sops.destroy', $sop->id) }}" onsubmit="return confirmDelete(event, 'Dokumen SOP ini akan dihapus permanen beserta seluruh file lampirannya!', 'Hapus SOP?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus SOP" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition-all-200">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-slate-200 p-12 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-4">
                        <i class="fa-solid fa-file-circle-xmark"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Dokumen SOP</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        @if($tab === 'draft')
                            Anda belum memiliki draf dokumen SOP yang disimpan.
                        @else
                            Belum ada SOP resmi yang dipublikasikan pada kategori/filter ini.
                        @endif
                    </p>
                    @if(auth()->user()->canManageDocuments())
                        <div class="mt-5">
                            <a href="{{ route('sops.create') }}" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md transition-all-200">
                                <i class="fa-solid fa-plus"></i>
                                <span>Buat SOP Baru Sekarang</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    @endif

    <!-- Pagination -->
    @if($sops->hasPages())
        <div class="mt-6">
            {{ $sops->links() }}
        </div>
    @endif

</x-app-layout>

