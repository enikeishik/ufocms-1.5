<?php
/**
 * ��������� ������� ������, �������������� ������.
 * ��� ������ �������� ������� ������ ������������� ���� ��������� 
 * ��� ��� �������� ����������.
 * 
 * @author enikeishik
 *
 */
interface UfoTemplateInterface
{
    /**
     * ����� ���� �����.
     */
    public function drawMetaTags();
    
    /**
     * ����� ���������, ������������� � ��������� ���������.
     */
    public function drawHeadTitle();
    
    /**
     * ����� ��������������� ���� (JS, CSS, ...) � ��������� ���������.
     */
    public function drawHeadCode();
    
    /**
     * ����� ���������, ������������� �� ��������.
     */
    public function drawBodyTitle();
    
    /**
     * ����� ��������� ����������� ��������.
     */
    public function drawBodyContent();
    
    /**
     * ����� ������� ���������� �� ��������.
     * @param array $params = null    ��������� �������, �������������� ������, ������������ ������ ������� �������
     */
    public function drawInsertion(array $params = null);
}
