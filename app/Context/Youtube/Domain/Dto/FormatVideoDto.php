<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Dto;

final readonly class FormatVideoDto
{
    public function __construct(
        private int $formatId,
        private string $formatNote,
        private string $videoExt,
        private string $vCodec,
        private string $resolution,
    ) {
    }

    public function getFormatId(): int
    {
        return $this->formatId;
    }

    public function getFormatNote(): string
    {
        return $this->formatNote;
    }

    public function getVideoExt(): string
    {
        return $this->videoExt;
    }

    public function getVCodec(): string
    {
        return $this->vCodec;
    }

    public function getResolution(): string
    {
        return $this->resolution;
    }


}
