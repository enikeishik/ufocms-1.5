<?php
require_once 'UfoTemplateGlobal.php';

class UfoTplDocuments extends UfoTemplateGlobal
{
    /*
     * Определять конструктор здесь, а не в родительском абрстрактном классе, 
     * для приведения модуля к типу реального модуля, нет смысла, 
     * поскольку это необходимо лишь для IDE и уже работает 
     * посредством переопределения поля $module с указанием в phpDoc нужного типа.
     */
    /**
     * Ссылка на объект модуля текущего раздела.
     * Переопределена здесь чтобы получить тип текущего класса, 
     * а не абстрактного родительского класса (для IDE).
     * @var UfoModDocuments
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
