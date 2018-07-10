<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Article;
use App\Http\Resources\ArticleResource;

class CommentController extends Controller
{
    public function store(Article $article)
    {
        $this->authorize('comment', Article::class);
        request()->validate([
            'body' => 'required|min:6'
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'article_id' => $article->id,
            'body' => request('body')
        ]);

        return new ArticleResource($article);
    }

}
