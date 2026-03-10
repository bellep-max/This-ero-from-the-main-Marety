<?php

namespace App\Traits;

use App\Constants\DefaultConstants;

trait PrivacyTrait
{
    public function scopeVisible($query)
    {
        return $query->where(function ($query) {
            $query->where('is_visible', DefaultConstants::TRUE)
                ->orWhere('user_id', auth()->id());
        });
    }

    public function scopeApproved($query)
    {
        return $query->where(function ($query) {
            $query->where('approved', DefaultConstants::TRUE)
                ->orWhere('user_id', auth()->id());
        });
    }

    public function scopeFullPrivacy($query)
    {
        return $query->where('user_id', auth()->id())
            ->orWhere(function ($query) {
                $query->where('is_visible', DefaultConstants::TRUE)
                    ->orWhere('approved', DefaultConstants::TRUE);
            });
    }
}
