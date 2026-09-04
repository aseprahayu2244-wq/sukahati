<x-app-layout>
    <x-slot name="headerTitle">Kelola Master Divisi</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Master Divisi</span>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Add New Division Form -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm h-fit">
            <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-plus-circle text-blue-600"></i>
                <span>Tambah Divisi Baru</span>
            </h3>

            <form action="{{ route('admin.divisions.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Divisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Human Resources, Keuangan" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Divisi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi singkat mengenai divisi ini..." class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil</label>
                    <input type="number" name="order" value="0" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 rounded-xl shadow-md shadow-blue-500/20 transition-all-200">
                    <i class="fa-solid fa-check mr-1"></i> Simpan Divisi
                </button>
            </form>
        </div>

        <!-- Right: Division List Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
            <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-layer-group text-blue-600"></i>
                <span>Daftar Divisi Terdaftar</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama Divisi</th>
                            <th class="py-3 px-4 text-center">Jumlah Jabatan</th>
                            <th class="py-3 px-4 text-center">Jumlah Dokumen</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($divisions as $div)
                            <tr class="hover:bg-slate-50 transition-all-200">
                                <td class="py-4 px-4">
                                    <p class="font-bold text-slate-800 text-sm">{{ $div->name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $div->description ?? 'Tanpa deskripsi' }}</p>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="bg-blue-50 text-blue-700 font-bold text-xs px-3 py-1 rounded-full border border-blue-200">
                                        {{ $div->positions_count }} Jabatan
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="bg-emerald-50 text-emerald-700 font-bold text-xs px-3 py-1 rounded-full border border-emerald-200">
                                        {{ $div->documents_count }} File
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <form action="{{ route('admin.divisions.destroy', $div->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini beserta jabatannya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all-200" title="Hapus Divisi">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-slate-400 text-xs">Belum ada divisi terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
