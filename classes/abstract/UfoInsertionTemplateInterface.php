<?php
/**
 * ��������� ������� ������� ������.
 * ��� ������ �������� ������� ������� ������ ������������� ���� ���������
 * ��� ��� �������� ����������.
 *
 * @author enikeishik
 *
 */
interface UfoInsertionTemplateInterface
{
    /**
     * ����� ������ �������.
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawBegin(array $options = null);
    
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
    
    /**
     * ����� ��������, ���� ������� �� �������� ���������.
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawEmpty(array $options = null);
    
    /**
     * ����� ��������� �������.
     * @param array $options = null    �������������� ������, ������������ ������ ������� �������
     */
    public function drawEnd(array $options = null);
}
