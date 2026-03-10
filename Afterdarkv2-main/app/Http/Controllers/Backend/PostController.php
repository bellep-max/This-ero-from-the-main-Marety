<?php

/**
 * Created by PhpStorm.
 * User: lechchut
 * Date: 7/23/19
 * Time: 3:55 PM.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Constants\NewsStatusConstants;
use App\Constants\SearchConstants;
use App\Constants\TypeConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Post\PostIndexRequest;
use App\Http\Requests\Backend\Post\PostMassActionRequest;
use App\Http\Requests\Backend\Post\PostStoreRequest;
use App\Http\Requests\Backend\Post\PostUpdateRequest;
use App\Models\Poll;
use App\Models\Post;
use App\Models\PostTag;
use App\Services\ArtworkService;
use App\Services\Backend\BackendService;
use Cache;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use View;

class PostController
{
    private const DEFAULT_ROUTE = 'backend.posts.index';

    public function index(PostIndexRequest $request): Factory|Application|\Illuminate\Contracts\View\View
    {
        $posts = Post::query()
            ->withoutGlobalScopes()
            ->when($request->input('term'), function ($query) use ($request) {
                if ($request->input('location')) {
                    switch ($request->input('location')) {
                        case SearchConstants::CODE_EVERYWHERE:
                            $query->search($request->input('term'));
                            break;
                        case SearchConstants::CODE_TITLE:
                            $query->where('title', 'LIKE', '%' . $request->input('term') . '%');
                            break;
                        case SearchConstants::CODE_SHORT_CONTENT:
                            $query->where('short_content', 'LIKE', '%' . $request->input('term') . '%');
                            break;
                        case SearchConstants::CODE_FULL_CONTENT:
                            $query->where('full_content', 'LIKE', '%' . $request->input('term') . '%');
                            break;
                    }
                } else {
                    $query->where('title', 'LIKE', '%' . $request->input('term') . '%');
                }
            })
            ->when($request->input('userIds'), function ($query) use ($request) {
                $query->whereIn('id', $request->input('userIds'));
            })
            ->when($request->input('category'), function ($query) use ($request) {
                $query->where('category', 'REGEXP', '(^|,)(' . implode(',', $request->input('category')) . ')(,|$)');
            })
            ->when($request->input('created_from'), function ($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->input('created_from')));
            })
            ->when($request->input('created_until'), function ($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->input('created_until')));
            })
            ->when($request->input('comment_count_from'), function ($query) use ($request) {
                $query->where('comment_count', '>=', $request->input('comment_count_from'));
            })
            ->when($request->input('comment_count_until'), function ($query) use ($request) {
                $query->where('comment_count', '<=', $request->input('comment_count_until'));
            })
            ->when($request->input('fixed'), function ($query) {
                $query->where('fixed', DefaultConstants::TRUE);
            })
            ->when($request->input('comment_disabled'), function ($query) {
                $query->where('allow_comments', DefaultConstants::FALSE);
            })
            ->when($request->input('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($request->input('approved'), function ($query) use ($request) {
                $query->where('approved', $request->input('approved'));
            })
            ->when($request->input('scheduled'), function ($query) {
                $query->where('created_at', '>', now());
            })
            ->when($request->input('status') && $request->input('status') != NewsStatusConstants::CODE_ALL, function ($query) use ($request) {
                $query->where('approved', intval($request->input('status')));
            })
            ->when($request->input('title'), function ($query) use ($request) {
                $query->orderBy('title', $request->input('title'));
            })
            ->when($request->input('created_at'), function ($query) use ($request) {
                $query->orderBy('created_at', $request->input('created_at'));
            })
            ->when($request->input('view_count'), function ($query) use ($request) {
                $query->orderBy('view_count', $request->input('view_count'));
            });

        return view('backend.posts.index')
            ->with([
                'posts' => $request->has('results_per_page')
                    ? $posts->paginate(intval($request->input('results_per_page')))
                    : $posts->paginate(20),
                'total_posts' => $posts->count(),
            ]);
    }

    public function create(): Factory|Application|\Illuminate\Contracts\View\View
    {
        return view('backend.posts.create');
    }

    public function store(PostStoreRequest $request): RedirectResponse
    {
        $post = Post::create($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $post);
        }

        if ($request->input('poll_title')) {
            Poll::query()
                ->updateOrCreate([
                    'object_type' => TypeConstants::POST,
                    'object_id' => $post->id,
                ], [
                    'title' => $request->input('poll_title'),
                    'body' => $request->input('poll_answers'),
                    'multiple' => $request->input('poll_multiple'),
                    'is_visible' => $request->input('poll_visibility'),
                    'ended_at' => $request->input('poll_ended_at'),
                ]);
        }

        /*
         * Save to post tags cloud
         */

        if ($request->input('tags_uncompressed')) {
            foreach ($request->input('tags_uncompressed') as $tag) {
                $post->tags()->create(['tag' => $tag]);
            }
        }

        Cache::clear('post_tags');

        return MessageHelper::redirectMessage('Article successfully created!', self::DEFAULT_ROUTE);
    }

    public function edit(Post $post, Request $request)
    {
        $options = BackendService::groupPermission($post->access);

        $view = View::make('backend.posts.edit')
            ->with([
                'post' => $post,
                'options' => $options,
                'poll' => Poll::query()->where('object_type', TypeConstants::POST)->where('object_id', $post->id)->first(),
            ]);

        if ($request->ajax()) {
            $sections = $view->renderSections();

            return $sections['media'];
        }

        return $view;
    }

    public function update(Post $post, PostUpdateRequest $request): RedirectResponse
    {
        $post->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $post);
        }

        if ($request->input('poll_title')) {
            Poll::query()
                ->updateOrCreate([
                    'object_type' => TypeConstants::POST,
                    'object_id' => $post->id,
                ], [
                    'title' => $request->input('poll_title'),
                    'body' => $request->input('poll_answers'),
                    'multiple' => $request->input('poll_multiple'),
                    'is_visible' => $request->input('poll_visibility'),
                    'ended_at' => $request->input('poll_ended_at'),
                ]);
        } else {
            $post->polls()->delete();
        }

        /*
         * Save to post tags cloud
         */

        if ($request->input('tags_uncompressed')) {
            $post->tags()->delete();

            foreach ($request->input('tags_uncompressed') as $tag) {
                $post->tags()->create(['tag' => $tag]);
            }
        }

        Cache::clear('post_tags');

        return MessageHelper::redirectMessage('Article successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return MessageHelper::redirectMessage('Article successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function batch(PostMassActionRequest $request)
    {
        $postQuery = Post::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $request->input('ids'));

        if ($request->input('action') == ActionConstants::ADD_CATEGORY) {
            return view('backend.commons.mass_category')
                ->with([
                    'message' => 'Add category',
                    'subMessage' => 'Add Category for Chosen Posts (<strong>' . count($request->input('ids')) . '</strong>)',
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::STORE_CATEGORY) {
            foreach ($request->input('ids') as $id) {
                $post = Post::query()->withoutGlobalScopes()->find($id);

                $currentCategory = explode(',', $post->category);
                $newCategory = array_unique(array_merge($currentCategory, $request->input('category')));

                $post->update([
                    'category' => implode(',', $newCategory),
                ]);
            }

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::EDIT_CATEGORY) {
            return view('backend.commons.mass_category')
                ->with([
                    'message' => 'Change category',
                    'subMessage' => 'Change Category for Chosen Posts (<strong>' . count($request->input('ids')) . '</strong>)',
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::UPDATE_CATEGORY) {
            $postQuery
                ->update([
                    'category' => implode(',', $request->input('category')),
                ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::EDIT_AUTHOR) {
            return view('backend.commons.mass_user')
                ->with([
                    'message' => 'Change Author',
                    'subMessage' => 'Change Author for Chosen Posts (<strong>' . count($request->input('ids')) . '</strong>)',
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::UPDATE_AUTHOR) {
            $postQuery->update([
                'user_id' => $request->input('user_id'),
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::APPROVE) {
            $postQuery->update([
                'approved' => DefaultConstants::TRUE,
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::NOT_APPROVE) {
            $postQuery->update([
                'approved' => DefaultConstants::FALSE,
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::SET_CURRENT) {
            $postQuery->update([
                'created_at' => now(),
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::FIXED) {
            $postQuery->update([
                'fixed' => DefaultConstants::TRUE,
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::NOT_FIXED) {
            $postQuery->update([
                'fixed' => DefaultConstants::FALSE,
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::CLEAR_VIEWS) {
            $postQuery->update([
                'view_count' => 0,
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::CLEAR_TAGS) {
            $postQuery->update([
                'tags' => null,
            ]);

            PostTag::query()
                ->whereIn('post_id', $request->input('ids'))
                ->delete();

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::COMMENTED) {
            $postQuery->update([
                'allow_comments' => DefaultConstants::TRUE,
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::UNCOMMENTED) {
            $postQuery->update([
                'allow_comments' => DefaultConstants::FALSE,
            ]);

            return MessageHelper::redirectMessage('Posts successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::DELETE) {
            Post::query()->withoutGlobalScopes()->whereIn('id', $request->input('ids'))->delete();

            return MessageHelper::redirectMessage('Posts successfully deleted!', self::DEFAULT_ROUTE);
        }
    }
}
