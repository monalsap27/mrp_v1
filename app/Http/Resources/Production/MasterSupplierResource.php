<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Resources\Json\JsonResource;

class MasterSupplierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'phone' => $this->phone,
            'address' => $this->address,
            'description' => $this->description,
        ];
    }
}
