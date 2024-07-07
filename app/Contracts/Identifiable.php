<?php

namespace App\Contracts;

interface Identifiable
{
    /**
     * Identifier type getter
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Identifier attributes getter
     *
     * @return array<string, string>
     */
    public function getAttributes(): array;
}
