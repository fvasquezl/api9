<?php

namespace Tests\Feature\Articles;


use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_create_articles()
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.articles.store'),[
               'title' => 'Nuevo articulo',
               'slug' => 'nuevo-articulo',
               'content' => 'Contenido del articulo'
        ]);

        $response->assertCreated();

        $article = Article::first();
        $response->assertHeader(
            'Location',
            route('api.v1.articles.show',$article)
        );

        $response->assertExactJson([
            'data'=>[
                'type'=> 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Nuevo articulo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del articulo'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show',$article)
                ]
            ],

        ]);
    }

    /** @test */
    public function title_is_required()
    {
       $this->postJson(route('api.v1.articles.store'),[
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {

        $this->postJson(route('api.v1.articles.store'),[
            'title' => 'Nue',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo'

        ])->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function slug_is_required()
    {

        $this->postJson(route('api.v1.articles.store'),[
            'title' => 'Nuevo Articulo',
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function content_is_required()
    {

        $this->postJson(route('api.v1.articles.store'),[
            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo-articulo'
        ])->assertJsonApiValidationErrors('content');

    }
}
