<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Service;

use App\Context\Tournaments\Domain\Model\Tournament;
use DB;
use Log;

final class ExpiredTournamentsCleaner
{
    /**
     * Удаляет турниры, дата которых уже прошла, вместе со связанными группами.
     *
     * @return array{tournaments: int, groups: int}
     */
    public function clean(): array
    {
        $deletedGroups = 0;
        $deletedTournaments = 0;

        $query = Tournament::query()
            ->where(static function ($query) {
                $query->where('date_end', '<', now())
                    ->orWhere(static function ($query) {
                        $query->whereNull('date_end')
                            ->where('date', '<', now());
                    });
            });

        $query->chunkById(100, static function ($tournaments) use (&$deletedGroups, &$deletedTournaments) {
            foreach ($tournaments as $tournament) {
                try {
                    DB::transaction(static function () use ($tournament, &$deletedGroups) {
                        $deletedGroups += $tournament->groups()->count();
                        $tournament->groups()->delete();
                        $tournament->delete();
                    });

                    $deletedTournaments++;
                } catch (\Throwable $e) {
                    Log::error('ExpiredTournamentsCleaner: ошибка удаления турнира', [
                        'tournament_id' => $tournament->getId(),
                        'title' => $tournament->getTitle(),
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });

        return [
            'tournaments' => $deletedTournaments,
            'groups' => $deletedGroups,
        ];
    }
}
