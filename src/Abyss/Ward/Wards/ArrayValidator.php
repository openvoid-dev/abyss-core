<?php

declare(strict_types=1);

namespace Abyss\Ward\Wards;

class ArrayValidator extends AbstractValidator
{
    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @var bool
     */
    private $_not_empty = false;

    /**
     * Constructor to initialize the array of validators.
     *
     * @param AbstractValidator[] $validators
     */
    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    /**
     * Validate each element of the array using the provided validators.
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool
    {
        if (!is_array($value)) {
            $this->error("This value should be of type array.", [
                "value" => $value,
                "type" => "array",
            ]);
            return false;
        }

        if ($this->_not_empty && empty($value)) {
            $this->error("Array should not be empty", [
                "value" => $value,
                "type" => "array",
            ]);
            return false;
        }

        foreach ($value as $item) {
            foreach ($this->validators as $validator) {
                if (!$validator->validate($item)) {
                    $this->error($validator->get_error(), []); // Collect errors from the inner validators
                    return false;
                }
            }
        }

        return true;
    }

    public function not_empty(): self
    {
        $this->_not_empty = true;
        return $this;
    }
}
