<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RFIDCard;
use Illuminate\Http\Request;

class RFIDCardController extends Controller
{
    public function index()
    {
        return response()->json(RFIDCard::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'uid' => 'required|string|unique:rfid_cards,uid',
            'pemilik' => 'required|string',
        ]);

        $rfid = RFIDCard::create($request->only(['uid', 'pemilik']));
        return response()->json($rfid, 201);
    }

    public function show($id)
    {
        return response()->json(RFIDCard::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $rfid = RFIDCard::findOrFail($id);
        $rfid->update($request->only(['uid', 'pemilik']));
        return response()->json($rfid);
    }

    public function destroy($id)
    {
        RFIDCard::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
