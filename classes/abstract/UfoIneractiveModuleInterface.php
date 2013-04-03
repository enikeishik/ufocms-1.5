<?php
require_once 'UfoModuleInterface.php';
/**
 * ��������� �������������� ������, �������������� ������.
 * ��� ������ ������� ������ ������������� ���� ��������� ��� ��� �������� ����������.
 *
 * @author enikeishik
 *
 */
interface UfoIneractiveModuleInterface extends UfoModuleInterface
{
    /**
     * ���������� �������� � ������ ����������� �����.
     */
    public function addItem();
    
    /**
     * ��������� �������� �������.
     */
    public function updateItem();
    
    /**
     * �������� �������� �� �������.
     */
    public function deleteItem();
}
