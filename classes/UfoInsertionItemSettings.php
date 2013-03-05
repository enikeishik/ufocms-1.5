<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * �����-��������� ��� �������� �������������� ������ (��������� ������ �������, ����������� ������) �������� �������.
 *
 * @author enikeishik
 *
 */
class UfoInsertionItemSettings extends UfoStruct
{
    /**
     * ���� �������-���������.
     * @var string
     */
    public $path = '';
    
    /**
     * ��� ����� ������� ������.
     * @var string
     */
    public $mfileins = '';
}
