<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Document;
use App\Models\Position;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'divisions'    => Division::count(),
            'positions'    => Position::count(),
            'users'        => User::count(),
            'sop'          => Document::where('type', 'sop')->count(),
            'job'          => Document::where('type', 'job')->count(),
            'job_analysis' => Document::where('type', 'job_analysis')->count(),
            'bpmn'         => Document::where('type', 'bpmn')->count(),
            'total'        => Document::count(),
        ];

        $recentDocuments = Document::with(['division', 'position', 'uploader'])
            ->latest()
            ->take(8)
            ->get();

        $divisions = Division::withCount('positions')
            ->orderBy('order')
            ->take(6)
            ->get();

        return view('dashboard', compact('stats', 'recentDocuments', 'divisions'));
    }
}
