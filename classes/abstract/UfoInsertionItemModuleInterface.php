<?php
/**
 * ��������� ������� ������.
 * ��� ������ ������� ������� ������ ������������� ���� ��������� 
 * ��� ��� �������� ����������.
 * 
 * @author enikeishik
 *
 */
interface UfoInsertionItemModuleInterface
{
    /**
     * ��������� ����������� �������� ����� �������.
     * @param UfoInsertionItemStruct $insertion     ������ �������� �������
     * @param UfoInsertionItemSettings $settings    �������������� ������ �������� ������� (���� �������-���������, ��������� ������ � �.�.)
     * @param mixed $options = null                 �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, 
                                 UfoInsertionItemSettings $settings, 
                                 array $options = null);
}
