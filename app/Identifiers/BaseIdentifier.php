<?php

namespace App\Identifiers;

use App\Contracts\Identifiable;

class BaseIdentifier implements Identifiable
{
    protected string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return [];
    }
}
