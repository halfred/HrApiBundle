<?php

namespace Hr\ApiBundle\Service;

/**
 * Class GenericFunction
 */
class GenericFunctionHelper
{
    public function getRecursiveDifference(array $array1, array $array2, array $options = [])
    {
        if (!array_key_exists('ignoreFields', $options)) {
            $options['ignoreFields'] = [];
        }
        
        $difference = [];
        foreach ($array1 as $key => $value) {
            
            if (in_array($key, $options['ignoreFields'])) {
                continue;
            }
            
            if (is_array($value) && isset($array2[$key])) {
                $newDiff = $this->getRecursiveDifference($value, $array2[$key], $options);
                if (!empty($newDiff)) {
                    $difference[$key] = $newDiff;
                }
            } elseif (is_string($value) && !in_array($value, $array2)) {
                $difference[$key] = "Value " . $value . " is missing from the second array";
            } elseif (!is_numeric($key) && !array_key_exists($key, $array2)) {
                $difference[$key] = "Key $key is missing from the second array";
            }
        }
        return $difference;
    }
    
}