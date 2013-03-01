<?php
require_once 'classes/abstract/UfoInsertionItemTemplate.php';
/**
 * Класс шаблона вставок модуля документов.
 * 
 * @author enikeishik
 *
 */
class UfoTplDocumentsIns extends UfoInsertionItemTemplate
{
    /**
     * Вывод начала элемента вставки.
     * @param mixed $item              идентификатор или параметры элемента блока вставки
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemBegin($item, array $options = null)
    {
        echo 'Insertion drawItemBegin ' . print_r($item, true) . '<br />' . "\r\n";
    }
    
    /**
     * Вывод содержимого элемента вставки.
     * Этот метод может вызываться множество раз в цикле для вывода данных элементов списков (например, новости ленты новостей).
     * @param mixed $item              идентификатор или параметры элемента блока вставки
     * @param array $data              данные (элемента) элемента блока вставки
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemContent($item, array $data, array $options = null)
    {
        echo 'Insertion drawItemContent; item: ' . 
             print_r($item, true) . '; data: ' . 
             print_r($data, true) . '<br />' . "\r\n";
    }
    
    /**
     * Вывод окончания элемента вставки.
     * @param mixed $item              идентификатор или параметры элемента блока вставки
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEnd($item, array $options = null)
    {
        echo 'Insertion drawItemEnd ' . print_r($item, true) . '<br />' . "\r\n";
    }
    
    /**
     * Вывод заглушки, если элемент вставки не содержит инфрмации.
     * @param mixed $item              идентификатор или данные элемента блока вставки
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEmpty($item, array $options = null)
    {
        echo 'Insertion drawItemEmpty<br />' . "\r\n";
    }
}
