<?php
require_once 'UfoTemplateInterface.php';
/**
 * ������������ ����� ������� ������, �������������� ������, 
 * �������� ������ ������ ������������� 
 * ��������� UfoTemplateInterface ��� ���� ������������.
 * ��� ������ �������� ������� ������ ����������� ���� �����.
 */
abstract class UfoTemplate implements UfoTemplateInterface
{
    /**
     * ������ �� ������-��������� ������ �� �������.
     * @var UfoContainer
     */
    protected $container = null;
    
    /**
     * ������ �� ������ �������� �������.
     * @var UfoSection
     */
    protected $section = null;

    /**
     * ������ �� ������ ������ �������� �������.
     * @var UfoModule
     */
    protected $module = null;

    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * ����� �������-��������� ����������� ������ �������.
     * @var UfoSectionStruct
     */
    protected $sectionFields = null;
    
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
    }
    
    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    protected function unpackContainer()
    {
        $this->module =& $this->container->getModule();
        $this->section =& $this->container->getSection();
        $this->debug =& $this->container->getDebug();
    }
    
    /**
     * ����� ���������� ������� (� ����� ��������, � ���� ����������� HTML).
     * ����� ���� ������������� � �������� ������� ��� ���������� �������������� ������.
     */
    public function drawDebug()
    {
        if (is_null($this->debug)) {
            return;
        }
        echo '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->' . "\r\n";
    }
}
