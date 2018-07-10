<?php

namespace App\Http\Controllers;

// use App\Http\Requests;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\BadResponseException ;

class TwitterAuthController extends Controller
{
    public function redirect()
    {
        // We will send request link /oauth/authorize?cliend_id=3
        $query = http_build_query([
            'client_id' => '6',
            'redirect_uri' => 'http://127.0.0.1/auth/tweetapi/callback',
            'response_type' => 'code',
            'scope' => 'view-tweets post-tweets',
        ]);

        return redirect('http://127.0.0.1:8000/oauth/authorize?'.$query);
    }

    public function callback(Request $request)
    {
        $http = new Guzzle;

        try{
            $response = $http->post('http://127.0.0.1:8000/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => '6',
                    'client_secret' => 'Gjgc5FZjwaClKkC4JN0gcpbq5oJ7TJMc0Vt9ULD8',
                    'redirect_uri' => 'http://127.0.0.1/auth/tweetapi/callback',
                    'code' => $request->code,
                ],
            ]);
        }catch (BadResponseException $exception) {
            $response = $exception->getResponse();
        }

        $response = json_decode((string) $response->getBody());

        $request->user()->accToken()->delete(); //delete all previous token incase we have
        $request->user()->accToken()->create([
            'access_token' => $response->access_token,
            'refresh_token' => $response->refresh_token,
            'expires_in' => $response->expires_in,
        ]);

        return redirect('api/tweets');

    }

    public function refresh(Request $request)
    {
        $http = new Guzzle;

        try{
            $response = $http->post('http://127.0.0.1:8000/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $request->user()->accToken->refresh_token,
                    'client_id' => '6',
                    'client_secret' => 'Gjgc5FZjwaClKkC4JN0gcpbq5oJ7TJMc0Vt9ULD8',
                    'scope' => 'view-tweets post-tweets'
                ]
            ]);
        }catch (BadResponseException $exception) {
            $response = $exception->getResponse();
        }


        $response = json_decode((string) $response->getBody());

        $request->user()->accToken()->update([
            'access_token' => $response->access_token,
            'expires_in' => $response->expires_in,
            'refresh_token' => $response->refresh_token,
        ]);

        return redirect()->back();
    }
}
