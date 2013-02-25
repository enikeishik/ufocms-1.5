<?php
require_once 'abstract/UfoCacheSettings.php';
/**
 * ����� ��������� ����������� ������ � ���������� � ���� ������ ��������� ������.
 * 
 * @author enikeishik
 *
 */
class UfoCacheFsSettings extends UfoCacheSettings
{
    /**
     * ���� � �����, � ������� �������� ��������� ����� ����.
     * @var string
     */
    private $dir = '';
    
    /**
     * ���������� ������.
     * @var string
     */
    private $fileExt = '';
    
    /**
     * ����� �������� ������ ����� �� ���������.
     * ����� �� ��������� �� ����������� ����, 
     * ��������� ����� �������������� ��� ������������ ������� 
     * � ������ ������� � ����� ������.
     * @var int
     */
    private $savetime = 0;
    
    /**
     * �����������.
     *
     * @param int    $lifetime    ����� ����� ����
     * @param string $dir         ���� �������� ������
     * @param string $fileExt     ���������� ������
     * @param string $savetime    ����� �������� ������
     */
    public function __construct($lifetime, $dir, $fileExt, $savetime)
    {
        $this->lifetime = $lifetime;
        $this->dir = $dir;
        $this->fileExt = $fileExt;
        $this->savetime = $savetime;
    }
    
    /**
     * ��������� ������� ����� ����.
     * @return int
     */
    public function getLifetime() { return $this->lifetime; }
    
    /**
     * ��������� ����� �������� ������ ����.
     * @return string
     */
    public function getDir() { return $this->dir; }
    
    /**
     * ��������� ���������� ������ ����.
     * @return string
     */
    public function getFileExt() { return $this->fileExt; }
    
    /**
     * ��������� ������� �������� ������ ����.
     * @return int
     */
    public function getSavetime() { return $this->savetime; }
}
