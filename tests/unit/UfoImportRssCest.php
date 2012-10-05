<?php

class UfoImportRssCest
{
    public $class = 'UfoImportRss';
    private $cacheSettings = array('Path'     => 'c:\tmp', 
                                   'FileExt'  => 'txt', 
                                   'Lifetime' => 10);
    
    /**
     * ѕровер€ем выполнение метода возвращающего массив элементов.
     * —мотрим чтобы возвращаемое значение было не false.
     */
    public function getItemsTest(\CodeGuy $I) {
        $I->wantTo('execute method `getItems`');
        $cache = new UfoCacheFs('rss-elementy', $this->cacheSettings);
        $rss = new UfoImportRss($cache, 'http://elementy.ru/rss/news');
        $I->executeMethod($rss, 'getItems');
        $I->seeMethodNotReturns($rss, 'getItems', false);
    }
}
