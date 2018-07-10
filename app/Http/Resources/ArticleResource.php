<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'data' => [
                'id' => $this->id,
                'title' => $this->title,
                'body' => $this->body,
                'like_count' => $this->likes->count(),
                'comment_count' => $this->comments->count(),
                'created_at' => $this->created_at->toDateTimeString(),
                'created_at_human' => $this->created_at->diffForHumans(),
                'user' => new UserResource($this->user),
                'tags' => $this->tags->pluck('name'),
                'likes' => UserResource::collection($this->likes->pluck('user')),
                'comments' => CommentResource::collection($this->comments),
            ],
            'links' => [
                'self' => route('articles.show',['article' => $this->id]),
            ],
        ];
    }
}
