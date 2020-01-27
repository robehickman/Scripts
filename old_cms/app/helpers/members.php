<?php
function strip_content($content, $char_limit = 100)
{
    $content = strip_tags($content);
    $content = str_replace("\n", ' ', $content);

    $words = explode(' ', $content);

    $string = ''; 
    $count  = 0;
    foreach($words as $word) {
        $count += strlen($word);
    
        if($count >= $char_limit)
            break;

        $string .= $word . ' ';
    }

    return hen($string);
}
