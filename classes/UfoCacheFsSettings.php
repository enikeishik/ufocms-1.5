<?php
require_once 'abstract/UfoCacheSettings.php';
/**
 * Класс установок кэширования данных с хранилищем в виде набора текстовых файлов.
 * 
 * @author enikeishik
 *
 */
class UfoCacheFsSettings extends UfoCacheSettings
{
    /**
     * Путь к папке, в которой хранятся текстовый файлы кэша.
     * @var string
     */
    private $dir = '';
    
    /**
     * Расширение файлов.
     * @var string
     */
    private $fileExt = '';
    
    /**
     * Время хранения файлов перед их удалением.
     * Файлы не удаляются по устареванию кэша, 
     * поскольку могут использоваться для формирования страниц 
     * в случае проблем с базой данных.
     * @var int
     */
    private $savetime = 0;
    
    /**
     * Конструктор.
     *
     * @param int    $lifetime    время жизни кэша
     * @param string $dir         пака хранения файлов
     * @param string $fileExt     расширение файлов
     * @param string $savetime    время хранения файлов
     */
    public function __construct($lifetime, $dir, $fileExt, $savetime)
    {
        $this->lifetime = $lifetime;
        $this->dir = $dir;
        $this->fileExt = $fileExt;
        $this->savetime = $savetime;
    }
    
    /**
     * Получение времени жизни кэша.
     * @return int
     */
    public function getLifetime() { return $this->lifetime; }
    
    /**
     * Получение папки хранения файлов кэша.
     * @return string
     */
    public function getDir() { return $this->dir; }
    
    /**
     * Получение расширения файлов кэша.
     * @return string
     */
    public function getFileExt() { return $this->fileExt; }
    
    /**
     * Получение времени хранения файлов кэша.
     * @return int
     */
    public function getSavetime() { return $this->savetime; }
}
