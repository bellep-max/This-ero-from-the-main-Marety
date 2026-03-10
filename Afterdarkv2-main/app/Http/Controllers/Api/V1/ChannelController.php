<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Services\ChannelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class ChannelController extends ApiController
{
    public function __construct(private readonly ChannelService $channelService) {}

    public function show(Channel $channel): JsonResponse
    {
        Gate::authorize('show', $channel);

        $objects = $this->channelService->getChannelObjects($channel);

        return $this->success([
            'channel' => ChannelResource::make($channel),
            'objects' => $this->channelService->setChannelObjectResource($channel, $objects->items()),
            'pagination' => Arr::except($objects->toArray(), 'items'),
        ]);
    }
}
