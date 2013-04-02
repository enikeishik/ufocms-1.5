<?php
require_once 'abstract/UfoCache.php';

/**
 * ����� ����������� ������ � ���������� � ���� ������ ��������� ������.
 * 
 * @author enikeishik
 *
 */
class UfoCacheFs extends UfoCache
{

    /**
     * ����, � ������� �������� ��� ��� �������� ����.
     *
     * @var string
     */
    protected $file = '';
    
    /**
     * ����������� ������.
     *
     * @param string $hash                    ��� ����
     * @param string $path                    ����� ����
     * @param UfoCacheFsSettings $settings    ��������� �����������
     */
    public function __construct($hash, UfoCacheFsSettings $settings)
    {
        if ('' == $hash) {
            $this->hash = 'empty,' . time();
        } else if (preg_match('/[^A-Za-z0-9~_,\.\/\-]|(\.{2})/', $hash)) {
            $this->hash = md5($hash);
        } else {
            $this->hash = str_replace('/', ',', $hash);
        }
        $this->lifetime = $settings->getLifetime();
        if ('' != $ext = $settings->getFileExt()) {
            $this->file = $settings->getDir() . DIRECTORY_SEPARATOR . 
                          $this->hash . '.' . 
                          $settings->getFileExt();
        } else {
            $this->file = $settings->getDir() . DIRECTORY_SEPARATOR .
                          $this->hash;
        }
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
    
    /**
     * �������� ������ ����, ���� �������� ������� �����.
     * ����� ������ �����������, ��������� ���������� ����� ���������� ������ ������� (register_shutdown_function).
     * 
     * @param UfoConfig $config    ������ ������������
     *
     * @todo ��������� ������������� ������ ����������� ���������� � ����� �������� ����������� ������� clearstatcache
     * @todo ������� ����� writeLog ����������� � ������������� ���
     */
    public static function deleteOld(UfoConfig $config)
    {
        $settings =& $config->cacheFsSettings;
        clearstatcache();
        $dir = $settings->getDir();
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                $filePath = $dir . '/' . $file;
                if (is_file($filePath) && 0 !== strpos($file, '.')) {
                    $fileTime = time() - filectime($filePath);
                    if ($settings->getLifetime() < $fileTime && $settings->getSavetime() < $fileTime) {
                        if (!@unlink($filePath)) {
                            //$this->writeLog(, $config->logError);
                            //api_WriteLog(C_LOGPATH_ERRORS, 'Can not unlink file ' . $file_path);
                        }
                    }
                }
            }
            closedir($dh);
        } else {
            //api_WriteLog(C_LOGPATH_ERRORS, 'Can not open dir ' . $dir);
        }
    }
}
