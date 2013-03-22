<?php
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
    }

    /**
     * ������������� ������� ������, �������������� ������.
     * @throws Exception
     * @todo ������������ ���������/���������� ������ ������ � throw
     */
    public function initModule()
    {
        $this->container->setSection($this);
        //��������� ������� � ������������� �� ������ ����������� � ������������
        if (!array_key_exists($this->pathSystem, $this->config->systemSections)) {
            throw new Exception('Module class not defined');
        }
        $module = $this->config->systemSections[$this->pathSystem];
        $this->loadModule($module);
        $this->module = new $module($this->container);
        if (!is_a($this->module, 'UfoSystemModule')) {
            throw new Exception('Module class must extends UfoSystemModule abstract class');
        }
    }
}
