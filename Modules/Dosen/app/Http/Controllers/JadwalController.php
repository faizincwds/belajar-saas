<?php

namespace Modules\Dosen\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Dosen\Models\Jadwal;

class JadwalController extends Controller
{
    public function index()
    {
        $data = Jadwal::latest()->paginate(10);
        return view('Dosen::jadwal.index', compact('data'));
    }

    public function create()
    {
        return view('Dosen::jadwal.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
            'jam' => 'required',

            ]);

            Jadwal::create($validated);
            return redirect()->route('jadwal.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data = Jadwal::findOrFail($id);
        return view('Dosen::jadwal.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
            'jam' => 'required',

            ]);

            $item = Jadwal::findOrFail($id);
            $item->update($validated);
            return redirect()->route('jadwal.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $data = Jadwal::findOrFail($id);
        return view('Dosen::jadwal.show', compact('data'));
    }

    public function destroy($id)
    {
        try {
            Jadwal::findOrFail($id)->delete();
            return redirect()->route('jadwal.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}