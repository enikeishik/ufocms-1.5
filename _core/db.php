<?php
/**
 * ����� ������������ ��������� � ���� ������.
 *
 * � ��������� ����� ������������ ����� ������� ������� ��� MySQLi
 */
class UfoDb extends mysqli
{
    public function __construct($host, $username, $password, $database)
    {
        parent::__construct($host, $username, $password, $database);
    }
}
