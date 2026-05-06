<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'projectId' => $this->whenHas(($this->projectId ?: 'id')),
            'projectName' => $this->name,
            'description' => $this->whenHas('description'),
            'url' =>  $this->whenHas('url'),
            'sub_projects' => $this->whenHas('sub_projects'),
            'development_pipeline' =>  $this->whenHas('development_pipeline'),
            'staging_pipeline' =>  $this->whenHas('staging_pipeline'),
            'production_Pipeline' =>   $this->whenHas('production_Pipeline'),
            'repository_name' =>  $this->whenHas('repository_name'),
            'created_at' =>  $this->whenHas('created_at'),
            'updated_at' =>  $this->whenHas('updated_at'),
            'created_by' =>  $this->whenHas('created_by'),
            'updated_by' => $this->whenHas('updated_by'),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'subProjects' => SubProjectResource::collection($this->whenLoaded('subprojects')),
        ];
    }
}
