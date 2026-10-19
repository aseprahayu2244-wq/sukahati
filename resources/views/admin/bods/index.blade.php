<x-app-layout>
    <x-slot name="headerTitle">Kelola BOD</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Kelola BOD</span>
    </x-slot>

    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 mb-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="flex items-center space-x-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-blue-600/30 border border-blue-500/30 flex items-center justify-center text-2xl text-blue-400 shadow-inner">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold tracking-tight">Manajemen BOD (Board of Directors)</h1>
                <p class="text-xs text-slate-300 mt-1">Kelola daftar BOD sebagai tingkat tertinggi folder dokumen.</p>
            </div>
        </div>

        <div class="flex items-center space-x-4 relative z-10 shrink-0">
            <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/10 text-center">
                <p class="text-[10px] uppercase tracking-wider font-bold text-slate-300">Total BOD</p>
                <p class="text-lg font-extrabold text-white">{{ $bods->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-xl mb-4 text-[11px] shadow-sm">
            <div class="flex items-center space-x-1.5 font-bold mb-1">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-xs"></i>
                <span>Terdapat kesalahan:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Form Tambah BOD -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm h-fit">
            <div class="flex items-center space-x-3 mb-5 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Tambah BOD Baru</h3>
                    <p class="text-[11px] text-slate-500">Buat folder BOD baru</p>
                </div>
            </div>

            <form action="{{ route('admin.bods.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama BOD <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: BOD A, BOD B..." 
                        class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi BOD</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi BOD..." 
                        class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna</label>
                        <input type="color" name="color" value="{{ old('color', '#3b82f6') }}" 
                            class="w-full h-9 rounded-lg border border-slate-200 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan <span class="text-rose-500">*</span></label>
                        <input type="number" name="order" min="1" required value="{{ old('order', $nextOrder) }}" 
                            class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 rounded-xl shadow-md shadow-blue-500/20 transition-all-200 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah BOD</span>
                </button>
            </form>
        </div>

        <!-- Right: BOD List -->
        <div class="lg:col-span-2 space-y-4">
            @forelse($bods as $bod)
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl shadow-sm" style="background-color: {{ $bod->color }}">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-base">{{ $bod->name }}</h4>
                                <p class="text-[11px] text-slate-500">{{ $bod->description ?? 'Tidak ada deskripsi' }}</p>
                                <div class="flex items-center space-x-3 mt-1">
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-semibold">
                                        Urutan #{{ $bod->order }}
                                    </span>
                                    <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-semibold">
                                        {{ $bod->departments_count ?? 0 }} Departemen
                                    </span>
                                    <span class="text-[10px] bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full font-semibold">
                                        {{ $bod->documents_count ?? 0 }} Dokumen
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Edit Button -->
                            <button onclick="openEditModal({{ $bod->id }}, '{{ addslashes($bod->name) }}', '{{ addslashes($bod->description ?? '') }}', '{{ $bod->color }}', {{ $bod->order }})"
                                class="px-3 py-1.5 text-xs font-semibold bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition-all-200 flex items-center space-x-1">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit</span>
                            </button>

                            <!-- Delete Button -->
                            <form action="{{ route('admin.bods.destroy', $bod->id) }}" method="POST" 
                                onsubmit="return confirmDelete(event, 'BOD {{ addslashes($bod->name) }} akan dihapus. Pastikan tidak ada departemen di dalamnya!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg transition-all-200 flex items-center space-x-1">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                    <i class="fa-solid fa-building text-5xl text-slate-200 mb-4"></i>
                    <h3 class="text-base font-bold text-slate-700">Belum Ada BOD</h3>
                    <p class="text-xs text-slate-500 mt-1">Tambahkan BOD pertama menggunakan form di sebelah kiri.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl mx-4">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Edit BOD</h3>
                        <p class="text-[11px] text-slate-500">Perbarui data BOD</p>
                    </div>
                </div>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama BOD <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="editName" required 
                        class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi</label>
                    <textarea name="description" id="editDescription" rows="3" 
                        class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna</label>
                        <input type="color" name="color" id="editColor" 
                            class="w-full h-9 rounded-lg border border-slate-200 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan <span class="text-rose-500">*</span></label>
                        <input type="number" name="order" id="editOrder" min="1" required 
                            class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-3 rounded-xl transition-all-200">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs py-3 rounded-xl shadow-md transition-all-200 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const routeBase = '{{ url('/admin/bods') }}';

        function openEditModal(id, name, description, color, order) {
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description;
            document.getElementById('editColor').value = color;
            document.getElementById('editOrder').value = order;
            document.getElementById('editForm').action = routeBase + '/' + id;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
    </script>
</x-app-layout>
