<?php

namespace App\Validation;

use Symfony\Component\Form\FormInterface;

class ValidationError
{
    public function getErrorsFromForms(FormInterface $form): array
    {
        $errors = array();

        foreach ($form->getErrors() as $error){
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm){
            if ($childForm instanceof FormInterface){
                $errors[$childForm->getName()] = $childForm;
            }
        }

        return $errors;
    }
}