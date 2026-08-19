<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Application\Data\ChadGptModel;
use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use Tests\TestCase;

class ChadGptDataTest extends TestCase
{
    public function test_chad_gpt_model_constructor(): void
    {
        $model = new ChadGptModel(
            id: 'gpt-5.6-terra',
            label: 'GPT 5.6 Terra',
            isDefault: true,
        );

        $this->assertSame('gpt-5.6-terra', $model->id);
        $this->assertSame('GPT 5.6 Terra', $model->label);
        $this->assertTrue($model->isDefault);
    }

    public function test_chad_gpt_model_defaults_to_not_default(): void
    {
        $model = new ChadGptModel(id: 'claude-5-sonnet', label: 'Claude 5 Sonnet');

        $this->assertFalse($model->isDefault);
    }

    public function test_chad_gpt_model_from_array(): void
    {
        $model = ChadGptModel::from([
            'id' => 'gpt-5-mini',
            'label' => 'GPT 5 Mini',
        ]);

        $this->assertSame('gpt-5-mini', $model->id);
        $this->assertSame('GPT 5 Mini', $model->label);
        $this->assertFalse($model->isDefault);
    }

    public function test_chad_gpt_request_data_with_all_fields(): void
    {
        $data = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Привет',
            'temperature' => 0.7,
            'maxTokens' => 500,
            'history' => [
                ['role' => 'user', 'content' => 'Предыдущий вопрос'],
            ],
            'images' => ['https://example.com/img.jpg'],
        ]);

        $this->assertSame('gpt-5.6-terra', $data->model);
        $this->assertSame('Привет', $data->userMessage);
        $this->assertSame(0.7, $data->temperature);
        $this->assertSame(500, $data->maxTokens);
        $this->assertIsArray($data->history);
        $this->assertSame(['role' => 'user', 'content' => 'Предыдущий вопрос'], $data->history[0]);
        $this->assertSame(['https://example.com/img.jpg'], $data->images);
    }

    public function test_chad_gpt_request_data_defaults_to_null_optionals(): void
    {
        $data = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Привет',
        ]);

        $this->assertNull($data->temperature);
        $this->assertNull($data->maxTokens);
        $this->assertNull($data->history);
        $this->assertNull($data->images);
    }
}
