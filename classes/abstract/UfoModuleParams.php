<?php
require_once 'UfoStruct.php';
/**
 * ����������� �����-��������� ��� �������� ������ ������.
 * ����� ���������� ����� ����, ������� ������ ���� ���������� ���������� �� ����������� ������.
 *
 * @author enikeishik
 * 
 */
abstract class UfoModuleParams extends UfoStruct
{
    /**
     * ������������� ����������� ��������.
     * @var int
     */
    public $id = 0;

    /**
     * ����� �������� ������������ ��� ������������ ������.
     * @var int
     */
    public $comments = 1;

    /**
     * ����� ������ � ������� RSS.
     * @var boolean
     */
    public $rss = false;
}
