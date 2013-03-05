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
     * @param UfoInsertionItemStruct $insertion     данные элемента вставки
     * @param UfoInsertionItemSettings $settings    дополнительные данные элемента вставки (путь раздела-источника, установки модуля и т.п.)
     * @param mixed $options = null                 дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     */
    public function generateItem(UfoInsertionItemStruct $insertion, 
                                 UfoInsertionItemSettings $settings, 
                                 array $options = null);
}
