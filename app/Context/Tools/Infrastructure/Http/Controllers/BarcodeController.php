<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

final class BarcodeController extends Controller
{
    /**
     * Доступные типы штрих-кодов.
     *
     * @var array<string, array{class: class-string, label: string}>
     */
    private const TYPES = [
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

    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();
        $text = $request->string('text')->toString();
        $scale = $request->integer('scale', 2);
        $height = $request->integer('height', 50);

        Log::debug('[BarcodeController.index] вход', [
            'type' => $type,
            'textLength' => mb_strlen($text),
            'scale' => $scale,
            'height' => $height,
        ]);

        $selectedType = array_key_exists($type, self::TYPES) ? $type : 'code128';
        $barcodeDataUri = null;
        $errorMessage = null;

        if ($request->hasAny(['type', 'text', 'scale', 'height'])) {
            $validator = Validator::make($request->all(), [
                'type' => ['nullable', Rule::in(array_keys(self::TYPES))],
                'text' => ['required_with:type', 'string', 'max:255'],
                'scale' => ['integer', 'between:1,5'],
                'height' => ['integer', 'between:20,200'],
            ]);

            if ($validator->fails()) {
                $errorMessage = $validator->errors()->first();
            } else {
                $typeClass = self::TYPES[$selectedType]['class'];
                try {
                    $barcode = (new $typeClass())->getBarcode($text);
                    $png = (new PngRenderer())->render($barcode, $barcode->getWidth() * $scale, $height);
                    $barcodeDataUri = 'data:image/png;base64,' . base64_encode($png);

                    Log::info('[BarcodeController.index] штрих-код сгенерирован', [
                        'type' => $selectedType,
                        'textLength' => mb_strlen($text),
                    ]);
                } catch (Throwable $e) {
                    $errorMessage = $e->getMessage();

                    Log::error('[BarcodeController.index] ошибка генерации', ['error' => $e->getMessage()]);
                }
            }
        }

        return view('personal.tools.barcode.index', [
            'types' => collect(self::TYPES),
            'selectedType' => $selectedType,
            'text' => $text,
            'scale' => $scale,
            'height' => $height,
            'barcodeDataUri' => $barcodeDataUri,
            'errorMessage' => $errorMessage,
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $type = $request->string('type')->toString();

        if (! array_key_exists($type, self::TYPES)) {
            return response()->json(['error' => 'Неизвестный тип штрих-кода.'], 422);
        }

        try {
            return response()->json(['text' => $this->generateSample($type)]);
        } catch (Throwable $e) {
            Log::error('[BarcodeController.generate] ошибка генерации', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Не удалось сгенерировать данные.'], 500);
        }
    }

    private function generateSample(string $type): string
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

    private function randomAlnum(int $min, int $max): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $result = '';

        for ($i = 0, $length = random_int($min, $max); $i < $length; ++$i) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $result;
    }

    private function randomDigits(int $min, ?int $max = null): string
    {
        $result = '';

        for ($i = 0, $length = random_int($min, $max ?? $min); $i < $length; ++$i) {
            $result .= (string) random_int(0, 9);
        }

        return $result;
    }

    private function withEanCheckDigit(string $digits, int $fullLength): string
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

    private function withItf14CheckDigit(string $digits): string
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

    private function generateUpcE(): string
    {
        $rest = $this->randomDigits(4);

        $gtin11 = '0' . $rest[0] . $rest[1] . $rest[2] . $rest[3] . (string) random_int(1, 9) . '0000' . (string) random_int(5, 9);

        return UpcEConverter::compress($this->withEanCheckDigit($gtin11, 12));
    }
}
