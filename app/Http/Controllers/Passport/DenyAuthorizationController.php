<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class DenyAuthorizationController
{
    use RetrievesAuthRequestFromSession;

    protected $response;

    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    public function deny(Request $request)
    {
        $this->assertValidAuthToken($request);

        $authRequest = $this->getAuthRequestFromSession($request);

        $clientUris = Arr::wrap($authRequest->getClient()->getRedirectUri());

        if (! in_array($uri = $authRequest->getRedirectUri(), $clientUris)) {
            $uri = Arr::first($clientUris);
        }

        $separator = $authRequest->getGrantTypeId() === 'implicit' ? '#' : (strstr($uri, '?') ? '&' : '?');

        return Inertia::location(
            $uri.$separator.'error=access_denied&state='.$request->input('state')
        );
    }
}
