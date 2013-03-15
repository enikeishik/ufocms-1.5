<?php
/**
 * ������������ ����� ��������� ����������� ������.
 * 
 * @author enikeishik
 *
 */
abstract class UfoCacheSettings
{
    protected $lifetime = 0;
    
    public function __construct($lifetime)
    {
        $this->lifetime = $lifetime;
    }
    
    public function getLifetime() { return $this->lifetime; }
}
