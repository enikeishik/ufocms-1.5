<?php
require_once 'UfoTools.php';
/**
 * ����� ��������� ������� ������ HTTP (404, 500).
 * 
 * @author enikeishik
 *
 */
class UfoError
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
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * ������-��������� � ������� ������.
     * @var UfoErrorStruct
     */
    protected $errorData = null;
    
    public function __construct(UfoErrorStruct $errorData, UfoContainer &$container)
    {
        $this->errorData = $errorData;
        $this->container =& $container;
        $this->unpackContainer();
    }

    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->site =& $this->container->getSite();
        $this->section =& $this->container->getSection();
        $this->db =& $this->container->getDb();
        $this->debug =& $this->container->getDebug();
    }
    
    /**
     * ���������� ������-��������� � ������� ������.
     * @return UfoErrorStruct
     */
    public function getError()
    {
        return $this->errorData;
    }
    
    public function getPage()
    {
        $this->container->setError($this);
        ob_start();
        $this->loadTemplate('UfoTemplateError');
        $template = new UfoTemplateError($this->container);
        $this->loadLayout($template, 'error');
        return ob_get_clean();
    }
}
