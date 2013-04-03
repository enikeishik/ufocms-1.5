<?php
require_once 'UfoModuleParams.php';
/**
 * ����������� �����-��������� ��� ����������� � �������� ���������� �������������� ������.
 * ����� ���������� ����� ����, ������� ������ ���� ���������� ���������� �� ����������� ������.
 *
 * @author enikeishik
 * 
 */
abstract class UfoInteractiveModuleParams extends UfoModuleParams
{
    /**
     * ����������� �������� (0 - ������ ������, 1 - ���������� �������� � �.�.).
     * @var int
     */
    public $action = 0;
}
