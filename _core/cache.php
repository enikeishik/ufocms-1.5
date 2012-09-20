<?php
/**
 * Абрстрактный класс кэширования данных.
 */
abstract class UfoCache
{
    /**
     * Хеш кэшируемых данных (URL страницы, идентификатор блока и т.п.).
     *
     * string
     */
    private $hash = '';
    
    /**
     * Конструктор класса.
     *
     * @param string $hash  хэш кэша
     * @param array $settings  параметры кэширования
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
    abstract protected function exists();
    
    /**
     * Проверка не устарел ли кэш.
     *
     * @return boolean
     */
    abstract protected function expired();
}
