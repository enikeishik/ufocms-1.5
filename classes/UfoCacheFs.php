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
    use UfoTools;
    
    /**
     * ��������� �����������.
     * @var UfoCacheFsSettings
     */
    protected $settings = null;
    
    /**
     * ������ ������ ��������� �������� ������.
     * @var UfoErrors
     */
    private $errors = null;
    
    /**
     * ����, � ������� �������� ��� ��� �������� ����.
     * @var string
     */
    protected $file = '';
    
    /**
     * ����������� ������.
     * @param string $hash                    ��� ����
     * @param UfoCacheFsSettings $settings    ��������� �����������
     * @param UfoErrors &$errors              ������ �� ������ � �������� ������
     */
    public function __construct($hash, UfoCacheFsSettings $settings, UfoErrors &$errors)
    {
        if ('' == $hash) {
            $this->hash = 'empty,' . time();
        } else if (preg_match('/[^A-Za-z0-9~_,\.\/\-]|(\.{2})/', $hash)) {
            $this->hash = md5($hash);
        } else {
            $this->hash = str_replace('/', ',', $hash);
        }
        $this->settings = $settings;
        $this->lifetime = $this->settings->getLifetime();
        if ('' != $ext = $settings->getFileExt()) {
            $this->file = $this->settings->getDir() . DIRECTORY_SEPARATOR . 
                          $this->hash . '.' . 
                          $this->settings->getFileExt();
        } else {
            $this->file = $this->settings->getDir() . DIRECTORY_SEPARATOR .
                          $this->hash;
        }
        $this->errors =& $errors;
    }
    
    /**
     * ��������� ����.
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
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->file);
    }
    
    /**
     * �������� �� ������� �� ���.
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
     */
    public function deleteOld()
    {
        //���� ���� ��������� ����� �� �������� ������ ��� �� �������
        //��� ������� ��� ����������� ������� ����� ������/�������
        //������� �� ������ ���������� ������ ������������� ���������� �������
        //clearstatcache();
        $dir = $this->settings->getDir();
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                $filePath = $dir . '/' . $file;
                if (is_file($filePath) && 0 !== strpos($file, '.')) {
                    $fileTime = time() - filectime($filePath);
                    if ($this->settings->getLifetime() < $fileTime 
                        && $this->settings->getSavetime() < $fileTime) {
                        if (!@unlink($filePath)) {
                            $this->writeLog(sprintf($this->errors->fsUnlink, $filePath), $config->logError);
                        }
                    }
                }
            }
            closedir($dh);
        } else {
            $this->writeLog(sprintf($this->errors->fsOpenDir, $dir), $config->logError);
        }
    }
}
