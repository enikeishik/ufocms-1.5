<?php
require_once 'classes/UfoSearch.php';
/**
 * ����� ��������� ����� UfoSearch � ������ ��� ���������� ������������ ������ ����������.
 */
class UfoSearchDummy extends UfoSearch
{
    public function getQueryWords($query)
    {
        return parent::getQueryWords($query);
    }
}
