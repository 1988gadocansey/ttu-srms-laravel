<?php

use \Illuminate\Contracts\Auth\Access\GateContract;
use \App\Policies\AdminPolicy;

// app/Policies/AdminPolicy.php
class AdminPolicy
{
    public function managePages($user){
        return $user->hasRole(['Admin', 'Rector']); 
    }

    // app/Providers/AuthServiceProvider.php
    public function boot(GateContract $gate)
    {
        foreach (get_class_methods(new AdminPolicy) as $method) {
            $gate->define($method, "App\Policies\AdminPolicy@{$method}"); }
            $this->registerPolicies($gate);
        
    }
}

$this->authorize('managePages'); // in Controllers
@can('managePages') ;// in Blade Templates
$user->can('managePages'); // via Eloquent