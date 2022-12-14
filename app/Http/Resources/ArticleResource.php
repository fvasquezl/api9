<?php

namespace App\Http\Resources;

use App\Http\JasonApi\Traits\JsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    use JsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'title'=> $this->resource->title,
            'slug'=> $this->resource->slug,
            'content' => $this->resource->content,
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['category'];
    }

    public function getIncludes()
    {
        return[
            CategoryResource::make($this->whenLoaded('category'))
        ];

    }

}
