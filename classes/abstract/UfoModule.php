<?php
require_once 'UfoModuleInterface.php';
//require_once '../exceptions/UfoExceptionPathNotexists.php';
/**
 * ������������ ����� ������, �������������� ������, 
 * �������� ������ ������ ������������� 
 * ��������� UfoModuleInterface ��� ���� ������������.
 * ��� ������ ������� ������ ����������� ���� �����.
 */
abstract class UfoModule implements UfoModuleInterface
{
    use UfoTools;
    
    /**
     * ������ �� ������-��������� ������ �� �������.
     * @var UfoContainer
     */
    protected $container = null;

    /**
     * ������ �� ������ ������������.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * ������ ��� ������ � ����� ������.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * ������ �� ������ UfoSection, �������������� ������� ������.
     * @var UfoSection
     */
    protected $section = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * ������ �� ������ ������� ������.
     * @var UfoTemplate
     */
    protected $template = null;

    /**
     * ����� �������-��������� ����������� ������ �������.
     * @var UfoSectionStruct
     */
    protected $sectionFields = null;
    
    /**
     * ������-��������� �������� �������� ����������, ������������ � URL.
     * @var UfoStruct
     */
    protected $params = null;
    
    /**
     * �����������.
     * @param UfoSection   &$section      ������ �� ������ �������� �������
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->config =& $container->getConfig();
        $this->section =& $container->getSection();
        $this->db =& $container->getDb();
        $this->debug =& $container->getDebug();
        $this->sectionFields = $this->section->getFields();
        
        $this->parseParams();
        
        $this->container->setModule($this);
        
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName($this->container);
    }
    
    /**
     * ������ ���������� � URL, ������� ������������� � ������.
     * @throws UfoExceptionPathNotexists
     */
    protected function parseParams()
    {
        $pathParams = $this->container->getSite()->getPathParams();
        
        $class = get_class($this);
        $struct = $class . 'Params';
        $this->loadClass($struct, $this->config->modulesDir . 
                                  $this->config->directorySeparator . 
                                  $class);
        $this->params = new $struct();
        $paramsArray = get_object_vars($this->params);
        
        foreach ($pathParams as $param) {
            $parsed = false;
            foreach ($paramsArray as $paramName => $paramValue) {
                $value = $this->parseParam($param, $paramName, gettype($paramValue), $paramValue);
                if (!is_null($value)) {
                    $this->params->$paramName = $value;
                    $parsed = true;
                }
            }
            if (!$parsed) {
                throw new UfoExceptionPathNotexists('Parameter ' . $param . ' not identified');
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
            }
        } else if (10 > strlen($paramRaw) && $this->isInt($paramRaw)) {
            $ret = (int) substr($paramRaw, strlen($paramName));
            return ($ret < $min ? $min : $ret);
        }
        return null;
    }
}
