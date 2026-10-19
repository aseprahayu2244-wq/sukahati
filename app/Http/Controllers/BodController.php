<?php

namespace App\Http\Controllers;

use App\Models\Bod;
use Illuminate\Http\Request;

class BodController extends Controller
{
    public function index()
    {
        $bods = Bod::withCount(['departments', 'documents'])->orderBy('order')->get();
        $nextOrder = (Bod::max('order') ?? 0) + 1;
        return view('admin.bods.index', compact('bods', 'nextOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:bods,name',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'order'       => 'required|integer|min:1|unique:bods,order',
        ], [
            'name.required'  => 'Nama BOD wajib diisi.',
            'name.unique'    => 'Nama BOD tersebut sudah ada.',
            'order.required' => 'Urutan tampil wajib diisi.',
            'order.integer'  => 'Urutan tampil harus berupa angka.',
            'order.min'      => 'Urutan tampil minimal 1.',
            'order.unique'   => 'Urutan tampil ' . $request->order . ' sudah digunakan BOD lain.',
        ]);

        Bod::create([
            'name'        => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'color'       => $request->color ?? '#3b82f6',
            'order'       => (int) $request->order,
        ]);

        return back()->with('success', 'BOD "' . $request->name . '" berhasil ditambahkan!');
    }

    public function update(Request $request, Bod $bod)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:bods,name,' . $bod->id,
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'order'       => 'required|integer|min:1|unique:bods,order,' . $bod->id,
        ], [
            'name.required'  => 'Nama BOD wajib diisi.',
            'name.unique'    => 'Nama BOD tersebut sudah digunakan.',
            'order.required' => 'Urutan tampil wajib diisi.',
            'order.integer'  => 'Urutan tampil harus berupa angka.',
            'order.min'      => 'Urutan tampil minimal 1.',
            'order.unique'   => 'Urutan tampil ' . $request->order . ' sudah digunakan BOD lain.',
        ]);

        $bod->update([
            'name'        => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'color'       => $request->color ?? $bod->color,
            'order'       => (int) $request->order,
        ]);

        return back()->with('success', 'BOD "' . $bod->name . '" berhasil diperbarui!');
    }

    public function destroy(Bod $bod)
    {
        // Check if BOD has departments
        if ($bod->departments()->count() > 0) {
            return back()->with('error', 'BOD "' . $bod->name . '" tidak bisa dihapus karena masih memiliki Departemen. Hapus departemen terlebih dahulu.');
        }

        $name = $bod->name;
        $bod->delete();

        return back()->with('success', 'BOD "' . $name . '" berhasil dihapus!');
    }
}
