<?php
/**
 * ������������ ����� ����������� ������.
 */
abstract class UfoCache
{
    /**
     * ��� ���������� ������ (URL ��������, ������������� ����� � �.�.).
     *
     * string
     */
    private $hash = '';
    
    /**
     * ����������� ������.
     *
     * @param string $hash  ��� ����
     * @param array $settings  ��������� �����������
     */
    abstract public function __construct($hash, $settings);
    
    /**
     * ��������� ����.
     *
     * @return string
     */
    abstract public function load();
    
    /**
     * ���������� ������ � ���
     *
     * @param string $data  ������
     * @return boolean
     */
    abstract public function save($data);
    
    /**
     * �������� ������������� ����.
     *
     * @return boolean
     */
    abstract protected function exists();
    
    /**
     * �������� �� ������� �� ���.
     *
     * @return boolean
     */
    abstract protected function expired();
}
