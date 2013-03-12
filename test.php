<?php
$a = array();
echo 'M: ' . memory_get_usage() . "<br />\r\n";
echo 'MT: ' . memory_get_usage(true) . "<br />\r\n<br />\r\n";
for ($i = 0; $i < 99999; $i++) {
    $s = '';
    for ($j = 0; $j < 1024; $j++) {
        $s .= ' ';
    }
    $a[] = $s;
}
echo 'M: ' . memory_get_usage() . "<br />\r\n";
echo 'MT: ' . memory_get_usage(true) . "<br />\r\n<br />\r\n";
unset($a);
echo 'M: ' . memory_get_usage() . "<br />\r\n";
echo 'MT: ' . memory_get_usage(true) . "<br />\r\n";
