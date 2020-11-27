<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class IsValidUsernameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\IsValidUsername */
        if (!$constraint instanceof IsValidUsername) {
            // @codeCoverageIgnoreStart
            throw new UnexpectedTypeException($constraint, IsValidUsername::class);
            // @codeCoverageIgnoreEnd
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        if (!is_string($value)) {
            // @codeCoverageIgnoreStart
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
            // @codeCoverageIgnoreEnd
        }

        // Regex for username, exemple here https://ihateregex.io/expr/username
        // only alhpanumeric caracteres and between 4 and 16 caracters
        if (!preg_match('/^[a-zA-Z0-9_-]{4,15}$/', $value)) {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
        }
    }
}
