<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    public function oAuth() {
        $oauthScopes = [
            "https://www.googleapis.com/auth/drive.readonly",
            'openid profile email https://www.googleapis.com/auth/spreadsheets'
        ];

        $params = [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => env('FRONTEND_URL'),
            'response_type' => 'token',
            'scope' => implode(' ', $oauthScopes), 
        ];
        
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . urldecode(http_build_query($params));
        return response($authUrl);
    }
}
