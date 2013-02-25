<?php
/**
 * Абрстрактный класс кэширования данных.
 * 
 * @author enikeishik
 *
 */
abstract class UfoCache
{
    /**
     * Хеш кэшируемых данных (URL страницы, идентификатор блока и т.п.).
     *
     * @var string
     */
    protected $hash = '';
    
    /**
     * Время жизни кэшируемых данных, сек., 0 - вечно.
     *
     * @var int
     */
    protected $lifetime = 0;
    
    /**
     * Конструктор класса.
     *
     * @param string $hash        хэш кэша
     * @param int    $lifetime    время жизни кэша
     */
    //abstract public function __construct($hash, UfoCacheSettings $settings);
    //наследуемые классы в качестве установок $settings 
    //принимают объекты классов наследников от UfoCacheSettings, 
    //но PHP не считает это допустимым, 
    //поэтому закомментируем пока этот метод здесь.
    
    /**
     * Получение кэша.
     *
     * @return string
     */
    abstract public function load();
    
    /**
     * Сохранение данных в кэш
     *
     * @param string $data  данные
     * @return boolean
     */
    abstract public function save($data);
    
    /**
     * Проверка существования кэша.
     *
     * @return boolean
     */
    abstract public function exists();
    
    /**
     * Проверка не устарел ли кэш.
     *
     * @return boolean
     */
    abstract public function expired();
}
