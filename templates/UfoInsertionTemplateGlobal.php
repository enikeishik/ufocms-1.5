<?php
require_once 'classes/abstract/UfoInsertionTemplate.php';
/**
 * Класс содержит базовые методы оформления вывода вставок.
 * Все классы шаблонов вставок модулей могут наследовать этот класс.
 * 
 * @author enikeishik
 *
 */
abstract class UfoInsertionTemplateGlobal extends UfoInsertionTemplate
{
    /**
     * Вывод начала вставки.
     */
    public function drawBegin()
    {
    
    }
    
    /**
     * Вывод заглушки, если вставка не содержит элементов.
     */
    public function drawEmpty()
    {
    
    }
    
    /**
     * Вывод окончания вставки.
     */
    public function drawEnd()
    {
    
    }
}
