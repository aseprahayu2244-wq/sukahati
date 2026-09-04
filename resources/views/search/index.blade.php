<x-app-layout>
    <x-slot name="headerTitle">Pencarian Dokumen HRD</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Pencarian Dokumen</span>
    </x-slot>

    <!-- Search & Filter Form Container -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm mb-8">
        <form action="{{ route('search') }}" method="GET" class="space-y-4">
            
            <!-- Main Query Input -->
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama dokumen, kata kunci deskripsi, atau nama file..." class="w-full bg-slate-50 text-slate-800 placeholder-slate-400 text-sm rounded-2xl pl-12 pr-32 py-4 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-5 text-slate-400 text-base"></i>
                <button type="submit" class="absolute right-2 top-2 bottom-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs px-6 rounded-xl shadow-md transition-all-200">
                    Cari Dokumen
                </button>
            </div>

            <!-- Filters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kategori Dokumen</label>
                    <select name="type" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Kategori Dokumen --</option>
                        <option value="sop" {{ request('type') === 'sop' ? 'selected' : '' }}>SOP (Standard Operating Procedure)</option>
                        <option value="job" {{ request('type') === 'job' ? 'selected' : '' }}>Job Description</option>
                        <option value="job_analysis" {{ request('type') === 'job_analysis' ? 'selected' : '' }}>Job Analysis</option>
                        <option value="bpmn" {{ request('type') === 'bpmn' ? 'selected' : '' }}>BPMN Diagram</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Divisi</label>
                    <select name="division_id" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ (string)request('division_id') === (string)$div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jabatan</label>
                    <select name="position_id" class="w-full bg-slate-50 text-slate-800 text-xs rounded-xl px-4 py-2.5 border border-slate-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Jabatan --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ (string)request('position_id') === (string)$pos->id ? 'selected' : '' }}>
                                {{ $pos->name }} ({{ $pos->division->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

        </form>
    </div>

    <!-- Search Results Section -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Hasil Pencarian Dokumen</h2>
                <p class="text-xs text-slate-500 mt-0.5">Ditemukan <span class="font-bold text-blue-600">{{ $documents->count() }}</span> berkas dokumen yang sesuai</p>
            </div>
            
            @if(request()->hasAny(['q', 'type', 'division_id', 'position_id']))
                <a href="{{ route('search') }}" class="text-xs font-semibold text-rose-600 hover:underline flex items-center space-x-1">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>

        @if($documents->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Nama Dokumen</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Divisi & Jabatan</th>
                            <th class="py-3.5 px-4">Ukuran / Waktu</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($documents as $doc)
                            <tr class="hover:bg-slate-50 transition-all-200">
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base shrink-0 
                                            {{ str_contains($doc->file_type, 'pdf') ? 'bg-red-50 text-red-600' : (str_contains($doc->file_type, 'image') ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600') }}">
                                            <i class="{{ str_contains($doc->file_type, 'pdf') ? 'fa-solid fa-file-pdf' : (str_contains($doc->file_type, 'image') ? 'fa-solid fa-file-image' : 'fa-solid fa-file-lines') }}"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('documents.preview', $doc->id) }}" class="font-bold text-slate-800 text-sm hover:text-blue-600 transition-all-200 line-clamp-1">
                                                {{ $doc->title }}
                                            </a>
                                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $doc->file_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase
                                        {{ $doc->type === 'sop' ? 'bg-emerald-100 text-emerald-700' : ($doc->type === 'job' ? 'bg-amber-100 text-amber-700' : ($doc->type === 'job_analysis' ? 'bg-purple-100 text-purple-700' : 'bg-rose-100 text-rose-700')) }}">
                                        {{ strtoupper(str_replace('_', ' ', $doc->type)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-xs">
                                    <p class="font-semibold text-slate-800">{{ $doc->division->name ?? '-' }}</p>
                                    <p class="text-slate-500">{{ $doc->position->name ?? '-' }}</p>
                                </td>
                                <td class="py-4 px-4 text-xs">
                                    <p class="font-semibold text-slate-700">{{ formatBytes($doc->file_size) }}</p>
                                    <p class="text-slate-400 text-[11px]">{{ $doc->created_at->format('d M Y') }}</p>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('documents.preview', $doc->id) }}" title="Preview Dokumen" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all-200">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('documents.download', $doc->id) }}" title="Download File" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all-200">
                                            <i class="fa-solid fa-download text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <i class="fa-solid fa-magnifying-glass text-4xl text-slate-300 mb-3"></i>
                <p class="text-sm font-semibold text-slate-600">Tidak ada dokumen yang ditemukan</p>
                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau ubah filter pencarian Anda.</p>
            </div>
        @endif
    </div>
</x-app-layout>
