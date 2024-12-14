<?php

namespace Abyss\Ward\Wards;

interface WardsInterface
{
    /**
     * Validate a given value
     *
     * @param mixed $value
     * @return bool
     */
    public function validate(mixed $value): bool;
}