<?php

declare(strict_types=1);

namespace App\Context\Tools\Application\Service;

use Illuminate\Support\Facades\Log;
use Picqer\Barcode\Helpers\UpcEConverter;
use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Types\TypeCodabar;
use Picqer\Barcode\Types\TypeCode11;
use Picqer\Barcode\Types\TypeCode128;
use Picqer\Barcode\Types\TypeCode39;
use Picqer\Barcode\Types\TypeCode93;
use Picqer\Barcode\Types\TypeEan13;
use Picqer\Barcode\Types\TypeEan8;
use Picqer\Barcode\Types\TypeInterleaved25;
use Picqer\Barcode\Types\TypeITF14;
use Picqer\Barcode\Types\TypeKix;
use Picqer\Barcode\Types\TypeMsi;
use Picqer\Barcode\Types\TypePharmacode;
use Picqer\Barcode\Types\TypePlanet;
use Picqer\Barcode\Types\TypePostnet;
use Picqer\Barcode\Types\TypeRms4cc;
use Picqer\Barcode\Types\TypeStandard2of5;
use Picqer\Barcode\Types\TypeUpcA;
use Picqer\Barcode\Types\TypeUpcE;
use Throwable;

final class BarcodeService
{
    /**
     * Доступные типы штрих-кодов.
     *
     * @var array<string, array{class: class-string, label: string}>
     */
    private const array TYPES = [
        'code128' => ['class' => TypeCode128::class, 'label' => 'Code 128'],
        'code39' => ['class' => TypeCode39::class, 'label' => 'Code 39'],
        'code93' => ['class' => TypeCode93::class, 'label' => 'Code 93'],
        'ean13' => ['class' => TypeEan13::class, 'label' => 'EAN-13'],
        'ean8' => ['class' => TypeEan8::class, 'label' => 'EAN-8'],
        'itf14' => ['class' => TypeITF14::class, 'label' => 'ITF-14'],
        'upca' => ['class' => TypeUpcA::class, 'label' => 'UPC-A'],
        'upce' => ['class' => TypeUpcE::class, 'label' => 'UPC-E'],
        'codabar' => ['class' => TypeCodabar::class, 'label' => 'Codabar'],
        'code11' => ['class' => TypeCode11::class, 'label' => 'Code 11'],
        'standard25' => ['class' => TypeStandard2of5::class, 'label' => 'Standard 2 of 5'],
        'interleaved25' => ['class' => TypeInterleaved25::class, 'label' => 'Interleaved 2 of 5'],
        'msi' => ['class' => TypeMsi::class, 'label' => 'MSI'],
        'postnet' => ['class' => TypePostnet::class, 'label' => 'Postnet'],
        'planet' => ['class' => TypePlanet::class, 'label' => 'Planet'],
        'rms4cc' => ['class' => TypeRms4cc::class, 'label' => 'RMS4CC'],
        'kix' => ['class' => TypeKix::class, 'label' => 'KIX'],
        'pharmacode' => ['class' => TypePharmacode::class, 'label' => 'Pharmacode'],
    ];

    /**
     * @return array<string, array{class: class-string, label: string}>
     */
    public function getTypes(): array
    {
        return self::TYPES;
    }

    public function hasType(string $type): bool
    {
        return isset(self::TYPES[$type]);
    }

    /**
     * Сгенерировать PNG-штрих-код и вернуть его как data-URI.
     */
    public function render(string $type, string $text, int $scale, int $height): string
    {
        $typeClass = self::TYPES[$type]['class'] ?? TypeCode128::class;

        try {
            $barcode = (new $typeClass())->getBarcode($text);
            $png = (new PngRenderer())->render($barcode, $barcode->getWidth() * $scale, $height);

            Log::info('[BarcodeService.render] штрих-код сгенерирован', [
                'type' => $type,
                'textLength' => mb_strlen($text),
            ]);

            return 'data:image/png;base64,' . base64_encode($png);
        } catch (Throwable $e) {
            Log::error('[BarcodeService.render] ошибка генерации', ['error' => $e->getMessage()]);

            throw $e;
        }
    }

    public function generateSample(string $type): string
    {
        return match ($type) {
            'code128' => $this->randomAlnum(8, 14),
            'code39', 'code93' => strtoupper($this->randomAlnum(6, 10)),
            'ean13' => $this->withEanCheckDigit($this->randomDigits(12), 13),
            'ean8' => $this->withEanCheckDigit($this->randomDigits(7), 8),
            'upca' => $this->withEanCheckDigit($this->randomDigits(11), 12),
            'upce' => $this->generateUpcE(),
            'itf14' => $this->withItf14CheckDigit($this->randomDigits(13)),
            'codabar', 'code11', 'standard25', 'interleaved25', 'msi' => $this->randomDigits(6, 10),
            'postnet', 'planet' => $this->randomDigits(5),
            'rms4cc' => strtoupper($this->randomAlnum(6, 10)),
            'kix' => strtoupper($this->randomAlnum(5, 8)) . 'X',
            'pharmacode' => (string) random_int(3, 131070),
            default => $this->randomAlnum(8, 12),
        };
    }

    public function randomAlnum(int $min, int $max): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $result = '';

        for ($i = 0, $length = random_int($min, $max); $i < $length; ++$i) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $result;
    }

    public function randomDigits(int $min, ?int $max = null): string
    {
        $result = '';

        for ($i = 0, $length = random_int($min, $max ?? $min); $i < $length; ++$i) {
            $result .= (string) random_int(0, 9);
        }

        return $result;
    }

    public function withEanCheckDigit(string $digits, int $fullLength): string
    {
        $sum = 0;

        for ($i = 0, $length = strlen($digits); $i < $length; ++$i) {
            $weight = $fullLength > 12 ? ($i % 2 === 1 ? 3 : 1) : ($i % 2 === 0 ? 3 : 1);
            $sum += (int) $digits[$i] * $weight;
        }

        $check = $sum % 10;
        if ($check > 0) {
            $check = 10 - $check;
        }

        return $digits . (string) $check;
    }

    public function withItf14CheckDigit(string $digits): string
    {
        $sum = 0;

        for ($i = 0; $i < 13; ++$i) {
            $sum += (int) $digits[$i] * ($i % 2 === 0 ? 3 : 1);
        }

        $check = 10 - ($sum % 10);
        if ($check === 10) {
            $check = 0;
        }

        return $digits . (string) $check;
    }

    public function generateUpcE(): string
    {
        $rest = $this->randomDigits(4);

        $gtin11 = '0' . $rest[0] . $rest[1] . $rest[2] . $rest[3] . (string) random_int(1, 9) . '0000' . (string) random_int(5, 9);

        return UpcEConverter::compress($this->withEanCheckDigit($gtin11, 12));
    }
}
