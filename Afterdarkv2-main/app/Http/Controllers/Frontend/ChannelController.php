<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Services\ChannelService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Torann\LaravelMetaTags\Facades\MetaTag;

class ChannelController extends Controller
{
    public function __construct(private readonly ChannelService $channelService) {}

    public function show(Channel $channel): Response
    {
        Gate::authorize('show', $channel);

        $objects = $this->channelService->getChannelObjects($channel);

        MetaTag::set('title', $channel->meta_title);
        MetaTag::set('description', $channel->meta_description);

        return Inertia::render('Channel/Show', [
            'channel' => ChannelResource::make($channel),
            'objects' => Inertia::merge($this->channelService->setChannelObjectResource($channel, $objects->items())),
            'pagination' => Arr::except($objects->toArray(), 'items'),
        ]);
    }
}
