<?php

declare(strict_types=1);

namespace App\Context\Webcams\Application\Dto;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateWebcamDto
{
    private function __construct(
        private readonly string $name,
        private readonly string $location,
        private readonly string $streamUrl,
        private readonly string $previewUrl,
        private readonly string $description,
        private readonly bool $isActive = true
    ) {
    }

    /**
     * Создать DTO из массива данных
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'stream_url' => 'required|url|max:500',
            'preview_url' => 'required|url|max:500',
            'description' => 'required|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        return new self(
            name: $validated['name'],
            location: $validated['location'],
            streamUrl: $validated['stream_url'],
            previewUrl: $validated['preview_url'],
            description: $validated['description'],
            isActive: $validated['is_active'] ?? true
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getStreamUrl(): string
    {
        return $this->streamUrl;
    }

    public function getPreviewUrl(): string
    {
        return $this->previewUrl;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
