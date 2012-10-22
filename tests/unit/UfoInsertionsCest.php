<?php

class UfoInsertionsCest
{
    public $class = 'UfoInsertions';
    
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
