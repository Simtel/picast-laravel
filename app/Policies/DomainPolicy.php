<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Domain $domain
     * @return mixed
     */
    public function view(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Domain $domain
     * @return mixed
     */
    public function update(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Domain $domain
     * @return mixed
     */
    public function delete(User $user, Domain $domain): bool
    {
        return $domain->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Domain $domain
     * @return mixed
     */
    public function restore(User $user, Domain $domain): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Domain $domain
     * @return mixed
     */
    public function forceDelete(User $user, Domain $domain): bool
    {
        return false;
    }
}
