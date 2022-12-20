<?php

namespace App\Policies;

use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserNotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserNotification  $userNotification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, UserNotification $userNotification)
    {
        return $user->id === $userNotification->user_id;
    }
}
