<?php

namespace App\Http\Controllers;

use App\Models\Bod;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $departments = Division::with('bod')->withCount(['positions', 'documents'])->orderBy('order')->get();
        $bods = Bod::orderBy('order')->get();
        $nextOrder = (Division::max('order') ?? 0) + 1;
        return view('admin.divisions.index', compact('departments', 'bods', 'nextOrder'));
    }

    public function store(Request $request)
    {
        // Super Admin and Admin can manage departments
        abort_unless(auth()->user()->canManageDepartment(), 403);

        $request->validate([
            'bod_id'      => 'required|exists:bods,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'order'       => 'required|integer|min:1',
        ], [
            'bod_id.required' => 'BOD induk wajib dipilih.',
            'name.required'   => 'Nama departemen wajib diisi.',
            'order.required'  => 'Urutan tampil wajib diisi.',
        ]);

        Division::create([
            'bod_id'      => $request->bod_id,
            'name'        => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'color'       => $request->color ?? '#3b82f6',
            'order'       => (int) $request->order,
        ]);

        return back()->with('success', 'Departemen "' . $request->name . '" berhasil ditambahkan!');
    }

    public function update(Request $request, Division $division)
    {
        abort_unless(auth()->user()->canManageDepartment(), 403);

        $request->validate([
            'bod_id'      => 'required|exists:bods,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'order'       => 'required|integer|min:1',
        ]);

        $division->update([
            'bod_id'      => $request->bod_id,
            'name'        => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'color'       => $request->color ?? $division->color,
            'order'       => (int) $request->order,
        ]);

        return back()->with('success', 'Departemen "' . $division->name . '" berhasil diperbarui!');
    }

    public function destroy(Division $division)
    {
        abort_unless(auth()->user()->canManageDepartment(), 403);

        // Check if department has positions or documents
        if ($division->positions()->count() > 0) {
            return back()->with('error', 'Departemen "' . $division->name . '" tidak bisa dihapus karena masih memiliki Jabatan. Hapus jabatan terlebih dahulu.');
        }

        if ($division->documents()->count() > 0) {
            return back()->with('error', 'Departemen "' . $division->name . '" tidak bisa dihapus karena masih memiliki Dokumen.');
        }

        $name = $division->name;
        $division->delete();

        return back()->with('success', 'Departemen "' . $name . '" berhasil dihapus!');
    }
}
