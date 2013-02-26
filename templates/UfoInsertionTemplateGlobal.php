<?php
require_once 'classes/abstract/UfoInsertionTemplate.php';
/**
 * ����� �������� ������� ������ ���������� ������ �������.
 * ��� ������ �������� ������� ������� ����� ����������� ���� �����.
 * 
 * @author enikeishik
 *
 */
abstract class UfoInsertionTemplateGlobal extends UfoInsertionTemplate
{
    /**
     * ����� ������ �������.
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawBegin(array $options = null)
    {
        echo 'Insertion drawBegin<br />' . "\r\n";
    }
    
    /**
     * ����� ��������, ���� ������� �� �������� ���������.
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawEmpty(array $options = null)
    {
        echo 'Insertion drawEmpty<br />' . "\r\n";
    }
    
    /**
     * ����� ��������� �������.
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawEnd(array $options = null)
    {
        echo 'Insertion drawEnd<br />' . "\r\n";
    }
}
