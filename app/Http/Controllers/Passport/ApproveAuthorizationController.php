<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use League\OAuth2\Server\AuthorizationServer;
use Nyholm\Psr7\Response as Psr7Response;

class ApproveAuthorizationController
{
    use ConvertsPsrResponses, RetrievesAuthRequestFromSession;

    protected $server;

    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    public function approve(Request $request)
    {
        $this->assertValidAuthToken($request);

        $authRequest = $this->getAuthRequestFromSession($request);
        $data = $this->server->completeAuthorizationRequest($authRequest, new Psr7Response);

        return Inertia::location($data->getHeader('Location')[0]);


//        return $this->convertResponse(
//            $this->server->completeAuthorizationRequest($authRequest, new Psr7Response)
//        );
    }
}
