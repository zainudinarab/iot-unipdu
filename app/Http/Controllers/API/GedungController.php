<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    public function index()
    {
        return response()->json(Gedung::all());
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|unique:gedungs']);

        $gedung = Gedung::create($request->only('nama'));
        return response()->json($gedung, 201);
    }

    public function show($id)
    {
        return response()->json(Gedung::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $gedung = Gedung::findOrFail($id);
        $gedung->update($request->only('nama'));
        return response()->json($gedung);
    }

    public function destroy($id)
    {
        Gedung::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
