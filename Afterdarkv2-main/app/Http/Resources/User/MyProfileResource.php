<?php

namespace App\Http\Resources\User;

use App\Enums\RoleEnum;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\Playlist\PlaylistShortResource;
use App\Http\Resources\Podcast\PodcastShortResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'username' => $this->username,
            'artwork' => $this->artwork,
            'linktree_link' => $this->linktree_link,
            'gender' => $this->gender,
            'country' => $this->country_id,
            'bio' => $this->bio,
            'birth' => $this->birth->format('Y-m-d'),
            'restore_queue' => $this->restore_queue,
            'allow_comments' => $this->allow_comments,
            'play_pause_fade' => $this->play_pause_fade,
            'own_profile' => true,
            'allow_upload' => $this->resource->hasAnyRole(RoleEnum::getUploadingRoles()),
            'can_upload' => $this->when($this->resource->hasAnyRole(RoleEnum::getUploadingRoles()), UserService::canUpload($this->resource)),
            'subscription' => $this->when($this->hasActiveSubscription(), SubscriptionResource::make($this->activeSubscription())),
            'is_admin' => $this->when($this->resource->hasAnyRole(RoleEnum::getAdminRoles()), true),
            'playlists' => $this->whenLoaded('playlists', PlaylistShortResource::collection($this->playlists)),
            'collaborated_playlists' => $this->whenLoaded('approvedCollaboratedPlaylists', PlaylistShortResource::collection($this->approvedCollaboratedPlaylists)),
            'group_settings' => $this->whenLoaded('group', $this->group?->permissions),
            'role' => $this->roles()->first()?->name,
            'unread_notifications' => $this->whenLoaded('unreadNotifications', NotificationResource::collection($this->unreadNotifications)),
            'podcasts' => $this->whenLoaded('podcasts', PodcastShortResource::collection($this->podcasts)),
            'type' => User::class,
        ];
    }
}
