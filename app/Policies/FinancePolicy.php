<?php

namespace App\Policies;

use App\User;
use App\Models;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can delete the given grade record.
     *
     * @param  User  $user
     * @param  Task  $task
     * @return bool
     */
    public function destroy(User $user, Models\FeePaymentModel $record)
    {
        return $user->id === $record->RECIEPIENT;
    }
}
