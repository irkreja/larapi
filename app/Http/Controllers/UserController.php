<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ArticlesResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\UsersResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\User;
use App\Article;

class UserController extends Controller
{
    public function index()
    {
    $users = User::withCount(['likes'])->paginate(30);

    return new UsersResource($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function articles(User $user)
    {
        $articles = $user->articles()->paginate(30);

        return new ArticlesResource($articles);
    }

    public function likedArticles(User $user)
    {
        $likedId = $liked = $user->likes->where('liked_type','App\Article')->pluck('liked_id')->toArray();
        $articles=Article::whereIn('id',$liked)->paginate(10);

        return new ArticlesResource($articles);
    }

    public function update(StoreUserRequest $request,User $user)
    {
        $this->authorize('update', $user);
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if($user->save()){
            return new UserResource($user);
        }

    }
}
