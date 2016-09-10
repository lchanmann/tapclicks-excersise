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

/**
* Wrap with backticks
* backticks_wrap($text)
*/
function backticks_wrap($text) {
    if ($text[0] !== chr(96)) $text = chr(96) . $text;
    if ($text[strlen($text) - 1] !== chr(96)) $text .= chr(96);
    return $text;
}
?>