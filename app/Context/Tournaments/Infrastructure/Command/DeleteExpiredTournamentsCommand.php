<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Command;

use App\Context\Tournaments\Infrastructure\Service\ExpiredTournamentsCleaner;
use Illuminate\Console\Command;

final class DeleteExpiredTournamentsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tournaments:clean';

    /**
     * @var string
     */
    protected $description = 'Delete expired tournaments with their groups';

    private ExpiredTournamentsCleaner $cleaner;

    public function __construct(ExpiredTournamentsCleaner $cleaner)
    {
        parent::__construct();
        $this->cleaner = $cleaner;
    }

    public function handle(): int
    {
        $result = $this->cleaner->clean();

        $this->info(sprintf('Deleted %d tournaments and %d groups', $result['tournaments'], $result['groups']));

        return self::SUCCESS;
    }
}
