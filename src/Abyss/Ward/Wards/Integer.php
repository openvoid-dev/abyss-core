<?php

declare(strict_types=1);

namespace Abyss\Ward\Wards;

use function ctype_digit;
use function is_int;
use function strval;

class Integer extends AbstractValidator
{
    /**
     * @var string
     */
    private $invalid_message = "This value should be of type {{ type }}.";
    private $min_message = "{{ value }} should be {{ limit }} or more.";
    private $max_message = "{{ value }} should be {{ limit }} or less.";

    /**
     * @var int|null
     */
    private $min;
    /**
     * @var int|null
     */
    private $max;

    public function validate($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (ctype_digit(strval($value)) === false) {
            $this->error($this->invalid_message, [
                "value" => $value,
                "type" => "integer",
            ]);
            return false;
        }

        if (is_int($this->min) && $value < $this->min) {
            $this->error($this->min_message, [
                "value" => $value,
                "limit" => $this->min,
            ]);
            return false;
        }

        if (is_int($this->max) && $value > $this->max) {
            $this->error($this->max_message, [
                "value" => $value,
                "limit" => $this->max,
            ]);
            return false;
        }

        return true;
    }

    public function invalid_message(string $invalid_message): self
    {
        $this->invalid_message = $invalid_message;
        return $this;
    }

    public function min_message(string $min_message): self
    {
        $this->min_message = $min_message;
        return $this;
    }

    public function max_message(string $max_message): self
    {
        $this->max_message = $max_message;
        return $this;
    }

    public function min(int $min): self
    {
        $this->min = $min;
        return $this;
    }

    public function max(int $max): self
    {
        $this->max = $max;
        return $this;
    }
}
