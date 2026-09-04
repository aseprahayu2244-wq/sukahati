<x-app-layout>
    <x-slot name="headerTitle">Pilih Jabatan &bull; {{ $division->name }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('documents.index', $type) }}" class="hover:underline">{{ $typeName }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">{{ $division->name }}</span>
    </x-slot>

    <!-- Top Info Bar -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-amber-500/30">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ $typeName }} &bull; Divisi {{ $division->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pilih folder jabatan di bawah untuk membuka file dokumen {{ $typeName }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('documents.index', $type) }}" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Divisi</span>
            </a>
            @if(auth()->check() && auth()->user()->isKetua())
            <a href="{{ route('documents.create', ['type' => $type, 'division_id' => $division->id]) }}" class="inline-flex items-center space-x-2 text-xs font-bold px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md shadow-blue-500/20 transition-all-200">
                <i class="fa-solid fa-plus"></i>
                <span>Upload Dokumen di Divisi Ini</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Position Folders Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($positions as $position)
            <a href="{{ route('documents.files', [$type, $division->id, $position->id]) }}" class="folder-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm transition-all-200 group relative block">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center text-3xl shadow-sm transition-all-200">
                        <i class="fa-solid fa-folder-closed"></i>
                    </div>
                    <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-100 group-hover:bg-white transition-all-200">
                        {{ $position->documents_count }} File
                    </span>
                </div>
                <h3 class="font-bold text-slate-900 text-lg group-hover:text-blue-600 transition-all-200 line-clamp-1">{{ $position->name }}</h3>
                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $position->description ?? 'Dokumen untuk jabatan ' . $position->name }}</p>
                
                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500 group-hover:text-blue-600">
                    <span>Lihat Berkas Dokumen</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-all-200"></i>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-200">
                <i class="fa-solid fa-folder-open text-5xl text-slate-300 mb-4"></i>
                <h3 class="text-base font-bold text-slate-700">Belum Ada Jabatan di Divisi Ini</h3>
                <p class="text-xs text-slate-500 mt-1">Silakan daftarkan jabatan untuk divisi {{ $division->name }} terlebih dahulu.</p>
                @if(auth()->check() && auth()->user()->isKetua())
                    <a href="{{ route('admin.positions.index') }}" class="inline-flex items-center space-x-2 bg-blue-600 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md mt-4">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Jabatan Sekarang</span>
                    </a>
                @endif
            </div>
        @endforelse
    </div>
</x-app-layout>
