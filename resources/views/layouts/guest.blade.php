<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SIMD HRD' }} - CV. Sukahati Pratama</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo-sukahati.png') }}" type="image/png">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
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
                        },
                        corporate: {
                            blue: '#1b4b9b',
                            red: '#d92526',
                            yellow: '#f4b815',
                            green: '#2da64e',
                            dark: '#0b162c'
                        }
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-mesh-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="h-full bg-slate-900 text-slate-800 antialiased font-sans">
    <div class="min-h-screen lg:h-screen lg:overflow-hidden flex flex-col lg:flex-row">
        
        <!-- Left Side: Corporate Branding Showcase (Desktop - Diam / Fixed di tempat) -->
        <div class="lg:w-5/12 xl:w-5/12 lg:h-screen lg:sticky lg:top-0 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-white p-6 lg:p-10 xl:p-14 flex flex-col justify-between relative overflow-hidden flex-shrink-0">
            
            <!-- Ambient Glow Accents -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 bg-mesh-pattern opacity-30 pointer-events-none"></div>

            <!-- Top Spacer -->
            <div class="hidden lg:block"></div>

            <!-- Centered Header & Logo -->
            <div class="relative z-10 my-auto flex flex-col items-center text-center py-6">
                <div class="p-3.5 bg-white rounded-3xl shadow-2xl shadow-black/50 ring-8 ring-white/10 flex-shrink-0 transition-transform duration-300 hover:scale-105 mb-5">
                    <img src="{{ asset('images/logo-sukahati.png') }}" alt="Logo CV. Sukahati Pratama" class="h-24 sm:h-28 w-auto object-contain">
                </div>
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-blue-500/20 text-blue-300 border border-blue-400/30 mb-2.5">
                    Chicken Processing Industry
                </span>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    CV. SUKAHATI PRATAMA
                </h1>
                
                <p class="text-xs sm:text-sm text-blue-200/80 font-medium mt-1.5 max-w-sm">
                    Sistem Informasi Manajemen Dokumen HRD (SIMD)
                </p>

                <div class="mt-5 flex items-center space-x-2 text-[11px] font-semibold uppercase tracking-wider text-amber-400/90 bg-white/5 border border-white/10 px-3.5 py-1.5 rounded-xl backdrop-blur-sm">
                    <i class="fa-solid fa-shield-halved text-amber-400"></i>
                    <span>Portal Karyawan Terpadu</span>
                </div>
            </div>

            <!-- Bottom Note & Copyright -->
            <div class="relative z-10 pt-4 border-t border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-400">
                <div>
                    <span class="font-medium text-slate-300">CV. Sukahati Pratama</span> &copy; {{ date('Y') }}
                </div>
                <div class="flex items-center space-x-1.5 text-slate-400">
                    <i class="fa-solid fa-lock text-[10px] text-blue-400"></i>
                    <span>Internal HRD System</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Authentication Forms (Area scroll independen) -->
        <div class="lg:w-7/12 xl:w-7/12 lg:h-screen lg:overflow-y-auto bg-slate-50 flex flex-col justify-center items-center p-4 sm:p-8 lg:p-12 relative min-h-screen lg:min-h-0">
            
            <!-- Mobile Brand Header (Visible on small screens) -->
            <div class="w-full max-w-lg mb-6 lg:hidden">
                <div class="bg-slate-900 rounded-2xl p-5 text-white shadow-xl flex items-center space-x-4 border border-slate-800">
                    <div class="p-2 bg-white rounded-xl shadow flex-shrink-0">
                        <img src="{{ asset('images/logo-sukahati.png') }}" alt="Logo CV. Sukahati Pratama" class="h-12 w-auto object-contain">
                    </div>
                    <div>
                        <span class="inline-block text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 bg-blue-500/20 text-blue-300 rounded border border-blue-400/30">
                            CV. Sukahati Pratama
                        </span>
                        <h2 class="text-base font-bold text-white leading-tight mt-0.5">SIMD HRD</h2>
                        <p class="text-[11px] text-slate-300">Chicken Processing Industry</p>
                    </div>
                </div>
            </div>

            <!-- Main Auth Card Container -->
            <div class="w-full max-w-lg">
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/80 border border-slate-200/80 p-6 sm:p-10 relative">
                    {{ $slot }}
                </div>

                <!-- Footer Help Info -->
                <div class="mt-6 text-center text-xs text-slate-500">
                    <p>Butuh bantuan atau kendala akun? Hubungi <span class="font-semibold text-slate-700">Divisi HRD</span> di kantor operasional.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }
    </script>
</body>
</html>
