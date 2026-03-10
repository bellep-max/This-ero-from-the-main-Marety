<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
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
            'subscription_name' => $this->pivot->subscription_id,
            'artwork' => $this->artwork,
            'status' => $this->pivot->status,
            'amount' => "{$this->pivot->amount} {$this->pivot->currency}",
            'last_payment_date' => $this->pivot->last_payment_date->format('d.m.Y'),
            'next_payment_date' => $this->pivot->next_billing_date->format('d.m.Y'),
        ];
    }
}
