<x-app-layout>
    <x-slot name="headerTitle">Kelola Jabatan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Master Jabatan</span>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Add New Position Form -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm h-fit">
            <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-plus-circle text-blue-600"></i>
                <span>Tambah Jabatan Baru</span>
            </h3>

            <form action="{{ route('admin.positions.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Departemen <span class="text-rose-500">*</span></label>
                    <select name="division_id" required class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">
                                {{ $dept->name }} @if($dept->bod) (BOD: {{ $dept->bod->name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Jabatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: HR Manager, Staff Recruiter" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Jabatan</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi singkat mengenai jabatan..." class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Atasan (Optional for Org Chart)</label>
                    <select name="parent_id" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Tidak Ada / Puncak Hirarki --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->division->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 rounded-xl shadow-md shadow-blue-500/20 transition-all-200">
                    <i class="fa-solid fa-check mr-1"></i> Simpan Jabatan
                </button>
            </form>
        </div>

        <!-- Right: Position List Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
            <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-id-badge text-blue-600"></i>
                <span>Daftar Jabatan Terdaftar</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama Jabatan</th>
                            <th class="py-3 px-4">Departemen & BOD</th>
                            <th class="py-3 px-4">Atasan Direct</th>
                            <th class="py-3 px-4 text-center">Jumlah Dokumen</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($positions as $pos)
                            <tr class="hover:bg-slate-50 transition-all-200">
                                <td class="py-4 px-4">
                                    <p class="font-bold text-slate-800 text-sm">{{ $pos->name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $pos->description ?? 'Tanpa deskripsi' }}</p>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="bg-slate-100 text-slate-700 font-bold text-xs px-2.5 py-1 rounded-full border border-slate-200">
                                        {{ $pos->division->name ?? '-' }}
                                    </span>
                                    @if(optional($pos->division)->bod)
                                        <p class="text-[11px] text-slate-400 mt-1 font-medium">BOD: {{ $pos->division->bod->name }}</p>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-xs font-medium text-slate-600">
                                    {{ $pos->parent->name ?? '-' }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="bg-emerald-50 text-emerald-700 font-bold text-xs px-3 py-1 rounded-full border border-emerald-200">
                                        {{ $pos->documents()->count() }} File
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="editPosition({{ json_encode($pos) }})" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white flex items-center justify-center transition-all-200" title="Edit Jabatan">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                        <form action="{{ route('admin.positions.destroy', $pos->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Jabatan {{ addslashes($pos->name) }} akan dihapus!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all-200" title="Hapus Jabatan">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-400 text-xs">Belum ada jabatan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Edit Position Modal -->
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xl max-w-md w-full mx-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="font-extrabold text-slate-800 text-base">Edit Jabatan</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Departemen <span class="text-rose-500">*</span></label>
                    <select name="division_id" id="edit_division_id" required class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }} @if($dept->bod) (BOD: {{ $dept->bod->name }}) @endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Jabatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit_name" required class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Jabatan</label>
                    <textarea name="description" id="edit_description" rows="2" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Atasan (Optional)</label>
                    <select name="parent_id" id="edit_parent_id" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Tidak Ada / Puncak Hirarki --</option>
                        @foreach($positions as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->division->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-2 flex items-center justify-end space-x-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function editPosition(pos) {
            document.getElementById('editForm').action = '/admin/positions/' + pos.id;
            document.getElementById('edit_division_id').value = pos.division_id;
            document.getElementById('edit_name').value = pos.name;
            document.getElementById('edit_description').value = pos.description || '';
            document.getElementById('edit_parent_id').value = pos.parent_id || '';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
