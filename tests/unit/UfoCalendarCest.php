<?php

class UfoCalendarCest
{
    public $class = 'UfoCalendar';
    
    private $storagePath = 'c:\tmp\calendar.xml';
    
    /**
     * ��������� ���������� ������ ������������� ������ ���������.
     * ������� ����� ������������ �������� ���� �� false.
     */
    public function getAllEvents(\CodeGuy $I) {
        $I->wantTo('execute method `getAllEvents`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getAllEvents');
        $I->seeMethodNotReturns($cal, 'getAllEvents', false);
    }
    
    /**
     * ��������� ���������� ������ ������������� ������ ���������, 
     * ��������������� �������� ����.
     * ������� ����� ������������ �������� ���� �� false 
     * � �� ������ ������.
     */
    public function getDayEvents(\CodeGuy $I) {
        $I->wantTo('execute method `getDayEvents`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getDayEvents', '1', '1');
        $I->seeMethodNotReturns($cal, 'getDayEvents', false, array('1', '1'));
        $I->seeMethodNotReturns($cal, 'getDayEvents', array(), array('1', '1'));
    }
    
    /**
     * ��������� ���������� ������ ������������� ������� 
     * �� ��������� ��������������.
     * ������� ����� ������������ �������� ���� �� false.
     *
     */
    public function getEvent(\CodeGuy $I) {
        $I->wantTo('execute method `getEvent`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getEvent', '0000-01-01-0000');
        $I->seeMethodNotReturns($cal, 'getEvent', false, array('0000-01-01-0000'));
    }
    
    /**
     * ��������� ���������� ������ ������������� ������ ���������, 
     * ��������������� �������� ����.
     * �������� ���� ��� ������� ��� ������.
     * ������� ����� ������������ �������� ���� ������ ��������.
     */
    public function getNotExistsDayEvents(\CodeGuy $I) {
        $I->wantTo('execute method `getDayEvents`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getDayEvents', '30', '2');
        $I->seeMethodReturns($cal, 'getDayEvents', array(), array('30', '2'));
    }
    
    /**
     * ��������� ���������� ������ ������������� ������� 
     * �� ��������� ��������������.
     * �������� ������������� ��� �������� ��� ������.
     * ������� ����� ������������ �������� ���� false.
     *
     */
    public function getNotExistsEvent(\CodeGuy $I) {
        $I->wantTo('execute method `getEvent`');
        $cal = new UfoCalendar($this->storagePath);
        $I->executeMethod($cal, 'getEvent', '0000-02-30-0000');
        $I->seeMethodReturns($cal, 'getEvent', false, array('0000-02-30-0000'));
    }
}
