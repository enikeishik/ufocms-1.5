<?php
/*
unset освобождает память
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





/*
$sql = 'SELECT i.`Id`,i.`TargetId`,i.`PlaceId`,i.`OrderId`,i.`SourceId`,i.`SourcesIds`,i.`Title`,i.`ItemsStart`,i.`ItemsCount`,i.`ItemsLength`,i.`ItemsStartMark`,i.`ItemsStopMark`,i.`ItemsOptions`,s.path,m.mfileins FROM ufocms_insertions AS i INNER JOIN ufocms_sections AS s ON s.id=i.SourceId INNER JOIN ufocms_modules AS m ON m.muid=s.moduleid WHERE (i.TargetId=-1 OR i.TargetId=0) AND i.PlaceId=1 AND s.isenabled!=0 AND m.isenabled!=0 ORDER BY i.OrderId';
*/
/*
//ex1
$matches = null;
$pattern = '/^(SELECT |INSERT |UPDATE |DELETE |TRUNCATE |DROP ).+ (ufocms_[^ ]+)/';
if (preg_match($pattern, $sql, $matches)) {
    print_r($matches);
} else {
    echo 'Вхождение не найдено';
}
*/
/*
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
*/




/*
разделитель вставляется только между элементами, если элемент один, разделитель не используется
echo implode(',', array('123')) . "\r\n";
echo implode(',', array('123', '456')) . "\r\n";
echo implode(',', array('1','2','3')) . "\r\n";
*/



/*
ошибка уровня Notice
$arr = array('a' => 1, 'b' => 2);
echo $arr[a];
*/



/*
современный PHP одинаково быстро обрабатывает локальные переменные и поля класса
class A
{
    private $arr = array();
    private $x = -1;
    
    public function __construct()
    {
        for ($i = 0; $i < 100000; $i++) {
            $this->arr['k' . $i] = $i;
        }
    }
    
    public function m1()
    {
        $x = -1;
        foreach ($this->arr as $key => $val) {
            $x = $val;
        }
    }
    
    public function m2()
    {
        $x = -1;
        $arr = $this->arr;
        foreach ($arr as $key => $val) {
            $x = $val;
        }
    }
    
    public function m22()
    {
        $x = -1;
        $arr =& $this->arr;
        foreach ($arr as $key => $val) {
            $x = $val;
        }
    }
    
    public function m3()
    {
        $this->x = -1;
        foreach ($this->arr as $key => $val) {
            $this->x = $val;
        }
    }
    
    public function m4()
    {
        $x = -1;
        $arr = $this->arr;
        foreach ($arr as $key => $val) {
            $x = $val;
        }
        $this->x = $x;
    }
}
$o = new A();

echo 'exec m1 ';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
$o->m1();
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";

echo 'exec m2 ';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
$o->m2();
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";

echo 'exec m22';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
$o->m2();
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";

echo 'exec m3 ';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
$o->m1();
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";

echo 'exec m4 ';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
$o->m2();
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";
*/



/*
использование unset практически не сказывается на производительности
function a1()
{
    $arr = array();
    for ($i = 0; $i < 10000; $i++) {
        $arr['k' . $i] = str_repeat(' ', $i);
        $x = $arr['k' . $i];
        $y = $x;
        $x = str_repeat(' ', $i);
    }
    return $y;
}

function a2()
{
    $arr = array();
    for ($i = 0; $i < 10000; $i++) {
        $arr['k' . $i] = str_repeat(' ', $i);
        $x = $arr['k' . $i];
        $y = $x;
        unset($x);
        $x = str_repeat(' ', $i);
    }
    unset($arr);
    return $y;
}

echo 'M: ' . memory_get_usage() . "\r\n";
echo 'MT: ' . memory_get_usage(true) . "\r\n";

echo 'exec a1 ';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
for ($i = 0; $i < 10; $i++) {
    a1();
}
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";

echo 'M: ' . memory_get_usage() . "\r\n";
echo 'MT: ' . memory_get_usage(true) . "\r\n";

echo 'exec a2 ';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
for ($i = 0; $i < 10; $i++) {
    a2();
}
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";

echo 'M: ' . memory_get_usage() . "\r\n";
echo 'MT: ' . memory_get_usage(true) . "\r\n";
*/



/*
2-3 секунды a1 против 0.0005-0.0009 a2
function a1()
{
    for ($i = 0; $i < 1000; $i++) {
        clearstatcache();
        $file = __DIR__ . '/test.php';
        $ftime = fileatime($file);
    }
    echo $ftime . "\r\n";
}

function a2()
{
    for ($i = 0; $i < 1000; $i++) {
        $file = __DIR__ . '/test.php';
        $ftime = fileatime($file);
    }
    echo $ftime . "\r\n";
}

echo 'exec a1 ';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
a1();
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";

echo 'exec a2 ';
list($msec, $sec) = explode(chr(32), microtime());
$start = $sec + $msec;
a2();
list($msec, $sec) = explode(chr(32), microtime());
echo ' time: ' . round(($sec + $msec) - $start, 8) . "\r\n";
*/





$err = 'Can not unlink file %1s';
echo sprintf($err, 'somefile.txt');
