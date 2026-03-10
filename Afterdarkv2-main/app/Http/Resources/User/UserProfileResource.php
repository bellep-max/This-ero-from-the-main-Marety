<?php

namespace App\Http\Resources\User;

use App\Models\User;
use App\Services\MenuService;
use App\Services\SubscriptionService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authCheck = auth()->check();

        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'username' => $this->username,
            'artwork' => $this->artwork,
            'favorite' => $this->favorite,
            'linktree_link' => $this->linktree_link,
            'own_profile' => UserService::ownProfile($this->resource),
            'menu' => MenuService::getUserMenu($this->resource),
            'subscription' => $this->when($authCheck && !UserService::ownProfile($this->resource),
                $authCheck ? SubscriptionService::getActiveUserSubscription(auth()->user(), $this->resource->id) : null
            ),
            $this->mergeWhen($request->routeIs('users.show') || $request->routeIs('songs.show') || $request->routeIs('playlists.show'), [
                'favorites' => $this->loves_count,
                'total_plays' => $this->total_plays,
            ]),
            $this->mergeWhen($request->routeIs('users.show') || $request->routeIs('users.followers.index'), [
                'patrons_count' => $this->whenHas('active_patrons_count'),
                'free_followers_count' => $this->whenHas('free_followers_count'),
            ]),
            'type' => User::class,
            'role' => $this->resource->roles()?->first()?->name,
        ];
    }
}
