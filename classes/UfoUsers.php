<?php
require_once 'UfoUserStruct.php';
require_once 'UfoUsersSettings.php';
/**
 * ����� ������������������ ������������� �����.
 * 
 * @author enikeishik
 *
 */
class UfoUsers
{
    use UfoTools;
    
    /**
     * ��� cookie ��������� ����� �������� ������������. 
     * @var string
     */
    const C_COOKIE_TICKET_NAME = 'ufo_users_ticket';
    
    /**
     * ����� ����� cookie ��������� ����� �������� ������������.
     * @var int
     */
    const C_COOKIE_TICKET_LIFETIME = 2592000; //3600 * 24 * 30
    
    /**
     * ������� ���� ������� �������������.
     * @var string
     */
    const C_BASE_PATH = '/users';
    
    //����� ����� ���� (�����, ������, �����������, �������������� ������)
    const C_FORM_FIELDNAME_LOGIN = 'login';
    const C_FORM_FIELDNAME_PASSWORD = 'password';
    const C_FORM_FIELDNAME_FROM = 'from';
    const C_FORM_FIELDNAME_EMAIL = 'email';
    const C_FORM_FIELDNAME_TITLE = 'title';
    
    //����� � ������ � ���� ����� (�������������� ������)
    const C_MARK_SITE = '{SITE}';
    const C_MARK_DT = '{DT}';
    const C_MARK_IP = '{IP}';
    const C_MARK_LOGIN = '{LOGIN}';
    const C_MARK_PASSWORD = '{PASSWORD}';
    const C_MARK_TITLE = '{TITLE}';
    const C_MARK_EMAIL = '{EMAIL}';
    
    /**
     * ������-��������� ��� �������� ��������� ������� �������������.
     * @var UfoUsersSettings
     */
    protected $settings = null;
    
    /**
     * ������-��������� ��� �������� ������ ������������.
     * @var UfoUserStruct
     */
    protected $item = null;
}
