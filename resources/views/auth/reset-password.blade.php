<x-guest-layout>
    <div class="mb-6">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold mb-2">
            <i class="fa-solid fa-lock-open text-[11px]"></i>
            <span>Atur Ulang Kata Sandi</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Perbarui Kata Sandi</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Silakan masukkan kata sandi baru untuk akun SIMD HRD Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                       value="{{ old('email', $request->email) }}" 
                       required 
                       autofocus 
                       autocomplete="username"
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
                <i class="fa-solid fa-lock text-slate-400 mr-1.5"></i> Kata Sandi Baru
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
                        onclick="togglePassword('password', 'reset-password-eye')" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i id="reset-password-eye" class="fa-solid fa-eye text-sm"></i>
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
                <i class="fa-solid fa-shield-halved text-slate-400 mr-1.5"></i> Konfirmasi Kata Sandi Baru
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
                       placeholder="Ulangi kata sandi baru"
                       class="w-full pl-10 pr-11 py-3 bg-slate-50/70 hover:bg-slate-50 border {{ $errors->has('password_confirmation') ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600' }} rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:bg-white transition-all duration-200 shadow-sm" />
                <button type="button" 
                        onclick="togglePassword('password_confirmation', 'reset-confirm-eye')" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i id="reset-confirm-eye" class="fa-solid fa-eye text-sm"></i>
                </button>
            </div>
            @if ($errors->has('password_confirmation'))
                <p class="mt-1.5 text-xs text-red-600 flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first('password_confirmation') }}</span>
                </p>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-sm bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center space-x-2">
                <span>Perbarui Kata Sandi</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>
        </div>
    </form>
</x-guest-layout>
