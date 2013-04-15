<?php
require_once 'UfoTemplateGlobal.php';

class UfoStpSearch extends UfoTemplateGlobal
{
    /**
     * ������ �� ������ ������ �������� �������.
     * �������������� ����� ����� �������� ��� �������� ������,
     * � �� ������������ ������������� ������ (��� IDE).
     * @var UfoSysSearch
     */
    protected $module = null;
    
    public function drawBodyContent()
    {
        $items = $this->module->getContent();
        if (false === $items) {
            echo '<p>����������� ������</p>' . "\r\n";
            return;
        }
        foreach ($items as $item) {
            echo '<p>' . $item['Title'] . '</p>' . "\r\n";
        }
    }
}
