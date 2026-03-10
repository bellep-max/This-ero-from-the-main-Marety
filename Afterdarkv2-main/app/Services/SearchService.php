<?php

namespace App\Services;

use App\Constants\DefaultConstants;
use App\Enums\RoleEnum;
use App\Http\Requests\Frontend\Song\DiscoverSearchRequest;
use App\Models\Adventure;
use App\Models\Song;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    private const TAKE_AMOUNT = 20;

    public function search(string $model, DiscoverSearchRequest $request): LengthAwarePaginator
    {
        $user = auth()->user();

        return $model::query()
            ->when($request->filled('genres'), function ($query) use ($request) {
                $query->whereHas('genres', function ($query) use ($request) {
                    $query->whereIn('genres.id', $request->input('genres'));
                });
            })
            ->when($request->filled('tags'), function ($query) use ($request) {
                $query->whereHas('tags', function ($query) use ($request) {
                    $query->whereIn('tags.id', $request->input('tags'));
                });
            })
            ->when($model === Song::class, function (Builder $query) use ($request, $user) {
                $query->when($request->filled('duration'), function ($query) use ($request) {
                    $query->whereBetween('duration', $request->input('duration'));
                })
                    ->when($request->filled('vocals'), function ($query) use ($request) {
                        $query->whereIn('vocal_id', $request->input('vocals'));
                    })
                    ->when(!$user || $user->hasRole(RoleEnum::Listener), function ($query) {
                        $query->where('is_patron', DefaultConstants::FALSE);
                    });
            })
            ->when($model === Adventure::class, function ($query) {
                $query->heading();
            })
            ->visible()
            ->when($request->filled('released_at'), function ($query) {
                $query->latest();
            }, function ($query) {
                $query->orderBy('id', 'ASC');
            })
//            ->when($user && $user->hasAnyRole([RoleEnum::Admin, RoleEnum::SuperAdmin, RoleEnum::Creator]), function ($query) {
//                $query->orWhere('is_patron', DefaultConstants::TRUE);
//            })
            ->has('user')
            ->with(['user:id,uuid,name,username', 'tags', 'genres'])
            ->paginate(self::TAKE_AMOUNT);
    }
}
