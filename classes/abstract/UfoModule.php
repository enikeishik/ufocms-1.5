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
     * @param UfoDb      &$db            ������ �� ������ ��� ������ � ����� ������
     * @param UfoSection &$section         ������ �� ������ �������� �������
     * @param UfoDebug   &$debug = null    ������ �� ������ �������
     */
    public function __construct(UfoDb &$db, UfoSection &$section, UfoDebug &$debug = null)
    {
        $this->db =& $db;
        $this->section =& $section;
        $this->debug =& $debug;
        $this->sectionFields = $this->section->getFields();
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName($this->section, $this, $this->debug);
    }
}
