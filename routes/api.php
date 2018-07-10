<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Get Current Logged In User Details
Route::middleware('auth:api')->get('/user', function (Request $request) {
    // return $request->user();
    return new UserResource($request->user());
});

// To Register a User Using API
Route::post('register', 'API\RegisterController@register');

// Route::resource('users','UserController');
Route::middleware('auth:api')->get('/tweets', 'TweetController@index');
Route::middleware('auth:api')->post('/tweets', 'TweetController@store');

Route::prefix('articles')->group(function () {
    // list of All Articles
    Route::get('/','ArticleController@index')->name('articles.index');

    // List a Single Article
    Route::get('/{article}','ArticleController@show')->where('article', '[0-9]+')->name('articles.show');

    // Create a New Article
    Route::middleware('auth:api')->post('/','ArticleController@store')->name('articles.store');

    // Update a Article
    Route::middleware('auth:api')->patch('/{article}','ArticleController@update')->name('articles.update');

    // Delete a Article
    Route::middleware('auth:api')->delete('/{article}','ArticleController@destroy')->name('articles.distroy');;

    // Like an Article
    Route::middleware('auth:api')->post('/{article}/likes', 'LikeController@store');

    Route::middleware('auth:api')->post('/{article}/comments', 'CommentController@store');

    // List of Archives Articles
    Route::get('/archives', 'ArticleController@archives');

    // list of All Articles for a specific tag
    Route::get('/t/{tag}','ArticleController@tags');
});

Route::middleware('auth:api')->prefix('users')->group(function(){
    // Get all users
    Route::get('/','UserController@index')->name('users.index');

    // Get a single user
    Route::get('/{user}','UserController@show')->name('users.show');

    // Get personal article about a user
    Route::get('/{user}/articles','UserController@articles')->name('users.articles');

    // Get liked article information about a user
    Route::get('/{user}/liked-article','UserController@likedArticles')->name('users.likedArticles');

    // Update the authenticated user
    Route::patch('/{user}','UserController@update')->name('users.update');
});

