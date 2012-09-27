<?php
require_once 'cache.php';

/**
 * ����� ����������� ������ � ���������� � ���� ������ ��������� ������.
 */
class UfoCacheFs extends UfoCache
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
     * ����, � ������� �������� ��� ��� �������� ����.
     *
     * @var string
     */
    private $file = '';
    
    /**
     * ����������� ������.
     *
     * @param string $hash        ��� ����
     * @param array  $settings    ��������� �����������
     */
    public function __construct($hash, $settings)
    {
        if (preg_match('/[^A-Za-z0-9~_,\.\/\-]|(\.{2})/', $hash)) {
            $this->hash = md5($hash);
        } else {
            $this->hash = str_replace('/', ',', $hash);
        }
        $this->lifetime = $settings['Lifetime'];
        $this->file = $settings['Path'] . DIRECTORY_SEPARATOR . 
                      $this->hash . '.' . 
                      $settings['FileExt'];
    }
    
    /**
     * ��������� ����.
     *
     * @return string
     */
    public function load()
    {
        if (!is_readable($this->file)) {
            return false;
        }
        return file_get_contents($this->file);
    }
    
    /**
     * ���������� ������ � ���
     *
     * @param string $data  ������
     * @return boolean
     */
    public function save($data)
    {
        if (!$handle = fopen($this->file, 'w')) {
            return false;
        }
        fwrite($handle, $data);
        fclose($handle);
        return true;
    }
    
    /**
     * �������� ������������� ����.
     *
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->file);
    }
    
    /**
     * �������� �� ������� �� ���.
     *
     * @return boolean
     */
    public function expired()
    {
        if (!$this->exists()) {
            return true;
        }
        clearstatcache();
        return $this->lifetime < (time() - filectime($this->file));
    }
}
