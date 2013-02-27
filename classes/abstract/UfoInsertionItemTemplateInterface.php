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
     * @param mixed $item              идентификатор или параметры элемента блока вставки
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemBegin($item, array $options = null);
    
    /**
     * Вывод содержимого элемента вставки.
     * Этот метод может вызываться множество раз в цикле для вывода данных элементов списков (например, новости ленты новостей).
     * @param mixed $item              идентификатор или параметры элемента блока вставки
     * @param array $data              данные (элемента) элемента блока вставки
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemContent($item, array $data, array $options = null);
    
    /**
     * Вывод окончания элемента вставки.
     * @param mixed $item              идентификатор или параметры элемента блока вставки
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEnd($item, array $options = null);
    
    /**
     * Вывод заглушки, если элемент вставки не содержит инфрмации.
     * @param mixed $item              идентификатор или параметры элемента блока вставки
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawItemEmpty($item, array $options = null);
}
