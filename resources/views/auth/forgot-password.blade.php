<x-guest-layout>
    <!-- Back to Login Header -->
    <div class="mb-6">
        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors mb-4">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Halaman Masuk
        </a>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold mb-2">
            <i class="fa-solid fa-key text-[11px]"></i>
            <span>Pemulihan Kata Sandi</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Lupa Kata Sandi?</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Tidak masalah. Masukkan alamat email akun Anda dan kami akan mengirimkan tautan reset kata sandi melalui email.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs sm:text-sm flex items-start space-x-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base mt-0.5 flex-shrink-0"></i>
            <div>
                <p class="font-semibold">Informasi Status</p>
                <p class="text-emerald-700 mt-0.5">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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
                       placeholder="nama@sukahatipratama.com"
                       class="w-full pl-10 pr-4 py-3 bg-slate-50/70 hover:bg-slate-50 border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-600' }} rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:bg-white transition-all duration-200 shadow-sm" />
            </div>
            @if ($errors->has('email'))
                <p class="mt-2 text-xs text-red-600 flex items-center space-x-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first('email') }}</span>
                </p>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-sm bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center space-x-2">
                <span>Kirim Tautan Reset Kata Sandi</span>
                <i class="fa-solid fa-paper-plane text-sm"></i>
            </button>
        </div>
    </form>
</x-guest-layout>
