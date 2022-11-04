<?php

namespace App\Http\JasonApi\Traits;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection as AnonymousResourceCollectionAlias;

trait JsonApiResource
{
    abstract public function toJsonApi():array;

    public function toArray($request): array
    {
        return [
            'type' => $this->getResourceType(),
            'id' => (string) $this->resource->getRouteKey(),
            'attributes' => $this->filterAttributes($this->toJsonApi()),
            'links' => [
                'self' => route('api.v1.'.$this->getResourceType().'.show',$this->resource)
            ]
        ];
    }

    public function withResponse($request, $response): void
    {
        $response->header(
            'Location',
            route('api.v1.'.$this->getResourceType().'.show',$this->resource)
        );
    }

    private function filterAttributes(array $attributes): array
    {
        return array_filter($attributes,function ($value){
            if (request()->isNotFilled('fields')){
                return true;
            }

            $fileds = explode(',',request('fields.'.$this->getResourceType()));

            if($value == $this->getRouteKey()){
                return in_array($this->getRouteKeyName(),$fileds);
            }

            return $value;

        });
    }

    public static function collection($resource): AnonymousResourceCollectionAlias
    {
        $collection = parent::collection($resource);

        $collection->with['links'] = [
            'self'=> $resource->path()
        ];

        return $collection;
    }
}
