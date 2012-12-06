<?php
/**
 * ��������� ������� ������, �������������� ������.
 * ��� ������ �������� ������� ������ ������������� ���� ��������� 
 * ��� ��� �������� ����������.
 */
interface UfoTemplateInterface
{
    /**
     * ��������� ���� �����.
     * @return string
     */
    public function getMetaTags();
    
    /**
     * ��������� ���������, ������������� � ���� <title>.
     * @return string
     */
    public function getHeadTitle();
    
    /**
     * ��������� ���������, ������������� �� ��������.
     * @return string
     */
    public function getBodyTitle();
    
    /**
     * ��������� ��������� ����������� ��������.
     * @return string
     */
    public function getBodyContent();
}
