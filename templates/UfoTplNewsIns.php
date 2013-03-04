<?php
require_once 'classes/abstract/UfoInsertionItemTemplate.php';
/**
 * ����� ������� ������� ������ ��������.
 * 
 * @author enikeishik
 *
 */
class UfoTplNewsIns extends UfoInsertionItemTemplate
{
    /**
     * ����� ������ �������� �������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemBegin(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        echo 'Insertion drawItemBegin ' . print_r($insertion, true) . '<br />' . "\r\n";
    }
    
    /**
     * ����� ����������� �������� �������.
     * ���� ����� ����� ���������� ��������� ��� � ����� ��� ������ ������ ��������� ������� (��������, ������� ����� ��������).
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $data                           ������ (��������) �������� ����� ������� (������ ������� �� ��)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemContent(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $data, array $options = null)
    {
        echo 'Insertion drawItemContent; insertion: ' . 
             print_r($insertion, true) . '; data: ' . 
             print_r($data, true) . '<br />' . "\r\n";
    }
    
    /**
     * ����� ��������� �������� �������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEnd(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        echo 'Insertion drawItemEnd ' . print_r($insertion, true) . '<br />' . "\r\n";
    }
    
    /**
     * ����� ��������, ���� ������� ������� �� �������� ���������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEmpty(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        echo 'Insertion drawItemEmpty<br />' . "\r\n";
    }
}
