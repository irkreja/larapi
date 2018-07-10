<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticlesResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'data' => $this->collection->transform(function($article){
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'like_count' => $article->likes->count(),
                    'comment_count' => $article->comments->count(),
                    'created_at' => $article->created_at->toDateTimeString(),
                    'created_at_human' => $article->created_at->diffForHumans(),
                    'user' => new UserResource($article->user),
                    'tags' => $article->tags->pluck('name'),
                ];
            })
        ];
    }
}
