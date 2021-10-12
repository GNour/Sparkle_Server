<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteOrUpdate(User $user, Course $course)
    {
        if (($user->role == "Admin" || $user->role == "Manager")
            || ($user->role == "Leader" && $course->created_by == $user->id)) {
            return true;
        }

        return false;
    }
}
