<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'avatar_url' => $this->avatar(),
            'email' => $this->email,
            // 'url' => route('users.show',['user' => $this->id]),
            // 'total_comment_count' => $this->comments->count(),
            // 'total_liked_article' => $this->likes->count(),
            'registerd_at' => $this->created_at->toDateTimeString(),
            // 'articles' => route('users.articles',['user' => $this->id]),
            // 'liked_article' => route('users.likedArticles',['user' => $this->id]),
        ];

    }
}
