<?php

namespace App\Providers;

use App\Models\Passport\Client;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::tokensCan([
            'discord' => '디스코드 ID 확인하기',
            'identify' => '계정 정보 보기'
        ]);

        Passport::routes(null, [
            'namespace' => 'App\Http\Controllers\Passport'
        ]);
    }
}
