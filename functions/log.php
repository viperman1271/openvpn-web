<?php

function logMsg($message)
{
    if(ini_get('display_errors') != '1')
    {
        echo $message;
    }
}

?>
