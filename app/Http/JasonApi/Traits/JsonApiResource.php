<?php

namespace App\Http\JasonApi\Traits;

use App\Http\JasonApi\Document;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection as AnonymousResourceCollectionAlias;
use Illuminate\Http\Resources\MissingValue;

trait JsonApiResource
{
    abstract public function toJsonApi():array;

    public function toArray($request): array
    {
        if($request->filled('include')){
            foreach ($this->getIncludes() as $included) {
                if($included->resource instanceof MissingValue){
                    continue;
                }
                $this->with['included'][] = $included;
            }
        }

        Return Document::type($this->resource->getResourceType())
            ->id($this->resource->getRouteKey())
            ->attributes($this->filterAttributes($this->toJsonApi()))
            ->relationshipLinks($this->getRelationshipLinks())
            ->links([
                'self' => route('api.v1.'.$this->resource->getResourceType().'.show',$this->resource)
            ])
            ->get('data');
    }

    public function getIncludes()
    {
       return [];
    }

    public function getRelationshipLinks()
    {
       return [];
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

    public static function collection($resources): AnonymousResourceCollectionAlias
    {
        $collection = parent::collection($resources);

        if(request()->filled('include')) {
            foreach ($resources as $resource) {
                foreach ($resource->getIncludes() as $included) {
                    if($included->resource instanceof MissingValue){
                        continue;
                    }
                    $collection->with['included'][] = $included;
                }
            }
        }

        $collection->with['links'] = [
            'self'=> $resources->path()
        ];

        return $collection;
    }
}
