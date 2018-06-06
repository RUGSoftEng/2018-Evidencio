<?php

namespace App\Policies;

use App\User;
use App\Workflow;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkflowPolicy
{
    use HandlesAuthorization;


    /**
     * Method run before the actual policy method
     *
     * @param  \App\User  $user
     * @param  \App\Workflow  $workflow
     * @return mixed
     */
    public function before($user, $ability)
    {
        if ($user->is_administrator) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the workflow on the designer side.
     *
     * @param  \App\User  $user
     * @param  \App\Workflow  $workflow
     * @return mixed
     */
    public function view(User $user, Workflow $workflow)
    {
        return $workflow->author_id == $user->id || $user->is_reviewer;
    }

    /**
     * Determine whether the user can save a workflow.
     *
     * @param  \App\User  $user
     * @param  \App\Workflow  $workflow
     * @return mixed
     */
    public function save(User $user, $workflow)
    {
        return $workflow->author_id == $user->id;
    }

    /**
     * Determine whether the user can delete the workflow.
     *
     * @param  \App\User  $user
     * @param  \App\Workflow  $workflow
     * @return mixed
     */
    public function delete(User $user, Workflow $workflow)
    {
        return $workflow->author_id == $user->id;
    }
}
