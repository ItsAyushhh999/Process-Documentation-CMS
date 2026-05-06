<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'categoryId' => $this->id,
            'name' => $this->name,
            'description' => $this->whenHas('description'),
            'logo' => $this->whenHas('logo'),
            'created_at' => $this->whenHas('created_at', function () {
                return Carbon::parse($this->created_at)->format('Y-m-d H:i:s');
            }),
            'updated_at' => $this->whenHas('updated_at', function () {
                return Carbon::parse($this->updated_at)->format('Y-m-d H:i:s');
            }),
        ];
    }
}
