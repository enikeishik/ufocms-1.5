<?php
/**
 *
 */
abstract class UfoCacheFs extends UfoCache
{
    /**
     * ��� ���������� ������ (URL ��������, ������������� ����� � �.�.).
     *
     * string
     */
    private $hash = '';
    
    /**
     * ����, � ������� �������� ��� ��� �������� ����.
     *
     * string
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
        $this->hash = $hash;
        $this->file = $settings['CachePath'] . DIRECTORY_SEPARATOR . 
                      $this->hash . '.' . 
                      $settings['CacheFileExt'];
    }
    
    /**
     *
     */
    public function load_()
    {
        if (!is_readable($this->file)) {
            return false;
        }
        return file_get_contents($this->file);
    }
    
    /**
     *
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
     *
     */
    protected function exists()
    {
        return file_exists($this->file);
    }
    
    /**
     *
     */
    protected function expired()
    {
        if (!exists()) {
            return true;
        }
        clearstatcache();
        return C_CACHE_LIFETIME < (time() - filectime($this->file));
    }
}
