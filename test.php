<?php
function isNumeric0($str)
{
    if (function_exists('ctype_digit')) {
        if (ctype_digit($str)) {
            return true;
        }
    } else {
        if (is_numeric($str)) {
            return true;
        }
    }
    return false;
}

function isNumeric00($str)
{
    return is_numeric($str);
}

function isNumeric01($str)
{
    return ctype_digit($str);
}

function isNumeric2($str)
{
    $int = (int) $str;
    return $str == $int && strlen($str) == strlen($int);
}

function isNumeric3($str)
{
    return $str == (int) $str && strlen($str) == strlen((int) $str);
}

function isInt($str)
{
    return ctype_digit($str) && ($str < PHP_INT_MAX) && ($str > (PHP_INT_MAX * -1) - 1);
}

/*
var_dump(isNumeric2('123a456'));
echo (int) '123a456';
*/

ob_start();
list($debugvar_msec, $debugvar_sec) = explode(chr(32), microtime());
$debugvar_page_start = $debugvar_sec + $debugvar_msec;
for ($i = 0; $i < 1000000; $i++) {
    isInt('123a456');
    ob_clean();
}
echo '<p>M: ' . memory_get_usage() . '; MT: ' . memory_get_usage(true) . '</p>';
list($debugvar_msec, $debugvar_sec) = explode(chr(32), microtime());
$debugvar_exec_time = round(($debugvar_sec + $debugvar_msec) - $debugvar_page_start, 8);
echo '<p>Exec time: ' . $debugvar_exec_time . ' s.</p>';
ob_flush();

echo PHP_INT_MAX;
