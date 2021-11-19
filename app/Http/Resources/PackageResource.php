<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ],
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->created_at->format('d/m/Y'),
        ];
    }
}
