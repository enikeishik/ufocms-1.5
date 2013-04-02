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

    /**
     * Файл, в котором хранится кэш для текущего хеша.
     *
     * @var string
     */
    protected $file = '';
    
    /**
     * Конструктор класса.
     *
     * @param string $hash                    хэш кэша
     * @param string $path                    папка кэша
     * @param UfoCacheFsSettings $settings    установки кэширования
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
     * Получение кэша.
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
     * Сохранение данных в кэш
     *
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
     *
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->file);
    }
    
    /**
     * Проверка не устарел ли кэш.
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
     * Удаление файлов кэша, срок хранения которых истек.
     * Метод сделан статическим, поскольку вызывается после завершения работы скрипта (register_shutdown_function).
     * 
     * @param UfoConfig $config    объект конфигурации
     *
     * @todo проверить необходимость отмены кэширования информации о файле системой посредством функции clearstatcache
     * @todo сделать метод writeLog статическим и зайдествовать его
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
