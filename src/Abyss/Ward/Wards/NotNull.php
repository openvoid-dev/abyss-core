<?php

declare(strict_types=1);

namespace Abyss\Ward\Wards;

class NotNull extends AbstractValidator
{
    private $message = "This value should not be null.";

    public function validate($value): bool
    {
        if ($value === null || empty($value)) {
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
