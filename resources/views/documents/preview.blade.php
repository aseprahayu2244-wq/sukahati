<x-app-layout>
    <x-slot name="headerTitle">Preview: {{ $document->title }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('documents.index', $document->type) }}" class="hover:underline">{{ strtoupper($document->type) }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Preview Dokumen</span>
    </x-slot>

    <!-- Top Action Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <button onclick="history.back()" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </button>
            <h1 class="text-lg font-bold text-slate-900 truncate max-w-md">{{ $document->title }}</h1>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('documents.download', $document->id) }}" class="inline-flex items-center space-x-2 text-xs font-bold px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md shadow-emerald-500/20 transition-all-200">
                <i class="fa-solid fa-download"></i>
                <span>Unduh File ({{ formatBytes($document->file_size) }})</span>
            </a>

            @if(auth()->check() && auth()->user()->isKetua())
                <a href="{{ route('documents.edit', $document->id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-md transition-all-200">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Edit</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content: Viewer + Metadata -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Viewer Container (3 columns) -->
        <div class="lg:col-span-3 bg-slate-900 rounded-3xl overflow-hidden shadow-xl border border-slate-800 flex flex-col min-h-[600px]">
            <div class="bg-slate-950 px-6 py-3 border-b border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center space-x-2">
                    <i class="fa-solid fa-eye text-blue-400"></i>
                    <span>Tampilan Berkas {{ strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION)) }}</span>
                </span>
                <span>{{ $document->file_name }}</span>
            </div>

            <div class="flex-1 flex items-center justify-center p-4 bg-slate-900">
                @php
                    $ext = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
                    $fileUrl = asset('storage/' . $document->file_path);
                @endphp

                @if(in_array($ext, ['pdf']))
                    <!-- PDF Viewer -->
                    <iframe src="{{ $fileUrl }}" class="w-full h-full min-h-[650px] rounded-xl border-0"></iframe>

                @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                    <!-- Image Viewer -->
                    <div class="text-center">
                        <img src="{{ $fileUrl }}" alt="{{ $document->title }}" class="max-h-[600px] max-w-full rounded-xl object-contain mx-auto shadow-2xl">
                    </div>

                @elseif(in_array($ext, ['mp4', 'webm', 'ogg']))
                    <!-- Video Player -->
                    <video controls class="max-h-[600px] w-full rounded-xl shadow-2xl">
                        <source src="{{ $fileUrl }}" type="{{ $document->file_type }}">
                        Browser Anda tidak mendukung pemutaran video.
                    </video>

                @elseif(in_array($ext, ['mp3', 'wav']))
                    <!-- Audio Player -->
                    <div class="text-center p-8 bg-slate-800 rounded-2xl border border-slate-700">
                        <i class="fa-solid fa-music text-5xl text-blue-400 mb-4"></i>
                        <audio controls class="w-full max-w-md mx-auto">
                            <source src="{{ $fileUrl }}" type="{{ $document->file_type }}">
                        </audio>
                    </div>

                @else
                    <!-- Office Doc / Fallback Preview Card -->
                    <div class="text-center py-16 px-6 max-w-md">
                        <div class="w-20 h-20 rounded-3xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-4xl mx-auto mb-4 border border-blue-500/20">
                            <i class="fa-solid fa-file-word"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-1">{{ $document->file_name }}</h3>
                        <p class="text-xs text-slate-400 mb-6">Format file .{{ $ext }} dapat diunduh untuk dibuka pada perangkat Anda.</p>
                        <a href="{{ route('documents.download', $document->id) }}" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-lg transition-all-200">
                            <i class="fa-solid fa-download"></i>
                            <span>Unduh File Sekarang</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Metadata (1 column) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3 flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    <span>Informasi Dokumen</span>
                </h3>

                <div class="space-y-4 text-xs">
                    <div>
                        <p class="text-slate-400 font-semibold">Judul Dokumen</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $document->title }}</p>
                    </div>

                    <div>
                        <p class="text-slate-400 font-semibold">Kategori Dokumen</p>
                        <span class="inline-block font-bold text-[10px] px-2.5 py-1 rounded-full uppercase mt-1
                            {{ $document->type === 'sop' ? 'bg-emerald-100 text-emerald-700' : ($document->type === 'job' ? 'bg-amber-100 text-amber-700' : ($document->type === 'job_analysis' ? 'bg-purple-100 text-purple-700' : 'bg-rose-100 text-rose-700')) }}">
                            {{ strtoupper(str_replace('_', ' ', $document->type)) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-slate-400 font-semibold">Divisi Organisasi</p>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $document->division->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-slate-400 font-semibold">Jabatan Terkait</p>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $document->position->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-slate-400 font-semibold">Deskripsi</p>
                        <p class="text-slate-600 mt-0.5 leading-relaxed">{{ $document->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nama File:</span>
                            <span class="font-semibold text-slate-700 truncate max-w-[140px]">{{ $document->file_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Ukuran File:</span>
                            <span class="font-semibold text-slate-700">{{ formatBytes($document->file_size) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Pengunggah:</span>
                            <span class="font-semibold text-slate-700">{{ $document->uploader->name ?? 'System' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Waktu Upload:</span>
                            <span class="font-semibold text-slate-700">{{ $document->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Button -->
            <a href="{{ route('documents.download', $document->id) }}" class="w-full text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs py-3 rounded-xl shadow-lg shadow-emerald-500/20 transition-all-200 block">
                <i class="fa-solid fa-download mr-1"></i> Download Berkas
            </a>
        </div>

    </div>
</x-app-layout>
