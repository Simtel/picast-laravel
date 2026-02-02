<?php

declare(strict_types=1);

namespace App\Context\Webcams\Application\Dto;

use App\Context\Webcams\Domain\Model\Webcam;

class WebcamDto
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $location,
        private readonly string $streamUrl,
        private readonly string $previewUrl,
        private readonly string $description,
        private readonly bool $isActive,
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null
    ) {
    }

    /**
     * Создать DTO из модели Webcam
     */
    public static function fromModel(Webcam $webcam): self
    {
        return new self(
            id: $webcam->getId(),
            name: $webcam->getName(),
            location: $webcam->getLocation(),
            streamUrl: $webcam->getStreamUrl(),
            previewUrl: $webcam->getPreviewUrl(),
            description: $webcam->getDescription(),
            isActive: $webcam->isActive(),
            createdAt: $webcam->getCreatedAt()?->toISOString(),
            updatedAt: $webcam->getUpdatedAt()?->toISOString()
        );
    }

    /**
     * Создать DTO из массива данных
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            location: $data['location'],
            streamUrl: $data['stream_url'],
            previewUrl: $data['preview_url'],
            description: $data['description'],
            isActive: $data['is_active'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'stream_url' => $this->streamUrl,
            'preview_url' => $this->previewUrl,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    // Геттеры
    public function getId(): int
    {
        return $this->id;
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
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
