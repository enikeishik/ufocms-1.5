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
     * Ссылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * Ссылка на объект конфигурации.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * Ссылка на объект ядра системы.
     * @var UfoCore
     */
    protected $core = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Ссылка на объект для работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * Ссылка на объект для работы с базой данных.
     * @var UfoCoreDbModel
     */
    protected $coreDbModel = null;
    
    /**
     * Ссылка на объект UfoSite, представляющий сайт.
     * @var UfoSite
     */
    protected $site = null;
    
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
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
     * @throws Exception
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        $this->setSettings();
        $this->setCurrentUser();
    }
    
    /**
     * Возвращает объект-структуру данных текущего пользователя.
     * @return UfoUserStruct|null
     */
    public function getCurrentUser()
    {
        return $this->item;
    }
    
    /**
     * Получение данных о принадлежности пользователя к группам.
     * @param int $userId    идентификатор пользователя
     * @return array
     */
    public function getUserGroups($userId)
    {
        $arr = array();
        if ($this->isInt($userId)) {
            if ($groups = $this->coreDbModel->getUserGroups($userId)) {
                $arr = array_values($groups);
            }
        }
        return $arr;
    }
    
    /**
     * Проверка существования зарегистрированного пользователя по логину.
     * @param string $login    логин пользователя
     * @return boolean
     */
    public function isLoginExists($login)
    {
        return $this->coreDbModel->isLoginExists($login);
    }
    
    /**
     * @throws Exception
     */
    public function register()
    {
        if (!isset($_POST[self::C_FORM_FIELDNAME_LOGIN])
            || !isset($_POST[self::C_FORM_FIELDNAME_PASSWORD])
            || !isset($_POST[self::C_FORM_FIELDNAME_PASSWORD . '2'])
            || !isset($_POST[self::C_FORM_FIELDNAME_EMAIL])) {
            //UfoExceptionUsersRegisterBadform
        }
        
        $login = htmlspecialchars(substr($_POST[self::C_FORM_FIELDNAME_LOGIN], 0, 255));
        if ($login != $_POST[self::C_FORM_FIELDNAME_LOGIN]) {
            //UfoExceptionUsersRegisterBadlogin('Login parameter contains bad characters');
        }
        if ($this->isLoginExists($login)) {
            //UfoExceptionUsersRegisterLoginexists
        }
        
        $password = substr($_POST[self::C_FORM_FIELDNAME_PASSWORD], 0, 255);
        if ($password != $_POST[self::C_FORM_FIELDNAME_PASSWORD]) {
            //UfoExceptionUsersRegisterBadpassword
        }
        if ($password != $_POST[self::C_FORM_FIELDNAME_PASSWORD . '2']) {
            //UfoExceptionUsersRegisterPasswordconfirmfail
        }
        
        $title = $_POST[self::C_FORM_FIELDNAME_LOGIN];
        if (isset($_POST[self::C_FORM_FIELDNAME_TITLE])) {
            $title = htmlspecialchars(substr($_POST[self::C_FORM_FIELDNAME_TITLE], 0, 255));
        }
        
        $email = substr($_POST[self::C_FORM_FIELDNAME_EMAIL], 0, 255);
        if (!$this->isEmail($email)) {
            //UfoExceptionUsersRegisterBademail
        }
        
        $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'users' . 
               ' (DateCreate,IsDisabled,Login,Password,Title,Email)' . 
               ' VALUES(NOW(),' . $this->settings->IsModerated . ',' . 
               "'" . $this->safeSql($login) . "'," . 
               "'" . $this->safeSql($password) . "'," . 
               "'" . $this->safeSql($title) . "'," . 
               "'" . $email . "'" . ')';
        if (!mysql_query($sql)) {
            //UfoExceptionUsersRegisterDbexeption
        }
        return true;
    }
    
    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->core =& $this->container->getCore();
        $this->debug =& $this->container->getDebug();
        $this->db =& $this->container->getDb();
        $this->coreDbModel =& $this->container->getCoreDbModel();
        $this->site =& $this->container->getSite();
    }
    
    /**
     * Установка параметров функционала.
     * @throws Exception
     */
    protected function setSettings()
    {
        $this->settings = new UfoUsersSettings();
        if ($settings = $this->coreDbModel->getUsersSettings($this->settings->getFields())) {
            $this->settings->setValues($settings);
            unset($settings);
        } else {
            throw new Exception('Error retrieving users settings');
        }
        
        if ($this->settings->IsGlobalAE) {
            $this->settings->AdminEmail = 
                $this->site->getSiteParam($this->config->siteParamsEmail, 
                                          $this->config->siteParamsEmailDefault);
        }
        if ($this->settings->IsGlobalAEF) {
            $this->settings->AdminEmailFrom = 
                $this->site->getSiteParam($this->config->siteParamsEmailFrom,
                                          $this->config->siteParamsEmailFromDefault);
        }
    }
    
    /**
     * Загрузка данных текущего пользователя (по кукам) в переменную класса.
     */
    protected function setCurrentUser()
    {
        $this->item = null;
        if (isset($_COOKIE[self::C_COOKIE_TICKET_NAME]) 
        && 0 < strlen($_COOKIE[self::C_COOKIE_TICKET_NAME])) {
            if ($user = $this->coreDbModel->getUsersCurrent($_COOKIE[self::C_COOKIE_TICKET_NAME])) {
                $this->item = new UfoUserStruct(array_merge($user, 
                                                            array('Groups' => $this->getUserGroups($user['Id']))));
            }
        }
    }
}
