<?php

namespace App\Http\Controllers;

use App\Models\Bod;
use App\Models\Division;
use App\Models\Document;
use App\Models\Position;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalBods       = Bod::count();
        $totalDepartments = Division::count();
        $totalPositions  = Position::count();

        $docStats = [
            'sop'      => Document::where('type', 'sop')->count(),
            'job_desk' => Document::where('type', 'job_desk')->count(),
            'bpm'      => Document::where('type', 'bpm')->count(),
            'struktur' => Document::where('type', 'struktur')->count(),
        ];

        $totalDocuments = array_sum($docStats);

        $recentDocuments = Document::with(['bod', 'division', 'position', 'uploader'])
            ->latest()
            ->take(8)
            ->get();

        $bods = Bod::withCount('departments')
            ->orderBy('order')
            ->take(6)
            ->get();

        // Super Admin also sees user stats
        $totalUsers = null;
        $recentUsers = collect();
        if ($user->isSuperAdmin()) {
            $totalUsers  = User::count();
            $recentUsers = User::latest()->take(5)->get();
        }

        return view('dashboard', compact(
            'totalBods',
            'totalDepartments',
            'totalPositions',
            'totalDocuments',
            'docStats',
            'recentDocuments',
            'bods',
            'totalUsers',
            'recentUsers'
        ));
    }
}
