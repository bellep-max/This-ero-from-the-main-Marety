<?php

namespace App\Services;

use App\Enums\PeriodEnum;
use App\Models\Genre;
use App\Models\History;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class FixedTrendingService
{
    public function popularAudios(PeriodEnum $period): ?Collection
    {
        return Genre::query()
            ->whereHas('songs.activities', function ($query) use ($period) {
                switch ($period) {
                    case PeriodEnum::Daily:
                        $query->whereBetween('updated_at', [now()->subDay(), now()]);
                        break;
                    case PeriodEnum::Weekly:
                        $query->whereBetween('updated_at', [now()->subWeek(), now()]);
                        break;
                    case PeriodEnum::Monthly:
                        $query->whereBetween('updated_at', [now()->subMonth(), now()]);
                        break;
                }
            })
            ->with('songs', function ($query) {
                $query->take(4);
            })
            ->get();
    }

    public function topByGenres(Collection $genres, int $limit = 4): array
    {
        $topByGenre = [];
        $genres->each(function ($genre) use (&$topByGenre, $limit) {
            $topByGenre[] = [
                'genre_id' => $genre->id,
                'name' => $genre->name,
                'description' => $genre->description,
                'songs' => $this->topByGenre($genre->id, $limit),
            ];
        });

        return $topByGenre;
    }

    public function topByGenre(int $genreId, int $limit): Collection
    {
        $userIds = Song::query()
            ->where('genre', 'REGEXP', '(^|,)(' . $genreId . ')(,|$)')
            ->limit($limit)
            ->pluck('user_id');

        $patronUsers = $this->getPatronUsers($userIds);

        return Song::query()
            ->where('genre', 'REGEXP', '(^|,)(' . $genreId . ')(,|$)')
            ->notPatron()
            ->orWhere(function (Builder $query) use ($patronUsers) {
                $query->patron()
                    ->whereIn('user_id', $patronUsers);
            })
            ->orderBy('plays', 'desc')
            ->limit($limit)
            ->get();
    }

    public function topByGenrePaginate(int $genreId, int $paginate): Paginator
    {
        $userIds = Song::query()
            ->where('genre', 'REGEXP', '(^|,)(' . $genreId . ')(,|$)')
            ->pluck('user_id');

        $patronUsers = $this->getPatronUsers($userIds);

        return Song::query()
            ->where('genre', 'REGEXP', '(^|,)(' . $genreId . ')(,|$)')
            ->notPatron()
            ->orWhere(function (Builder $query) use ($patronUsers) {
                $query->patron()
                    ->whereIn('user_id', $patronUsers);
            })
            ->orderBy('plays', 'desc')
            ->simplePaginate();
    }

    public function topByVoice(string $vocal, int $count): array
    {
        $userIds = Song::query()
            ->where('vocal', $vocal)
            ->limit($count)
            ->pluck('user_id');

        $patronUsers = $this->getPatronUsers($userIds);

        return Song::query()
            ->where('vocal', $vocal)
            ->notPatron()
            ->orWhere(function (Builder $query) use ($patronUsers) {
                $query->patron()
                    ->whereIn('user_id', $patronUsers);
            })
            ->limit($count)
            ->get()
            ->toArray();
    }

    public function topByVoicePaginate(string $vocal, int $paginate): Paginator
    {
        $userIds = Song::query()
            ->where('vocal', $vocal)
            ->pluck('user_id');

        $patronUsers = $this->getPatronUsers($userIds);

        return Song::query()
            ->where('vocal', $vocal)
            ->notPatron()
            ->orWhere(function (Builder $query) use ($patronUsers) {
                $query->patron()
                    ->whereIn('user_id', $patronUsers);
            })
            ->orderBy('plays', 'desc')
            ->simplePaginate($paginate);
    }

    private function getPatronUsers(\Illuminate\Support\Collection $ids): array
    {
        return auth()->check()
            ? collect($ids)->unique()->filter(function (int $userId) {
                return (bool) auth()->user()->activeUserSubscription($userId);
            })->toArray()
            : collect($ids)->unique()->toArray();
    }

    private function getHistory(Genre $genre, Carbon $dateFrom, int $takeAmount = 4)
    {
        return History::query()
            ->select([
                'id',
                'historyable_id',
                'historyable_type',
                'interaction_count',
                'updated_at',
                DB::raw('RANK() OVER (ORDER BY interaction_count DESC) interaction_count_rank'),
            ])
            ->whereBetween('updated_at', [$dateFrom, now()])
            ->with([
                'historyable' => function ($query) use ($genre) {
                    $query->where('genre', 'REGEXP', '(^|,)(' . $genre->id . ')(,|$)');
                },
            ])
            ->groupBy('historyable_id')
            ->get()
            ->pluck('historyable')
            ->filter()
            ->take($takeAmount);
    }
}
