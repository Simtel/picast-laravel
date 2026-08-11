<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Domain\Factory;

use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\User\Domain\Model\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChadGptConversation>
 */
class ChadGptConversationFactory extends Factory
{
    /**
     * @var class-string<ChadGptConversation>
     */
    protected $model = ChadGptConversation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'model' => $this->faker->randomElement(self::MODEL_IDS),
            'user_message' => $this->faker->sentence,
            'ai_response' => $this->faker->paragraph,
            'used_tokens_count' => $this->faker->numberBetween(10, 100),
        ];
    }

    private const array MODEL_IDS = [
        'gpt-5.6-terra',
        'gpt-5-mini',
        'claude-5-sonnet',
        'gemini-2.5-flash',
    ];
}
