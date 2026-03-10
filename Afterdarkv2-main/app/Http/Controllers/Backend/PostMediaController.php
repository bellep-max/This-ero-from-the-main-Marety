<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Encore\MediaManager;
use App\Models\File;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Laravel\Facades\Image;
use stdClass;

class PostMediaController
{
    private Request $request;

    private const DEFAULT_ROUTE = 'backend.posts.index';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /** Post media controller (use for featured image) */
    public function index()
    {
        $posts = Post::query()
            ->withoutGlobalScopes()
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $media = File::query()
            ->where('post_id', '=', '0')
            ->orderBy('id', 'desc')
            ->first();

        if (!isset($media->id)) {
            $media = new File;
            $media->save();
        }

        $view = View::make('backend.posts.media')
            ->with([
                'posts' => $posts,
                'attachments' => $media->getMedia('attachments'),
                'images' => $media->getMedia('images'),
                'media' => $media,
            ]);

        $sections = $view->renderSections();

        return $sections['content'];
    }

    public function show(File $file)
    {
        $posts = Post::query()
            ->withoutGlobalScopes()
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $view = View::make('backend.posts.media')
            ->with([
                'posts' => $posts,
                'attachments' => $file->getMedia('attachments'),
                'images' => $file->getMedia('images'),
                'media' => $file,
            ]);

        $sections = $view->renderSections();

        return $sections['content'];
    }

    public function get()
    {
        if ($this->request->input('id') == 0) {
            $media = File::query()
                ->where('post_id', '=', 0)
                ->orderBy('id', 'desc')
                ->first();
            $attachments = $media->getMedia('attachments');
            $images = $media->getMedia('images');
        } else {
            $media = File::query()
                ->where('post_id', '=', $this->request->input('id'))
                ->first();
            if (isset($media->id)) {
                $attachments = $media->getMedia('attachments');
                $images = $media->getMedia('images');
            } else {
                $attachments = new stdClass;
                $images = new stdClass;
            }
        }

        $view = View::make('backend.posts.media')
            ->with([
                'attachments' => $attachments,
                'images' => $images,
            ]);

        $sections = $view->renderSections();

        return $sections['media'];
    }

    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $this->request->validate([
            'id' => 'required|integer',
        ]);

        $files = $request->file('files');

        $media = $this->request->input('id') == 0
            ? File::query()->where('post_id', '=', 0)->orderBy('id', 'desc')->first()
            : File::query()->where('post_id', '=', $this->request->input('id'))->firstOrFail();

        $inputData = $request->all();

        $validator = Validator::make(
            $inputData,
            [
                'files.*' => 'required|max:20000',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray(),
            ], 422);
        }

        foreach ($files as $file) {
            $rules = [
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
            ];

            $validator = Validator::make(['image' => $file], $rules);

            if ($validator->fails()) {
                $media->addMedia($file->getPathName())
                    ->usingFileName($file->getClientOriginalName(), PATHINFO_FILENAME)
                    ->toMediaCollection('attachments');
            } else {
                $media->addMedia($file->getPathName())
                    ->usingFileName($file->getClientOriginalName(), PATHINFO_FILENAME)
                    ->toMediaCollection('images');
            }
        }

        try {
            if ($files) {
                return response()->json([
                    'status' => true,
                    'message' => ('Upload succeeded.'),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return back();
    }

    public function attachFile()
    {
        $files = $this->request->file('files');
        $media = File::find(4);
        $inputData = $this->request->all();

        $validator = Validator::make(
            $inputData,
            [
                'files.*' => 'required|max:200',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray(),
            ], 422);
        }

        foreach ($files as $file) {
            $media->addMedia($file->getPathName())->usingFileName($file->getClientOriginalName(), PATHINFO_FILENAME)->toMediaCollection('attachments');
        }

        try {
            if ($files) {
                return response()->json([
                    'status' => true,
                    'message' => ('Upload succeeded.'),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function download()
    {
        $file = $this->request->get('file');
        $manager = new MediaManager('public', $file);

        return $manager->download();
    }

    public function destroy(): JsonResponse
    {
        $this->request->validate([
            'id' => 'required|integer',
        ]);

        $mediaId = $this->request->input('id');

        File::query()
            ->whereHas('media', function ($query) use ($mediaId) {
                $query->whereId($mediaId);
            })
            ->first()
            ->deleteMedia($mediaId);

        return response()->json([
            'status' => true,
        ]);
    }
}
