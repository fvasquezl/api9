<?php

namespace App\Http\JasonApi;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;

class JsonApiTestResponse
{
    public function assertJsonApiValidationErrors(): \Closure
    {
        return function ($attribute) {

            /** @var TestResponse $this */
            $pointer = "/data/attributes/{$attribute}";

            if (Str::of($attribute)->startsWith('data')){
                $pointer = "/" . str_replace('.', '/', $attribute);
            }elseif (Str::of($attribute)->startsWith('relationships')){
                $pointer = "/data/" . str_replace('.', '/', $attribute).'/data/id';
            }


            try {

                $this->assertJsonFragment([
                    'source' => ['pointer' => $pointer],
                ])->assertStatus(422);

            }catch (ExpectationFailedException $e) {
                PHPUnit::fail("Failed to find a JSON:API validation error for key:'{$attribute}'"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }

            try {

                $this->assertJsonStructure([
                    'errors' =>[
                        ['title','detail','source'=>['pointer']]
                    ]
                ]);

            }catch (ExpectationFailedException $e){

                PHPUnit::fail("Failed to find a valid JSON:API error response"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );

            }

            return $this->assertHeader(
                'content-type','application/vnd.api+json'
            )->assertStatus(422);

        };
    }

    public function assertJsonApiResource():\Closure
    {
        return function ($model,$attributes){

            /** @var TestResponse $this*/

            return $this->assertJson([
                'data'=> [
                    'type' => $model->getResourceType(),
                    'id' => (string) $model->getRouteKey(),
                    'attributes' => $attributes,
                    'links' => [
                        'self' => route('api.v1.'.$model->getResourceType().'.show',$model)
                    ]
                ]
            ])->assertHeader(
                'Location',
                route('api.v1.'.$model->getResourceType().'.show',$model)
            );
        };
    }

    public function AssertJsonApiResourceCollection():\Closure
    {
        return function ($models, $attributesKeys){
            /** @var TestResponse $this*/
            try {
                $this->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'attributes' => $attributesKeys
                        ]
                    ]
                ]);

            }catch (ExpectationFailedException $e) {
                PHPUnit::fail("Failed, a JSON:API validation error for Attributes: ".implode(", ", $attributesKeys)
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }


            foreach ($models as $model){
                $this->assertJsonFragment([
                    'type' => $model->getResourceType(),
                    'id' => (string) $model->getRouteKey(),
                    'links' => [
                        'self' => route('api.v1.'.$model->getResourceType().'.show',$model)
                    ]
                ]);
            }

            return $this;
        };
    }
}
