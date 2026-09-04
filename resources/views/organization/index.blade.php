<x-app-layout>
    <x-slot name="headerTitle">Struktur Organisasi Perusahaan</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Struktur Organisasi</span>
    </x-slot>

    <!-- Top Banner -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-purple-500/20">
                <i class="fa-solid fa-sitemap"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Bagan Struktur Organisasi & Hirarki Jabatan</h1>
                <p class="text-xs text-slate-500 mt-0.5">Visualisasi hierarki manajerial dan hubungan kerja antar divisi & jabatan</p>
            </div>
        </div>

        @if(auth()->check() && auth()->user()->isKetua())
            <a href="{{ route('admin.positions.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-md shadow-purple-500/20 transition-all-200">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Atur Atasan Direct Jabatan</span>
            </a>
        @endif
    </div>

    <!-- Divisions Organization Tree -->
    <div class="space-y-8">
        @foreach($divisions as $division)
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Divisi {{ $division->name }}</h2>
                            <p class="text-xs text-slate-500">{{ $division->description ?? 'Hierarki posisi dalam divisi ' . $division->name }}</p>
                        </div>
                    </div>
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $division->positions->count() }} Position Nodes
                    </span>
                </div>

                <!-- Tree nodes -->
                @if($division->positions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($division->positions as $pos)
                            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 relative transition-all-200 hover:bg-white hover:shadow-md hover:border-blue-300">
                                
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="inline-block bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase mb-2">
                                            Level {{ $pos->level ?? 1 }}
                                        </span>
                                        <h3 class="font-bold text-slate-800 text-base">{{ $pos->name }}</h3>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-white text-indigo-600 flex items-center justify-center text-lg shadow-sm border border-slate-200">
                                        <i class="fa-solid fa-id-badge"></i>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-slate-200/60 text-xs text-slate-500 space-y-1">
                                    <div class="flex justify-between">
                                        <span>Atasan Direct:</span>
                                        <span class="font-semibold text-slate-700">{{ $pos->parent->name ?? 'Puncak / Kepala Divisi' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total Dokumen HRD:</span>
                                        <span class="font-bold text-emerald-600">{{ $pos->documents_count ?? 0 }} File</span>
                                    </div>
                                </div>

                                <!-- Quick document links -->
                                <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-semibold">
                                    <a href="{{ route('documents.files', ['sop', $division->id, $pos->id]) }}" class="text-emerald-600 hover:underline">
                                        <i class="fa-solid fa-folder mr-1"></i> File Dokumen
                                    </a>

                                    @if(auth()->check() && auth()->user()->isKetua())
                                        <button onclick="openParentModal({{ $pos->id }}, '{{ $pos->name }}', {{ $pos->parent_id ?? 'null' }})" class="text-blue-600 hover:underline">
                                            <i class="fa-solid fa-diagram-next"></i> Set Atasan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Belum ada jabatan terdaftar di divisi ini.
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Parent Assignment Modal (Ketua Only) -->
    @if(auth()->check() && auth()->user()->isKetua())
        <div id="parentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-base" id="modalTitle">Set Direct Manager / Atasan</h3>
                    <button onclick="closeParentModal()" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form id="parentForm" method="POST" action="">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Atasan Direct</label>
                        <select name="parent_id" id="modalParentSelect" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-3 border border-slate-200 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Puncak / Tidak Ada Atasan --</option>
                            @foreach($divisions as $div)
                                <optgroup label="{{ $div->name }}">
                                    @foreach($div->positions as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $div->name }})</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" onclick="closeParentModal()" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20">Simpan Relasi</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openParentModal(posId, posName, parentId) {
                document.getElementById('modalTitle').innerText = 'Set Direct Manager: ' + posName;
                document.getElementById('parentForm').action = '/organization/positions/' + posId + '/set-parent';
                document.getElementById('modalParentSelect').value = parentId || '';
                document.getElementById('parentModal').classList.remove('hidden');
            }

            function closeParentModal() {
                document.getElementById('parentModal').classList.add('hidden');
            }
        </script>
    @endif
</x-app-layout>
