<?php

namespace App\Services;

use App\Http\Requests\Backend\Podcast\PodcastUpdateRequest;
use App\Http\Requests\Frontend\Podcast\PodcastStoreRequest;
use App\Models\Podcast;

class PodcastService
{
    public function store(PodcastStoreRequest $request): ?Podcast
    {
        $podcast = Podcast::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'language_id' => $request->input('language_id'),
            'country_id' => $request->input('country_id'),
            'is_visible' => $request->input('is_visible'),
            'allow_comments' => $request->input('allow_comments'),
            'user_id' => auth()->id(),
        ]);

        if (!$podcast) {
            return null;
        }

        if ($request->filled('tags')) {
            TagService::setModelTags($podcast, $request->input('tags'));
        }

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $podcast);
        }

        return $podcast;
    }

    public function update(PodcastUpdateRequest $request, Podcast $podcast): ?Podcast
    {
        $podcast->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'language_id' => $request->input('language_id'),
            'country_id' => $request->input('country_id'),
            'is_visible' => $request->input('is_visible'),
            'allow_comments' => $request->input('allow_comments'),
        ]);

        if ($request->filled('tags')) {
            TagService::setModelTags($podcast, $request->input('tags'));
        }

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $podcast);
        }

        return $podcast;
    }
}
