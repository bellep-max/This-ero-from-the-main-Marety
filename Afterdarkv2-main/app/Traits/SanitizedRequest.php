<?php

namespace App\Traits;

trait SanitizedRequest
{
    public static function bootSanitizedRequest(): void
    {
        /*
         * Prevent xss attack by clean the attribute
         */
        self::creating(function ($model) {
            foreach ($model->attributes as $key => $attribute) {
                if (is_string($attribute)) {
                    $model->$key = trim(clean($attribute));
                }
            }
        });

        self::saving(function ($model) {
            foreach ($model->attributes as $key => $attribute) {
                if (is_string($attribute)) {
                    $model->$key = trim(clean($attribute));
                }
            }
        });

        self::created(function ($model) {});

        self::updating(function ($model) {});

        self::updated(function ($model) {});

        self::deleting(function ($model) {});

        self::deleted(function ($model) {});
    }
}
