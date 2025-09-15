<?php

namespace App\Http\Resources\Menu;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'serving' => $this->serving,
            'costs' => new MenuCostResource($this->costs),
            'ingredients' => MenuIngredientResource::collection($this->ingredients),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
