<?php
/**
 * ����� ������������ ��������� � ���� ������.
 *
 * � ��������� ����� ������������ ����� ������� ������� ��� MySQLi
 */
class UfoDbSimple extends mysqli
{
    public function __construct($host, $username, $password, $database)
    {
        parent::__construct($host, $username, $password, $database);
    }
}
