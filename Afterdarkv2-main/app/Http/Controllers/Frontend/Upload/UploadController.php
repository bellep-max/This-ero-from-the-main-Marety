<?php

namespace App\Http\Controllers\Frontend\Upload;

use App\Http\Controllers\Controller;
use App\Http\Resources\Genre\GenreShortResource;
use App\Models\Genre;
use App\Models\MEPlan;
use Inertia\Inertia;
use Inertia\Response;

class UploadController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Upload', [
            'genres' => GenreShortResource::collection(Genre::query()->discover()->get()),
            'plan' => MEPlan::query()->firstWhere('type', 'site'),
        ]);
    }
}
