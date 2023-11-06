<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkstationDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'workstation_id' => $this->workstation_id,
            'product_id' => $this->product_id,
            'product_name' => $this->name,
            'product_code' => $this->code,
            'qty' => $this->qty,
        ];
    }
}
