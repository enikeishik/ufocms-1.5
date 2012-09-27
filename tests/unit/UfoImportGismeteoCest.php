<?php

class UfoImportGismeteoCest
{
    public $class = 'UfoImportGismeteo';
    private $cacheSettings = array('Path'     => 'c:\tmp', 
                                   'FileExt'  => 'txt', 
                                   'Lifetime' => 10);
    
    /**
     * ��������� ���������� ������ ������������� ������ ���������.
     * ������� ����� ������������ �������� ���� �� false.
     */
    public function getItems(\CodeGuy $I) {
        $I->wantTo('execute method `getItems`');
        $cache = new UfoCacheFs('gismeteo', $this->cacheSettings);
        $gm = new UfoImportGismeteo($cache);
        $I->executeMethod($gm, 'getItems');
        $I->seeMethodNotReturns($gm, 'getItems', false);
    }
}
