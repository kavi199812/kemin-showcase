<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Company $company): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, Company $company): bool
    {
        return (bool) $user->is_admin;
    }

    public function delete(User $user, Company $company): bool
    {
        return (bool) $user->is_admin;
    }

    public function restore(User $user, Company $company): bool
    {
        return (bool) $user->is_admin;
    }

    public function forceDelete(User $user, Company $company): bool
    {
        return (bool) $user->is_admin;
    }
}
