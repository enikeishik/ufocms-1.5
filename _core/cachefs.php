<?php
require_once 'cache.php';

/**
 * Класс кэширования данных с хранилищем в виде набора текстовых файлов.
 */
class UfoCacheFs extends UfoCache
{
    /**
     * Хеш кэшируемых данных (URL страницы, идентификатор блока и т.п.).
     *
     * @var string
     */
    private $hash = '';
    
    /**
     * Время жизни кэшируемых данных, сек., 0 - вечно.
     *
     * @var int
     */
    private $lifetime = 0;

    /**
     * Файл, в котором хранится кэш для текущего хеша.
     *
     * @var string
     */
    private $file = '';
    
    /**
     * Конструктор класса.
     *
     * @param string $hash        хэш кэша
     * @param array  $settings    параметры кэширования
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
}
