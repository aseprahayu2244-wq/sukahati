<x-app-layout>
    <x-slot name="headerTitle">Pemberitahuan Sistem</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:underline">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-800 font-semibold">Notifikasi</span>
    </x-slot>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Notifikasi & Riwayat Persetujuan</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pantau status pengajuan, hasil evaluasi, dan persetujuan SOP secara real-time.</p>
            </div>
        </div>

        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.markAll') }}">
                @csrf
                <button type="submit" class="inline-flex items-center space-x-2 text-xs font-bold px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all-200">
                    <i class="fa-solid fa-check-double text-blue-600"></i>
                    <span>Tandai Semua Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200 mb-6 pb-2">
        <a href="{{ route('notifications.index', ['tab' => 'all']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold flex items-center space-x-2 transition-all-200 {{ $tab === 'all' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>Semua</span>
        </a>
        <a href="{{ route('notifications.index', ['tab' => 'unread']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold flex items-center space-x-2 transition-all-200 {{ $tab === 'unread' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>Belum Dibaca</span>
            @if($unreadCount > 0)
                <span class="text-[10px] px-2 py-0.5 rounded-full {{ $tab === 'unread' ? 'bg-blue-700 text-white' : 'bg-rose-500 text-white' }} font-bold">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>
    </div>

    <!-- Notifications List -->
    <div class="space-y-3 mb-6">
        @forelse($notifications as $notif)
            <a href="{{ route('notifications.read', $notif->id) }}" 
               class="block bg-white rounded-2xl p-5 border {{ $notif->isRead() ? 'border-slate-200/80' : 'border-blue-300 bg-blue-50/20' }} shadow-sm hover:shadow-md transition-all-200 group">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 {{ $notif->getBgClass() }} shadow-sm">
                        <i class="fa-solid {{ $notif->getIcon() }}"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h3 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                {{ $notif->title }}
                            </h3>
                            <span class="text-[11px] text-slate-400 shrink-0">
                                {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $notif->message }}
                        </p>

                        @if($notif->sop)
                            <div class="mt-2.5 flex items-center space-x-2 text-[11px] font-semibold text-blue-700">
                                <span>Lihat SOP:</span>
                                <span class="bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md text-blue-800 font-bold">
                                    {{ $notif->sop->nama_sop }} ({{ $notif->sop->nomor_dokumen }})
                                </span>
                                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        @elseif($notif->bpm)
                            <div class="mt-2.5 flex items-center space-x-2 text-[11px] font-semibold text-rose-700">
                                <span>Lihat BPM:</span>
                                <span class="bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-md text-rose-800 font-bold">
                                    {{ $notif->bpm->nama_bpm }} ({{ $notif->bpm->nomor_dokumen }})
                                </span>
                                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        @endif
                    </div>

                    @if(!$notif->isRead())
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-600 shrink-0 self-center" title="Belum dibaca"></div>
                    @endif
                </div>
            </a>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-4">
                    <i class="fa-regular fa-bell-slash"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Notifikasi</h3>
                <p class="text-xs text-slate-500 mt-1">Saat ini Anda tidak memiliki pemberitahuan baru.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div>
            {{ $notifications->links() }}
        </div>
    @endif
</x-app-layout>

