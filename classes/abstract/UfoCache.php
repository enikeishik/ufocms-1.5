<?php
/**
 * ������������ ����� ����������� ������.
 * 
 * @author enikeishik
 *
 */
abstract class UfoCache
{
    /**
     * ��� ���������� ������ (URL ��������, ������������� ����� � �.�.).
     *
     * @var string
     */
    protected $hash = '';
    
    /**
     * ����� ����� ���������� ������, ���., 0 - �����.
     *
     * @var int
     */
    protected $lifetime = 0;
    
    /**
     * ����������� ������.
     *
     * @param string $hash        ��� ����
     * @param int    $lifetime    ����� ����� ����
     */
    //abstract public function __construct($hash, UfoCacheSettings $settings);
    //����������� ������ � �������� ��������� $settings 
    //��������� ������� ������� ����������� �� UfoCacheSettings, 
    //�� PHP �� ������� ��� ����������, 
    //������� �������������� ���� ���� ����� �����.
    
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
