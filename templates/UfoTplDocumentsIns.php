<?php
require_once 'classes/abstract/UfoInsertionItemTemplate.php';
/**
 * ����� ������� ������� ������ ����������.
 * 
 * @author enikeishik
 *
 */
class UfoTplDocumentsIns extends UfoInsertionItemTemplate
{
    /**
     * ����� ������ �������� �������.
     * @param mixed $item              ������������� ��� ��������� �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemBegin($item, array $options = null)
    {
        echo 'Insertion drawItemBegin ' . print_r($item, true) . '<br />' . "\r\n";
    }
    
    /**
     * ����� ����������� �������� �������.
     * ���� ����� ����� ���������� ��������� ��� � ����� ��� ������ ������ ��������� ������� (��������, ������� ����� ��������).
     * @param mixed $item              ������������� ��� ��������� �������� ����� �������
     * @param array $data              ������ (��������) �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemContent($item, array $data, array $options = null)
    {
        echo 'Insertion drawItemContent; item: ' . 
             print_r($item, true) . '; data: ' . 
             print_r($data, true) . '<br />' . "\r\n";
    }
    
    /**
     * ����� ��������� �������� �������.
     * @param mixed $item              ������������� ��� ��������� �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEnd($item, array $options = null)
    {
        echo 'Insertion drawItemEnd ' . print_r($item, true) . '<br />' . "\r\n";
    }
    
    /**
     * ����� ��������, ���� ������� ������� �� �������� ���������.
     * @param mixed $item              ������������� ��� ������ �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEmpty($item, array $options = null)
    {
        echo 'Insertion drawItemEmpty<br />' . "\r\n";
    }
}
