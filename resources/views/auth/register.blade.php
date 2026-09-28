<x-guest-layout>
    <!-- Navigation Tabs between Login & Register -->
    <div class="flex items-center justify-between p-1 bg-slate-100 rounded-2xl mb-8">
        <a href="{{ route('login') }}" 
           class="flex-1 py-2.5 px-4 text-center text-xs sm:text-sm font-semibold rounded-xl text-slate-600 hover:text-slate-900 transition-all duration-200 flex items-center justify-center space-x-2">
            <i class="fa-solid fa-right-to-bracket text-slate-400"></i>
            <span>Masuk</span>
        </a>
        <a href="{{ route('register') }}" 
           class="flex-1 py-2.5 px-4 text-center text-xs sm:text-sm font-bold rounded-xl bg-white text-blue-700 shadow-sm transition-all duration-200 flex items-center justify-center space-x-2">
            <i class="fa-solid fa-user-plus text-blue-600"></i>
            <span>Daftar Akun</span>
        </a>
    </div>

    <!-- Title Section -->
    <div class="mb-6">
        
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Buat Akun SIMD HRD</h2>
        
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                <i class="fa-solid fa-user text-slate-400 mr-1.5"></i> Nama Lengkap
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                    <i class="fa-solid fa-id-badge"></i>
                </div>
                <input id="name" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       autocomplete="name"
                       placeholder="name"
                       class="w-full pl-10 pr-4 py-3 bg-slate-50/70 hover:bg-slate-50 border {{ $errors->has('name') ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600' }} rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:bg-white transition-all duration-200 shadow-sm" />
            </div>
            @if ($errors->has('name'))
                <p class="mt-1.5 text-xs text-red-600 flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first('name') }}</span>
                </p>
            @endif
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                <i class="fa-solid fa-envelope text-slate-400 mr-1.5"></i> Alamat Email
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                    <i class="fa-solid fa-at"></i>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="username"
                       placeholder="username"
                       class="w-full pl-10 pr-4 py-3 bg-slate-50/70 hover:bg-slate-50 border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600' }} rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:bg-white transition-all duration-200 shadow-sm" />
            </div>
            @if ($errors->has('email'))
                <p class="mt-1.5 text-xs text-red-600 flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first('email') }}</span>
                </p>
            @endif
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                <i class="fa-solid fa-lock text-slate-400 mr-1.5"></i> Kata Sandi
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                    <i class="fa-solid fa-key"></i>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Minimal 8 karakter"
                       class="w-full pl-10 pr-11 py-3 bg-slate-50/70 hover:bg-slate-50 border {{ $errors->has('password') ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600' }} rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:bg-white transition-all duration-200 shadow-sm" />
                <button type="button" 
                        onclick="togglePassword('password', 'reg-password-eye')" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i id="reg-password-eye" class="fa-solid fa-eye text-sm"></i>
                </button>
            </div>
            @if ($errors->has('password'))
                <p class="mt-1.5 text-xs text-red-600 flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first('password') }}</span>
                </p>
            @endif
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                <i class="fa-solid fa-shield-halved text-slate-400 mr-1.5"></i> Konfirmasi Kata Sandi
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                    <i class="fa-solid fa-check-double"></i>
                </div>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       placeholder="Ulangi kata sandi di atas"
                       class="w-full pl-10 pr-11 py-3 bg-slate-50/70 hover:bg-slate-50 border {{ $errors->has('password_confirmation') ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600' }} rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:bg-white transition-all duration-200 shadow-sm" />
                <button type="button" 
                        onclick="togglePassword('password_confirmation', 'reg-password-confirm-eye')" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i id="reg-password-confirm-eye" class="fa-solid fa-eye text-sm"></i>
                </button>
            </div>
            @if ($errors->has('password_confirmation'))
                <p class="mt-1.5 text-xs text-red-600 flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first('password_confirmation') }}</span>
                </p>
            @endif
        </div>

        <!-- Policy Note -->
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 text-[11px] text-slate-500 leading-relaxed">
            <i class="fa-solid fa-info-circle text-blue-500 mr-1"></i>
            Akun baru akan mendapatkan hak akses default untuk penjelajahan dokumen internal sesuai kebijakan HRD CV. Sukahati Pratama.
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" 
                    class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-sm bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center space-x-2">
                <span>Daftar Akun Baru</span>
                <i class="fa-solid fa-user-plus text-sm"></i>
            </button>
        </div>

       
    </form>
</x-guest-layout>
