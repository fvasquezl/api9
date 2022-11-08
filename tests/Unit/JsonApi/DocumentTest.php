<?php

namespace Tests\Unit\JsonApi;

use Mockery;
use App\Models\Category;
use PHPUnit\Framework\TestCase;
use App\Http\JasonApi\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;


class DocumentTest extends TestCase
{
    /** @test */
    public function can_create_json_api_documents()
    {
        $category = Mockery::mock('Category', function ($mock){
            $mock->shouldReceive('getResourceType')->andReturn('categories');
            $mock->shouldReceive('getRouteKey')->andReturn('category-id');
        });

        $document = Document::type('articles')
            ->id('article-id')
            ->attributes([
                'title' => 'Article title'
            ])->relationshipData([
                'category' => $category
            ])
            ->toArray();


        $expected = [
            'data' => [
                'type' => 'articles',
                'id' => 'article-id',
                'attributes' => [
                    'title' => 'Article title'
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => 'categories',
                            'id' => $category->getRouteKey()
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $document);
    }
}
