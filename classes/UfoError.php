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
     * ������-��������� � ������� ������.
     * @var UfoErrorStruct
     */
    protected $errorData = null;
    
    public function __construct(UfoErrorStruct $errorData, UfoContainer &$container)
    {
        $this->errorData = $errorData;
        $this->container =& $container;
        $this->unpackContainer();
        
        $data = '';
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $data = $_SERVER['REMOTE_ADDR'];
        }
        $data .= "\t" . $this->errorData;
        if (isset($_SERVER['REQUEST_URI'])) {
            $data .= "\t" . $_SERVER['REQUEST_URI'];
        } else {
            $data .= "\t";
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $data .= "\t" . $_SERVER['HTTP_REFERER'];
        } else {
            $data .= "\t";
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $data .= "\t" . $_SERVER['HTTP_USER_AGENT'];
        } else {
            $data .= "\t";
        }
        if (500 == $this->errorData->code) {
            $this->writeLog($data, $this->config->logError);
        } else {
            $this->writeLog($data, $this->config->logWarnings);
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
        $this->db =& $this->container->getDb();
        $this->site =& $this->container->getSite();
        $this->section =& $this->container->getSection();
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
