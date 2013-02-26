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
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawBegin(array $options = null)
    {
        echo 'Insertion drawBegin<br />' . "\r\n";
    }
    
    /**
     * Вывод заглушки, если вставка не содержит элементов.
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawEmpty(array $options = null)
    {
        echo 'Insertion drawEmpty<br />' . "\r\n";
    }
    
    /**
     * Вывод окончания вставки.
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawEnd(array $options = null)
    {
        echo 'Insertion drawEnd<br />' . "\r\n";
    }
}
