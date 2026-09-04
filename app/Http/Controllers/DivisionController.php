<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount(['positions', 'documents'])->orderBy('order')->get();
        return view('admin.divisions.index', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:divisions,name',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'order'       => 'nullable|integer',
        ]);

        $maxOrder = Division::max('order') ?? 0;
        Division::create([
            'name'        => $request->name,
            'description' => $request->description,
            'color'       => $request->color ?? '#3b82f6',
            'order'       => $request->order ?? ($maxOrder + 1),
        ]);

        return back()->with('success', 'Divisi berhasil ditambahkan!');
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:divisions,name,' . $division->id,
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'order'       => 'nullable|integer',
        ]);

        $division->update([
            'name'        => $request->name,
            'description' => $request->description,
            'color'       => $request->color ?? $division->color,
            'order'       => $request->order ?? $division->order,
        ]);
        return back()->with('success', 'Divisi berhasil diperbarui!');
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return back()->with('success', 'Divisi berhasil dihapus!');
    }
}
