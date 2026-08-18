<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tournaments;

use App\Context\Tournaments\Domain\Factory\TournamentGroupFactory;
use App\Context\Tournaments\Domain\Model\TournamentGroup;
use Tests\TestCase;

final class TournamentGroupFactoryTest extends TestCase
{
    public function test_factory_creates_valid_group(): void
    {
        /** @var TournamentGroup $group */
        $group = (new TournamentGroupFactory())->create();

        $this->assertGreaterThanOrEqual(1, $group->getNumber());
        $this->assertLessThanOrEqual(20, $group->getNumber());
        $this->assertNotSame('', $group->getName());
        $this->assertGreaterThanOrEqual(4, $group->getRegistrations());
        $this->assertLessThanOrEqual(30, $group->getRegistrations());
    }

    public function test_factory_definition_returns_expected_keys(): void
    {
        $definition = (new TournamentGroupFactory())->definition();

        $this->assertArrayHasKey('tournament_id', $definition);
        $this->assertArrayHasKey('number', $definition);
        $this->assertArrayHasKey('name', $definition);
        $this->assertArrayHasKey('registrations', $definition);
    }
}
