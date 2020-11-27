<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsValidUsername extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'L\'identifiant "{{ value }}" ne respecte pas les règles de complexité : 
                        entre 4 et 16 caractères alphanumériques ainsi que - et _';
}
