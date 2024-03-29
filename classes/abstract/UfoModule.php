<?php
require_once 'UfoModuleInterface.php';
require_once 'classes/UfoToolsExt.php';
require_once 'classes/exceptions/UfoExceptionPathNotexists.php';
/**
 * ������������ ����� ������, �������������� ������, 
 * �������� ������ ������ ������������� ��������� UfoModuleInterface ��� ���� ������������.
 * ��� ������ ������� ������ ����������� ���� �����.
 * 
 * @author enikeishik
 *
 */
abstract class UfoModule implements UfoModuleInterface
{
    use UfoTools, UfoToolsExt;
    
    /**
     * ������ �� ������-��������� ������ �� �������.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * ������ ������ ��������� �������� ������.
     * @var UfoErrors
     */
    private $errors = null;
    
    /**
     * ������ �� ������ ������������.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * ������ �� ������ ���� �������.
     * @var UfoCore
     */
    protected $core = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * ������ ��� ������ � ����� ������.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * ������ �� ������ UfoSite, �������������� ����.
     * @var UfoSite
     */
    protected $site = null;
    
    /**
     * ������ �� ������ UfoSection, �������������� ������� ������.
     * @var UfoSection
     */
    protected $section = null;
    
    /**
     * ����� �������-��������� ����������� ������ �������.
     * @var UfoSectionStruct
     */
    protected $sectionFields = null;
    
    /**
     * ������ �� ������ ������� ������.
     * @var UfoTemplate
     */
    protected $template = null;
    
    /**
     * ������-��������� �������� �������� ����������, ������������ � URL.
     * @var UfoModuleParams
     */
    protected $params = null;
    
    /**
     * �����������.
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     * @throws UfoExceptionPathNotexists
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        if (!is_null($this->section)) {
            $this->sectionFields = $this->section->getFields();
        }
        
        $this->parseParams();
        
        $this->container->setModule($this);
        
        $templateName = str_replace($this->config->modulesPrefix, 
                                    $this->config->templatesPrefix, 
                                    get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName($this->container);
    }
    
    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    protected function unpackContainer()
    {
        $this->errors =& $this->container->getErrors();
        $this->config =& $this->container->getConfig();
        $this->core =& $this->container->getCore();
        $this->debug =& $this->container->getDebug();
        $this->db =& $this->container->getDb();
        $this->site =& $this->container->getSite();
        $this->section =& $this->container->getSection();
    }
    
    /**
     * ��������� ��������� ����������� ��������.
     * ����� ���� ������������� � �������� ������� ��� ���������� �������������� ������.
     * @return string
     */
    public function getPage()
    {
        ob_start();
        switch ($this->core->getUserAgent()) {
            case 'browser':
                $this->loadLayout($this->template);
                break;
            case 'printer':
                $this->loadLayout($this->template, 'print');
                break;
            case 'mobile':
                $this->loadLayout($this->template, 'mobil');
                break;
            default:
                $this->loadLayout($this->template);
        }
        return ob_get_clean();
    }
    
    /**
     * ����������� �������� ��������� ������������� � URL.
     * @param string $name    ��� ���������
     * @return mixed|null     ���������� �������� ��������� ��� NULL ���� ��������� �� ����������
     */
    public function getParam($name)
    {
        if (property_exists($this->params, $name)) {
            return $this->params->$name;
        }
        return null;
    }
    
    /**
     * ������ ���������� � URL, ������� ������������� � ������.
     * @throws UfoExceptionPathNotexists
     */
    protected function parseParams()
    {
        $pathParams = $this->site->getPathParams();
        
        $class = get_class($this);
        $struct = $class . $this->config->structParamsSuffix;
        $this->loadClass($struct, $this->config->modulesDir . 
                                  $this->config->directorySeparator . 
                                  $class);
        $this->params = new $struct();
        $paramsArray = get_object_vars($this->params);
        
        foreach ($pathParams as $param) {
            $parsed = false;
            foreach ($paramsArray as $paramName => $paramDefaultValue) {
                $value = $this->parseParam($param, 
                                           $paramName, 
                                           gettype($paramDefaultValue), 
                                           $paramDefaultValue);
                if (!is_null($value)) {
                    $this->params->$paramName = $value;
                    $parsed = true;
                }
            }
            if (!$parsed) {
                throw new UfoExceptionPathNotexists(sprintf($this->errors->moduleParamNotDefined, 
                                                            $param));
            }
        }
    }
    
    /**
     * ��������� ��������� � ����� URL, ����������� ��� ��������, ���������� �������� � ������� ���� � ���������.
     * @param string $paramRaw     ����� URL
     * @param string $paramName    ��� ���������/���� ������� ��������� ������
     * @param string $paramType    ��� �������� ���������
     * @param int    $min = 0      ����������� �������� ���������, ��� �������� �����
     * @return mixed
     */
    protected function parseParam($paramRaw, $paramName, $paramType, $min = 0)
    {
        if (0 === strpos($paramRaw, $paramName)) {
            switch ($paramType) {
                case 'int':
                case 'integer':
                case 'float':
                case 'double':
                    $ret = (int) substr($paramRaw, strlen($paramName));
                    return ($ret < $min ? $min : $ret);
                case 'bool':
                case 'boolean':
                    return true;
                default:
                    return substr($paramRaw, strlen($paramName));
            }
        
        //��������� ������� �� �������� ������ �� ����
        //������, ����� ������������� ������ ����������� � URL ��� �������� id
        } else if (10 > strlen($paramRaw) && $this->isInt($paramRaw)) {
            $ret = (int) $paramRaw;
            return ($ret < $min ? $min : $ret);
        
        //��������� �� ���� ���� nnnn-nn-nn, 
        //������ �������������� ���� ��� �������� dt
        } else if (10 == strlen($paramRaw)) {
            $dt = $this->dateFromString($paramRaw);
            if (!is_null($dt)) {
                return $dt;
            }
            return $min;
        }
        return null;
    }
}
