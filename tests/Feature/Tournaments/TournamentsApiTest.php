<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use App\Context\Tournaments\Domain\Model\TournamentGroup;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

final class TournamentsApiTest extends TestCase
{
    private function apiToken(): string
    {
        return $this->getAdminUser()->createToken('test')->plainTextToken;
    }

    /**
     * @return \Illuminate\Testing\TestResponse<\Illuminate\Http\JsonResponse>
     */
    private function getWithToken(string $uri): \Illuminate\Testing\TestResponse
    {
        return $this->get($uri, ['Authorization' => 'Bearer ' . $this->apiToken()]);
    }

    public function test_index_requires_authentication(): void
    {
        $this->get(route('api.tournaments.index'))->assertStatus(401);
    }

    public function test_show_requires_authentication(): void
    {
        $this->get(route('api.tournaments.show', 1))->assertStatus(401);
    }

    public function test_index_returns_future_tournaments_with_cities(): void
    {
        Tournament::factory()->create([
            'title' => 'Spring Cup',
            'city' => 'Москва',
            'date' => Carbon::now()->addWeek(),
        ]);
        Tournament::factory()->create([
            'title' => 'Summer Cup',
            'city' => 'Казань',
            'date' => Carbon::now()->addWeeks(2),
        ]);
        Tournament::factory()->create([
            'date' => Carbon::now()->subWeek(),
        ]);

        $response = $this->getWithToken(route('api.tournaments.index'));

        $response->assertStatus(200);
        $response->assertJson(
            static fn (AssertableJson $json) => $json
                ->whereType('tournaments.data', 'array')
                ->where('cities', ['Казань', 'Москва'])
                ->where('selectedCity', null)
                ->has('tournaments.data', 2)
        );
        $response->assertJsonFragment(['title' => 'Spring Cup']);
        $response->assertJsonFragment(['title' => 'Summer Cup']);
    }

    public function test_index_filters_by_city(): void
    {
        Tournament::factory()->create([
            'title' => 'Moscow Cup',
            'city' => 'Москва',
            'date' => Carbon::now()->addWeek(),
        ]);
        Tournament::factory()->create([
            'title' => 'Kazan Cup',
            'city' => 'Казань',
            'date' => Carbon::now()->addWeek(),
        ]);

        $response = $this->getWithToken(route('api.tournaments.index', ['city' => 'Казань']));

        $response->assertStatus(200);
        $response->assertJson(
            static fn (AssertableJson $json) => $json
                ->where('selectedCity', 'Казань')
                ->has('tournaments.data', 1)
                ->etc()
        );
        $response->assertJsonFragment(['title' => 'Kazan Cup']);
        $response->assertJsonMissing(['title' => 'Moscow Cup']);
    }

    public function test_show_returns_tournament_with_groups(): void
    {
        $tournament = Tournament::factory()->create([
            'title' => 'Grand Final',
            'city' => 'Москва',
        ]);
        $group = TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Юниоры Стандарт C',
            'registrations' => 12,
        ]);

        $response = $this->getWithToken(route('api.tournaments.show', $tournament->getId()));

        $response->assertStatus(200);
        $response->assertJson(
            static fn (AssertableJson $json) => $json
                ->where('tournament.id', $tournament->getId())
                ->where('tournament.title', 'Grand Final')
                ->where('tournament.city', 'Москва')
                ->where('groups.data.0.name', $group->getName())
        );
    }

    public function test_show_filters_groups_by_search(): void
    {
        $tournament = Tournament::factory()->create();
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Adults Standard',
            'registrations' => 5,
        ]);
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 2,
            'name' => 'Youth Latin',
            'registrations' => 6,
        ]);

        $response = $this->getWithToken(
            route('api.tournaments.show', $tournament->getId()) . '?search=Standard'
        );

        $response->assertStatus(200);
        $response->assertJson(
            static fn (AssertableJson $json) => $json
                ->where('groups.total', 1)
                ->has('groups.data', 1)
                ->etc()
        );
        $response->assertJsonFragment(['name' => 'Adults Standard']);
        $response->assertJsonMissing(['name' => 'Youth Latin']);
    }

    public function test_show_returns_404_for_missing_tournament(): void
    {
        $response = $this->getWithToken(route('api.tournaments.show', 999999));

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Турнир не найден']);
    }
}
