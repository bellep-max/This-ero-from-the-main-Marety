<?php

namespace App\Models;

use App\Enums\ActivityTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'object_id',
        'notificationable_id',
        'notificationable_type',
        'hostable_id',
        'hostable_type',
        'action',
        'read_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function getActionAttribute($value): ?ActivityTypeEnum
    {
        if (empty($value)) {
            return ActivityTypeEnum::default;
        }

        try {
            return ActivityTypeEnum::from($value);
        } catch (\ValueError $e) {
            // Handle legacy camelCase values
            $mapping = [
                'followUser' => ActivityTypeEnum::followUserCamel,
                'commentMusic' => ActivityTypeEnum::commentMusic,
                'replyComment' => ActivityTypeEnum::replyCommentCamel,
                'reactComment' => ActivityTypeEnum::reactComment,
            ];

            return $mapping[$value] ?? ActivityTypeEnum::default;
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notificationable(): MorphTo
    {
        return $this->morphTo();
    }

    public function hostable(): MorphTo
    {
        return $this->morphTo();
    }
}
