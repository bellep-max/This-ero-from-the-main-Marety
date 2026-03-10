<?php

namespace App\Services;

use App\Contracts\TrendingInterface;
use App\Models\Genre;
use App\Models\History;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class TrendingService implements TrendingInterface
{
    public function popularAudios(Collection $genres): array
    {
        $popularAudios = [
            'daily' => [],
            'weekly' => [],
            'monthly' => [],
        ];

        $genres->each(function (Genre $genre) use (&$popularAudios) {
            if ($dailyContent = $this->getHistory($genre, now()->subDay())) {
                $popularAudios['daily'][] = [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'description' => $genre->description,
                    'songs' => $dailyContent,
                ];
            }

            if ($weeklyContent = $this->getHistory($genre, now()->subWeek())) {
                $popularAudios['weekly'][] = [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'description' => $genre->description,
                    'songs' => $weeklyContent,
                ];
            }

            if ($monthlyContent = $this->getHistory($genre, now()->subMonth())) {
                $popularAudios['monthly'][] = [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'description' => $genre->description,
                    'songs' => $monthlyContent,
                ];
            }
        });

        return $popularAudios;
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
        $songs = History::query()
            ->select([
                'id as history_id',
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

        return $songs->isNotEmpty()
            ? $songs
            : false;
    }
}
