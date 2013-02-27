<?php
/**
 * Интерфейс шаблона вставки.
 *
 * @author enikeishik
 *
 */
interface UfoInsertionTemplateInterface
{
    /**
     * Вывод начала вставки.
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawBegin(array $options = null);
    
    /**
     * Вывод заглушки, если вставка не содержит элементов.
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawEmpty(array $options = null);
    
    /**
     * Вывод окончания вставки.
     * @param array $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     */
    public function drawEnd(array $options = null);
}
