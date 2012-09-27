<?php

class UfoCalendarCest
{
    public $class = 'UfoCalendar';
    
    private $storagePath = 'c:\tmp\calendar.xml';
    
    /**
     * ѕровер€ем выполнение метода возвращающего массив элементов.
     * —мотрим чтобы возвращаемое значение было не false.
     */
    public function getAllEvents(\CodeGuy $I) {
        $I->wantTo('execute method `getAllEvents`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getAllEvents');
        $I->seeMethodNotReturns($cal, 'getAllEvents', false);
    }
    
    /**
     * ѕровер€ем выполнение метода возвращающего массив элементов, 
     * соответствующих заданной дате.
     * —мотрим чтобы возвращаемое значение было не false 
     * и не пустой массив.
     */
    public function getDayEvents(\CodeGuy $I) {
        $I->wantTo('execute method `getDayEvents`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getDayEvents', '1', '1');
        $I->seeMethodNotReturns($cal, 'getDayEvents', false, array('1', '1'));
        $I->seeMethodNotReturns($cal, 'getDayEvents', array(), array('1', '1'));
    }
    
    /**
     * ѕровер€ем выполнение метода возвращающего элемент 
     * по заданному идентификатору.
     * —мотрим чтобы возвращаемое значение было не false.
     *
     */
    public function getEvent(\CodeGuy $I) {
        $I->wantTo('execute method `getEvent`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getEvent', '0000-01-01-0000');
        $I->seeMethodNotReturns($cal, 'getEvent', false, array('0000-01-01-0000'));
    }
    
    /**
     * ѕровер€ем выполнение метода возвращающего массив элементов, 
     * соответствующих заданной дате.
     * ѕередаем дату дл€ которой нет данных.
     * —мотрим чтобы возвращаемое значение было пустым массивом.
     */
    public function getNotExistsDayEvents(\CodeGuy $I) {
        $I->wantTo('execute method `getDayEvents`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getDayEvents', '30', '2');
        $I->seeMethodReturns($cal, 'getDayEvents', array(), array('30', '2'));
    }
    
    /**
     * ѕровер€ем выполнение метода возвращающего элемент 
     * по заданному идентификатору.
     * ѕередаем идентификатор дл€ которого нет данных.
     * —мотрим чтобы возвращаемое значение было false.
     *
     */
    public function getNotExistsEvent(\CodeGuy $I) {
        $I->wantTo('execute method `getEvent`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getEvent', '0000-02-30-0000');
        $I->seeMethodReturns($cal, 'getEvent', false, array('0000-02-30-0000'));
    }
}
