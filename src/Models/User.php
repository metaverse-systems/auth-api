<?php

namespace MetaverseSystems\AuthApi\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use MetaverseSystems\AuthApi\Traits\UsesUUID;
use MetaverseSystems\AuthApi\Notifications\ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;

class User extends \App\Models\User implements MustVerifyEmail
{
    use UsesUUID, HasRoles, SoftDeletes, Notifiable;

    public function guardName()
    {
        return "api";
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, $this->email));
    }
}
