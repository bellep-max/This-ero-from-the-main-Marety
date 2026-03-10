<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-22
 * Time: 18:11.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Sitemap\SitemapStoreRequest;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Playlist;
use App\Models\Post;
use App\Models\Song;
use App\Services\AjaxViewService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use View;

class SitemapController
{
    private const DEFAULT_ROUTE = 'backend.sitemap.index';

    private AjaxViewService $ajaxViewService;

    public function __construct(AjaxViewService $ajaxViewService)
    {
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index(Request $request)
    {
        $view = View::make('backend.sitemap.index');

        if (file_exists(public_path('sitemap.xml'))) {
            $view->with([
                'filemtime' => Carbon::parse(filemtime(public_path('sitemap.xml')))->format('Y/m/d H:i:s'),
            ]);
        }

        return $request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
            : $view;
    }

    public function store(SitemapStoreRequest $request): RedirectResponse
    {
        $sitemap = Sitemap::create();

        // General the category
        foreach (Category::all() as $category) {
            $sitemap->add(Url::create($category)
                ->setLastModificationDate($category->updated_at)
                ->setPriority($request->input('blog_priority')));
        }

        // General the post
        Post::query()
            ->orderBy('id', 'desc')
            ->limit($request->input('post_num', 1000))
            ->chunk(200, function (Collection $posts) use ($sitemap, $request) {
                foreach ($posts as $post) {
                    $sitemap->add(Url::create($post)
                        ->setLastModificationDate($post->updated_at)
                        ->setPriority($request->input('blog_priority')));
                }
            });

        // General the genre
        foreach (Genre::query()->discover()->get() as $genre) {
            $sitemap->add(Url::create($genre)
                ->setLastModificationDate($genre->updated_at)
                ->setPriority($request->input('song_priority')));
        }

        // General the playlist
        Playlist::query()
            ->orderBy('id', 'desc')
            ->limit($request->input('song_num', 1000))
            ->has('user')
            ->with('user:id,username')
            ->chunk(200, function (Collection $playlists) use ($sitemap, $request) {
                foreach ($playlists as $playlist) {
                    $playlist->username = $playlist->user->username;
                    $sitemap->add(Url::create($playlist)
                        ->setLastModificationDate($playlist->updated_at)
                        ->setPriority($request->input('song_priority')));
                }
            });

        // General music
        Artist::query()
            ->orderBy('id', 'desc')
            ->limit($request->input('song_num', 1000))
            ->chunk(200, function (Collection $artists) use ($sitemap, $request) {
                foreach ($artists as $artist) {
                    $sitemap->add(Url::create($artist)
                        ->setLastModificationDate($artist->updated_at)
                        ->setPriority($request->input('song_priority')));
                }
            });

        Album::query()
            ->orderBy('id', 'desc')
            ->limit($request->input('song_num', 1000))
            ->has('user')
            ->with('user:id,username')
            ->chunk(200, function (Collection $albums) use ($sitemap, $request) {
                foreach ($albums as $album) {
                    $sitemap->add(Url::create($album)
                        ->setLastModificationDate($album->updated_at)
                        ->setPriority($request->input('song_priority')));
                }
            });

        Song::query()
            ->orderBy('id', 'desc')
            ->limit($request->input('song_num', 1000))
            ->has('user')
            ->with('user:id,username')
            ->chunk(200, function (Collection $songs) use ($sitemap, $request) {
                foreach ($songs as $song) {
                    $sitemap->add(Url::create($song)
                        ->setLastModificationDate($song->updated_at)
                        ->setPriority($request->input('song_priority')));
                }
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        return MessageHelper::redirectMessage('Sitemap successfully updated!', self::DEFAULT_ROUTE);
    }
}
