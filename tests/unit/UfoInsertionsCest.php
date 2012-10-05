<?php

class UfoInsertionsCest
{
    public $class = 'UfoInsertions';
    private $dbSettings = array('Host'     => 'sql09.freemysql.net', 
                                'Username' => 'lifehacker', 
                                'Password' => 'ahbvecrekm', 
                                'Database' => 'lifehacker');
    private $corePath = '';
    
    function __construct()
    {
        $this->corePath = __DIR__ . DIRECTORY_SEPARATOR . 
                          '..' . DIRECTORY_SEPARATOR . 
                          '..' . DIRECTORY_SEPARATOR  . 
                          '_core';
    }
    
    /**
     * “ест загрузки существующего класса.
     */
    public function showItemTest(\CodeGuy $I) {
        $I->wantTo('test method `showItem`');
        $I->testMethod('UfoInsertions::showItem');
        
        $I->execute(function() {
            UfoInsertions::showItem(array('Title' => 'InsertionTitle', 
                                          'Path' => 'InsertionPath'), 
                                    array());
            echo "\r\n";
            return class_exists('UfoInsertionNews') 
                   && class_exists('UfoInsertionTemplateNews');
        });
        $I->seeResultEquals(true);
    }
}
