<x-app-layout>
    <x-slot name="headerTitle">Tambah Pengguna Baru</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('admin.users.index') }}" class="hover:underline">Kelola Pengguna</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Tambah Pengguna</span>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Buat Akun Pengguna Baru</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan role dan informasi login untuk staf atau pimpinan</p>
                </div>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Role Selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Role Pengguna <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Super Admin (Purple) -->
                        <label class="cursor-pointer border-2 border-slate-200 bg-white rounded-2xl p-4 flex flex-col items-center justify-center text-center transition-all-200 hover:border-purple-300 has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50/60 has-[:checked]:shadow-sm">
                            <input type="radio" name="role" value="super_admin" {{ old('role') === 'super_admin' ? 'checked' : '' }} required class="sr-only">
                            <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-2 shadow-inner">
                                <i class="fa-solid fa-crown"></i>
                            </div>
                            <span class="text-xs font-extrabold text-slate-900">Super Admin</span>
                            <span class="text-[11px] text-slate-400 mt-0.5">Akses Penuh + Kelola Akun</span>
                        </label>

                        <!-- Admin (Amber / Oranye) -->
                        <label class="cursor-pointer border-2 border-slate-200 bg-white rounded-2xl p-4 flex flex-col items-center justify-center text-center transition-all-200 hover:border-amber-300 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/60 has-[:checked]:shadow-sm">
                            <input type="radio" name="role" value="admin" {{ old('role', 'admin') === 'admin' ? 'checked' : '' }} required class="sr-only">
                            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-2 shadow-inner">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <span class="text-xs font-extrabold text-slate-900">Admin</span>
                            <span class="text-[11px] text-slate-400 mt-0.5">CRUD Dokumen & Dept</span>
                        </label>

                        <!-- Anggota (Slate) -->
                        <label class="cursor-pointer border-2 border-slate-200 bg-white rounded-2xl p-4 flex flex-col items-center justify-center text-center transition-all-200 hover:border-slate-300 has-[:checked]:border-slate-600 has-[:checked]:bg-slate-100/70 has-[:checked]:shadow-sm">
                            <input type="radio" name="role" value="anggota" {{ old('role') === 'anggota' ? 'checked' : '' }} required class="sr-only">
                            <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl mb-2 shadow-inner">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="text-xs font-extrabold text-slate-900">Anggota</span>
                            <span class="text-[11px] text-slate-400 mt-0.5">Lihat & Download Saja</span>
                        </label>
                    </div>
                    @error('role')
                        <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-rose-400 @enderror">
                    @error('name')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username <span class="text-rose-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}" required placeholder="Contoh: budi.santoso" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('username') border-rose-400 @enderror">
                        @error('username')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="budi@perusahaan.com" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-rose-400 @enderror">
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Jabatan & No Telepon -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jabatan / Posisi</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: HR Manager" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Password & Konfirmasi Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-rose-400 @enderror">
                        @error('password')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi password" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-between">
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-all-200">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Batal & Kembali
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-lg shadow-blue-500/20 transition-all-200 flex items-center space-x-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Buat Pengguna</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
