<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Document;
use App\Models\Position;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query   = $request->get('q', '');
        $type    = $request->get('type', '');
        $results = collect();

        if (strlen($query) >= 2) {
            $q = Document::with(['division', 'position', 'uploader'])
                ->where(function ($db) use ($query) {
                    $db->where('title', 'like', "%{$query}%")
                       ->orWhere('description', 'like', "%{$query}%")
                       ->orWhereHas('division', fn($d) => $d->where('name', 'like', "%{$query}%"))
                       ->orWhereHas('position', fn($p) => $p->where('name', 'like', "%{$query}%"));
                });

            if ($type && in_array($type, ['sop', 'job', 'job_analysis', 'bpmn'])) {
                $q->where('type', $type);
            }

            $results = $q->latest()->paginate(20)->withQueryString();
        }

        $typeLabels = Document::$typeLabels;

        return view('search.index', compact('query', 'type', 'results', 'typeLabels'));
    }
}
