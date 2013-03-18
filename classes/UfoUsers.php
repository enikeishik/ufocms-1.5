<?php
require_once 'UfoUserStruct.php';
require_once 'UfoUsersSettings.php';
/**
 * Класс зарегистрированных пользователей сайта.
 * 
 * @author enikeishik
 *
 */
class UfoUsers
{
    use UfoTools;
    
    /**
     * Имя cookie хранящего тикет текущего пользователя. 
     * @var string
     */
    const C_COOKIE_TICKET_NAME = 'ufo_users_ticket';
    
    /**
     * Время жизни cookie хранящего тикет текущего пользователя.
     * @var int
     */
    const C_COOKIE_TICKET_LIFETIME = 2592000; //3600 * 24 * 30
    
    /**
     * Базовый путь раздела пользователей.
     * @var string
     */
    const C_BASE_PATH = '/users';
    
    //имена полей форм (входа, выхода, регистрации, восстановления пароля)
    const C_FORM_FIELDNAME_LOGIN = 'login';
    const C_FORM_FIELDNAME_PASSWORD = 'password';
    const C_FORM_FIELDNAME_FROM = 'from';
    const C_FORM_FIELDNAME_EMAIL = 'email';
    const C_FORM_FIELDNAME_TITLE = 'title';
    
    //метки в тексте и теме писем (восстановления пароля)
    const C_MARK_SITE = '{SITE}';
    const C_MARK_DT = '{DT}';
    const C_MARK_IP = '{IP}';
    const C_MARK_LOGIN = '{LOGIN}';
    const C_MARK_PASSWORD = '{PASSWORD}';
    const C_MARK_TITLE = '{TITLE}';
    const C_MARK_EMAIL = '{EMAIL}';
    
    /**
     * Объект-структура для хранения установок раздела пользователей.
     * @var UfoUsersSettings
     */
    protected $settings = null;
    
    /**
     * Объект-структура для хранения данных пользователя.
     * @var UfoUserStruct
     */
    protected $item = null;
}
