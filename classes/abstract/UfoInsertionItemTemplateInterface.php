<?php
/**
 * Интерфейс шаблона вставки модуля.
 * Все классы шаблонов вставок модулей должны реализовывать этот интерфейс
 * или его дочерние интерфейсы.
 *
 * @author enikeishik
 *
 */
interface UfoInsertionItemTemplateInterface
{
    /**
     * Вывод начала элемента вставки.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemBegin(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null);
    
    /**
     * Вывод содержимого элемента вставки.
     * Этот метод может вызываться множество раз в цикле для вывода данных элементов списков (например, новости ленты новостей).
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $data                           данные (элемента) элемента блока вставки (строка выборки из БД)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemContent(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $data, array $options = null);
    
    /**
     * Вывод окончания элемента вставки.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEnd(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null);
    
    /**
     * Вывод заглушки, если элемент вставки не содержит инфрмации.
     * @param UfoInsertionItemStruct $insertion     параметры элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные вставки (путь раздела-источника, установки модуля и т.п.)
     * @param array $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEmpty(UfoInsertionItemStruct $insertion, UfoInsertionItemSettings $settings, array $options = null);
}
