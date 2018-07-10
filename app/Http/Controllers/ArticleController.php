<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use App\User;
use App\Tag;
use App\Http\Resources\ArticlesResource;
use App\Http\Resources\ArticleResource;
use Carbon\Carbon;
use App\Http\Requests\StoreArticleRequest;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request(['month', 'year'])) {
            try {
                $articles = Article::withCount(['likes'])
                    ->with('tags', 'likes')
                    ->latest();

                if ($month = request('month')) {
                    $articles->whereMonth('created_at', Carbon::parse($month)->month);
                }

                if ($year = request('year')) {
                    $articles->whereYear('created_at', $year);
                }
            } catch (Exception $e) {
            // report($e);

            // return false;
            }
        } else {
            $articles = Article::withCount(['likes'])->with('tags', 'likes');
        }

        if ($articles->count()) {
            $articles = $articles->paginate(10);
            return new ArticlesResource($articles);
        }

        return response()->json([
            'data' => [
                'message' => 'No Articles Found'
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreArticleRequest $request)
    {
        // any user can create an article
        $this->authorize('create', Article::class,Tag::class);
        $this->authorize('create', Tag::class);

        //create article instance
        $article = new Article();
        $article->title  = request('title');
        $article->body = request('body');
        $article->user()->associate($request->user());
        $article->save();

        // Managing tags
        $tags = collect(explode(',', request('tags')))->map(function ($tag) {
            return str_slug($tag, '');
        })->unique();

        // attach tag to article
        $tags->each(function ($tag) use ($article) {
            if (!Tag::where(['name'=>$tag])->exists()) {
                Tag::create(['name' => $tag]);
            }
            $tag = Tag::where('name', $tag)->get();
            $article->tags()->attach($tag);
        });

        // save the arricle

            return new ArticleResource($article);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return new ArticleResource($article->load('tags'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
            // if($request->isMethod('put')){}
            $this->authorize('update', $article);
            $this->authorize('create', Tag::class);
            $article->title  = request('title');
            $article->body = request('body');
            $article->save();

            // Managing tags
            $tags = collect(explode(',', request('tags')))->map(function ($tag) {
                return str_slug($tag, '');
            })->unique();
            // dd($tags->toArray());

            // sync tag to article
            $tags->each(function ($tag) use ($article) {
                if (!Tag::where(['name'=>$tag])->exists()) {
                    Tag::create(['name' => $tag]);
                }
            });
            // this lines solve the problem
            $tags = Tag::whereIn('name', $tags)->get();

            $article->tags()->sync($tags);

            return new ArticleResource($article);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        if ($article->delete()) {
            return new ArticleResource($article);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function archives()
    {
        $archives = Article::archives();

        return response($archives, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tags(Tag $tag)
    {
        $articles = $tag->articles()->paginate(10);
        return new ArticlesResource($articles);
    }
}
