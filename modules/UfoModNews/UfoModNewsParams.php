<?php
require_once 'classes/abstract/UfoModuleParams.php';
/**
 * �����-��������� ��� �������� ������ ������.
 * @author enikeishik
 */
class UfoModNewsParams extends UfoModuleParams
{
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
