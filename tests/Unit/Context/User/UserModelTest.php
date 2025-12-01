<?php

declare(strict_types=1);

namespace Tests\Unit\Context\User;

use App\Context\User\Domain\Model\User;
use Event;
use Illuminate\Support\Carbon;
use Tests\TestCase;

final class UserModelTest extends TestCase
{
    public function test_user_has_domains(): void
    {
        Event::fake();
        /** @var User $user */
        $user = User::factory()->hasDomains(2)->create();

        self::assertCount(2, $user->domains()->get());
    }

    public function test_user_get_methods(): void
    {
        $user = User::factory()->create(['email' => 'test@mail.com']);

        self::assertEquals('test@mail.com', $user->getEmail());
    }

    public function test_birth_date_is_fillable(): void
    {
        $birthDate = '1990-01-15';
        $user = User::factory()->create(['birth_date' => $birthDate]);

        self::assertNotNull($user->birth_date);
        self::assertEquals($birthDate, $user->birth_date->format('Y-m-d'));
    }

    public function test_birth_date_can_be_null(): void
    {
        $user = User::factory()->create(['birth_date' => null]);

        self::assertNull($user->birth_date);
        self::assertNull($user->getBirthDate());
    }

    public function test_birth_date_is_cast_to_carbon(): void
    {
        $birthDate = '1990-01-15';
        $user = User::factory()->create(['birth_date' => $birthDate]);

        self::assertInstanceOf(Carbon::class, $user->birth_date);
        self::assertEquals('1990-01-15', $user->birth_date->format('Y-m-d'));
    }

    public function test_get_birth_date_method(): void
    {
        $birthDate = '1990-01-15';
        $user = User::factory()->create(['birth_date' => $birthDate]);

        $result = $user->getBirthDate();

        self::assertInstanceOf(Carbon::class, $result);
        self::assertEquals('1990-01-15', $result->format('Y-m-d'));
    }

    public function test_get_birth_date_method_returns_null_when_no_birth_date(): void
    {
        $user = User::factory()->create(['birth_date' => null]);

        $result = $user->getBirthDate();

        self::assertNull($result);
    }

    public function test_birth_date_can_be_updated(): void
    {
        $user = User::factory()->create(['birth_date' => '1990-01-15']);
        $newBirthDate = '1985-12-25';

        $user->update(['birth_date' => $newBirthDate]);
        $user->refresh();

        self::assertNotNull($user->birth_date);
        self::assertEquals($newBirthDate, $user->getBirthDate()->format('Y-m-d'));
        self::assertEquals(1985, $user->getBirthdayYear());
    }

    public function test_birth_date_can_be_queried(): void
    {
        Event::fake();
        $birthDate = '1990-01-15';
        $uniqueEmail1 = 'user_query_test_1_' . uniqid('', true) . '@test.com';
        $uniqueEmail2 = 'user_query_test_2_' . uniqid('', true) . '@test.com';
        $uniqueEmail3 = 'user_query_test_3_' . uniqid('', true) . '@test.com';

        $user1 = User::factory()->create(['birth_date' => $birthDate, 'email' => $uniqueEmail1]);
        $user2 = User::factory()->create(['birth_date' => '1985-12-25', 'email' => $uniqueEmail2]);
        $user3 = User::factory()->create(['birth_date' => null, 'email' => $uniqueEmail3]);

        $usersWithSpecificBirthDate = User::where('birth_date', $birthDate)
            ->whereIn('email', [$uniqueEmail1, $uniqueEmail2, $uniqueEmail3])
            ->get();
        $usersWithNullBirthDate = User::whereNull('birth_date')
            ->whereIn('email', [$uniqueEmail1, $uniqueEmail2, $uniqueEmail3])
            ->get();

        self::assertCount(1, $usersWithSpecificBirthDate);
        self::assertEquals($uniqueEmail1, $usersWithSpecificBirthDate->first()->email);
        self::assertCount(1, $usersWithNullBirthDate);
        self::assertEquals($uniqueEmail3, $usersWithNullBirthDate->first()->email);
    }

    public function test_birth_date_accepts_carbon_instance(): void
    {
        $birthDate = Carbon::parse('1990-01-15');
        $user = User::factory()->create(['birth_date' => $birthDate]);

        self::assertNotNull($user->birth_date);
        self::assertEquals('1990-01-15', $user->birth_date->format('Y-m-d'));
    }

    public function test_birth_date_accepts_string_format(): void
    {
        $birthDate = '1990-01-15';
        $user = User::factory()->create(['birth_date' => $birthDate]);

        self::assertNotNull($user->birth_date);
        self::assertEquals('1990-01-15', $user->birth_date->format('Y-m-d'));
    }

    public function test_birth_date_is_included_in_fillable_array(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        self::assertContains('birth_date', $fillable);
    }

    public function test_birth_date_mass_assignment(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'birth_date' => '1990-01-15'
        ];

        $user = User::factory()->create($userData);

        self::assertEquals('John Doe', $user->name);
        self::assertEquals('john@example.com', $user->email);
        self::assertNotNull($user->birth_date);
        self::assertEquals('1990-01-15', $user->birth_date->format('Y-m-d'));
    }
}
