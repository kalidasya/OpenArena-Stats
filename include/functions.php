<?php

function explode_to_assoc($string, $separator) {
    $array = array();
    $parts = explode($separator, $string);

    foreach($parts as $key => $value) {
        if (($key % 2) == FALSE) {
            $array[$value] = $parts[$key+1];
        }
    }

    return $array;
}
