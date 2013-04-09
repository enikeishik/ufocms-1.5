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
    
    public function isResultsExists($query)
    {
        return parent::isResultsExists($query);
    }
    
    public function isResultsExpired($query)
    {
        return parent::isResultsExpired($query);
    }
    
    public function resultsDelete($query)
    {
        return parent::resultsDelete($query);
    }
    
    public function resultsClearOld($query)
    {
        return parent::resultsClearOld($query);
    }
    
    public function resultsClearDoubles()
    {
        return parent::resultsClearDoubles();
    }
    
    public function getLongWords(array $words)
    {
        return parent::getLongWords($words);
    }
}
