<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'link' => $this->whenNotNull($this->title_link),
            'description' => $this->description,
            'artwork' => $this->artwork,
        ];
    }
}
