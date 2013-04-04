<?php
require_once 'abstract/UfoCache.php';

/**
 * Класс кэширования данных с хранилищем в виде набора текстовых файлов.
 * 
 * @author enikeishik
 *
 */
class UfoCacheFs extends UfoCache
{
    use UfoTools;
    
    /**
     * Установки кэширования.
     * @var UfoCacheFsSettings
     */
    protected $settings = null;
    
    /**
     * Объект набора текстовых описаний ошибок.
     * @var UfoErrors
     */
    private $errors = null;
    
    /**
     * Файл, в котором хранится кэш для текущего хеша.
     * @var string
     */
    protected $file = '';
    
    /**
     * Конструктор класса.
     * @param string $hash                    хэш кэша
     * @param UfoCacheFsSettings $settings    установки кэширования
     * @param UfoErrors &$errors              ссылка на объект с текстами ошибок
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
     * Получение кэша.
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
     * Сохранение данных в кэш
     * @param string $data  данные
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
     * Проверка существования кэша.
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->file);
    }
    
    /**
     * Проверка не устарел ли кэш.
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
     * Удаление файлов кэша, срок хранения которых истек.
     */
    public function deleteOld()
    {
        //даже если некоторые файлы не удалятся сейчас это не страшно
        //они удаятся при последующих вызовах этого метода/скрипта
        //функция по тестам достаточно сильно затормаживает выполнение скрипта
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
