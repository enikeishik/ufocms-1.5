<?php
/**
 *  ласс абстрактного обращени€ к базе данных.
 *
 * ¬ насто€щее врем€ представл€ет собой простую обертку дл€ MySQLi
 */
class UfoDbSimple extends mysqli
{
    public function __construct($host, $username, $password, $database)
    {
        parent::__construct($host, $username, $password, $database);
    }
}
