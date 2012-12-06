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
    protected $fields = null;
    
    /**
     * �����������.
     * @param UfoSection &$section         ������ �� ������ �������� �������
     * @param UfoModule  &$module          ������ �� ������ ������ �������� �������
     * @param UfoDebug   &$debug = null    ������ �� ������ �������
     */
    public function __construct(UfoSection &$section, UfoModule &$module, UfoDebug &$debug = null)
    {
        $this->section =& $section;
        $this->module =& $module;
        $this->debug =& $debug;
        $this->fields = $this->section->getFields(); 
    }
}
