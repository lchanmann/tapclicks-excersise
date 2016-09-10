<?php

/**
* array helper methods
*/

/**
* Check if the two arrays have the same values for the specified keys
* array_have_same($keys, $array1, $array2)
* @return true or false
*/
function array_have_same($keys, $array1, $array2) {
    foreach ($keys as $key) {
        if ($array1[$key] !== $array2[$key]) {
            return false;
        }
    }
    return true;
}
?>