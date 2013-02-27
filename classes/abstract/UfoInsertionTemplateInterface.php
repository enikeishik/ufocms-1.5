<?php
/**
 * ��������� ������� �������.
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
