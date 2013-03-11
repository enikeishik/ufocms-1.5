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
     * ����� HTTP ����������.
     */
    public function drawHttpHeaders();
    
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
     * @param array $options = null    ��������� �������, �������������� ������, ������������ ������ ������� �������
     */
    public function drawInsertion(array $options = null);
}
