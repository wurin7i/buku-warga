<?php

namespace App\Enums\Concerns;

use Throwable;
use App\Enums\Comparable;

trait Operations
{
    public function is($agains): bool
    {
        if (!$agains instanceof Comparable) {
            try {
                $agains = self::tryFrom($agains);
            } catch (Throwable $th) {
                $agains = null;
            }
        }

        return $this === $agains;
    }
}