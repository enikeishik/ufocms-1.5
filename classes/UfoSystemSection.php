<?php
require_once 'UfoSection.php';
/**
 * ����� ����������� ��������� ������ �����.
 *
 * @author enikeishik
 * 
 */
class UfoSystemSection extends UfoSection
{
    /**
     * ���� ������ ���������� ������� �����.
     * @var string
     */
    protected $pathSystem = '';
    
    /**
     * �����������, ��������� ������ �� �������������� ��� ����.
     *
     * @param string       $pathSystem    ���� ������ ���������� ������� �����
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     *
     * @throws Exception
     */
    public function __construct($pathSystem, UfoContainer &$container)
    {
        $this->pathSystem = $pathSystem;
        $this->container =& $container;
        $this->unpackContainer();
        
        $this->setFields();
    }
    
    /**
     * ��������� ������ ������� � ���� �������-��������� UfoSectionStruct.
     * @param mixed $section = null    ������ ������� �����
     */
    protected function setFields($section = null)
    {
        $moduleName = $this->config->systemSections[$this->pathSystem];
        $structName = $moduleName . 
                      $this->config->structSuffix;
        $this->loadModuleStruct($moduleName, $structName);
        if (is_null($section)) {
            $this->fields = new $structName();
        } else if (is_array($section)) {
            $this->fields = new $structName($section);
        } else if (is_object($section) && is_a($section, $structName)) {
            $this->fields = $section;
        }
    }
    
    /**
     * ������������� ������� ������, �������������� ������.
     * @throws Exception
     */
    public function initModule()
    {
        $this->container->setSection($this);
        //��������� ������� � ������������� �� ������ ����������� � ������������
        if (!array_key_exists($this->pathSystem, $this->config->systemSections)) {
            throw new Exception($this->errors->syssectModuleNotDefined);
        }
        $module = $this->config->systemSections[$this->pathSystem];
        $this->loadModule($module);
        $this->module = new $module($this->container);
        if (!is_a($this->module, 'UfoSystemModule')) {
            throw new Exception($this->errors->syssectModuleIncorrect);
        }
    }
}
