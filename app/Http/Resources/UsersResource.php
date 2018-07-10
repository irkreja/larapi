<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UsersResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($user){
                return [
                    'id' => $user->id,
                    'avatar_url' => $user->avatar(),
                    'url' => route('users.show',['user' => $user->id]),
                    // 'name' => $user->likes->count(),
                    'username' => $user->username,
                    'email' => $user->email,
                    'total_comment_count' => $user->comments->count(),
                    'total_liked_article' => $user->likes->count(),
                    'registerd_at' => $user->created_at->toDateTimeString(),
                    'articles' => route('users.articles',['user' => $user->id]),
                    'liked_article' => route('users.likedArticles',['user' => $user->id]),
                ];
            })
        ];
    }
}
