<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Team $team)
    {
        // Give access to Admins and Manages; Leaders if they lead the team; User if they are in the team.
        if (($user->role === "Admin" || $user->role === "Manager")
            || ($user->role === "Leader" && $user->id === $team->leader_id)
            || ($user->role === "Staff" && $user->team_id === $team->id)) {
            return true;
        }
        return false;
    }
}
