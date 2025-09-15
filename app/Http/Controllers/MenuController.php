<?php

namespace App\Http\Controllers;

use App\Http\Resources\Menu\MenuResource;
use App\Models\Ingredient\Ingredient;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuCost;
use App\Models\Menu\MenuIngredient;
use App\Models\Unit\Unit;
use Brick\Math\Exception\DivisionByZeroException;
use DivisionByZeroError;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MenuController extends Controller
{
    public function index(Request $request){
        $menus = Menu::orderBy('created_at', 'desc')->get();
        return response()->json([
            'message' => 'Data Menu berhasil diambil!',
            'status' => 'success',
            'data' => MenuResource::collection($menus)
        ]);
    }

    public function hitung(Request $request){
        $request->validate([
            'name' => 'required|string',
            'serving' => 'required|integer',
            'ingredients_id' => 'required|array',
            'ingredients_id.*' => 'integer|exists:ingredients,id',
            'ingredients_unit_id' => 'required|array',
            'ingredients_unit_id.*' => 'integer|exists:units,id',
            'ingredients_quantity' => 'required|array',
            'ingredients_quantity.*' => 'integer|min:1',
            'selling_price' => 'nullable|integer',
        ]);

        $menu = $this->hitungMenu(
            $request->name,
            $request->serving,
            $request->selling_price,
            $request->ingredients_id,
            $request->ingredients_unit_id,
            $request->ingredients_quantity,
        );

        $rekomendasiHarga = $this->rekomendasiHarga($menu);

        return response()->json([
            'message' => 'Data menu berhasil dihitung.',
            'status' => 'success',
            'rekomendasi_harga' => $rekomendasiHarga,
            'data' => new MenuResource($menu)
        ]);
    }

    public function hitungMenu(String $name, int $serving, int $selling_price, array $ingredients, array $units, array $quantities): Menu{
        // models
        $menu = new Menu();
        $menuIngredients = new Collection();
        $menuCost = new MenuCost();

        // menu attribute
        $menu->name = $name;
        $menu->serving = $serving;
        $menu->created_at = now();
        $menu->updated_at = now();

        // menu cost attribute
        $costHPP = 0;
        $overHead = 0;
        $totalCost = 0;

        for ($i=0; $i < count($ingredients); $i++) { 
            $ingId = $ingredients[$i];
            $quantity = $quantities[$i];
            $unitId = $units[$i];
            $ingredient = Ingredient::find($ingId);
            $unit = Unit::find($unitId);
            if(!empty($ingredient) && !empty($unitId)){
                $menuIng = new MenuIngredient();
                $menuIng->ingredient_id = $ingredient->id;
                $menuIng->unit_id = $unit->id;
                $menuIng->quantity = $quantity;
                $menuPricePerUnit = $ingredient->price_per_gr;
                if($unit->name == 'pcs'){
                    $menuPricePerUnit = $ingredient->price_per_pcs;
                }
                $menuIng->price_per_unit = $menuPricePerUnit;
                $menuIng->total_price = $menuPricePerUnit * $quantity;
                $menuIngredients->push($menuIng);
            }
        }
        
        if(count($menuIngredients) > 0){
            $costHPP = $menuIngredients->sum('total_price');
            $overHead = $costHPP * 0.1;
            $totalCost = $costHPP + $overHead;
            $menuCost->hpp_cost = $costHPP;
            $menuCost->overhead = $overHead;
            $menuCost->total_cost = $totalCost;
            try {
                $menuCost->food_cost = round(($totalCost / $selling_price ?? $totalCost) * 100, 2);
            } catch (DivisionByZeroError $th) {
                $menuCost->food_cost = round(($totalCost / $totalCost) * 100, 2);
            }
            $menuCost->selling_price = $selling_price;
        }

        $menu->ingredients = $menuIngredients;
        $menu->costs = $menuCost;

        return $menu;
    }

    public function rekomendasiHarga(Menu $menu): int{
        $rekomendasiHarga = 0;
        if(!empty($menu->costs) && $menu->costs->total_cost > 0){
            $rekomendasiHarga = round($menu->costs->total_cost / 0.35);
        }
        return $rekomendasiHarga;
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string',
            'serving' => 'required|integer',
            'ingredients_id' => 'required|array',
            'ingredients_id.*' => 'integer|exists:ingredients,id',
            'ingredients_unit_id' => 'required|array',
            'ingredients_unit_id.*' => 'integer|exists:units,id',
            'ingredients_quantity' => 'required|array',
            'ingredients_quantity.*' => 'integer|min:1',
            'selling_price' => 'nullable|integer',
        ]);

        $menu = $this->hitungMenu(
            $request->name,
            $request->serving,
            $request->selling_price,
            $request->ingredients_id,
            $request->ingredients_unit_id,
            $request->ingredients_quantity,
        );

        $ingredients = $menu->ingredients;
        $costs = $menu->costs;

        unset($menu->ingredients);
        unset($menu->costs);

        $menu->save();
        
        foreach ($ingredients as $menuIng) {
            $menuIng->menu_id = $menu->id;
            $menuIng->save();
        }
        
        $costs->menu_id = $menu->id;
        $costs->save();

        return response()->json([
            'message' => 'Data menu berhasil disimpan.',
            'status' => 'success',
            'data' => new MenuResource($menu),
        ]);
    }

    public function update(Request $request, $id){
        $menu = Menu::find($id);
        if(empty($menu)){
            return response()->json([
                'status' => 'error',
                'message' => 'Data Menu tidak ditemukan!',
            ], 404, [
                'Content-Type' => 'application/json'
            ]); 
        }

        $request->validate([
            'name' => 'required|string',
            'serving' => 'required|integer',
            'ingredients_id' => 'required|array',
            'ingredients_id.*' => 'integer|exists:ingredients,id',
            'ingredients_unit_id' => 'required|array',
            'ingredients_unit_id.*' => 'integer|exists:units,id',
            'ingredients_quantity' => 'required|array',
            'ingredients_quantity.*' => 'integer|min:1',
            'selling_price' => 'nullable|integer',
        ]);

        // Delete existing ingredients and costs
        $menu->ingredients()->delete();
        $menu->costs()->delete();

        $updatedMenu = $this->hitungMenu(
            $request->name,
            $request->serving,
            $request->selling_price,
            $request->ingredients_id,
            $request->ingredients_unit_id,
            $request->ingredients_quantity,
        );

        $ingredients = $updatedMenu->ingredients;
        $costs = $updatedMenu->costs;

        $menu->name = $updatedMenu->name;
        $menu->serving = $updatedMenu->serving;
        $menu->save();
        
        foreach ($ingredients as $menuIng) {
            $menuIng->menu_id = $menu->id;
            $menuIng->save();
        }
        
        $costs->menu_id = $menu->id;
        $costs->save();

        $menu = Menu::find($id);

        return response()->json([
            'message' => 'Data menu berhasil diupdate.',
            'status' => 'success',
            'data' => new MenuResource($menu),
        ]);
    }

    public function destroy(Request $request, $id){
        $menu = Menu::find($id);        
        if(empty($menu)){
            return response()->json([
                'status' => 'error',
                'message' => 'Data Menu tidak ditemukan!',
            ], 404, [
                'Content-Type' => 'application/json'
            ]); 
        }

        $menu->delete();

        return response()->json([
            'message' => 'Data menu berhasil dihapus!',
            'status' => 'success',
            'data' => new MenuResource($menu)
        ]);
    }

}
