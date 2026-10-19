<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions   = Position::with('division.bod')->orderBy('order')->get();
        $departments = Division::with('bod')->orderBy('order')->get();
        return view('admin.positions.index', compact('positions', 'departments'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->canManageDepartment(), 403);

        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:positions,id',
        ]);

        $maxOrder = Position::where('division_id', $request->division_id)->max('order') ?? 0;
        Position::create([
            'division_id' => $request->division_id,
            'name'        => $request->name,
            'description' => $request->description,
            'parent_id'   => $request->parent_id,
            'level'       => $request->parent_id ? 1 : 0,
            'order'       => $maxOrder + 1,
        ]);

        return back()->with('success', 'Jabatan berhasil ditambahkan!');
    }

    public function update(Request $request, Position $position)
    {
        abort_unless(auth()->user()->canManageDepartment(), 403);

        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:positions,id',
        ]);

        $position->update([
            'division_id' => $request->division_id,
            'name'        => $request->name,
            'description' => $request->description,
            'parent_id'   => $request->parent_id,
        ]);

        return back()->with('success', 'Jabatan berhasil diperbarui!');
    }

    public function destroy(Position $position)
    {
        abort_unless(auth()->user()->canManageDepartment(), 403);

        if ($position->documents()->count() > 0) {
            return back()->with('error', 'Jabatan "' . $position->name . '" tidak bisa dihapus karena masih memiliki Dokumen.');
        }

        $position->delete();
        return back()->with('success', 'Jabatan berhasil dihapus!');
    }

    /** AJAX: Get positions by division/department */
    public function byDivision(Division $division)
    {
        $positions = Position::where('division_id', $division->id)->orderBy('order')->get(['id', 'name']);
        return response()->json($positions);
    }

    /** AJAX: Get departments by BOD */
    public function departmentsByBod(\App\Models\Bod $bod)
    {
        $departments = Division::where('bod_id', $bod->id)->orderBy('order')->get(['id', 'name']);
        return response()->json($departments);
    }
}
