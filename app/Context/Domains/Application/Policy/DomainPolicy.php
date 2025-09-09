<?php

declare(strict_types=1);

namespace App\Context\Domains\Application\Policy;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\User\Domain\Model\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class DomainPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
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
     * @return bool
     */
    public function view(User $user, Domain $domain): bool
    {
        return $user->getid() === $domain->getUser()->getId();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
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
     * @return bool
     */
    public function update(User $user, Domain $domain): bool
    {
        return $user->getid() === $domain->getUser()->getId();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Domain $domain
     * @return bool
     */
    public function delete(User $user, Domain $domain): bool
    {
        return $domain->getUser()->getId() === $user->getid();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Domain $domain
     * @return bool
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
     * @return bool
     */
    public function forceDelete(User $user, Domain $domain): bool
    {
        return false;
    }

}
