<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Jetstream;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Http\Rules\RedirectRule;
use Laravel\Passport\Passport;

class ClientController
{
    /**
     * The client repository instance.
     *
     * @var \Laravel\Passport\ClientRepository
     */
    protected $clients;

    /**
     * The validation factory implementation.
     *
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected $validation;

    /**
     * The redirect validation rule.
     *
     * @var \Laravel\Passport\Http\Rules\RedirectRule
     */
    protected $redirectRule;

    /**
     * Create a client controller instance.
     *
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @param  \Illuminate\Contracts\Validation\Factory  $validation
     * @param  \Laravel\Passport\Http\Rules\RedirectRule  $redirectRule
     * @return void
     */
    public function __construct(
        ClientRepository $clients,
        ValidationFactory $validation,
        RedirectRule $redirectRule
    ) {
        $this->clients = $clients;
        $this->validation = $validation;
        $this->redirectRule = $redirectRule;
    }

    /**
     * Get all of the clients for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser(Request $request)
    {
        $userId = $request->user()->currentTeam->id;

        $clients = $this->clients->activeForUser($userId);

        if (Passport::$hashesClientSecrets) {
            return $clients;
        }

        return $clients->makeVisible('secret');
    }

    public function store(Request $request)
    {
        $team = Jetstream::newTeamModel()->findOrFail($request->user()->currentTeam->id);

        if (!$request->user()->hasTeamPermission($team, 'create')) return back(401);

        $this->validation->make($request->all(), [
            'name' => 'required|max:191',
            'redirect' => ['required', $this->redirectRule],
            'confidential' => 'boolean',
        ])->validate();

        $client = $this->clients->create(
            $request->user()->currentTeam->id, $request->name, $request->redirect,
            null, false, false, (bool) $request->input('confidential', true)
        );

        return back(303);
    }

    public function update(Request $request, $clientId)
    {
        $teamId = $request->user()->currentTeam->id;

        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        if (!$request->user()->hasTeamPermission($team, 'update')) return back(401);

        $client = $this->clients->findForUser($clientId, $teamId);

        if (! $client) {
            return new Response('', 404);
        }

        $this->validation->make($request->all(), [
            'name' => 'required|max:191',
            'redirect' => ['required', $this->redirectRule],
        ])->validate();

        $this->clients->update(
            $client, $request->name, $request->redirect
        );

        return back(303);
    }

    public function destroy(Request $request, $clientId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($request->user()->currentTeam->id);

        if (!$request->user()->hasTeamPermission($team, 'delete')) return back(401);

        $client = $this->clients->findForUser($clientId, $request->user()->currentTeam->id);

        if (! $client) {
            return new Response('', 404);
        }

        $this->clients->delete($client);

        return back(303);
    }
}
