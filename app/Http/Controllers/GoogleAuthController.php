<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callBackGoogle()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate([
            'google_id' => $googleUser->id,
        ], [
            'name' => $googleUser->name,
            'email' => $googleUser->email,
        ]);

        $token = $user->createToken('API Token')->plainTextToken;
        $user->api_token = $token;
        if ($user->wasRecentlyCreated) {
            $user->name = $googleUser->name;
        }
        $user->save();

        if ($user->refresh()->status != '1') {
            return 'Inactive user.';
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }

    public function localLogin()
    {
        Auth::login(User::find(2));

        return redirect('/dashboard');
    }

    public function testInertia()
    {
        return Inertia::render('TestApp/Test');
    }
}
