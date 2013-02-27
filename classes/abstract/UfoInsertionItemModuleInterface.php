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
     * @param UfoInsertionItemStruct $insertion    ������ �������� �������
     * @param string $path                         ���� �������-��������� �������
     * @param mixed $options = null                �������������� ������, ������������ ������ ������� �������
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, $path, array $options = null);
}
