<?php

namespace App\Policies;

use App\Models\Country;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Str;

class CountryPolicy
{
    /**
     * Define the list of email domains that are allowed to access the country resources.
     *
     * @var array
     */
    private array $allowedDomains = [
        '@auren-con-permiso.com',
        '@example.com',
    ];


    private function isAurenUser(User $user): bool
    {
        return Str::endsWith($user->email, $this->allowedDomains);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->isAurenUser($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Country $country): bool
    {
        return $this->isAurenUser($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isAurenUser($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Country $country): bool
    {
        return $this->isAurenUser($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Country $country): bool
    {
        return $this->isAurenUser($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Country $country): bool
    {
        return $this->isAurenUser($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Country $country): bool
    {
        return $this->isAurenUser($user);
    }
}
