<?php
require_once 'classes/abstract/UfoInsertionItemTemplate.php';
/**
 * Класс шаблона вставок модуля новостей.
 * 
 * @author enikeishik
 *
 */
class UfoTplNewsIns extends UfoInsertionItemTemplate
{
    /**
     * Вывод начала элемента вставки.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemBegin(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        echo 'Insertion drawItemBegin ' . print_r($insertion, true) . '<br />' . "\r\n";
    }
    
    /**
     * Вывод содержимого элемента вставки.
     * Этот метод может вызываться множество раз в цикле для вывода данных элементов списков (например, новости ленты новостей).
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $data                           данные (элемента) элемента блока вставки (строка выборки из БД)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemContent(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $data, array $options = null)
    {
        echo 'Insertion drawItemContent; insertion: ' . 
             print_r($insertion, true) . '; data: ' . 
             print_r($data, true) . '<br />' . "\r\n";
    }
    
    /**
     * Вывод окончания элемента вставки.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEnd(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        echo 'Insertion drawItemEnd ' . print_r($insertion, true) . '<br />' . "\r\n";
    }
    
    /**
     * Вывод заглушки, если элемент вставки не содержит инфрмации.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEmpty(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null)
    {
        echo 'Insertion drawItemEmpty<br />' . "\r\n";
    }
}
