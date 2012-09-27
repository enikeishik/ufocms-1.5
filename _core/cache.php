<?php
/**
 * Абрстрактный класс кэширования данных.
 */
abstract class UfoCache
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
     * Конструктор класса.
     *
     * @param string $hash        хэш кэша
     * @param array  $settings    параметры кэширования
     */
    abstract public function __construct($hash, $settings);
    
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
