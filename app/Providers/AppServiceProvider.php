<?php

namespace App\Providers;

use App\Models\Bpm;
use App\Models\Notification;
use App\Models\Sop;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $unreadNotifsCount = Notification::where('user_id', $user->id)->unread()->count();
                $recentNotifs = Notification::where('user_id', $user->id)->latest('created_at')->take(5)->get();
                
                $pendingSopCount = 0;
                $rejectedSopCount = 0;
                $pendingBpmCount = 0;
                $rejectedBpmCount = 0;
                
                if ($user->isSuperAdmin()) {
                    $pendingSopCount = Sop::where('status', Sop::STATUS_PENDING)->count();
                    $pendingBpmCount = Bpm::where('status', Bpm::STATUS_PENDING)->count();
                } elseif ($user->isAdmin()) {
                    $rejectedSopCount = Sop::where('status', Sop::STATUS_REJECTED)->where('created_by', $user->id)->count();
                    $pendingSopCount = Sop::where('status', Sop::STATUS_PENDING)->where('created_by', $user->id)->count();
                    $rejectedBpmCount = Bpm::where('status', Bpm::STATUS_REJECTED)->where('created_by', $user->id)->count();
                    $pendingBpmCount = Bpm::where('status', Bpm::STATUS_PENDING)->where('created_by', $user->id)->count();
                }

                $view->with([
                    'globalUnreadNotifsCount' => $unreadNotifsCount,
                    'globalRecentNotifs' => $recentNotifs,
                    'globalPendingSopCount' => $pendingSopCount,
                    'globalRejectedSopCount' => $rejectedSopCount,
                    'globalPendingBpmCount' => $pendingBpmCount,
                    'globalRejectedBpmCount' => $rejectedBpmCount,
                ]);
            }
        });
    }
}
