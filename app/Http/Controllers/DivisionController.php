<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount(['positions', 'documents'])->orderBy('order')->get();
        $nextOrder = (Division::max('order') ?? 0) + 1;
        return view('admin.divisions.index', compact('divisions', 'nextOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:divisions,name',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'order'       => 'required|integer|min:1|unique:divisions,order',
        ], [
            'name.required'  => 'Nama divisi wajib diisi.',
            'name.unique'    => 'Nama divisi tersebut sudah ada.',
            'order.required' => 'Urutan tampil wajib diisi.',
            'order.integer'  => 'Urutan tampil harus berupa angka.',
            'order.min'      => 'Urutan tampil minimal 1.',
            'order.unique'   => 'Urutan tampil ' . $request->order . ' sudah digunakan oleh divisi lain. Nomor urut tidak boleh ada yang sama.',
        ]);

        Division::create([
            'name'        => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'color'       => $request->color ?? '#3b82f6',
            'order'       => (int) $request->order,
        ]);

        return back()->with('success', 'Divisi "' . $request->name . '" berhasil ditambahkan!');
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:divisions,name,' . $division->id,
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'order'       => 'required|integer|min:1|unique:divisions,order,' . $division->id,
        ], [
            'name.required'  => 'Nama divisi wajib diisi.',
            'name.unique'    => 'Nama divisi tersebut sudah digunakan oleh divisi lain.',
            'order.required' => 'Urutan tampil wajib diisi.',
            'order.integer'  => 'Urutan tampil harus berupa angka.',
            'order.min'      => 'Urutan tampil minimal 1.',
            'order.unique'   => 'Urutan tampil ' . $request->order . ' sudah digunakan oleh divisi lain. Nomor urut tidak boleh ada yang sama.',
        ]);

        $division->update([
            'name'        => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'color'       => $request->color ?? $division->color,
            'order'       => (int) $request->order,
        ]);

        return back()->with('success', 'Divisi "' . $division->name . '" berhasil diperbarui!');
    }

    public function destroy(Division $division)
    {
        $name = $division->name;
        $division->delete();

        return back()->with('success', 'Divisi "' . $name . '" berhasil dihapus!');
    }
}
