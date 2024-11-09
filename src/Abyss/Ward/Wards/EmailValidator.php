<?php

declare(strict_types=1);

namespace Abyss\Ward\Wards;

use function filter_var;
use function is_string;

class EmailValidator extends AbstractValidator
{
    /**
     * @var string
     */
    private string $message = "{{ value }} is not a valid email address.";

    public function validate($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_string($value)) {
            $this->error($this->message, ["value" => $value]);
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->error($this->message, ["value" => $value]);
            return false;
        }

        return true;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
