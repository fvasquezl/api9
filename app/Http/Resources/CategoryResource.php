<?php

namespace App\Http\Resources;

use App\Http\JasonApi\Traits\JsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    use JsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'name' => $this->resource->name
        ];
    }
}
