<?php
/**
 * ��������� ������� �����.
 * ��� ������ �������� ������ ������������� ���� ��������� ��� ��� �������� ����������.
 * 
 * @author enikeishik
 *
 */
interface UfoSectionInterface
{
    /**
     * ������������� ������� ������, �������������� ������.
     * @throws Exception
     */
    public function initModule();
    
    /**
     * ����������� ��������������� ������� ��������.
     * @return string
     */
    public function getPage();
}
