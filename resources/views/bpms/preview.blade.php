<x-app-layout>
    <x-slot name="headerTitle">Preview Dokumen &bull; {{ $bpm->nama_bpm }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('bpms.index') }}" class="hover:underline">Workflow BPM</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('bpms.show', $bpm->id) }}" class="hover:underline">{{ $bpm->nama_bpm }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Preview Dokumen Resmi</span>
    </x-slot>

    <!-- Top Action Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Pratinjau Dokumen BPM</h1>
            <p class="text-xs text-slate-500 mt-0.5">Tampilan format standar dokumen resmi Business Process Management.</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('bpms.show', $bpm->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all-200">
                <i class="fa-solid fa-arrow-left mr-1"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('bpms.print', $bpm->id) }}" target="_blank" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all-200 shadow-sm shadow-indigo-600/30">
                <i class="fa-solid fa-print mr-1"></i>
                <span>Cetak / Download PDF</span>
            </a>
        </div>
    </div>

    <!-- Official Document Sheet Container -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-lg max-w-5xl mx-auto mb-12">
        
        <!-- Header Kop Dokumen Resmi -->
        <div class="border-b-2 border-slate-900 pb-6 mb-8 flex items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-xl bg-white p-2 border border-slate-200 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo-sukahati.png') }}" alt="CV. Sukahati Pratama" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-wider">CV. SUKAHATI PRATAMA</h2>
                    <p class="text-xs text-slate-600 font-bold uppercase tracking-widest mt-0.5">SISTEM MANAJEMEN OPERASIONAL & PROSES BISNIS</p>
                    <p class="text-[11px] text-slate-400">Jl. Sukahati No. 123, Cibinong, Bogor &bull; Telp: (021) 87654321</p>
                </div>
            </div>

            <div class="text-right">
                <div class="inline-block bg-slate-900 text-white px-3 py-1 rounded font-extrabold text-xs uppercase tracking-widest mb-1">
                    DOKUMEN BPM
                </div>
                <p class="font-mono text-xs font-bold text-slate-700">{{ $bpm->nomor_dokumen }}</p>
                <p class="text-[11px] text-slate-500 font-medium">Versi: {{ $bpm->version }}</p>
            </div>
        </div>

        <!-- Meta Table -->
        <table class="w-full text-xs text-slate-800 border border-slate-300 mb-8">
            <tbody>
                <tr class="border-b border-slate-200">
                    <td class="w-1/4 bg-slate-50 p-2.5 font-bold uppercase tracking-wider text-[10px] text-slate-600 border-r border-slate-300">Nama BPM</td>
                    <td class="p-2.5 font-bold text-sm text-slate-900" colspan="3">{{ $bpm->nama_bpm }}</td>
                </tr>
                <tr class="border-b border-slate-200">
                    <td class="w-1/4 bg-slate-50 p-2.5 font-bold uppercase tracking-wider text-[10px] text-slate-600 border-r border-slate-300">Nomor Dokumen</td>
                    <td class="w-1/4 p-2.5 font-mono font-bold">{{ $bpm->nomor_dokumen }}</td>
                    <td class="w-1/4 bg-slate-50 p-2.5 font-bold uppercase tracking-wider text-[10px] text-slate-600 border-r border-l border-slate-300">Tanggal Efektif</td>
                    <td class="w-1/4 p-2.5 font-semibold">{{ $bpm->tanggal_efektif ? $bpm->tanggal_efektif->format('d F Y') : '-' }}</td>
                </tr>
                <tr class="border-b border-slate-200">
                    <td class="bg-slate-50 p-2.5 font-bold uppercase tracking-wider text-[10px] text-slate-600 border-r border-slate-300">Direktorat (BOD)</td>
                    <td class="p-2.5 font-semibold">{{ $bpm->bod?->name ?? '-' }}</td>
                    <td class="bg-slate-50 p-2.5 font-bold uppercase tracking-wider text-[10px] text-slate-600 border-r border-l border-slate-300">Departemen</td>
                    <td class="p-2.5 font-semibold">{{ $bpm->department?->name ?? '-' }}</td>
                </tr>
                @if($bpm->deskripsi)
                <tr>
                    <td class="bg-slate-50 p-2.5 font-bold uppercase tracking-wider text-[10px] text-slate-600 border-r border-slate-300">Deskripsi / Ruang Lingkup</td>
                    <td class="p-2.5" colspan="3">{{ $bpm->deskripsi }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Hierarki & Matriks RACI Table -->
        @if($bpm->isBuilder())
            <div class="mb-10">
                <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-900 mb-3 pb-1 border-b border-slate-300">
                    BAGIAN I &bull; TAHAPAN PROSES BISNIS (L1, L2, L3) & MATRIKS TANGGUNG JAWAB RACI
                </h3>

                <div class="border border-slate-300 rounded-lg overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-100 text-slate-800 font-extrabold uppercase text-[10px] border-b border-slate-300">
                            <tr>
                                <th class="py-2.5 px-3 border-r border-slate-300 min-w-[280px]">Uraian Aktivitas (Hierarki L1-L3)</th>
                                <th class="py-2.5 px-3 border-r border-slate-300 w-36 text-center">Responsible (R)</th>
                                <th class="py-2.5 px-3 border-r border-slate-300 w-36 text-center">Accountable (A)</th>
                                <th class="py-2.5 px-3 border-r border-slate-300 w-36 text-center">Consulted (C)</th>
                                <th class="py-2.5 px-3 w-36 text-center">Informed (I)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($bpm->steps as $step)
                                @php
                                    $indent = match($step->level) {
                                        'L1' => 'pl-3 font-bold bg-slate-50/70 text-slate-900',
                                        'L2' => 'pl-8 font-semibold text-slate-800',
                                        'L3' => 'pl-14 text-slate-700',
                                        default => 'pl-3',
                                    };
                                    $prefix = match($step->level) {
                                        'L1' => '',
                                        'L2' => '↳ ',
                                        'L3' => '↳ ↳ ',
                                        default => '',
                                    };
                                @endphp
                                <tr>
                                    <td class="py-2 px-3 border-r border-slate-300 {{ $indent }}">
                                        <span class="font-mono text-slate-400 text-xs">{{ $prefix }}</span>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-extrabold mr-1 border {{ $step->getLevelBadgeClass() }}">
                                            {{ $step->level }}
                                        </span>
                                        {{ $step->activity_description }}
                                    </td>
                                    <td class="py-2 px-2.5 text-[11px] border-r border-slate-300 text-center">
                                        {{ $step->responsible->pluck('position.name')->join(', ') ?: '-' }}
                                    </td>
                                    <td class="py-2 px-2.5 text-[11px] border-r border-slate-300 text-center font-semibold">
                                        {{ $step->accountable->pluck('position.name')->join(', ') ?: '-' }}
                                    </td>
                                    <td class="py-2 px-2.5 text-[11px] border-r border-slate-300 text-center">
                                        {{ $step->consulted->pluck('position.name')->join(', ') ?: '-' }}
                                    </td>
                                    <td class="py-2 px-2.5 text-[11px] text-center">
                                        {{ $step->informed->pluck('position.name')->join(', ') ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-slate-400 italic">Belum ada tahapan proses.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-[10px] text-slate-500 italic">
                    * Keterangan RACI: <strong>R</strong> (Responsible - Pelaksana), <strong>A</strong> (Accountable - Penanggung Jawab Utama), <strong>C</strong> (Consulted - Pihak Berkonsultasi), <strong>I</strong> (Informed - Pihak Diberi Informasi).
                </div>
            </div>
        @else
            <!-- Uploaded Document Details -->
            <div class="mb-10">
                <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-900 mb-3 pb-1 border-b border-slate-300">
                    BAGIAN I &bull; BERKAS DOKUMEN PROSES BISNIS
                </h3>
                <div class="p-6 bg-slate-50 border border-slate-300 rounded-lg text-center">
                    @foreach($bpm->attachments as $att)
                        <i class="fa-solid fa-file-pdf text-4xl text-rose-500 mb-2"></i>
                        <p class="font-bold text-sm text-slate-800">{{ $att->file_name }}</p>
                        <p class="text-xs text-slate-500">{{ $att->formatted_size }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Lembar Pengesahan Resmi -->
        <div class="mt-12 pt-6 border-t border-slate-300">
            <h3 class="text-center font-bold text-xs uppercase tracking-widest text-slate-700 mb-8">LEMBAR PENGESAHAN DOKUMEN</h3>
            <div class="grid grid-cols-2 gap-8 text-center text-xs">
                <!-- Diajukan oleh -->
                <div class="border border-slate-200 p-4 rounded-xl">
                    <p class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-1">Diajukan Oleh,</p>
                    <p class="text-[11px] text-slate-400 mb-14">Admin Penyusun BPM</p>
                    <p class="font-extrabold text-slate-900 underline">{{ $bpm->creator?->name ?? 'Admin Departemen' }}</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Tanggal: {{ $bpm->submitted_at ? $bpm->submitted_at->format('d/m/Y') : '-' }}</p>
                </div>

                <!-- Disetujui oleh -->
                <div class="border border-slate-200 p-4 rounded-xl">
                    <p class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-1">Disetujui & Disahkan Oleh,</p>
                    <p class="text-[11px] text-slate-400 mb-14">Super Admin / Manajemen Eksekutif</p>
                    <p class="font-extrabold text-slate-900 underline">{{ $bpm->approver?->name ?? '( Belum Disetujui )' }}</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        Tanggal: {{ $bpm->approved_at ? $bpm->approved_at->format('d/m/Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

