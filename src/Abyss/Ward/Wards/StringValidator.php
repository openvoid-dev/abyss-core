<?php

declare(strict_types=1);

namespace Abyss\Ward\Wards;

use function is_int;
use function strlen;

class StringValidator extends AbstractValidator
{
    private string $invalid_message = "Invalid type given. String expected.";
    private string $min_message = "{{ value }} must be at least {{ limit }} characters long";
    private string $max_message = "{{ value }} cannot be longer than {{ limit }} characters";
    private ?int $min = null;
    private ?int $max = null;

    public function validate($value): bool
    {
        if (null === $value) {
            return true;
        }

        if (!is_string($value)) {
            $this->error($this->invalid_message, ["value" => $value]);
            return false;
        }

        if (is_int($this->min) && strlen($value) < $this->min) {
            $this->error($this->min_message, [
                "value" => $value,
                "limit" => $this->min,
            ]);
            return false;
        }

        if (is_int($this->max) && strlen($value) > $this->max) {
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
