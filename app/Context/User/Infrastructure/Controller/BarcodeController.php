<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

        return view('personal.barcode.index', [
            'types' => collect(self::TYPES),
            'selectedType' => $selectedType,
            'text' => $text,
            'scale' => $scale,
            'height' => $height,
            'barcodeDataUri' => $barcodeDataUri,
            'errorMessage' => $errorMessage,
        ]);
    }
}
