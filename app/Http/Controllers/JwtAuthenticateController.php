<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthenticateController extends Controller
{
    public function index()
    {
        //return response()->json(['auth' => Auth::user(), 'users' => Users::all()]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email');

        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }
}