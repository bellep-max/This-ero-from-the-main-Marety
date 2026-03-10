<?php

namespace App\Events;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddedTrackToPlaylist
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Playlist $playlist, public Song $song) {}
}
