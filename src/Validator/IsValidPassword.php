<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsValidPassword extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Le mot de passe n\'est pas valide (minimum 8 caractères, au moins une majuscule, au moins une minuscule, au moins un chiffre et au moins un caractère spécial parmi #?!@$ %^&*-).';
}
