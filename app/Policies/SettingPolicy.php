<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function manage(User $user): bool
    {
        return (bool) $user->is_admin;
    }
}
