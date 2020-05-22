<?php
declare(strict_types=1);

namespace App\Services\Converter;

/**
 *  Converter array to object using hydrator pattern
 */
class ArrayConverter
{

    /**
     * toObject Convert array to given object instance (it supports only one parameter functions)
     * @param  array  $array  Array with data which we want bind to object
     * @param  $object  Object which takes the data from array 
     * @return $object
     */
    public static function toObject(array $array, $object)
    {
        $class = new \ReflectionObject($object);
        $methodList = $class->getMethods();
        //$class = get_class($object);
        //$methodList = get_class_methods($class);

        foreach ($methodList as $method) {
            preg_match(' /^(set)(.*?)$/i', $method->getName(), $matches);
            
            $prefix = $matches[1]  ?? '';
            $key = $matches[2]  ?? '';

            //to be safety with properties like speedAverageMin
            $key = strtolower(substr($key, 0, 1)) . substr($key, 1);

            if($prefix == 'set' && !empty($array[$key])) {
                $typos = $method->getParameters()[0]->getType()->getName();
                $methodName = $method->getName();
                if ($typos) {
                    settype($array[$key], $typos);
                } 
                
                $object->$methodName($array[$key]);
            }
        }
        
        return $object;
    }

} 

