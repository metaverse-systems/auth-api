<?php

namespace MetaverseSystems\AuthApi\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use MetaverseSystems\AuthApi\Traits\UsesUUID;
use Spatie\Permission\Traits\HasRoles;

class User extends \App\Models\User implements MustVerifyEmail
{
    use UsesUUID, HasRoles, SoftDeletes;

    public function guardName()
    {
        return "api";
    }
}
