<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Domain;

enum ChatModels: string
{
    case GPT_5 = 'gpt-5';
    case GPT_5_MINI = 'gpt-5-mini';
    case GPT_5_NANO = 'gpt-5-nano';
    case GPT_4O_MINI = 'gpt-4o-mini';
    case GPT_4O = 'gpt-4o';
    case CLAUDE_3_HAIKU = 'claude-3-haiku';
    case CLAUDE_3_OPUS = 'claude-3-opus';
    case CLAUDE_4_5_SONNET = 'claude-4.5-sonnet';
    case CLAUDE_3_7_SONNET_THINKING = 'claude-3.7-sonnet-thinking';
    case CLAUDE_4_1_OPUS = 'claude-4.1-opus';
    case GEMINI_2_0_FLASH = 'gemini-2.0-flash';
    case GEMINI_2_5_PRO = 'gemini-2.5-pro';
    case DEEPSEEK_V3_1 = 'deepseek-v3.1';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn (self $model) => $model->value, self::cases());
    }
}
