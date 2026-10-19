<x-app-layout>
    <x-slot name="headerTitle">Dashboard</x-slot>
    <x-slot name="breadcrumb">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-medium">Dashboard</span>
    </x-slot>

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 text-white rounded-3xl p-8 shadow-xl mb-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 opacity-10 flex items-center pr-12 pointer-events-none">
            <i class="fa-solid fa-folder-tree text-9xl"></i>
        </div>
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center space-x-2 bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs px-3.5 py-1.5 rounded-full font-semibold mb-4 backdrop-blur-md">
                <i class="fa-solid fa-shield-halved text-amber-400"></i>
                <span>Role: {{ auth()->user()->getRoleLabel() }} &bull; {{ auth()->user()->isSuperAdmin() ? 'Akses Penuh Sistem' : (auth()->user()->isAdmin() ? 'Pengelola Dokumen & Departemen' : 'Akses Dokumen (View & Download)') }}</span>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-slate-300 text-sm leading-relaxed">
                Sistem Informasi Manajemen Dokumen HRD memudahkan pengelolaan dokumen SOP, Job Desk, BPM, dan Struktur Organisasi dengan terstruktur berbasis hirarki BOD, Departemen, dan Jabatan.
            </p>
            @if(auth()->user()->canManageDocuments())
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a href="{{ route('documents.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all-200">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Dokumen Baru</span>
                    </a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.bods.index') }}" class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl backdrop-blur-md transition-all-200">
                            <i class="fa-solid fa-building-user"></i>
                            <span>Kelola BOD</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl backdrop-blur-md transition-all-200">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- Stats 1: BOD -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between transition-all-200 hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total BOD</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalBods ?? 0 }}</h3>
                <p class="text-xs text-slate-500 mt-1">Board of Directors</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-building-user"></i>
            </div>
        </div>

        <!-- Stats 2: Departemen -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between transition-all-200 hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Departemen</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalDepartments ?? 0 }}</h3>
                <p class="text-xs text-slate-500 mt-1">Departemen aktif</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>

        <!-- Stats 3: Jabatan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between transition-all-200 hover:shadow-md">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Jabatan</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalPositions ?? 0 }}</h3>
                <p class="text-xs text-slate-500 mt-1">Jabatan dalam organisasi</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-id-badge"></i>
            </div>
        </div>

        <!-- Stats 4: Dokumen / User -->
        @if(auth()->user()->isSuperAdmin() && isset($totalUsers))
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between transition-all-200 hover:shadow-md">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengguna</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalUsers }}</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-purple-600 hover:underline font-semibold mt-1 inline-block">Kelola Akun &rarr;</a>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between transition-all-200 hover:shadow-md">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Dokumen</p>
                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalDocuments ?? 0 }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Dokumen tersimpan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
            </div>
        @endif

    </div>

    <!-- Document Category Cards -->
    <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center space-x-2">
        <i class="fa-solid fa-cubes text-blue-600"></i>
        <span>Menu Utama Dokumen</span>
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- SOP Card -->
        <a href="{{ route('documents.index', 'sop') }}" class="folder-card bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm transition-all-200 group relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-500/20 group-hover:scale-110 transition-all-200">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
                <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200">
                    {{ $docStats['sop'] ?? 0 }} File
                </span>
            </div>
            <h4 class="font-bold text-slate-900 text-base group-hover:text-emerald-600 transition-all-200">SOP</h4>
            <p class="text-xs text-slate-500 mt-1">Standard Operating Procedure</p>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-emerald-600">
                <span>Buka Folder SOP</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-all-200"></i>
            </div>
        </a>

        <!-- Job Desk Card -->
        <a href="{{ route('documents.index', 'job_desk') }}" class="folder-card bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm transition-all-200 group relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shadow-md shadow-amber-500/20 group-hover:scale-110 transition-all-200">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
                <span class="bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200">
                    {{ $docStats['job_desk'] ?? 0 }} File
                </span>
            </div>
            <h4 class="font-bold text-slate-900 text-base group-hover:text-amber-600 transition-all-200">Job Desk</h4>
            <p class="text-xs text-slate-500 mt-1">Uraian tugas per jabatan</p>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-amber-600">
                <span>Buka Folder Job Desk</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-all-200"></i>
            </div>
        </a>

        <!-- BPM Card -->
        <a href="{{ route('documents.index', 'bpm') }}" class="folder-card bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm transition-all-200 group relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-500 text-white flex items-center justify-center text-xl shadow-md shadow-purple-500/20 group-hover:scale-110 transition-all-200">
                    <i class="fa-solid fa-diagram-project"></i>
                </div>
                <span class="bg-purple-50 text-purple-700 text-xs font-bold px-3 py-1 rounded-full border border-purple-200">
                    {{ $docStats['bpm'] ?? 0 }} File
                </span>
            </div>
            <h4 class="font-bold text-slate-900 text-base group-hover:text-purple-600 transition-all-200">BPM</h4>
            <p class="text-xs text-slate-500 mt-1">Business Process Management</p>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-purple-600">
                <span>Buka Folder BPM</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-all-200"></i>
            </div>
        </a>

        <!-- Struktur Organisasi Card -->
        <a href="{{ route('documents.index', 'struktur') }}" class="folder-card bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm transition-all-200 group relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center text-xl shadow-md shadow-rose-500/20 group-hover:scale-110 transition-all-200">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
                <span class="bg-rose-50 text-rose-700 text-xs font-bold px-3 py-1 rounded-full border border-rose-200">
                    {{ $docStats['struktur'] ?? 0 }} File
                </span>
            </div>
            <h4 class="font-bold text-slate-900 text-base group-hover:text-rose-600 transition-all-200">Struktur Organisasi</h4>
            <p class="text-xs text-slate-500 mt-1">Bagan organisasi departemen</p>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-rose-600">
                <span>Buka Folder Struktur</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-all-200"></i>
            </div>
        </a>

    </div>

    <!-- Recent Documents Table / List -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600"></i>
                    <span>Dokumen Terbaru Dimuat</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar file HRD terbaru yang diunggah ke sistem</p>
            </div>
            <a href="{{ route('documents.index', 'sop') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Lihat Folder Dokumen &rarr;</a>
        </div>

        @if(isset($recentDocuments) && $recentDocuments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Nama Dokumen</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">BOD & Departemen</th>
                            <th class="py-3.5 px-4">Pengunggah</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentDocuments as $doc)
                            <tr class="hover:bg-slate-50/80 transition-all-200">
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base shrink-0 {{ $doc->getFileColorClass() }} shadow-sm">
                                            <i class="{{ $doc->getFileIconClass() }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm line-clamp-1">{{ $doc->title }}</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $doc->file_name }} &bull; {{ formatBytes($doc->file_size) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase
                                        {{ $doc->type === 'sop' ? 'bg-emerald-100 text-emerald-700' : ($doc->type === 'job_desk' ? 'bg-amber-100 text-amber-700' : ($doc->type === 'bpm' ? 'bg-purple-100 text-purple-700' : 'bg-rose-100 text-rose-700')) }}">
                                        {{ $doc->type === 'job_desk' ? 'JOB DESK' : ($doc->type === 'struktur' ? 'STRUKTUR' : strtoupper($doc->type)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-xs">
                                    <p class="font-semibold text-slate-800">{{ $doc->division->name ?? '-' }}</p>
                                    <p class="text-slate-400 text-[11px]">
                                        BOD: {{ $doc->bod->name ?? '-' }}
                                        @if($doc->position) &bull; {{ $doc->position->name }} @endif
                                    </p>
                                </td>
                                <td class="py-4 px-4 text-xs">
                                    <p class="font-medium text-slate-700">{{ $doc->uploader->name ?? 'System' }}</p>
                                    <p class="text-slate-400 text-[11px]">{{ $doc->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('documents.preview', $doc->id) }}" title="Preview Dokumen" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all-200">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('documents.download', $doc->id) }}" title="Download File" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all-200">
                                            <i class="fa-solid fa-download text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <i class="fa-solid fa-folder-open text-4xl text-slate-300 mb-3"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada dokumen yang diunggah</p>
                <p class="text-xs text-slate-400 mt-1">Unggah dokumen baru dari tombol 'Upload Dokumen'</p>
            </div>
        @endif
    </div>
</x-app-layout>
