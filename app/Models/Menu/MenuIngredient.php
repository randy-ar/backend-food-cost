<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;

class MenuIngredient extends Model
{
    protected $guarded = [];
    protected $softDeletes = true;

    public function menu(){
        return $this->belongsTo(\App\Models\Menu\Menu::class);
    }

    public function ingredient(){
        return $this->belongsTo(\App\Models\Ingredient\Ingredient::class);
    }
}
