<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ArticleController extends Controller
{

    public function index():AnonymousResourceCollection
    {
        $articles = Article::query()
            ->allowedFilters(['title','content','month','year'])
            ->allowedSorts(['title','content'])
            ->sparseFieldset()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
    }

    public function show($article):JsonResource
    {
        $article = Article::where('slug', $article)
            ->sparseFieldset()
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    /**
     * @param SaveArticleRequest $request
     * @return ArticleResource
     */
    public function store(SaveArticleRequest $request)
    {
       $article = Article::create($request->validated());
        return ArticleResource::make($article);
    }

    /**
     * @param Article $article
     * @param SaveArticleRequest $request
     * @return ArticleResource
     */
    public function update(SaveArticleRequest $request, Article $article)
    {
        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    /**
     * @param Article $article
     * @return Response
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->noContent();
    }
}
