<?php

namespace App\Http\Controllers;

use App\Http\Resources\Ingredient\IngredientResource;
use App\Models\Ingredient\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
  public function index(Request $request){
    $ingredients = Ingredient::orderBy('created_at', 'desc')->get();
    return response()->json([
      'message' => 'Data Ingredient berhasil diambil!',
      'status' => 'success',
      'data' => IngredientResource::collection($ingredients)
    ]);
  }

  public function store(Request $request){
    $request->validate([
      'name' => 'required|string',
      'unit_id' => 'required|exists:units,id',
      'weight' => 'required|integer',
      'pieces' => 'nullable|integer',
      'price' => 'required|integer|min:100',
    ]);

    $pricePerGram = floor($request->price / $request->weight);
    $pricePerPcs = !empty($request->pieces) ? floor($request->price / $request->pieces) : null;

    $ingredient = Ingredient::create([
      'name' => $request->name,
      'unit_id' => $request->unit_id,
      'weight' => $request->weight,
      'price' => $request->price,
      'price_per_gr' => $pricePerGram,
      'price_per_pcs' => $pricePerPcs
    ]);

    return response()->json([
      'status' => 'success',
      'message' => 'Data Ingredient berhasil ditambahkan!',
      'data' => new IngredientResource($ingredient)
    ]);
  }

  public function update(Request $request, $id){
    $ingredient = Ingredient::find($id);
    if(empty($ingredient)){
      return response()->json([
        'status' => 'error',
        'message' => 'Data Ingredient tidak ditemukan!',
      ], 404, [
        'Content-Type' => 'application/json'
      ]); 
    }

    $request->validate([
      'name' => 'required|string',
      'unit_id' => 'required|exists:units,id',
      'weight' => 'required|integer',
      'pieces' => 'nullable|integer',
      'price' => 'required|integer|min:100',
    ]);

    $pricePerGram = floor($request->price / $request->weight);
    $pricePerPcs = !empty($request->pieces) ? floor($request->price / $request->pieces) : null;

    $ingredient->update([
      'name' => $request->name,
      'unit_id' => $request->unit_id,
      'weight' => $request->weight,
      'price' => $request->price,
      'price_per_gr' => $pricePerGram,
      'price_per_pcs' => $pricePerPcs ?? $ingredient->price_per_pcs
    ]);

    return response()->json([
      'status' => 'success',
      'message' => 'Data Ingredient berhasil diupdate!',
      'data' => new IngredientResource($ingredient)
    ]);
  }

  public function destroy(Request $request, $id){
    $ingredient = Ingredient::find($id);
    if(empty($ingredient)){
      return response()->json([
        'status' => 'error',
        'message' => 'Data Ingredient tidak ditemukan!',
      ], 404, [
        'Content-Type' => 'application/json'
      ]); 
    }

    $ingredient->delete();

    return response()->json([
      'message' => 'Data Ingredient berhasil dihapus!',
      'status' => 'success',
      'data' => new IngredientResource($ingredient)
    ]);
  }
}
