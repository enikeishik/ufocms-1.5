<?php
require_once 'UfoTemplateGlobal.php';

class UfoStpSitemap extends UfoTemplateGlobal
{
    /**
     * —сылка на объект модул€ текущего раздела.
     * ѕереопределена здесь чтобы получить тип текущего класса, 
     * а не абстрактного родительского класса (дл€ IDE).
     * @var UfoSysSitemap
     */
    protected $module = null;
    
    public function drawBodyContent()
    {
?>
<table border="1" style="width: 100%;">
<tr>
<td width="200">
<?php
$parent = $this->section->getParent();
if (!is_null($parent)) {
    echo $parent->title;
} else {
    echo 'parent not exists';
}
?>
</td>
<td>
<?php
$items = $this->module->getContent();
$host = array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : 'localhost';
foreach ($items as $item) {
    $indic = $item['indic'];
    if (0 === strpos($indic, '!') ) {
        $indic = substr($indic, 1);
    }
    if (-1 == $item['levelid']) {
        echo '<div style="margin-bottom: 5px;">' .
                '<div style="padding: 2px; background-color: #EEEEEE;"><a href="' . $item['path'] . '"><b>' . $indic . '</b></a></div>' .
                '<div style="padding: 2px;">' . $item['metadesc'] . '</div>' .
                '<div style="padding: 2px; color: #666666">http://' . $host . $item['path'] . '</div>' .
                '</div>' . "\r\n";
    } else if (0 == $item['levelid']) {
        echo '<div style="margin-bottom: 5px; margin-top: 15px;">' .
                '<div style="padding: 2px; background-color: #EEEEEE;"><a href="' . $item['path'] . '"><b>' . $indic . '</b></a></div>' .
                '<div style="padding: 2px;">' . $item['metadesc'] . '</div>' .
                '<div style="padding: 2px; color: #666666">http://' . $host . $item['path'] . '</div>' .
                '</div>' . "\r\n";
    } else {
        echo '<div style="margin-left: ' . (20 * $item['levelid']) . 'px; margin-bottom: 5px;">' .
                '<div><a href="' . $item['path'] . '">' . $indic . '</a></div>' .
                '<div>' . $item['metadesc'] . '</div>' .
                '<div style="color: #666666">http://' . $host . $item['path'] . '</div>' .
                '</div>' . "\r\n";
    }
}
?>
</td>
</tr>
</table>
<?php
    }
}
