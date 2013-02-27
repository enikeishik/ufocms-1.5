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
     * @param mixed $item              ������������� ��� ��������� �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemBegin($item, array $options = null);
    
    /**
     * ����� ����������� �������� �������.
     * ���� ����� ����� ���������� ��������� ��� � ����� ��� ������ ������ ��������� ������� (��������, ������� ����� ��������).
     * @param mixed $item              ������������� ��� ��������� �������� ����� �������
     * @param array $data              ������ (��������) �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemContent($item, array $data, array $options = null);
    
    /**
     * ����� ��������� �������� �������.
     * @param mixed $item              ������������� ��� ��������� �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEnd($item, array $options = null);
    
    /**
     * ����� ��������, ���� ������� ������� �� �������� ���������.
     * @param mixed $item              ������������� ��� ��������� �������� ����� �������
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawItemEmpty($item, array $options = null);
}
