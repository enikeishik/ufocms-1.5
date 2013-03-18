<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 *  ласс-структура дл€ хранени€ данных пользовател€.
 * 
 * @author enikeishik
 *
 */
class UfoUserStruct extends UfoStruct
{
    public $Id = 0;
    public $DateCreate = '0000-00-00 00:00:00';
    public $IsDisabled = false;
    public $IsHidden = false;
    public $Ticket = '';
    public $ExtUID = '';
    public $Login = '';
    public $Password = '';
    public $Title = '';
    public $Image = '';
    public $Email = '';
    public $Description = '';
}
