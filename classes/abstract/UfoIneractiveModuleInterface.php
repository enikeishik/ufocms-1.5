<?php
require_once 'UfoModuleInterface.php';
/**
 * Интерфейс интерактивного модуля, обслуживающего раздел.
 * Все классы модулей должны реализовывать этот интерфейс или его дочерние интерфейсы.
 *
 * @author enikeishik
 *
 */
interface UfoIneractiveModuleInterface extends UfoModuleInterface
{
    /**
     * Добавление элемента в раздел посетителем сайта.
     */
    public function addItem();
    
    /**
     * Изменение элемента раздела.
     */
    public function updateItem();
    
    /**
     * Удаление элемента из раздела.
     */
    public function deleteItem();
}
