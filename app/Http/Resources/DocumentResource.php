<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'documentId' => $this->whenHas($this->documentId ?: 'id'),
            'name' => $this->name,
            'updatedAt' => $this->whenHas('updated_at', function () {
                return $this->updated_at ? Carbon::parse($this->updated_at)->format('m/d/Y h:i A') : null;
            }),
            'updatedBy' => $this->whenHas('updatedBy'),
            'description' => $this->whenHas('description'),
            'categories' => $this->whenLoaded('category')->pluck('name'),
            'projectName' => $this->whenLoaded('project', function () {
                return optional($this->project)->name;
            }),
        ];
    }
}
