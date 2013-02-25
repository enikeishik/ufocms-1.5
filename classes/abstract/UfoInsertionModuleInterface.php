<?php
/**
 * Интерфейс вставки модуля.
 * Все классы вставок модулей должны реализовывать этот интерфейс 
 * или его дочерние интерфейсы.
 * 
 * @author enikeishik
 *
 */
interface UfoInsertionModuleInterface
{
    /**
     * Генерация содержимого блока вставки.
     * Блок может содержать множество конкретных вставок, 
     * определенных для данной страницы (targetId) и данного места (placeId).
     * @param UfoInsertionStruct $insertion    параметры вставки
     * @param int $offset = 0                  выводить элементы начиная с $offset
     * @param int $limit = 0                   выводить всего $limit элементов (если $limit > 0)
     * @return string
     */
    public function generate(UfoInsertionStruct $insertion, $offset = 0, $limit = 0, array $options = null);
    
    /**
     * Генерация содержимого элемента блока вставки.
     * @param mixed $item              идентификатор или данные элемента
     * @param mixed $options = null    дополнительные данные, передаваемые сквозь цепочку вызовов
     * @return string
     */
    public function generateItem($item, array $options = null);
}
