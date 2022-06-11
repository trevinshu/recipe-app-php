<?php
//move this to includes/_functions.php
function makeClickableLinks($text)
{
    $text = " " . $text; // fixes problem of not linking if no chars before the link
    $text = preg_replace(
        '/(((f|ht){1}tps?:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
        '<a href="\\1">\\1</a>',
        $text
    );
    $text = preg_replace(
        '/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
        '\\1<a href="http://\\2">\\2</a>',
        $text
    );
    $text = preg_replace(
        '/([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})/i',
        '<a href="mailto:\\1">\\1</a>',
        $text
    );
    return trim($text);
} // end makeClickableLinks
