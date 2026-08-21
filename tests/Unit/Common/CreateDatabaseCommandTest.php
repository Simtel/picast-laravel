<?php

declare(strict_types=1);

namespace Tests\Unit\Common;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CreateDatabaseCommandTest extends TestCase
{
    public function test_creates_database_with_valid_name(): void
    {
        DB::shouldReceive('statement')
            ->once()
            ->with('CREATE DATABASE IF NOT EXISTS test_db');

        $command = $this->artisan('app:create-database', ['name' => 'test_db']);

        $command->expectsOutputToContain('Database test_db has been created successfully.');
        $command->assertSuccessful();
    }

    public function test_rejects_database_name_with_special_characters(): void
    {
        DB::shouldReceive('statement')->never();

        $command = $this->artisan('app:create-database', ['name' => 'bad-name!']);

        $command->expectsOutputToContain("Invalid database name: 'bad-name!'");
        $command->assertSuccessful();
    }

    public function test_rejects_empty_database_name(): void
    {
        DB::shouldReceive('statement')->never();

        $command = $this->artisan('app:create-database', ['name' => '']);

        $command->expectsOutputToContain("Invalid database name: ''");
        $command->assertSuccessful();
    }

    public function test_reports_failure_when_database_statement_throws(): void
    {
        DB::shouldReceive('statement')
            ->once()
            ->with('CREATE DATABASE IF NOT EXISTS test_db')
            ->andThrow(new \Exception('connection refused'));

        $command = $this->artisan('app:create-database', ['name' => 'test_db']);

        $command->expectsOutputToContain('Failed to create database: connection refused');
        $command->assertSuccessful();
    }
}
