<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TodoPolicy
{
    use HandlesAuthorization;

    public function deleteOrUpdate(User $user, Todo $todo)
    {
        if (($user->role == "Admin" || $user->role == "Manager")
            || ($user->role == "Leader" && $todo->created_by == $user->id)) {
            return true;
        }

        return false;
    }
}
