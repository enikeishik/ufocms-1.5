<?php
/**
 * �����-���������, ������ ������ �� �������.
 * 
 * ������������ ��� �������� ����������� �������� ������ 
 * �� ������������������ ������� � �������, �������-���������, 
 * ����������� � ��������������� �������.
 * 
 * ��������, ������ ���� (UfoCore) �������� ������ ������� (UfoSection) 
 * �������� ��� ������ �� ������������������ � ���� ������� 
 * ������ � ����� ������, ������ ������������ � ��. ������ �������, 
 * � ���� �������, �������� ������������ �� ������� ������ �������
 * ������ �� ��� ������� � ������ �� ������ ����, ����� ������ ���
 * ������������ ������ � ������ ������� � ����� �����, ���� ��������
 * ������ �� ������ ������� ������ - ������� ������� �������.
 * 
 * @author enikeishik
 *
 */
class UfoContainer
{
    /**
     * ������ �� ������ ������������ �������.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * ������ �� ������ ������ � ����� ������.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * ������ �� ������ ������ ������.
     * @var UfoCoreDbModel
     */
    protected $coreDbModel = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;

    /**
     * ������ �� ������ ���� �������.
     * @var UfoCore
     */
    protected $core = null;
    
    /**
     * ������ �� ������ ������ � ������������������� �������������� �����.
     * @var UfoUsers
     */
    private $users = null;
    
    /**
     * ������ �� ������ �����.
     * @var UfoSite
     */
    protected $site = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoSection
     */
    protected $section = null;

    /**
     * ������ �� ������-��������� �������� ������ �������.
     * @var UfoSectionStruct
     */
    protected $sectionStruct = null;
    
    /**
     * ������ �� ������ ������.
     * @var UfoModule
     */
    protected $module = null;
    
    /**
     * ������ �� ������ ������.
     * @var UfoError
     */
    protected $error = null;
    
    /**
     * ������ �� ������ ������ ��������� �������� ������.
     * @var UfoErrors
     */
    protected $errors = null;
    
    /**
     * �����������.
     * @param array $vars = null    ������ ������ �� �������
     */
    public function __construct(array $vars = null)
    {
        if (is_null($vars)) {
            return;
        }
        foreach ($vars as $key => $val) {
            if (property_exists(__CLASS__, $key)) {
                if (is_object($val)) {
                    /*
                     NOT $this->$key =& $val;
                     ������ ��� $val - ������ � ��� ��������� �������� 
                     ����� ��������� �� ������ ��������, � � ����� � ���� 
                     � ��� ���������� $this->$key (������� ����� 
                     ��� ����� ������������ `=&` ���������� $val) 
                     ����� ����� ��������� �� ����� �������� $val;
                     */
                    $this->$key =& $vars[$key]; 
                } else {
            		$this->$key = (object) $val;
                }
            }
        }
    }
    
    /**
     * 
     * @param UfoConfig $config
     */
    public function setConfig(UfoConfig &$config) { $this->config =& $config; }
    
    /**
     * 
     * @return UfoConfig
     */
    public function &getConfig() { return $this->config; }
    
    /**
     * 
     * @param UfoDb $db
     */
    public function setDb(UfoDb &$db) { $this->db =& $db; }
    
    /**
     * 
     * @return UfoDb
     */
    public function &getDb() { return $this->db; }
    
    /**
     *
     * @param UfoCoreDbModel $coreDbModel
     */
    public function setCoreDbModel(UfoCoreDbModel &$coreDbModel) { $this->coreDbModel =& $coreDbModel; }
    
    /**
     *
     * @return UfoCoreDbModel
     */
    public function &getCoreDbModel() { return $this->coreDbModel; }
    
    /**
     * 
     * @param UfoDebug $debug
     */
    public function setDebug(UfoDebug &$debug) { $this->debug =& $debug; }
    
    /**
     *
     * @return UfoDebug
     */
    public function &getDebug() { return $this->debug; }
    
    /**
     *
     * @param UfoCore $core
     */
    public function setCore(UfoCore &$core) { $this->core =& $core; }
    
    /**
     *
     * @return UfoCore
     */
    public function &getCore() { return $this->core; }
    
    /**
     *
     * @param UfoUsers $users
     */
    public function setUsers(UfoUsers &$users) { $this->users =& $users; }
    
    /**
     *
     * @return UfoUsers
     */
    public function &getUsers() { return $this->users; }
    
    /**
     * 
     * @param UfoSite $site
     */
    public function setSite(UfoSite &$site) { $this->site =& $site; }
    
    /**
     * 
     * @return UfoSite
     */
    public function &getSite() { return $this->site; }
    
    /**
     * 
     * @param UfoSection $section
     */
    public function setSection(UfoSection &$section) { $this->section =& $section; }
    
    /**
     * 
     * @return UfoSection
     */
    public function &getSection() { return $this->section; }
    
    /**
     *
     * @param UfoSectionStruct $sectionStruct
     */
    public function setSectionStruct(UfoSectionStruct &$sectionStruct) { $this->sectionStruct =& $sectionStruct; }
    
    /**
     *
     * @return UfoSectionStruct
     */
    public function &getSectionStruct() { return $this->sectionStruct; }
    
    /**
     * 
     * @param UfoModule $module
     */
    public function setModule(UfoModule &$module) { $this->module =& $module; }
    
    /**
     * 
     * @return UfoModule
     */
    public function &getModule() { return $this->module; }
    
    /**
     * 
     * @param UfoError $error
     */
    public function setError(UfoError &$error) { $this->error =& $error; }
    
    /**
     * 
     * @return UfoError
     */
    public function &getError() { return $this->error; }
    
    /**
     * 
     * @param UfoErrors $errors
     */
    public function setErrors(UfoErrors &$errors) { $this->errors =& $errors; }
    
    /**
     * 
     * @return UfoErrors
     */
    public function &getErrors() { return $this->errors; }
}
