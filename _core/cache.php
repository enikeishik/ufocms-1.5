<?php
/**
 * ������������ ����� ����������� ������.
 */
abstract class UfoCache
{
    /**
     * ��� ���������� ������ (URL ��������, ������������� ����� � �.�.).
     *
     * @var string
     */
    private $hash = '';
    
    /**
     * ����� ����� ���������� ������, ���., 0 - �����.
     *
     * @var int
     */
    private $lifetime = 0;
    
    /**
     * ����������� ������.
     *
     * @param string $hash        ��� ����
     * @param array  $settings    ��������� �����������
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
    abstract public function exists();
    
    /**
     * �������� �� ������� �� ���.
     *
     * @return boolean
     */
    abstract public function expired();
}
