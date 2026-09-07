<x-app-layout>
    <x-slot name="headerTitle">Kelola Master Divisi</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Kelola Divisi</span>
    </x-slot>

    <!-- Top Summary Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 mb-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="flex items-center space-x-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-blue-600/30 border border-blue-500/30 flex items-center justify-center text-2xl text-blue-400 shadow-inner">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold tracking-tight">Manajemen Master Divisi</h1>
                <p class="text-xs text-slate-300 mt-1">Tambah, perbarui, dan atur hirarki divisi organisasi HRD secara terpusat.</p>
            </div>
        </div>

        <div class="flex items-center space-x-4 relative z-10 shrink-0">
            <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/10 text-center">
                <p class="text-[10px] uppercase tracking-wider font-bold text-slate-300">Total Divisi</p>
                <p class="text-lg font-extrabold text-white">{{ $divisions->count() }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/10 text-center">
                <p class="text-[10px] uppercase tracking-wider font-bold text-slate-300">Total Jabatan</p>
                <p class="text-lg font-extrabold text-blue-400">{{ $divisions->sum('positions_count') }}</p>
            </div>
        </div>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-xl mb-4 text-[11px] shadow-sm">
            <div class="flex items-center space-x-1.5 font-bold mb-1">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-xs"></i>
                <span>Terdapat kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Form Tambah Divisi Baru -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm h-fit">
            <div class="flex items-center space-x-3 mb-5 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Tambah Divisi Baru</h3>
                    <p class="text-[11px] text-slate-500">Buat divisi unit kerja baru</p>
                </div>
            </div>

            <form action="{{ route('admin.divisions.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Divisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: HRD, Keuangan, IT..." 
                        class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Divisi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi peran dan tanggung jawab divisi..." 
                        class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna Label</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="addColorPicker" name="color" value="{{ old('color', '#3b82f6') }}" 
                                class="w-9 h-9 rounded-xl border border-slate-200 cursor-pointer p-0.5 bg-white">
                            <span id="addColorHex" class="text-xs font-mono text-slate-600 font-semibold">{{ old('color', '#3b82f6') }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil <span class="text-rose-500">*</span></label>
                        <input type="number" name="order" min="1" required value="{{ old('order', $nextOrder) }}" 
                            class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-[10px] text-slate-400 mt-0.5">Nomor urut tidak boleh sama</p>
                    </div>
                </div>

                <!-- Palette options -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Pilih Warna Cepat:</label>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="setAddColor('#3b82f6')" class="w-6 h-6 rounded-full bg-blue-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setAddColor('#10b981')" class="w-6 h-6 rounded-full bg-emerald-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setAddColor('#8b5cf6')" class="w-6 h-6 rounded-full bg-purple-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setAddColor('#f59e0b')" class="w-6 h-6 rounded-full bg-amber-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setAddColor('#f43f5e')" class="w-6 h-6 rounded-full bg-rose-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setAddColor('#6366f1')" class="w-6 h-6 rounded-full bg-indigo-500 hover:scale-110 transition-all"></button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 rounded-xl shadow-md shadow-blue-500/20 transition-all-200 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Divisi Baru</span>
                </button>
            </form>
        </div>

        <!-- Right: Division List Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Daftar Divisi Terdaftar</h3>
                            <p class="text-[11px] text-slate-500">Kelola dan update data divisi organisasi</p>
                        </div>
                    </div>

                    <!-- Live search -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="divisionSearchInput" onkeyup="filterDivisions()" placeholder="Cari divisi..." 
                            class="w-full bg-slate-50 text-slate-800 placeholder-slate-400 text-xs rounded-xl pl-9 pr-4 py-2 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600" id="divisionTable">
                        <thead class="bg-slate-50 text-slate-500 font-semibold text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Urutan</th>
                                <th class="py-3 px-4">Nama Divisi</th>
                                <th class="py-3 px-4 text-center">Jumlah Jabatan</th>
                                <th class="py-3 px-4 text-center">Jumlah Dokumen</th>
                                <th class="py-3 px-4 text-center">Aksi (CRUD)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($divisions as $div)
                                <tr class="division-row hover:bg-slate-50/80 transition-all-200" data-name="{{ strtolower($div->name) }}">
                                    <td class="py-4 px-4 font-mono text-xs font-bold text-slate-400">
                                        #{{ $div->order }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-3">
                                            <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-sm" style="background-color: {{ $div->color ?? '#3b82f6' }}"></span>
                                            <div>
                                                <p class="font-bold text-slate-900 text-sm">{{ $div->name }}</p>
                                                <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $div->description ?? 'Tidak ada deskripsi' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="bg-blue-50 text-blue-700 font-bold text-xs px-3 py-1 rounded-full border border-blue-200 inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-id-badge text-[10px]"></i>
                                            <span>{{ $div->positions_count }} Jabatan</span>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="bg-emerald-50 text-emerald-700 font-bold text-xs px-3 py-1 rounded-full border border-emerald-200 inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-file text-[10px]"></i>
                                            <span>{{ $div->documents_count }} File</span>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <!-- Edit Button -->
                                            <button type="button" 
                                                onclick="openEditModal('{{ $div->id }}', '{{ addslashes($div->name) }}', '{{ addslashes($div->description ?? '') }}', '{{ $div->color ?? '#3b82f6' }}', '{{ $div->order }}')"
                                                class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white flex items-center justify-center transition-all-200" title="Edit Divisi">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.divisions.destroy', $div->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Divisi {{ addslashes($div->name) }} dan jabatannya akan dihapus!')">
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
                                    <td colspan="5" class="text-center py-12 text-slate-400 text-xs">
                                        <i class="fa-solid fa-folder-open text-4xl text-slate-300 mb-2"></i>
                                        <p class="font-semibold text-slate-600">Belum ada divisi terdaftar.</p>
                                        <p class="text-[11px] text-slate-400">Gunakan form di samping untuk membuat divisi baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Edit Division Modal Dialog -->
    <div id="editDivisionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden transition-all duration-300">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xl w-full max-w-lg mx-4 transform transition-all scale-95 duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Edit Data Divisi</h3>
                        <p class="text-[11px] text-slate-500">Perbarui informasi divisi organisasi</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="editDivisionForm" action="" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Divisi <span class="text-rose-500">*</span></label>
                    <input type="text" id="editName" name="name" required class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Divisi</label>
                    <textarea id="editDescription" name="description" rows="3" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna Label</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="editColorPicker" name="color" class="w-9 h-9 rounded-xl border border-slate-200 cursor-pointer p-0.5 bg-white">
                            <span id="editColorHex" class="text-xs font-mono text-slate-600 font-semibold">#3b82f6</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan Tampil <span class="text-rose-500">*</span></label>
                        <input type="number" id="editOrder" name="order" min="1" required class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-[10px] text-slate-400 mt-0.5">Nomor urut tidak boleh sama</p>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Pilih Warna Cepat:</label>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="setEditColor('#3b82f6')" class="w-6 h-6 rounded-full bg-blue-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setEditColor('#10b981')" class="w-6 h-6 rounded-full bg-emerald-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setEditColor('#8b5cf6')" class="w-6 h-6 rounded-full bg-purple-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setEditColor('#f59e0b')" class="w-6 h-6 rounded-full bg-amber-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setEditColor('#f43f5e')" class="w-6 h-6 rounded-full bg-rose-500 hover:scale-110 transition-all"></button>
                        <button type="button" onclick="setEditColor('#6366f1')" class="w-6 h-6 rounded-full bg-indigo-500 hover:scale-110 transition-all"></button>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md shadow-amber-600/20 transition-all flex items-center space-x-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Color sync for Add Form
        const addColorPicker = document.getElementById('addColorPicker');
        const addColorHex = document.getElementById('addColorHex');
        if(addColorPicker) {
            addColorPicker.addEventListener('input', (e) => {
                addColorHex.innerText = e.target.value;
            });
        }
        function setAddColor(hex) {
            if(addColorPicker) {
                addColorPicker.value = hex;
                addColorHex.innerText = hex;
            }
        }

        // Color sync for Edit Form
        const editColorPicker = document.getElementById('editColorPicker');
        const editColorHex = document.getElementById('editColorHex');
        if(editColorPicker) {
            editColorPicker.addEventListener('input', (e) => {
                editColorHex.innerText = e.target.value;
            });
        }
        function setEditColor(hex) {
            if(editColorPicker) {
                editColorPicker.value = hex;
                editColorHex.innerText = hex;
            }
        }

        // Modal triggers
        function openEditModal(id, name, description, color, order) {
            const modal = document.getElementById('editDivisionModal');
            const form = document.getElementById('editDivisionForm');
            
            form.action = `/admin/divisions/${id}`;
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description;
            document.getElementById('editColorPicker').value = color || '#3b82f6';
            document.getElementById('editColorHex').innerText = color || '#3b82f6';
            document.getElementById('editOrder').value = order || 0;

            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            const modal = document.getElementById('editDivisionModal');
            modal.classList.add('hidden');
        }

        // Live Filter
        function filterDivisions() {
            const query = document.getElementById('divisionSearchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.division-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                if(name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</x-app-layout>
