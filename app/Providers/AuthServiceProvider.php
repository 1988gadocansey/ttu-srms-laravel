<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
       ' App\Models\AcademicRecordsModel' =>'App\Policies\GradePolicy',
         'App\Models\FeeModel' => 'App\Policies\FinancePolicy',
         'App\Models\FeePaymentModel' => 'App\Policies\AdminPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
       /* $gate->before(function ($user, $ability) {
            if ($user->hasRole(['Admin', 'Rector','Vice-Rector','Registrar'])) {
                return true;
            }
        });
        $gate->define('update', function ($user, $record) {
            return $user->id == $record->enteredBy;
        });*/
         
    }
}
