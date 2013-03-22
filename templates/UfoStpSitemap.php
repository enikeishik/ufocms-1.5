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
<p><?php echo $this->module->getContent(); ?></p>
</td>
</tr>
</table>
<?php
    }
}
