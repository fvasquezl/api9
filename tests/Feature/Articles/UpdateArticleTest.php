<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_update_articles()
    {
        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update',$article),[
            'title' => 'Updated Article',
            'slug' => $article->slug,
            'content' => 'Updated Content'
        ])->assertOk();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show',$article)
        );

        $response->assertExactJson([
            'data'=>[
                'type'=> 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Updated Article',
                    'slug' => $article->slug,
                    'content' => 'Updated Content'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show',$article)
                ]
            ],

        ]);
    }

    /** @test */
    public function title_is_required()
    {       $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article),[
            'slug' => 'updated-article',
            'content' => 'Article content'
        ])->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article),[
            'title' => 'Art',
            'slug' => 'article-updated',
            'content' => 'Article content'

        ])->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article),[
            'title' => 'Updated Article',
            'content' => 'Article content'
        ])->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_be_unique()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update',$article1),[
            'title' => 'Nuevo Articulo',
            'slug' => $article2->slug,
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update',$article),[
            'title' => 'Nuevo Articulo',
            'slug' => '%$^$',
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update',$article),[
            'title' => 'Nuevo Articulo',
            'slug' => 'with_underscores',
            'content' => 'Contenido del articulo'
        ])->assertSee(
            trans('validation.no_underscores',['attribute'=>'data.attributes.slug']
            )
        )->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update',$article),[
            'title' => 'Nuevo Articulo',
            'slug' => '-start-with-dashes',
            'content' => 'Contenido del articulo'
        ])->assertSee(
            trans('validation.no_starting_dashes',['attribute'=>'data.attributes.slug']
            )
        )->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_not_ending_with_dashes()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update',$article),[
            'title' => 'Nuevo Articulo',
            'slug' => 'ending-with-dashes-',
            'content' => 'Contenido del articulo'
        ])->assertSee(
            trans('validation.no_ending_dashes',['attribute'=>'data.attributes.slug']
            )
        )->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function content_is_required()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update',$article),[
            'title' => 'Updated Article',
            'slug' => 'article-updated'
        ])->assertJsonApiValidationErrors('content');

    }
}
