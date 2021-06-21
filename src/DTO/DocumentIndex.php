<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

class DocumentIndex
{
    public string $type;

    public function __construct(
        public string $name,
        public int | string $value,
    ) {
        $this->type = is_integer($value) ? 'Int' : 'String';
    }

    public static function make(string $name, int | string $value): self
    {
        return new self($name, $value);
    }

    public function valuess(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => $this->value,
            'ItemElementName' => $this->type,
        ];
    }

    public static function makeContent(Collection $indexes): string
    {
        $indexContent = (object) [
            'Fields' => $indexes
                ->map(fn (DocumentIndex $index) => $index->values())
                ->toArray(),
        ];

        return json_encode($indexContent);
    }
}
