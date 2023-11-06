<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkstationGroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->name,
            'arr_workstation_id' => $this->arr_workstation_id,
            'total_workforce' => $this->total_workforce,
            'total_timing' => $this->total_timing,
        ];
    }
}
