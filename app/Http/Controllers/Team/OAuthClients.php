<?php
namespace App\Http\Controllers\Team;

use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;
use Laravel\Passport\ClientRepository;

class OAuthClients {
    private $clients;

    public function __construct(ClientRepository $clients)
    {
        $this->clients = $clients;
    }

    public function main(Request $request, $teamId) {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);


        return Jetstream::inertia()->render($request, 'Teams/Clients', [
            'clients' => $this->clients->activeForUser($request->user()->currentTeam->id),
            'permissions' => [
                'canManageClient' => $request->user()->hasTeamPermission($team, 'create'),
            ],
        ]);
    }
}
