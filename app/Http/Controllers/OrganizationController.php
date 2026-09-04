<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $divisions = Division::with(['positions' => function ($q) {
            $q->orderBy('level')->orderBy('order');
        }])->orderBy('order')->get();

        // Build tree structure for org chart
        $tree = $this->buildTree($divisions);

        return view('organization.index', compact('divisions', 'tree'));
    }

    public function setParent(Request $request, Position $position)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:positions,id',
        ]);

        $position->update(['parent_id' => $request->parent_id]);
        return back()->with('success', 'Hierarki jabatan berhasil diperbarui!');
    }

    private function buildTree($divisions): array
    {
        $tree = [];
        foreach ($divisions as $division) {
            $divNode = [
                'id'       => 'div_' . $division->id,
                'name'     => $division->name,
                'color'    => $division->color,
                'type'     => 'division',
                'children' => [],
            ];

            // Get root positions (no parent)
            $rootPositions = $division->positions->whereNull('parent_id');
            foreach ($rootPositions as $pos) {
                $divNode['children'][] = $this->buildPositionNode($pos, $division->positions);
            }

            $tree[] = $divNode;
        }
        return $tree;
    }

    private function buildPositionNode($position, $allPositions): array
    {
        $node = [
            'id'       => 'pos_' . $position->id,
            'name'     => $position->name,
            'type'     => 'position',
            'children' => [],
        ];

        $children = $allPositions->where('parent_id', $position->id);
        foreach ($children as $child) {
            $node['children'][] = $this->buildPositionNode($child, $allPositions);
        }

        return $node;
    }
}
