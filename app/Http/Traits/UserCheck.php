<?php

namespace App\Http\Traits;

trait UserCheck
{
    public function checkUser($user)
    {
        if ($user->is_super_admin) {
            return true;
        }

        return false;
    }
}
