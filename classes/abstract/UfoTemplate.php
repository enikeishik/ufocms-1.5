<?php
require_once 'UfoTemplateInterface.php';
require_once 'classes/UfoToolsExt.php';
/**
 * ������������ ����� ������� ������, �������������� ������, 
 * �������� ������ ������ ������������� 
 * ��������� UfoTemplateInterface ��� ���� ������������.
 * ��� ������ �������� ������� ������ ����������� ���� �����.
 * 
 * @author enikeishik
 *
 */
abstract class UfoTemplate implements UfoTemplateInterface
{
    use UfoToolsExt;
    
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
     * ������ ������.
     * @var UfoError
     */
    protected $error = null;
    
    /**
     * ������-��������� � ������� ������.
     * @var UfoErrorStruct
     */
    protected $errorData = null;
    
    /**
     * ������ �� ������ UfoSite, �������������� ����.
     * @var UfoSite
     */
    protected $site = null;
    
    /**
     * ������ �� ������ �������� �������.
     * @var UfoSection
     */
    protected $section = null;
    
    /**
     * ����� �������-��������� ����������� ������ �������.
     * @var UfoSectionStruct
     */
    protected $sectionFields = null;
    
    /**
     * ������ �� ������ ������ �������� �������.
     * @var UfoModule
     */
    protected $module = null;
    
    /**
     * �����������.
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        if (!is_null($this->section)) {
            $this->sectionFields = $this->section->getFields();
        }
        if (!is_null($this->error)) {
            $this->errorData = $this->error->getError();
        }
    }
    
    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->core =& $this->container->getCore();
        $this->debug =& $this->container->getDebug();
        $this->error =& $this->container->getError();
        $this->site =& $this->container->getSite();
        $this->section =& $this->container->getSection();
        $this->module =& $this->container->getModule();
    }
}
