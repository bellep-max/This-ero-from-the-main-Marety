<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Blog\BlogSearchRequest;
use App\Http\Resources\CategoryShortResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\Post\PostArchiveCountResource;
use App\Http\Resources\Post\PostFullResource;
use App\Http\Resources\Post\PostShortResource;
use App\Http\Resources\Post\PostTagShortResource;
use App\Models\Category;
use App\Models\Group;
use App\Models\Poll;
use App\Models\Post;
use App\Services\BlogService;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Torann\LaravelMetaTags\Facades\MetaTag;

class BlogController extends Controller
{
    private BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function index(BlogSearchRequest $request): Response
    {
        $posts = Post::query()
            ->when($request->input('categories'), function ($query) use ($request) {
                $query->whereIn('category_id', $request->input('categories'));
            })
            ->when($request->input('dates'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    foreach ($request->input('dates') as $index => $date) {
                        $date = Carbon::parse($date);

                        if ($index === 0) {
                            $query->where(function ($query) use ($date) {
                                $query->whereYear('created_at', $date->format('Y'))
                                    ->whereMonth('created_at', $date->format('m'));
                            });
                        } else {
                            $query->orWhere(function ($query) use ($date) {
                                $query->whereYear('created_at', $date->format('Y'))
                                    ->whereMonth('created_at', $date->format('m'));
                            });
                        }

                    }
                });
            })
            ->when($request->input('tags'), function ($query) use ($request) {
                $query->whereHas('tags', function ($query) use ($request) {
                    $query->whereIn('tag', $request->input('tags'));
                });
            })
            ->orderBy('fixed', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Blog/Index', [
            'posts' => PostShortResource::collection($posts),
            'categories' => CategoryShortResource::collection(Category::all('id', 'name')),
            'archives' => PostArchiveCountResource::collection($this->blogService->getPostStats()),
            'tags' => PostTagShortResource::collection($this->blogService->getPostTags()),
            'filters' => $request->validated(),
        ]);
    }

    public function show(Post $post): Response
    {
        $post->loadMissing([
            'category',
            'user',
        ]);

        if ($post->allow_comments) {
            $post->loadMissing([
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->with([
                            'replies.user:id,name',
                            'user:id,name',
                        ]);
                },
            ]);
        }

        //        $options = groupPermission($post->access);
        //
        //        if (isset($options[Group::groupId()])) {
        //            $permission = $options[Group::groupId()];
        //
        //            switch ($permission) {
        //                case 1:
        //                    $post->allow_comments = DefaultConstants::FALSE;
        //                    break;
        //                case 2:
        //                    $post->allow_comments = DefaultConstants::TRUE;
        //                    break;
        //                case 3:
        //                    abortNoPermission();
        //                    break;
        //            }
        //        }

        if (stripos($post->full_content, '[hide') !== false) {
            $post->full_content = preg_replace_callback(
                "#\[hide(.*?)](.+?)\[/hide]#is",
                function ($matches) {
                    $matches[1] = str_replace(['=', ' '], '', $matches[1]);

                    return Group::getValue('blog_allow_hide')
                        ? $matches[2]
                        : '<div class="alert alert-danger">' . __('blog.prohibited_of_viewing') . '</div>';
                },
                $post->full_content
            );
        }

        //        $post->full_content = preg_replace("'\[attachment=(.*?)\:(.*?)\]'si", '<span class="attachment"><a href="'.route('frontend.post.download.attachment', ['id' => $post->id, 'attachment-id' => 111]).'" target="_blank">$2</a> [306.84 Kb] (downloads: 2)</span>', $post->full_content);
        $post->full_content = $this->blogService->setPostLink($post->full_content ?? '');
        $post->short_content = $this->blogService->setPostLink($post->short_content ?? '');

        /* Set meta tags */
        MetaTag::set('title', $post->meta_title ? $post->meta_title : $post->title);
        MetaTag::set('description', $post->meta_description ? $post->meta_description : $post->short_content);
        //        MetaTag::set('keywords', $post->meta_keywords ? $post->meta_keywords : keywordGenerator($post->full_content));
        isset($post->artwork) && MetaTag::set('image', url($post->artwork));

        $post->increment('view_count', 1);

        return Inertia::render('Blog/Show', [
            'post' => PostFullResource::make($post),
            'comments' => CommentResource::collection($post->comments),
            'poll' => $post->poll()->visible()->exists()
                ? Poll::buildResult($post->poll)
                : null,
        ]);
    }
}
