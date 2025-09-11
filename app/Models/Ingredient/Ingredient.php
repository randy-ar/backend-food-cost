<?php

namespace App\Models\Ingredient;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $guarded = [];
    protected $softDeletes = true;

    public function menus(){
        return $this->hasMany(\App\Models\Menu\MenuIngredient::class);
    }
    public function units(){
        return $this->belongsTo(\App\Models\Unit\Unit::class);
    }
}
