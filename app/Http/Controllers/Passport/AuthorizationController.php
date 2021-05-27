<?php

namespace App\Http\Controllers\Passport;

use App\Models\Team;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;
use Laravel\Passport\Bridge\User;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\AuthorizationServer;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use SebastianBergmann\Environment\Console;

class AuthorizationController
{
    use HandlesOAuthErrors;

    protected $server;

    protected $response;

    public function __construct(AuthorizationServer $server, ResponseFactory $response)
    {
        $this->server = $server;
        $this->response = $response;
    }

    public function authorize(ServerRequestInterface $psrRequest,
                              Request $request,
                              ClientRepository $clients,
                              TokenRepository $tokens)
    {
        $authRequest = $this->withErrorHandling(function () use ($psrRequest) {
            return $this->server->validateAuthorizationRequest($psrRequest);
        });


        $user = $request->user();

        $scopes = $this->parseScopes($authRequest);

        if (array_map(function ($i) {
                return $i->id;
            }, $scopes) && !$user->discord_id) {
            return Socialite::driver('discord')->stateless()->with(['state' => $request->fullUrl()])->redirect();
        }

//        $token = $tokens->findValidToken(
//            $user = $request->user(),
//        );

        $client = $clients->find($authRequest->getClient()->getIdentifier());


//        Log::info($token->scopes);
//
//        if (($token && $token->scopes === collect($scopes)->pluck('id')->all()) ||
//            $client->skipsAuthorization()) {
//            return $this->approveRequest($authRequest, $user);
//        }


        $team = Jetstream::newTeamModel()->findOrFail($client->user_id);

        $request->session()->put('authToken', $authToken = Str::random());
        $request->session()->put('authRequest', $authRequest);

//        return $this->response->view('passport::authorize', [
//            'client' => $client,
//            'user' => $user,
//            'scopes' => $scopes,
//            'request' => $request,
//            'authToken' => $authToken,
//        ]);

        return Jetstream::inertia()->render($request, 'Authorize', [
            'client' => $client,
            'user' => $user,
            'scopes' => $scopes,
            'request' => $request,
            'authToken' => $authToken,
            'team' => $team->name
        ]);
    }

    protected function parseScopes($authRequest)
    {
        return Passport::scopesFor(
            collect($authRequest->getScopes())->map(function ($scope) {
                return $scope->getIdentifier();
            })->unique()->all()
        );
    }

    protected function approveRequest($authRequest, $user)
    {
        $authRequest->setUser(new User($user->getAuthIdentifier()));

        $authRequest->setAuthorizationApproved(true);

        $data = $this->server->completeAuthorizationRequest($authRequest, new Psr7Response);

        return Inertia::location($data->getHeader('Location')[0]);
    }
}
