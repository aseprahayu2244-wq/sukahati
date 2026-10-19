<x-app-layout>
    <x-slot name="headerTitle">Kelola Pengguna</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Kelola Pengguna</span>
    </x-slot>

    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 mb-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="flex items-center space-x-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-blue-600/30 border border-blue-500/30 flex items-center justify-center text-2xl text-blue-400 shadow-inner">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold tracking-tight">Manajemen Pengguna</h1>
                <p class="text-xs text-slate-300 mt-1">Kelola akun pengguna, role, dan status aktif.</p>
            </div>
        </div>

        <div class="flex items-center space-x-4 relative z-10 shrink-0">
            <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/10 text-center">
                <p class="text-[10px] uppercase tracking-wider font-bold text-slate-300">Total User</p>
                <p class="text-lg font-extrabold text-white">{{ $users->total() }}</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all-200">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Pengguna</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, email..." 
                    class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl pl-9 pr-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>
            <select name="role" class="bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Role</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="anggota" {{ request('role') === 'anggota' ? 'selected' : '' }}>Anggota</option>
            </select>
            <select name="status" class="bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all-200">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl transition-all-200 flex items-center">
                Reset
            </a>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold text-slate-600 uppercase tracking-wider">Pengguna</th>
                        <th class="px-5 py-3 text-left font-bold text-slate-600 uppercase tracking-wider">Role</th>
                        <th class="px-5 py-3 text-left font-bold text-slate-600 uppercase tracking-wider">Jabatan</th>
                        <th class="px-5 py-3 text-left font-bold text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left font-bold text-slate-600 uppercase tracking-wider">Dibuat</th>
                        <th class="px-5 py-3 text-right font-bold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-all-200">
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" class="w-9 h-9 rounded-full object-cover shadow-sm" alt="">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-slate-500">{{ $user->username ? '@' . $user->username : '' }} &bull; {{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $user->getRoleBadgeClass() }}">
                                    {{ $user->getRoleLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $user->jabatan ?? '-' }}</td>
                            <td class="px-5 py-4">
                                @if($user->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle text-[6px] mr-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-circle text-[6px] mr-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Toggle Active -->
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" 
                                                class="p-1.5 rounded-lg text-xs {{ $user->is_active ? 'bg-slate-100 text-slate-600 hover:bg-rose-50 hover:text-rose-600' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} transition-all-200">
                                                <i class="fa-solid {{ $user->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit -->
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 text-xs transition-all-200" title="Edit Pengguna">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Delete -->
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirmDelete(event, 'Akun {{ addslashes($user->name) }} akan dihapus permanen!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs transition-all-200" title="Hapus Pengguna">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i class="fa-solid fa-users text-4xl text-slate-200 mb-3"></i>
                                <p class="text-slate-500 font-medium">Tidak ada pengguna ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
