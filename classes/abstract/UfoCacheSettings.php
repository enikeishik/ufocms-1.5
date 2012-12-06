<?php
/**
 * Абрстрактный класс установок кэширования данных.
 */
abstract class UfoCacheSettings
{
    private $lifetime = 0;
    
    public function __construct($lifetime)
    {
        $this->lifetime = $lifetime;
    }
    
    public function getLifetime() { return $this->lifetime; }
}
