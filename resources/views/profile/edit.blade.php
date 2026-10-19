<x-app-layout>
    <x-slot name="headerTitle">Pengaturan Profil Pengguna</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Profil Saya</span>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Profile Information Form -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Informasi Pribadi & Akun</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui foto profil, nama, kontak, dan jabatan Anda</p>
                </div>
            </div>

            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('patch')

                <!-- Avatar Upload Section -->
                <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6 pb-6 border-b border-slate-100">
                    <div class="relative group">
                        <img id="avatarPreview" src="{{ auth()->user()->getAvatarUrl() }}" alt="{{ auth()->user()->name }}" class="w-24 h-24 rounded-2xl object-cover border-2 border-slate-200 shadow-sm group-hover:opacity-80 transition-all-200">
                    </div>
                    <div class="space-y-1 text-center sm:text-left">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Profil</label>
                        <p class="text-xs text-slate-400">Mendukung format JPG, PNG, atau WebP (Maksimal 2MB)</p>
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="previewAvatar(this)" class="mt-2 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        @error('avatar')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-rose-400 @enderror">
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role (Readonly badge) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Role Akun</label>
                        <div class="w-full bg-slate-100 text-slate-700 text-xs font-bold rounded-xl px-4 py-3 border border-slate-200 flex items-center justify-between">
                            <span>{{ $user->getRoleLabel() }}</span>
                            <span class="text-[11px] font-normal text-slate-400">(Dikelola Super Admin)</span>
                        </div>
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('username') border-rose-400 @enderror">
                        @error('username')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-rose-400 @enderror">
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jabatan / Posisi</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Contoh: HR Manager" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- No WhatsApp / Telepon -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-lg shadow-blue-500/20 transition-all-200 flex items-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Profil</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password Form -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm">
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shadow-lg shadow-amber-500/20">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Ubah Password</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pastikan menggunakan kombinasi password yang kuat untuk menjaga keamanan akun</p>
                </div>
            </div>

            <form method="post" action="{{ route('profile.password') }}" class="space-y-5 max-w-xl">
                @csrf
                @method('patch')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password Saat Ini <span class="text-rose-500">*</span></label>
                    <input type="password" name="current_password" required placeholder="Masukkan password saat ini" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-rose-400 @enderror">
                    @error('current_password')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-rose-400 @enderror">
                        @error('password')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi password baru" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-lg shadow-amber-500/20 transition-all-200 flex items-center space-x-2">
                        <i class="fa-solid fa-key"></i>
                        <span>Ubah Password</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    @push('scripts')
    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-app-layout>
