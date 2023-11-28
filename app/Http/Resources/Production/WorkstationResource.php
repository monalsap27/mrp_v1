<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkstationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'timing' => $this->timing,
            'workforce' => $this->workforce,
            'total_workforce' => $this->total_workforce,
            'change_material' => $this->change_material,
        ];
    }
}
