<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "control_id" => $this->control_id,
            "harga_beli" => number_format($this->harga_beli, 2, ",", "."),
            "harga_jual" => number_format($this->harga_jual, 2, ",", "."),
            "date"       => date_format(date_create($this->created_at), "d-m-Y H:i:s"),
            "description" => $this->description,
        ];
    }
}
