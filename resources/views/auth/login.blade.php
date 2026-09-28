<x-guest-layout>
    <!-- Navigation Tabs between Login & Register -->
    <div class="flex items-center justify-between p-1 bg-slate-100 rounded-2xl mb-8">
        <a href="{{ route('login') }}" 
           class="flex-1 py-2.5 px-4 text-center text-xs sm:text-sm font-bold rounded-xl bg-white text-blue-700 shadow-sm transition-all duration-200 flex items-center justify-center space-x-2">
            <i class="fa-solid fa-right-to-bracket text-blue-600"></i>
            <span>Masuk</span>
        </a>
        <a href="{{ route('register') }}" 
           class="flex-1 py-2.5 px-4 text-center text-xs sm:text-sm font-semibold rounded-xl text-slate-600 hover:text-slate-900 transition-all duration-200 flex items-center justify-center space-x-2">
            <i class="fa-solid fa-user-plus text-slate-400"></i>
            <span>Daftar Akun</span>
        </a>
    </div>

    <!-- Title Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk SIMD HRD</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Gunakan email dan kata sandi Anda untuk mengakses sistem SIMD HRD.
        </p>
    </div>

    <!-- Session Status Alert -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs sm:text-sm flex items-start space-x-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base mt-0.5 flex-shrink-0"></i>
            <div>
                <p class="font-semibold">Informasi Status</p>
                <p class="text-emerald-700 mt-0.5">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
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
                       autofocus 
                       autocomplete="username"
                       placeholder="username"
                       class="w-full pl-10 pr-4 py-3 bg-slate-50/70 hover:bg-slate-50 border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600' }} rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:bg-white transition-all duration-200 shadow-sm" />
            </div>
            @if ($errors->has('email'))
                <p class="mt-2 text-xs text-red-600 flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first('email') }}</span>
                </p>
            @endif
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    <i class="fa-solid fa-lock text-slate-400 mr-1.5"></i> Kata Sandi
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" 
                       class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                    <i class="fa-solid fa-key"></i>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full pl-10 pr-11 py-3 bg-slate-50/70 hover:bg-slate-50 border {{ $errors->has('password') ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600' }} rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:bg-white transition-all duration-200 shadow-sm" />
                <button type="button" 
                        onclick="togglePassword('password', 'login-password-eye')" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i id="login-password-eye" class="fa-solid fa-eye text-sm"></i>
                </button>
            </div>
            @if ($errors->has('password'))
                <p class="mt-2 text-xs text-red-600 flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first('password') }}</span>
                </p>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember" 
                       class="w-4 h-4 rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                <span class="ms-2.5 text-xs sm:text-sm text-slate-600 font-medium">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" 
                    class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-sm bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center space-x-2">
                <span>Masuk ke Sistem</span>
                <i class="fa-solid fa-arrow-right-to-bracket text-sm"></i>
            </button>
        </div>

        <!-- Register Switcher Footer -->
      
    </form>
</x-guest-layout>
