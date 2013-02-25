<?php
require_once 'UfoTemplateGlobal.php';

class UfoTplDocuments extends UfoTemplateGlobal
{
    /*
     * ���������� ����������� �����, � �� � ������������ ������������ ������, 
     * ��� ���������� ������ � ���� ��������� ������, ��� ������, 
     * ��������� ��� ���������� ���� ��� IDE � ��� �������� 
     * ����������� ��������������� ���� $module � ��������� � phpDoc ������� ����.
     */
    /**
     * ������ �� ������ ������ �������� �������.
     * �������������� ����� ����� �������� ��� �������� ������, 
     * � �� ������������ ������������� ������ (��� IDE).
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
