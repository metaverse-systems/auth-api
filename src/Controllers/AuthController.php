<?php

namespace MetaverseSystems\AuthApi\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use MetaverseSystems\AuthApi\Models\User;
use Auth;
use Validator;
use Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

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

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if($status == Password::RESET_LINK_SENT)
        {
            return response()->json(['message' => __($status)], 200);
        }
        else 
        {
            return response()->json(['message' => __($status)], 400);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));
        
                $user->save();
        
                event(new PasswordReset($user));
            }
        );

        if($status == Password::PASSWORD_RESET)
        {
            return response()->json(['message' => __($status)], 200);
        }
        else
        {
            return response()->json(['message' => __($status)], 400);
        }
    }
}
