<?php

class UfoImportCbrCest
{
    public $class = 'UfoImportCbr';
    private $cacheSettings = array('Path'     => 'c:\tmp', 
                                   'FileExt'  => 'txt', 
                                   'Lifetime' => 10);
    
    /**
     * ��������� ���������� ������ ������������� ������ ���������.
     * ������� ����� ������������ �������� ���� �� false.
     */
    public function getItemsTest(\CodeGuy $I) {
        $I->wantTo('execute method `getItems`');
        $cache = new UfoCacheFs('cbr', $this->cacheSettings);
        $cbr = new UfoImportCbr($cache);
        $I->executeMethod($cbr, 'getItems');
        $I->seeMethodNotReturns($cbr, 'getItems', false);
    }
}
