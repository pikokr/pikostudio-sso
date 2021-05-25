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

        if (!$request->user()->hasTeamPermission($team, 'read')) return back(401);

        return Jetstream::inertia()->render($request, 'Teams/Clients', [
            'clients' => $this->clients->activeForUser($request->user()->currentTeam->id)->makeVisible('secret'),
            'permissions' => [
                'canAddClient' => $request->user()->hasTeamPermission($team, 'create'),
                'canEditClient' => $request->user()->hasTeamPermission($team, 'update'),
                'canDeleteClient' => $request->user()->hasTeamPermission($team, 'delete'),
            ],
        ]);
    }
}
