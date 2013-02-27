<?php
/**
 * Интерфейс вставки модуля.
 * Все классы вставок модулей должны реализовывать этот интерфейс 
 * или его дочерние интерфейсы.
 * 
 * @author enikeishik
 *
 */
interface UfoInsertionItemModuleInterface
{
    /**
     * Генерация содержимого элемента блока вставки.
     * @param UfoInsertionItemStruct $insertion    данные элемента вставки
     * @param string $path                         путь раздела-источника вставки
     * @param mixed $options = null                дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, $path, array $options = null);
}
