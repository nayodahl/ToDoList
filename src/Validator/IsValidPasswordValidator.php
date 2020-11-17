<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class IsValidPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\IsValidPassword */
        if (!$constraint instanceof IsValidPassword) {
            // @codeCoverageIgnoreStart
            throw new UnexpectedTypeException($constraint, IsValidPassword::class);
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

        // Regex for password, exemple here https://ihateregex.io/expr/password
        if (!preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{8,}$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
        }
    }
}
