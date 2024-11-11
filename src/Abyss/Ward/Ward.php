<?php

/**
 * Custom Validator class Ward used for validating your data,
 * mainly used for validating post request so you know
 * exactly what you are getting from it.
 *
 * It was taken from https://github.com/openvoid-dev/php-validator
 * and rebranded from 'PHP Validator' to 'Ward' and will later be
 * added as a standalone package again under the name 'Ward'
 */

namespace Abyss\Ward;

use Abyss\Ward\Wards\ValidatorInterface as AbyssValidatorInterface;

use InvalidArgumentException;
use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function sprintf;

final class Ward
{
    /**
     * @var array<string,array>
     */
    private $validators;

    /**
     * @var array<string,string>
     */
    private $errors = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param array<int,mixed> $field_validators
     */
    public function __construct(array $field_validators)
    {
        foreach ($field_validators as $field => $validators) {
            if (!is_array($validators)) {
                $validators = [$validators];
            }

            $this->add_validator($field, $validators);
        }
    }
    /**
     * @param array<int,mixed> $data
     */
    public function validate(array $data): bool
    {
        $this->data = $data;

        /**
         * @var $validators array<ValidatorInterface>
         */
        foreach ($this->validators as $field => $validators) {
            if (!isset($this->data[$field])) {
                $this->data[$field] = null;
            }

            foreach ($validators as $validator) {
                if ($validator->validate($this->data[$field]) === false) {
                    $this->add_error($field, (string) $validator->get_error());
                }
            }
        }

        return $this->get_errors() === [];
    }

    /**
     * @return array<string,string>
     */
    public function get_errors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function get_data(): array
    {
        return $this->data;
    }

    private function add_error(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * @param string $field
     * @param array<ValidatorInterface> $validators
     * @return void
     */
    private function add_validator(string $field, array $validators): void
    {
        foreach ($validators as $validator) {
            if (!$validator instanceof AbyssValidatorInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        $field .
                            ' validator must be an instance of ValidatorInterface, "%s" given.',
                        is_object($validator)
                            ? get_class($validator)
                            : gettype($validator)
                    )
                );
            }

            $this->validators[$field][] = $validator;
        }
    }
}
