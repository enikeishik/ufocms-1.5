<?php
/**
 * Интерфейс шаблона вставки модуля.
 * Все классы шаблонов вставок модулей должны реализовывать этот интерфейс
 * или его дочерние интерфейсы.
 *
 * @author enikeishik
 *
 */
interface UfoInsertionTemplateInterface
{
    /**
     * Вывод начала вставки.
     */
    public function drawBegin();
    
    /**
     * Вывод начала элемента вставки.
     */
    public function drawItemBegin();
    
    /**
     * Вывод содержимого элемента вставки.
     */
    public function drawItemContent();
    
    /**
     * Вывод окончания элемента вставки.
     */
    public function drawItemEnd();
    
    /**
     * Вывод заглушки, если элемент вставки не содержит инфрмации.
     */
    public function drawItemEmpty();
    
    /**
     * Вывод заглушки, если вставка не содержит элементов.
     */
    public function drawEmpty();
    
    /**
     * Вывод окончания вставки.
     */
    public function drawEnd();
}
