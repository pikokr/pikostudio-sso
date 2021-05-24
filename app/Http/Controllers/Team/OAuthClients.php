<?php
namespace App\Http\Controllers\Team;

use App\Http\Controllers\Passport\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
