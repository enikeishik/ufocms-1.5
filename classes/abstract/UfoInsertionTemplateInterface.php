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
     */
    public function drawBegin();
    
    /**
     * ����� ������ �������� �������.
     */
    public function drawItemBegin();
    
    /**
     * ����� ����������� �������� �������.
     */
    public function drawItemContent();
    
    /**
     * ����� ��������� �������� �������.
     */
    public function drawItemEnd();
    
    /**
     * ����� ��������, ���� ������� ������� �� �������� ���������.
     */
    public function drawItemEmpty();
    
    /**
     * ����� ��������, ���� ������� �� �������� ���������.
     */
    public function drawEmpty();
    
    /**
     * ����� ��������� �������.
     */
    public function drawEnd();
}
