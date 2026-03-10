<?php

namespace App\Http\Resources;

use App\Http\Resources\User\UserShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'subject' => NotificationSubjectResource::make($this->notificationable),
            'user' => UserShortResource::make($this->hostable),
            'created_at' => $this->created_at->diffForHumans(),
            'is_read' => (bool) $this->read_at,
        ];
    }
}
