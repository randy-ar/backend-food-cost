<?php

namespace App\Models\Unit;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $guarded = [];
    protected $softDeletes = true;

    public function menus(){
        return $this->hasMany(\App\Models\Menu\Menu::class);
    }

    public function ingredients(){
        return $this->hasMany(\App\Models\Ingredient\Ingredient::class);
    }
}
