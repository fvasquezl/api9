<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SaveArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * @return ArticleCollection
     */
    public function index(Request $request)
    {
        $articles = Article::query();

        if($request->filled('sort')){

            $sortFields =explode(',', $request->input('sort'));


            $allowedSorts = ['title','content'];

            foreach ($sortFields as $sortField){
                $sortDirection= Str::of($sortField)->startsWith('-') ? 'desc':'asc';

                $sortField = ltrim($sortField,'-');

                abort_unless(in_array($sortField,$allowedSorts),400);

                $articles->orderBy($sortField,$sortDirection);
            }

        }
        
        return ArticleCollection::make( $articles->get());
    }

    public function show(Article $article)
    {
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
