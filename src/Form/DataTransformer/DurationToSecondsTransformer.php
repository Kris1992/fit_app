<?php
declare(strict_types=1);

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DurationToSecondsTransformer implements DataTransformerInterface
{

    private $fields;

    public function __construct()
    {
        $fields = ['hour', 'minute', 'second'];
        
        $this->fields = $fields;     
    }

    public function transform($value)
    {   
        if (null === $value) {
            return null;
        }

        if(!is_integer($value)) {
            throw new TransformationFailedException('This field expected argument of type integer!');
        }  

        $array['hour'] = (int)($value / (60 * 60));
        $array['minute'] = (int)(($value / 60) % 60);
        $array['second'] = (int)($value % 60);
        

        return $array;
    }
    public function reverseTransform($array)
    {   
        if($array === null){
            return null;
        }

        // To make sure it is everything ok
        if(!is_array($array)) {
            throw new TransformationFailedException('This field expected argument of type array!');
        } 
        if(!$this->isIntegers($array)) {
            throw new TransformationFailedException('This field expected argument of type array of integers!');
        }

        //if choice is wrong value the array with this key is missing
        /*if(
            !array_key_exists('hour', $array) || 
            !array_key_exists('minute', $array) || 
            !array_key_exists('second', $array)
        ) {
            throw new \LogicException('Unexpected value given!');
        }*/

        $emptyFields = [];

        foreach ($this->fields as $field) {
            if (!isset($array[$field])) {
                $emptyFields[] = $field;
            }
        }

        if (count($emptyFields) > 0) {
            throw new TransformationFailedException(sprintf('The fields "%s" should not be empty', implode('", "', $emptyFields)));
        }

        //Now prevent before values e.g 01
        if (isset($array['hour']) && !ctype_digit((string) $array['hour'])) {
            throw new TransformationFailedException('Hour given is invalid');
        }

        if (isset($array['minute']) && !ctype_digit((string) $array['minute'])) {
            throw new TransformationFailedException('Minute given is invalid');
        }

        if (isset($array['second']) && !ctype_digit((string) $array['second'])) {
            throw new TransformationFailedException('Second given is invalid');
        }

        $seconds = ($array['hour'] *(60 * 60)) + ($array['minute'] * 60) + $array['second'];
        
        return $seconds;
    }

    private function isIntegers(array $array): bool
    {
        foreach($array as $value) {
            if(!is_integer($value)){
                return false;
            }
        }
        return true;
    }

}
