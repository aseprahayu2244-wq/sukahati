<x-app-layout>
    <x-slot name="headerTitle">{{ $position->name }} &bull; {{ $typeName }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('documents.index', $type) }}" class="hover:underline">{{ $typeName }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('documents.positions', [$type, $division->id]) }}" class="hover:underline">{{ $division->name }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">{{ $position->name }}</span>
    </x-slot>

    <!-- Header Section -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ $typeName }} &bull; {{ $position->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Divisi: <span class="font-semibold text-slate-700">{{ $division->name }}</span> &bull; Total {{ $documents->count() }} berkas dokumen</p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('documents.positions', [$type, $division->id]) }}" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>

            @if(auth()->check() && auth()->user()->isKetua())
            <a href="{{ route('documents.create', ['type' => $type, 'division_id' => $division->id, 'position_id' => $position->id]) }}" class="inline-flex items-center space-x-2 text-xs font-bold px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md shadow-blue-500/20 transition-all-200">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Upload Dokumen Di Sini</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <input type="text" id="fileFilterInput" onkeyup="filterFiles()" placeholder="Cari berkas dalam folder..." class="w-full bg-slate-50 text-slate-800 placeholder-slate-400 text-xs rounded-xl pl-9 pr-4 py-2.5 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
        </div>

        <div class="flex items-center space-x-2 text-xs text-slate-500">
            <span class="font-semibold">Urutkan: Terbaru</span>
        </div>
    </div>

    <!-- File Cards Grid -->
    <div id="fileGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($documents as $doc)
            <div class="file-item bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-all-200 flex flex-col justify-between" data-title="{{ strtolower($doc->title) }} {{ strtolower($doc->file_name) }}">
                
                <div>
                    <!-- Top File Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 
                            {{ str_contains($doc->file_type, 'pdf') ? 'bg-red-50 text-red-600 border border-red-200' : (str_contains($doc->file_type, 'image') ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-blue-50 text-blue-600 border border-blue-200') }}">
                            <i class="{{ str_contains($doc->file_type, 'pdf') ? 'fa-solid fa-file-pdf' : (str_contains($doc->file_type, 'image') ? 'fa-solid fa-file-image' : 'fa-solid fa-file-lines') }}"></i>
                        </div>

                        <!-- Dropdown Menu / Quick actions -->
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('documents.preview', $doc->id) }}" title="Preview Dokumen" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all-200">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('documents.download', $doc->id) }}" title="Download File" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all-200">
                                <i class="fa-solid fa-download text-xs"></i>
                            </a>
                        </div>
                    </div>

                    <!-- File Details -->
                    <h3 class="font-bold text-slate-900 text-base line-clamp-2 leading-snug hover:text-blue-600 transition-all-200">
                        <a href="{{ route('documents.preview', $doc->id) }}">{{ $doc->title }}</a>
                    </h3>

                    @if($doc->description)
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $doc->description }}</p>
                    @endif
                </div>

                <!-- Footer details -->
                <div class="mt-5 pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span class="truncate max-w-[120px]" title="{{ $doc->file_name }}">{{ $doc->file_name }}</span>
                        <span class="font-semibold text-slate-600">{{ formatBytes($doc->file_size) }}</span>
                    </div>

                    <div class="flex items-center justify-between mt-3 pt-2">
                        <span class="text-[11px] text-slate-400 flex items-center space-x-1">
                            <i class="fa-regular fa-clock"></i>
                            <span>{{ $doc->created_at->format('d M Y') }}</span>
                        </span>

                        @if(auth()->check() && auth()->user()->isKetua())
                            <div class="flex items-center space-x-1">
                                <a href="{{ route('documents.edit', $doc->id) }}" class="p-1 text-slate-400 hover:text-amber-600 text-xs" title="Edit Dokumen">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Berkas {{ addslashes($doc->title) }} akan dihapus permanen!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 text-xs" title="Hapus Dokumen">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-200">
                <i class="fa-solid fa-file-circle-xmark text-5xl text-slate-300 mb-4"></i>
                <h3 class="text-base font-bold text-slate-700">Belum Ada Dokumen</h3>
                <p class="text-xs text-slate-500 mt-1">Belum ada file {{ $typeName }} yang diunggah untuk posisi {{ $position->name }}.</p>
                @if(auth()->check() && auth()->user()->isKetua())
                    <a href="{{ route('documents.create', ['type' => $type, 'division_id' => $division->id, 'position_id' => $position->id]) }}" class="inline-flex items-center space-x-2 bg-blue-600 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md mt-4">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Dokumen Pertama</span>
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <script>
        function filterFiles() {
            let input = document.getElementById('fileFilterInput').value.toLowerCase();
            let items = document.getElementsByClassName('file-item');
            
            for (let i = 0; i < items.length; i++) {
                let title = items[i].getAttribute('data-title');
                if (title.includes(input)) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }
    </script>
</x-app-layout>
