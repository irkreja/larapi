<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;

class TweetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

    }
    public function index(Request $request)
    {
        $tweets = collect(); //declare empty collection
        // 1. check user has a token?
        // 2. check user has any tweets
        $http = new Guzzle;
        if($request->user()->accToken){
            $response = $http->get('http://127.0.0.1:8000/api/tweets', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $request->user()->accToken->access_token
                ]
            ]);
        }

        $tweets = collect(json_decode((string) $response->getBody()));
        // (string) refers string cast Type Casting

        // return view('tweets',with([
        //     'tweets' => $tweets
        // ]));
        return $tweets;
    }

    public function store(Request $request)
    {
        $tweet = collect(); //declare empty collection

        // 1. check user has a token?
        // 2. check user has any tweets
        $http = new Guzzle;
        if($request->user()->accToken){
            $response = $http->post('http://127.0.0.1:8000/api/tweets', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $request->user()->accToken->access_token
                ],
                'json' => ['body' => 'Russia Lost the match']
            ]);
        }

        $tweet = collect(json_decode((string) $response->getBody()));
        // (string) refers string cast Type Casting

        return $tweet;
    }
}
