<?php

namespace App\Services\Converter;

/**
 *  Converter object to array using hydrator pattern
 */
class ObjectConverter
{

    /**
     * toArray Convert data from object (entity or model) to array
     * @param  $object  Object which we will map to an array 
     * @return array $array
     */
    public static function toArray($object): array
    {
        $class = get_class($object);
        $methodList = get_class_methods($class);

        foreach ($methodList as $method) {
            preg_match(' /^(get)(.*?)$/i', $method, $matches);
            
            $prefix = $matches[1]  ?? '';
            $key = $matches[2]  ?? '';

            //to be safety with properties like speedAverageMin
            $key = strtolower(substr($key, 0, 1)) . substr($key, 1);

            if($prefix == 'get') {
                $array[$key] = $object->$method();
            }
        }
        
        return $array;
    }

} 

