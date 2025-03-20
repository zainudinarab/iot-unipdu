<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lantai;
use Illuminate\Http\Request;

class LantaiController extends Controller
{
    public function index()
    {
        return response()->json(Lantai::with('gedung')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'nomor' => 'required|integer',
        ]);

        $lantai = Lantai::create($request->only(['gedung_id', 'nomor']));
        return response()->json($lantai, 201);
    }

    public function show($id)
    {
        return response()->json(Lantai::with('gedung')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $lantai = Lantai::findOrFail($id);
        $lantai->update($request->only(['gedung_id', 'nomor']));
        return response()->json($lantai);
    }

    public function destroy($id)
    {
        Lantai::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
