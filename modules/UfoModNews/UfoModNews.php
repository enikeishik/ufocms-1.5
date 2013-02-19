<?php
require_once 'classes/abstract/UfoModule.php';

class UfoModNews extends UfoModule
{
    /**
     * ������������� ���������� ��������.
     * @var int
     */
    protected $id = 0;
    
    /**
     * ������������ ������� ������ ������ ��������.
     * @return array
     */
    public function getItem()
    {
        return array('');
    }
    
    /**
     * ������������ ������� �������� ������ ���������.
     * @return array:array
     */
    public function getItems()
    {
        return array(array(''), array(''));
    }
    
    /**
     * ��������� �������������� �������� ��������, ���������� 0 ���� �������� �� �������, � ������.
     * @return int
     */
    protected function getItemId()
    {
        return 0;
    }
}
