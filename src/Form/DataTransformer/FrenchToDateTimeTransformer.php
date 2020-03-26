<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface{

    public function transform($date)
    {
        if($date === null){
            return '';
        }

        return $date->format('d/m/y');
    }

    public function reverseTransform($frenchdate)
    {
        if($frenchdate === null){
            //Exception
            throw new TransformationFailedException('Vous devez fournir une date');
        }

        $date = \DateTime::createFromFormat('d/m/Y', $frenchdate);

        if($date === false){
            //Exception
            throw new TransformationFailedException('Mauvais format de date');
        }
        return $date;
    }
}