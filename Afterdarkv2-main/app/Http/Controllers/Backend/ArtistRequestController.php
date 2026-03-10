<?php

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\ArtistRequest\ArtistRequestIndexRequest;
use App\Http\Requests\Backend\ArtistRequest\ArtistRequestUpdateRequest;
use App\Models\Album;
use App\Models\Artist;
use App\Models\ArtistRequest;
use App\Models\Email;
use App\Models\Song;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class ArtistRequestController
{
    private const DEFAULT_ROUTE = 'backend.artist.access.index';

    public function index(ArtistRequestIndexRequest $request): View|Application|Factory
    {
        $search = $request->get('search');

        $query = ArtistRequest::query()->withoutGlobalScopes();

        $data = $query->when($search, function ($query) use ($search) {
            $query->where('artist_name', 'LIKE', '%' . $search . '%')
                ->orWhereHas('artist', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
        })->with('artist')
            ->paginate(20);

        return view('backend.artist-access.index')
            ->with([
                'requests' => $data,
                'total_requests' => $query->count(),
                'search' => $search,
            ]);
    }

    //    public function add()
    //    {
    //        return view('backend.artists.add');
    //    }

    public function edit(ArtistRequest $artistRequest): View|Application|Factory
    {
        return view('backend.artist-access.edit')
            ->with([
                'artist_request' => $artistRequest,
            ]);
    }

    public function update(ArtistRequest $artistRequest, ArtistRequestUpdateRequest $request): RedirectResponse
    {
        if ($request->input('reject')) {
            try {
                (new Email)->rejectedArtist($artistRequest, $request->input('comment'));
            } catch (Exception $e) {
            }

            $artistRequest->delete();

            return MessageHelper::redirectMessage('The artist has been rejected!', self::DEFAULT_ROUTE);
        }

        $artistRequest->update([
            'approved' => DefaultConstants::TRUE,
        ]);

        if ($artistRequest->artist) {
            $artistRequest->user->artist_id = $artistRequest->artist->id;
            $artistRequest->user->save();
            $artistRequest->artist->verified = DefaultConstants::TRUE;
            $artistRequest->artist->save();

            Song::query()
                ->where('artistIds', 'REGEXP', '(^|,)(' . $artistRequest->artist->id . ')(,|$)')
                ->update([
                    'user_id' => $artistRequest->user->id,
                ]);

            Album::query()
                ->where('artistIds', 'REGEXP', '(^|,)(' . $artistRequest->artist->id . ')(,|$)')
                ->update([
                    'user_id' => $artistRequest->user->id,
                ]);
        } else {
            $artist = Artist::create([
                'name' => $artistRequest->artist_name,
                'verified' => DefaultConstants::TRUE,
            ]);

            $artistRequest->user->update([
                'artist_id' => $artist->id,
            ]);

            $artistRequest->update([
                'artist_id' => $artist->id,
            ]);
        }

        try {
            (new Email)->approvedArtist($artistRequest);
        } catch (Exception $e) {
        }

        return MessageHelper::redirectMessage('The artist has been approved!', self::DEFAULT_ROUTE);
    }

    public function reject(ArtistRequest $artistRequest): RedirectResponse
    {
        try {
            (new Email)->rejectedArtist($artistRequest);
        } catch (Exception $e) {
        }

        $artistRequest->delete();

        return MessageHelper::redirectMessage('The artist request has been rejected!', self::DEFAULT_ROUTE);
    }
}
