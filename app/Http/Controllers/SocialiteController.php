<?php


namespace App\Http\Controllers;


use App\Actions\Fortify\CreateNewUser;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Jetstream;
use Laravel\Socialite\Facades\Socialite;
use function redirect;

class SocialiteController
{
    /**
     * @var CreateNewUser
     */
    private $creator;

    function discord()
    {
        return Socialite::driver('discord')->stateless()->redirect();
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

    function discordUnlink(Request $request) {
        $user = $request->user();
        $user->discord_id = null;
        $user->save();
        return back(303);
    }

    function discordCallback(Request $request)
    {
        $user = Socialite::driver('discord')->stateless()->user();

        $u = User::where('discord_id', $user->id)->first();

        $currentUser = $request->user();

        $redirect = $request->query('state');

        if ($u == null) {
            $u = User::where('email', $user->email)->first();

            if ($u != null && $currentUser == null) {
                return redirect()->route('auth.error', ['message' => '이 디스코드 계정이 사용하는 이메일로 가입된 계정이 이미 존재합니다. 디스코드 로그인을 이용하시려면 기존 계정에 디스코드 계정을 연결해주세요.']);
            } else if ($u != null && $currentUser != null) {
                if ($u->discord_id != $user->id && User::where('discord_id', $user->id)->first()) {
                    return redirect()->route('auth.error', ['message' => '이 디스코드 계정에 연동된 계정이 이미 존재합니다. 다른 디스코드 계정을 사용하거나 기존 계정의 연동을 해제해주세요.']);
                }

                $currentUser->discord_id = $user->id;
                $currentUser->save();
                return redirect($redirect ?: '/login');
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

            auth()->login($u);
            return redirect($redirect ?: '/user/profile');
        }

        auth()->login($u);

        return redirect('/login');
    }
}
