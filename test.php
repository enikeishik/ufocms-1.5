<?php
/*
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
*/


$sql = 'SELECT i.`Id`,i.`TargetId`,i.`PlaceId`,i.`OrderId`,i.`SourceId`,i.`SourcesIds`,i.`Title`,i.`ItemsStart`,i.`ItemsCount`,i.`ItemsLength`,i.`ItemsStartMark`,i.`ItemsStopMark`,i.`ItemsOptions`,s.path,m.mfileins FROM ufocms_insertions AS i INNER JOIN ufocms_sections AS s ON s.id=i.SourceId INNER JOIN ufocms_modules AS m ON m.muid=s.moduleid WHERE (i.TargetId=-1 OR i.TargetId=0) AND i.PlaceId=1 AND s.isenabled!=0 AND m.isenabled!=0 ORDER BY i.OrderId';
/*
//ex1
$matches = null;
$pattern = '/^(SELECT |INSERT |UPDATE |DELETE |TRUNCATE |DROP ).+ (ufocms_[^ ]+)/';
if (preg_match($pattern, $sql, $matches)) {
    print_r($matches);
} else {
    echo '¬хождение не найдено';
}
*/
//ex2
$arr = array();
$matches = null;
$pattern = '/ufocms_[^ ]+/';
if (0 === stripos($sql, 'SELECT ')) {
    $arr[] = 'SELECT';
    preg_match_all($pattern, $sql, $matches);
    $arr = array_merge($arr, $matches[0]);
    //print_r($matches);
} else if (0 === stripos($sql, 'INSERT ')) {
    
} else if (0 === stripos($sql, 'UPDATE ')) {
    
} else if (0 === stripos($sql, 'DELETE ')) {
    
} else if (0 === stripos($sql, 'TRUNCATE ')) {
    
} else if (0 === stripos($sql, 'DROP ')) {
    
}
echo implode(' ', $arr);
