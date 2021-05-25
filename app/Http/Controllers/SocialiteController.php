<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController
{
    function discord()
    {
        return Socialite::driver('discord')->with(['prompt' => 'none'])->redirect();
    }

    function error(Request $request)
    {
        return Jetstream::inertia()->render($request, 'Auth/SocialiteError', [
            'message' => $request->query('message'),
            'login' => $request->user() != null
        ]);
    }

    function discordCallback(Request $request)
    {
        $user = Socialite::driver('discord')->user();

        $u = User::where('discord_id', $user->id)->first();

        $currentUser = $request->user();

        if ($u == null) {
            $u = User::where('email', $user->email)->first();

            if ($u != null && $currentUser == null) {
                return redirect()->route('auth.error', ['message' => '이 디스코드 계정이 사용하는 이메일로 가입된 계정이 이미 존재합니다. 디스코드 로그인을 이용하시려면 기존 계정에 디스코드 계정을 연결해주세요.']);
            } else if ($u != null && $currentUser != null) {
                $currentUser->discord_id = $user->id;
                $currentUser->save();
                return \redirect('/login');
            }
        }

        $u = User::create([
            'name' => $user->id,
            'email' => $user->email,
            'discord_id' => $user->id
        ]);

        auth()->login($u);

        return \redirect('/login');
    }
}
