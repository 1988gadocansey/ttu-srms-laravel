<?php

namespace App\Policies;

use App\User;
use App\Models;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradePolicy
{
    use HandlesAuthorization;
    
    



    /**
     * Determine if the given user can update the given grade record.
     *
     * @param  User  $user
     * @param  AcademicRecordsModel  $record
     * @return bool
     */
    public function view(User $user, Models\AcademicRecordsModel $record)
    {
         
        return $user->id === $record->lecturer;
    }

    /**
     * Determine if the given user can update the given grade record.
     *
     * @param  User  $user
     * @param  AcademicRecordsModel  $record
     * @return bool
     */
    public function update(User $user, Models\AcademicRecordsModel $record)
    {
        dd("dd");
        return $user->id===$record->lecturer;
    }
    /**
     * Determine if the given user can delete the given grade record.
     *
     * @param  User  $user
     * @param  AcademicRecordsModel  $record
     * @return bool
     */
    public function destroy(User $user, Models\AcademicRecordsModel $record)
    {
        return $user->id === $record->lecturer;
    }
     public function boot(GateContract $gate)
    {
        
            parent::registerPolicies($gate);
        
    }
    
}
