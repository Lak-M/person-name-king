<?php

declare(strict_types=1);

namespace App\Data;

final readonly class Name
{
    public function __construct(
        public string $first,
        public ?string $middle,
        public ?string $last,
        public ?string $sorted,
        public ?string $possessive,
        public ?string $prefix = null,
        public ?string $suffix = null,
        public ?string $redated = null,
        public ?string $mentionable = null,
        public ?string $family = null,
        /** @var string[] $abbreviations */
        public array $abbreviations = [],
        public ?string $father = null,
        public ?string $grand_father = null,
        public ?string $kunya = null,
        public ?string $ism = null,
        public ?string $nasab = null,
        public ?string $laqab = null,
        public ?string $nisbah = null,
    ) {}

    public function __toArray(): array
    {
        return array_filter(get_object_vars($this));
    }
}
