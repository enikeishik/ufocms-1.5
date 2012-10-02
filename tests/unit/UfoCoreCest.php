<?php

class UfoCoreCest
{
    public $class = 'UfoCore';
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
    public function loadExistingClass(\CodeGuy $I) {
        $I->wantTo('test method `loadClass`');
        $I->testMethod('UfoCore::loadClass');
        
        $I->execute(function() {
            $class = $this->corePath . DIRECTORY_SEPARATOR . 'dummy';
            UfoCore::loadClass($class);
            return class_exists('UfoDummy');
        });
        $I->seeResultEquals(true);
    }
    
    /**
     * “ест загрузки несуществующего класса.
     */
    public function loadNoexistingClass(\CodeGuy $I) {
        $I->wantTo('test method `loadClass`');
        $I->testMethod('UfoCore::loadClass');
        
        $I->execute(function() {
            $class = $this->corePath . DIRECTORY_SEPARATOR . 'baddummy';
            @UfoCore::loadClass($class);
            return class_exists('UfoBaddummy');
        });
        $I->seeResultEquals(false);
    }
}
