<?php

namespace Tests\Feature\Articles;


use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_create_articles()
    {
        $this->withoutExceptionHandling();

        $category =Category::factory()->create();

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo',
            '_relationships' => [
                'category' => $category
            ]
        ])->assertCreated();

        $article = Article::first();
        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string)$article->getRouteKey(),
                'attributes' => [
                    'title' => 'Nuevo articulo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del articulo'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ],

        ]);
    }

    /** @test */
    public function title_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nue',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo'

        ])->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function slug_is_required()
    {

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_be_unique()
    {
        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => $article->slug,
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => '%$^$',
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => 'with_underscores',
            'content' => 'Contenido del articulo'
        ])->assertSee(
            trans('validation.no_underscores', ['attribute' => 'data.attributes.slug']
            )
        )->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => '-start-with-dashes',
            'content' => 'Contenido del articulo'
        ])->assertSee(
            trans('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']
            )
        )->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_not_ending_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => 'ending-with-dashes-',
            'content' => 'Contenido del articulo'
        ])->assertSee(
            trans('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']
            )
        )->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function content_is_required()
    {

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo-articulo'
        ])->assertJsonApiValidationErrors('content');

    }
}
