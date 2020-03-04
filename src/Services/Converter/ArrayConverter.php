<?php

namespace App\Services\Converter;

/**
 *  Converter array to object using hydrator pattern
 */
class ArrayConverter
{

    /**
     * toObject Convert array to given object instance
     * @param  array  $array  Array with data which we want bind to object
     * @param  $object  Object which takes the data from array 
     * @return $object
     */
    public static function toObject(array $array, $object)
    {
        $class = get_class($object);
        $methodList = get_class_methods($class);

        foreach ($methodList as $method) {
            preg_match(' /^(set)(.*?)$/i', $method, $matches);
            
            $prefix = $matches[1]  ?? '';
            $key = $matches[2]  ?? '';

            //to be safety with properties like speedAverageMin
            $key = strtolower(substr($key, 0, 1)) . substr($key, 1);

            if($prefix == 'set' && !empty($array[$key])) {
                $object->$method($array[$key]);
            }
        }
        
        return $object;
    }

} 

