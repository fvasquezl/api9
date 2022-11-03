<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SaveArticleRequest;
use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use Illuminate\Http\Response;


class ArticleController extends Controller
{
    /**
     * @return ArticleCollection
     */
    public function index()
    {
        $articles = Article::query();

        //filters
        $allowedFilters = ['title','content','month','year'];

        foreach(request('filter',[]) as $filter =>$value){
            abort_unless(in_array($filter,$allowedFilters),400);

            if ($filter === 'year'){
                $articles->whereYear('created_at',$value);
            }else if ($filter === 'month'){
                $articles->whereMonth('created_at',$value);
            }else{
                $articles->where($filter,'LIKE','%'.$value.'%');
            }
        };

        $articles->allowedSorts(['title','content']);

        return ArticleCollection::make($articles->jsonPaginate());
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
