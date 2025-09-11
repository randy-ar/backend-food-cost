<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];
    protected $softDeletes = true;

    public function costs(){
        return $this->hasOne(\App\Models\Menu\MenuCost::class);
    }

    public function ingredients(){
        return $this->hasMany(\App\Models\Menu\MenuIngredient::class);
    }
}
