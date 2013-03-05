<?php
/**
 * ��������� ������� ������� ������.
 * ��� ������ �������� ������� ������� ������ ������������� ���� ���������
 * ��� ��� �������� ����������.
 *
 * @author enikeishik
 *
 */
interface UfoInsertionItemTemplateInterface
{
    /**
     * ����� ������ �������� �������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemBegin(UfoInsertionItemStruct $insertion, 
                                  UfoInsertionItemSettings $settings, 
                                  array $options = null);
    
    /**
     * ����� ����������� �������� �������.
     * ���� ����� ����� ���������� ��������� ��� � ����� ��� ������ ������ ��������� ������� (��������, ������� ����� ��������).
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $item                           ������ (��������) �������� ����� ������� (������ ������� �� ��)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemContent(UfoInsertionItemStruct $insertion, 
                                    UfoInsertionItemSettings $settings, 
                                    array $item, 
                                    array $options = null);
    
    /**
     * ����� ��������� �������� �������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEnd(UfoInsertionItemStruct $insertion, 
                                UfoInsertionItemSettings $settings, 
                                array $options = null);
    
    /**
     * ����� ��������, ���� ������� ������� �� �������� ���������.
     * @param UfoInsertionItemStruct $insertion     ��������� �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param array $options = null                 �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEmpty(UfoInsertionItemStruct $insertion, 
                                  UfoInsertionItemSettings $settings, 
                                  array $options = null);
}
