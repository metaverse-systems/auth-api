<?php

namespace MetaverseSystems\AuthApi\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use MetaverseSystems\AuthApi\Models\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends \App\Http\Controllers\Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        event(new Registered($user));
        $token = $user->createToken('MyApp');

        return response()->json(["message"=>"Account created. Please check your email for verification link.", "token"=>$token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if(Auth::attempt($credentials)) {
            return response()->json(['user'=>Auth::user(), "token"=>Auth::user()->createToken('MyApp')], 201);
        }

        return response()->json(['message'=>'Invalid username or password.'], 401);
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        $message = "Account ".Auth::user()->email." verified.";
        return response()->json(["message"=>$message], 201);
    }
}
