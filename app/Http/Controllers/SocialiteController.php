<?php


namespace App\Http\Controllers;


use App\Actions\Fortify\CreateNewUser;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController
{
    /**
     * @var CreateNewUser
     */
    private $creator;

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
    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));
    }


    public function __construct(CreateNewUser $creator)
    {
        $this->creator = $creator;
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
                return redirect('/login');
            }
        }

        if ($u == null) {
            $input = [
                'name' => $user->id,
                'email' => $user->email,
                'discord_id' => $user->id,
                'password' => null,
            ];

            $u = DB::transaction(function () use ($user, $input) {
                return tap(User::create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => null,
                    'shouldUpdatePw' => true,
                    'discord_id' => $user->id
                ]), function (User $user) {
                    $this->createTeam($user);
                });
            });

            Log::info($u);

        }

        auth()->login($u);

        return \redirect('/login');
    }
}
