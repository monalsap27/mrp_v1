<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralMasterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
        ];
    }
}
