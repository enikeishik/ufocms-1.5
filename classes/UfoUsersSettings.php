<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 * Класс-структура для хранения установок раздела пользователей.
 * 
 * @author enikeishik
 *
 */
class UfoUsersSettings extends UfoStruct
{
    public $Id = 0;
    public $BodyHead = '';
    public $BodyFoot = '';
    public $PageLength = 0;
    public $Orderby = 0;
    public $IsModerated = false;
    public $IsGlobalAE = false;
    public $IsGlobalAEF = false;
    public $AdminEmail = '';
    public $AdminEmailFrom = '';
    public $RecoverySubject = '';
    public $RecoveryMessage = '';
}
