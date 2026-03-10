<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

interface TrendingInterface
{
    public function popularAudios(Collection $genres): array;

    public function topByGenres(Collection $genres, int $limit = 4): array;

    public function topByGenre(int $genreId, int $limit): Collection;

    public function topByVoice(string $vocal, int $count): array;

    public function topByGenrePaginate(int $genreId, int $paginate): Paginator;

    public function topByVoicePaginate(string $vocal, int $paginate): Paginator;
}
