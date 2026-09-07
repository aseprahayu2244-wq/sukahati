<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SIMD HRD' }} - Sistem Informasi Manajemen Dokumen HRD</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons (FontAwesome 6) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tailwind CSS CDN (for instant seamless rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            50: '#f0f6fe',
                            100: '#ddebfd',
                            200: '#c3dbfb',
                            300: '#99c3f8',
                            400: '#67a3f3',
                            500: '#3f7feb',
                            600: '#2860df',
                            700: '#1f4bc9',
                            800: '#1f3ea3',
                            900: '#0f172a',
                            950: '#0a0e1a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-header { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); }
        .folder-card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1); }
        .transition-all-200 { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased font-sans">
    <div class="min-h-screen flex flex-col md:flex-row">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-72 bg-slate-900 text-white flex-shrink-0 flex flex-col justify-between shadow-xl min-h-screen">
            <div>
                <!-- Brand Header -->
                <div class="px-6 py-6 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-all-200">
                            <i class="fa-solid fa-folder-tree text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="font-extrabold text-lg text-white tracking-wider leading-none">SIMD <span class="text-blue-400">HRD</span></h1>
                            <p class="text-[10px] text-slate-400 tracking-wider uppercase font-semibold mt-1">Dokumen System</p>
                        </div>
                    </a>
                </div>

                <!-- Global Search Quick Bar -->
                <div class="px-4 py-4">
                    <form action="{{ route('search') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari semua dokumen..." 
                                class="w-full bg-slate-800/90 text-slate-200 placeholder-slate-400 text-xs rounded-xl pl-9 pr-4 py-2.5 border border-slate-700/60 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all-200">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </form>
                </div>

                <!-- Navigation Links -->
                <nav class="px-4 space-y-6 py-2">
                    
                    <!-- Main Menu -->
                    <div>
                        <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Main Menu</p>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-chart-pie w-5 text-center text-slate-400 group-hover:text-white"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('organization.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->routeIs('organization.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-sitemap w-5 text-center text-slate-400"></i>
                                <span>Struktur Organisasi</span>
                            </a>
                        </div>
                    </div>

                    <!-- Dokumen HRD -->
                    <div>
                        <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Kategori Dokumen</p>
                        <div class="space-y-1">
                            <a href="{{ route('documents.index', 'sop') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->is('docs/sop*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-file-contract w-5 text-center text-emerald-400"></i>
                                    <span>Dokumen SOP</span>
                                </div>
                                <span class="bg-emerald-500/20 text-emerald-300 text-[10px] px-2 py-0.5 rounded-full font-bold">SOP</span>
                            </a>

                            <a href="{{ route('documents.index', 'job') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->is('docs/job*') && !request()->is('docs/job_analysis*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-user-gear w-5 text-center text-amber-400"></i>
                                    <span>Job Description</span>
                                </div>
                                <span class="bg-amber-500/20 text-amber-300 text-[10px] px-2 py-0.5 rounded-full font-bold">JOB</span>
                            </a>

                            <a href="{{ route('documents.index', 'job_analysis') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->is('docs/job_analysis*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-magnifying-glass-chart w-5 text-center text-purple-400"></i>
                                    <span>Job Analysis</span>
                                </div>
                                <span class="bg-purple-500/20 text-purple-300 text-[10px] px-2 py-0.5 rounded-full font-bold">JA</span>
                            </a>

                            <a href="{{ route('documents.index', 'bpmn') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->is('docs/bpmn*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-diagram-project w-5 text-center text-rose-400"></i>
                                    <span>Dokumen BPMN</span>
                                </div>
                                <span class="bg-rose-500/20 text-rose-300 text-[10px] px-2 py-0.5 rounded-full font-bold">BPMN</span>
                            </a>
                        </div>
                    </div>

                    <!-- Administration (Ketua Only) -->
                    @if(auth()->check() && auth()->user()->isKetua())
                    <div>
                        <p class="px-3 text-[11px] font-bold text-amber-400 uppercase tracking-widest mb-2 flex items-center space-x-1">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                            <span>Menu Admin (Ketua)</span>
                        </p>
                        <div class="space-y-1">
                            <a href="{{ route('documents.create') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->routeIs('documents.create') ? 'bg-amber-500 text-slate-900 shadow-md shadow-amber-500/30 font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-cloud-arrow-up w-5 text-center text-amber-400"></i>
                                <span>Upload Dokumen</span>
                            </a>
                            <a href="{{ route('admin.divisions.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->routeIs('admin.divisions.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-layer-group w-5 text-center text-slate-400"></i>
                                <span>Kelola Divisi</span>
                            </a>
                            <a href="{{ route('admin.positions.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all-200 {{ request()->routeIs('admin.positions.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-id-badge w-5 text-center text-slate-400"></i>
                                <span>Kelola Jabatan</span>
                            </a>
                        </div>
                    </div>
                    @endif

                </nav>
            </div>

            <!-- User Profile / Footer Sidebar -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-700 to-slate-600 flex items-center justify-center font-bold text-white shadow-md shrink-0">
                            {{ uppercase(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                            <div class="flex items-center space-x-1 mt-0.5">
                                @if(auth()->user()->isKetua())
                                    <span class="bg-amber-500/20 text-amber-300 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase border border-amber-500/30">Ketua (Admin)</span>
                                @else
                                    <span class="bg-slate-700 text-slate-300 text-[10px] px-2 py-0.5 rounded-full font-medium uppercase">User Umum</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-all-200">
                            <i class="fa-solid fa-right-from-bracket text-base"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 min-h-screen">
            
            <!-- Top Header Navbar -->
            <header class="glass-header sticky top-0 z-30 border-b border-slate-200/80 px-6 py-4 flex items-center justify-between">
                <div>
                    @if(isset($headerTitle))
                        <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ $headerTitle }}</h2>
                    @endif
                    @if(isset($breadcrumb))
                        <nav class="flex items-center space-x-2 text-xs text-slate-500 mt-0.5">
                            {{ $breadcrumb }}
                        </nav>
                    @endif
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('search') }}" class="inline-flex items-center space-x-2 text-xs font-semibold px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200">
                        <i class="fa-solid fa-magnifying-glass text-slate-500"></i>
                        <span>Pencarian Dokumen</span>
                    </a>

                    @if(auth()->check() && auth()->user()->isKetua())
                    <a href="{{ route('documents.create') }}" class="inline-flex items-center space-x-2 text-xs font-bold px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl shadow-md shadow-blue-500/20 transition-all-200">
                        <i class="fa-solid fa-plus"></i>
                        <span>Upload Dokumen</span>
                    </a>
                    @endif
                </div>
            </header>

            <!-- Notification Messages -->
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'p-2 text-xs shadow-md rounded-xl',
                                title: 'text-xs font-semibold'
                            }
                        });
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "error",
                            title: "{{ session('error') }}",
                            showConfirmButton: false,
                            timer: 2500,
                            customClass: {
                                popup: 'p-2 text-xs shadow-md rounded-xl',
                                title: 'text-xs font-semibold'
                            }
                        });
                    });
                </script>
            @endif

            <!-- Page Main Body -->
            <main class="flex-1 px-6 py-6">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="px-6 py-4 border-t border-slate-200 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} SIMD HRD - Sistem Informasi Manajemen Dokumen HRD. Framework Laravel 10.
            </footer>
        </div>

    </div>

    <script>
        function confirmDelete(event, text = "You won't be able to revert this!", title = "Apakah benar?") {
            event.preventDefault();
            const form = event.target || event.currentTarget;
            
            Swal.fire({
                title: title,
                text: text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "iya",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
    </script>
</body>
</html>
