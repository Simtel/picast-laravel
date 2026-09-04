<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Http\Controllers;

use App\Context\Tools\Application\Service\BarcodeService;
use App\Context\Tools\Infrastructure\Request\BarcodeGenerateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

final class BarcodeController extends Controller
{
    public function __construct(
        private readonly BarcodeService $barcodeService,
    ) {
    }

    public function index(BarcodeGenerateRequest $request): View
    {
        $type = $request->string('type')->toString();
        $text = $request->string('text')->toString();
        $scale = $request->integer('scale', 2);
        $height = $request->integer('height', 50);

        $selectedType = $this->barcodeService->hasType($type) ? $type : 'code128';
        $barcodeDataUri = null;
        $errorMessage = null;

        if ($text !== '') {
            try {
                $barcodeDataUri = $this->barcodeService->render($selectedType, $text, $scale, $height);
            } catch (Throwable $e) {
                $errorMessage = $e->getMessage();
            }
        }

        return view('personal.tools.barcode.index', [
            'types' => collect($this->barcodeService->getTypes()),
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

        if (!$this->barcodeService->hasType($type)) {
            return response()->json(['error' => 'Неизвестный тип штрих-кода.'], 422);
        }

        try {
            return response()->json(['text' => $this->barcodeService->generateSample($type)]);
        } catch (Throwable $e) {
            Log::error('[BarcodeController.generate] ошибка генерации', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Не удалось сгенерировать данные.'], 500);
        }
    }
}
