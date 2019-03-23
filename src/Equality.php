<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL;

interface Equality
{
    /**
     * @param mixed $that
     * @return bool
     */
    public function equals($that): bool;
}
