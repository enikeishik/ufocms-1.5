<?php
require_once 'classes/UfoSearch.php';
/**
 * Класс наследует класс UfoSearch и делает все подлежащие тестированию методы публичными.
 */
class UfoSearchDummy extends UfoSearch
{
    public function getQueryWords($query)
    {
        return parent::getQueryWords($query);
    }
}
