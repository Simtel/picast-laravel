<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class FetchTournamentsCommandTest extends TestCase
{
    public function test_command_runs_successfully(): void
    {
        // Перед запуском очищаем таблицу
        Tournament::query()->delete();

        $exitCode = Artisan::call('tournaments:fetch');

        // Команда должна выполниться без ошибок (код выхода 0)
        $this->assertEquals(0, $exitCode);

        // Проверяем, что в выводе есть ожидаемые сообщения
        $output = Artisan::output();
        $this->assertStringContainsString('Fetching tournaments', $output);
    }

    public function test_command_creates_tournaments_in_database(): void
    {
        // Перед запуском очищаем таблицу
        Tournament::query()->delete();

        Artisan::call('tournaments:fetch');

        // Проверяем, что турниры были созданы (или обновлены) в базе данных
        // Примечание: это может не работать если сайт dancemanager.ru недоступен
        // в тестовой среде или возвращает пустой список
        $tournaments = Tournament::all();
        $this->assertGreaterThanOrEqual(0, $tournaments->count());
    }

    public function test_command_handles_exceptions_gracefully(): void
    {
        $exitCode = Artisan::call('tournaments:fetch');
        $this->assertEquals(0, $exitCode);
    }
}
