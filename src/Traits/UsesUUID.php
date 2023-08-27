<?php

namespace MetaverseSystems\AuthApi\Traits;

trait UsesUUID
{
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->{$model->getKeyName()} = (string)\Str::uuid(); 
        });
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function getIncrementing()
    {
        return false;
    }
}
