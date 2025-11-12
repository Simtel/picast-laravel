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

    public function label(): string
    {
        return match($this) {
            self::GPT_4O_MINI => 'GPT-4o Mini (Fast & Cheap)',
            self::GPT_4O => 'GPT-4o (Balanced)',
            self::GPT_5 => 'GPT-5 (Smart)',
            self::GPT_5_MINI => 'GPT-5 Mini',
            self::GPT_5_NANO => 'GPT-5 Nano',
            self::CLAUDE_4_1_OPUS => 'Claude 4.1 Opus (Most Intelligent)',
            self::CLAUDE_4_5_SONNET => 'Claude 4.5 Sonnet',
            self::CLAUDE_3_7_SONNET_THINKING => 'Claude 3.7 Sonnet Thinking',
            self::GEMINI_2_5_PRO => 'Gemini 2.5 Pro',
            self::GEMINI_2_0_FLASH => 'Gemini 2.0 Flash',
            self::DEEPSEEK_V3_1 => 'DeepSeek v3.1',
            self::CLAUDE_3_HAIKU => 'CLAUDE 3 Haiku',
            self::CLAUDE_3_OPUS => 'CLAUDE 3 Opus',
        };
    }

    public function isDefault(): bool
    {
        return $this === self::GPT_4O_MINI;
    }
}
