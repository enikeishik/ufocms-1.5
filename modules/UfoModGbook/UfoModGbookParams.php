<?php
require_once 'classes/abstract/UfoModuleParams.php';
/**
 * �����-��������� ��� �������� ������ ������.
 * @author enikeishik
 */
class UfoModGbookParams extends UfoModuleParams
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
    
    /**
     * �������� (�������� ����� ����������� � �.�.).
     * @var int
     */
    public $action = 0;
}
