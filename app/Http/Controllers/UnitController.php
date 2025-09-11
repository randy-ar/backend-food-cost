<?php

namespace App\Http\Controllers;

use App\Http\Resources\Unit\UnitResource;
use App\Models\Unit\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    public function index(Request $request){
        $unit = Unit::orderBy('created_at', 'desc')->get();
        return response()->json([
            'message' => 'Data Unit berhasil diambil!',
            'status' => 'success',
            'data' => UnitResource::collection($unit)
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string',
        ]);
        
        $unit = Unit::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json([
            'message' => 'Data Unit berhasil ditambahkan!',
            'status' => 'success',
            'data' => new UnitResource($unit),
        ]);
    }    

    public function update(Request $request, $id){
        $unit = Unit::find($id);
        if(empty($unit)){
            return response()->json([
                'message' => 'Data Unit tidak ditemukan!',
                'status' => 'error',
            ], 404, [
                'Content-Type' => 'application/json'
            ]);
        }

        $request->validate([
            'name' => 'required|string',
        ]);

        $unit->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json([
            'message' => 'Data Unit berhasil diupdate!',
            'status' => 'success',
            'data' => new UnitResource($unit),
        ]);
    }

    public function destroy(Request $request, $id){
        $unit = Unit::find($id);
        if(empty($unit)){
            return response()->json([
                'message' => 'Data Unit tidak ditemukan!',
                'status' => 'error',
            ], 404, [
                'Content-Type' => 'application/json'
            ]);
        }

        $unit->delete();

        return response()->json([
            'message' => 'Data Unit berhasil dihapus!',
            'status' => 'success',
        ]);
    }
}
