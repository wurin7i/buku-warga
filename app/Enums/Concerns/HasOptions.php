<?php

namespace App\Enums\Concerns;

use Illuminate\Support\Str;

trait HasOptions
{
    public static function getValues(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public static function getArrayOptions(): array
    {
        $keys = array_map(fn (self $case) => $case->value, self::cases());
        $labels = array_map(fn (self $case) => $case->label(), self::cases());

        return array_combine($keys, $labels);
    }

    public function label(): string
    {
        return Str::headline($this->name);
    }

    public function is($against): bool
    {
        if (!$against instanceof self) {
            $against = self::tryFrom($against);
        }

        return $this === $against;
    }
}
