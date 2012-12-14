<?php
require_once 'classes/abstract/UfoStruct.php';

/**
 * �����-��������� ��� �������� ������ ������.
 * @author enikeishik
 */
class UfoModNewsParams extends UfoStruct
{
    /**
     * ������������� ����������� ��������.
     * @var int
     */
    public $id = 0;
    
    /**
     * ����� �������� ��� ������������ ������.
     * @var int
     */
    public $page = 1;
    
    /**
     * ����, �� ������� ���� ������� ��������.
     * @var DateTime
     */
    public $dt = null;
}
