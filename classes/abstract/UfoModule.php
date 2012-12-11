<?php
require_once 'UfoModuleInterface.php';
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
     * �����������.
     * @param UfoSection   &$section      ������ �� ������ �������� �������
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->section =& $container->getSection();
        $this->db =& $container->getDb();
        $this->debug =& $container->getDebug();
        $this->sectionFields = $this->section->getFields();
        
        $this->container->setModule($this);
        
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName($this->container);
    }
}
