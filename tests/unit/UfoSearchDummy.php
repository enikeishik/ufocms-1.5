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
    
    public function resultsAdd($results)
    {
        return parent::resultsAdd($results);
    }
    
    public function searchWordExec($sql, $relevance, $query)
    {
        return parent::searchWordExec($sql, $relevance, $query);
    }
    
    public function searchWords(array $words, $relevanceFactor, $query, $ignoreMinwordlen = false)
    {
        return parent::searchWords($words, $relevanceFactor, $query, $ignoreMinwordlen);
    }
    
    public function searchWord($word, $relevanceFactor, $query)
    {
        return parent::searchWord($word, $relevanceFactor, $query);
    }
    
    public function rawSearchStemmed($query, array $words)
    {
        return parent::rawSearchStemmed($query, $words);
    }
    
    public function rawSearch($query)
    {
        return parent::rawSearch($query);
    }
    
    public function logSearchQuery($query)
    {
        return parent::logSearchQuery($query);
    }
}
