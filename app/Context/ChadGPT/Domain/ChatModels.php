<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Domain;

enum ChatModels: string
{
    case GPT_5 = 'gpt-5';
    case GPT_5_THINKING = 'gpt-5-thinking';
    case GPT_5_1 = 'gpt-5.1';
    case GPT_5_2 = 'gpt-5.2';
    case GPT_5_2_THINKING = 'gpt-5.2-thinking';
    case GPT_5_3_CODEX = 'gpt-5.3-codex';
    case GPT_5_5 = 'gpt-5.5';
    case GPT_5_6_SOL = 'gpt-5.6-sol';
    case GPT_5_6_SOL_PRO = 'gpt-5.6-sol-pro';
    case GPT_5_6_TERRA = 'gpt-5.6-terra';
    case GPT_5_6_LUNA = 'gpt-5.6-luna';
    case GPT_5_MINI = 'gpt-5-mini';
    case GPT_5_NANO = 'gpt-5-nano';
    case GEMINI_2_5_PRO = 'gemini-2.5-pro';
    case GEMINI_2_5_FLASH = 'gemini-2.5-flash';
    case GEMINI_3_1_PRO_PREVIEW = 'gemini-3.1-pro-preview';
    case GEMINI_3_FLASH_PREVIEW = 'gemini-3-flash-preview';
    case GEMINI_3_5_FLASH = 'gemini-3.5-flash';
    case GEMINI_3_6_FLASH = 'gemini-3.6-flash';
    case GEMINI_3_1_FLASH_LITE_PREVIEW = 'gemini-3.1-flash-lite-preview';
    case GEMINI_3_5_FLASH_LITE = 'gemini-3.5-flash-lite';
    case CLAUDE_4_OPUS = 'claude-4-opus';
    case CLAUDE_4_1_OPUS = 'claude-4.1-opus';
    case CLAUDE_4_5_OPUS = 'claude-4.5-opus';
    case CLAUDE_4_6_OPUS = 'claude-4.6-opus';
    case CLAUDE_4_7_OPUS = 'claude-4.7-opus';
    case CLAUDE_4_8_OPUS = 'claude-4.8-opus';
    case CLAUDE_5_OPUS = 'claude-5-opus';
    case CLAUDE_5_FABLE = 'claude-5-fable';
    case CLAUDE_4_SONNET = 'claude-4-sonnet';
    case CLAUDE_4_SONNET_THINKING = 'claude-4-sonnet-thinking';
    case CLAUDE_4_5_SONNET = 'claude-4.5-sonnet';
    case CLAUDE_4_5_SONNET_THINKING = 'claude-4.5-sonnet-thinking';
    case CLAUDE_5_SONNET = 'claude-5-sonnet';
    case CLAUDE_5_SONNET_THINKING = 'claude-5-sonnet-thinking';
    case CLAUDE_4_5_HAIKU = 'claude-4.5-haiku';
    case DEEPSEEK_V4_FLASH = 'deepseek-v4-flash';
    case DEEPSEEK_V4_PRO = 'deepseek-v4-pro';
    case GLM_5_2 = 'glm-5.2';
    case KIMI_K3 = 'kimi-k3';
    case QWEN_3_8_MAX = 'qwen3.8-max';
    case GROK_4_LATEST = 'grok-4-latest';
    case GROK_4_WITH_WEB_SEARCH = 'grok-4-with-web-search';
    case GROK_4_FAST_LATEST = 'grok-4-fast-latest';
    case GROK_4_FAST_WITH_WEB_SEARCH = 'grok-4-fast-with-web-search';
    case GROK_4_1_FAST_LATEST = 'grok-4.1-fast-latest';
    case GROK_4_1_FAST_WITH_WEB_SEARCH = 'grok-4.1-fast-with-web-search';
    case GROK_4_5 = 'grok-4.5';
    case GROK_4_5_WITH_WEB_SEARCH = 'grok-4.5-with-web-search';
    case GROK_4_5_THINKING = 'grok-4.5-thinking';
    case GROK_4_5_THINKING_WITH_WEB_SEARCH = 'grok-4.5-thinking-with-web-search';

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
            self::GPT_5 => 'GPT-5',
            self::GPT_5_THINKING => 'GPT-5 Thinking',
            self::GPT_5_1 => 'GPT-5.1',
            self::GPT_5_2 => 'GPT-5.2',
            self::GPT_5_2_THINKING => 'GPT-5.2 Thinking',
            self::GPT_5_3_CODEX => 'GPT-5.3 Codex',
            self::GPT_5_5 => 'GPT-5.5',
            self::GPT_5_6_SOL => 'GPT-5.6 Sol',
            self::GPT_5_6_SOL_PRO => 'GPT-5.6 Sol Pro',
            self::GPT_5_6_TERRA => 'GPT-5.6 Terra (Умная)',
            self::GPT_5_6_LUNA => 'GPT-5.6 Luna',
            self::GPT_5_MINI => 'GPT-5 Mini',
            self::GPT_5_NANO => 'GPT-5 Nano',
            self::GEMINI_2_5_PRO => 'Gemini 2.5 Pro',
            self::GEMINI_2_5_FLASH => 'Gemini 2.5 Flash',
            self::GEMINI_3_1_PRO_PREVIEW => 'Gemini 3.1 Pro Preview',
            self::GEMINI_3_FLASH_PREVIEW => 'Gemini 3 Flash Preview',
            self::GEMINI_3_5_FLASH => 'Gemini 3.5 Flash',
            self::GEMINI_3_6_FLASH => 'Gemini 3.6 Flash',
            self::GEMINI_3_1_FLASH_LITE_PREVIEW => 'Gemini 3.1 Flash Lite Preview',
            self::GEMINI_3_5_FLASH_LITE => 'Gemini 3.5 Flash Lite',
            self::CLAUDE_4_OPUS => 'Claude 4 Opus',
            self::CLAUDE_4_1_OPUS => 'Claude 4.1 Opus',
            self::CLAUDE_4_5_OPUS => 'Claude 4.5 Opus',
            self::CLAUDE_4_6_OPUS => 'Claude 4.6 Opus',
            self::CLAUDE_4_7_OPUS => 'Claude 4.7 Opus',
            self::CLAUDE_4_8_OPUS => 'Claude 4.8 Opus',
            self::CLAUDE_5_OPUS => 'Claude 5 Opus',
            self::CLAUDE_5_FABLE => 'Claude 5 Fable',
            self::CLAUDE_4_SONNET => 'Claude 4 Sonnet',
            self::CLAUDE_4_SONNET_THINKING => 'Claude 4 Sonnet Thinking',
            self::CLAUDE_4_5_SONNET => 'Claude 4.5 Sonnet',
            self::CLAUDE_4_5_SONNET_THINKING => 'Claude 4.5 Sonnet Thinking',
            self::CLAUDE_5_SONNET => 'Claude 5 Sonnet',
            self::CLAUDE_5_SONNET_THINKING => 'Claude 5 Sonnet Thinking',
            self::CLAUDE_4_5_HAIKU => 'Claude 4.5 Haiku',
            self::DEEPSEEK_V4_FLASH => 'DeepSeek v4 Flash',
            self::DEEPSEEK_V4_PRO => 'DeepSeek v4 Pro',
            self::GLM_5_2 => 'GLM 5.2',
            self::KIMI_K3 => 'Kimi k3',
            self::QWEN_3_8_MAX => 'Qwen3.8 Max',
            self::GROK_4_LATEST => 'Grok 4 Latest',
            self::GROK_4_WITH_WEB_SEARCH => 'Grok 4 (с поиском в вебе)',
            self::GROK_4_FAST_LATEST => 'Grok 4 Fast Latest',
            self::GROK_4_FAST_WITH_WEB_SEARCH => 'Grok 4 Fast (с поиском в вебе)',
            self::GROK_4_1_FAST_LATEST => 'Grok 4.1 Fast Latest',
            self::GROK_4_1_FAST_WITH_WEB_SEARCH => 'Grok 4.1 Fast (с поиском в вебе)',
            self::GROK_4_5 => 'Grok 4.5',
            self::GROK_4_5_WITH_WEB_SEARCH => 'Grok 4.5 (с поиском в вебе)',
            self::GROK_4_5_THINKING => 'Grok 4.5 Thinking',
            self::GROK_4_5_THINKING_WITH_WEB_SEARCH => 'Grok 4.5 Thinking (с поиском в вебе)',
        };
    }

    public function isDefault(): bool
    {
        return $this === self::GPT_5_6_TERRA;
    }

    public static function default(): self
    {
        return self::GPT_5_6_TERRA;
    }
}
