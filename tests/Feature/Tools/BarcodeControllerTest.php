<?php

declare(strict_types=1);

namespace Tests\Feature\Tools;

use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class BarcodeControllerTest extends TestCase
{
    public function test_tools_index_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.tools.index');
        $response->assertSee('Инструменты');
    }

    public function test_barcode_index_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.barcode.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.tools.barcode.index');
        $response->assertSee('Генератор штрих-кодов');
        $response->assertSee('Сгенерировать');
    }

    public function test_barcode_generate_requires_auth(): void
    {
        $this->get(route('tools.barcode.generate', ['type' => 'code128']))->assertRedirect(route('login'));
    }

    #[DataProvider('typeProvider')]
    public function test_barcode_generate_returns_valid_text(string $type): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.barcode.generate', ['type' => $type]));

        $response->assertStatus(200);
        $response->assertJson(static fn (AssertableJson $json): AssertableJson => $json
            ->whereType('text', 'string')
            ->whereNot('text', ''));
    }

    public function test_barcode_generate_rejects_unknown_type(): void
    {
        $this->loginAdmin();

        $this->get(route('tools.barcode.generate', ['type' => 'unknown']))->assertStatus(422);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function typeProvider(): array
    {
        return [
            'code128' => ['code128'],
            'code39' => ['code39'],
            'code93' => ['code93'],
            'ean13' => ['ean13'],
            'ean8' => ['ean8'],
            'itf14' => ['itf14'],
            'upca' => ['upca'],
            'upce' => ['upce'],
            'codabar' => ['codabar'],
            'code11' => ['code11'],
            'standard25' => ['standard25'],
            'interleaved25' => ['interleaved25'],
            'msi' => ['msi'],
            'postnet' => ['postnet'],
            'planet' => ['planet'],
            'rms4cc' => ['rms4cc'],
            'kix' => ['kix'],
            'pharmacode' => ['pharmacode'],
        ];
    }
}
