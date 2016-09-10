<?php

/**
* sql helper methods
*/

/**
* Sanitize single quote char
* sanitize($text)
*/
function sanitize($text) {
    return str_replace("'", "''", $text);
}
?>