<?php
require_once 'UfoModuleInterface.php';
require_once 'classes/UfoToolsExt.php';
require_once 'classes/exceptions/UfoExceptionPathNotexists.php';
/**
 * Абрстрактный класс модуля, обслуживающего раздел, 
 * дочерние классы должны реализовывать 
 * интерфейс UfoModuleInterface или быть абстрактными.
 * Все классы модулей должны наследовать этот класс.
 * 
 * @author enikeishik
 *
 */
abstract class UfoModule implements UfoModuleInterface
{
    use UfoTools, UfoToolsExt;
    
    /**
     * Ссылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;

    /**
     * Ссылка на объект конфигурации.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * Объект для работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * Ссылка на объект UfoSite, представляющий сайт.
     * @var UfoSite
     */
    protected $site = null;
    
    /**
     * Ссылка на объект UfoSection, представляющий текущий раздел.
     * @var UfoSection
     */
    protected $section = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Ссылка на объект шаблона модуля.
     * @var UfoTemplate
     */
    protected $template = null;

    /**
     * Копия объекта-структуры содержащего данные раздела.
     * @var UfoSectionStruct
     */
    protected $sectionFields = null;
    
    /**
     * Объект-структура хранящий значения параметров, передаваемых в URL.
     * @var UfoStruct
     */
    protected $params = null;
    
    /**
     * Конструктор.
     * @param UfoSection   &$section      ссылка на объект текущего раздела
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
     * @throws UfoExceptionPathNotexists
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        if (!is_null($this->section)) {
            $this->sectionFields = $this->section->getFields();
        }
        
        $this->parseParams();
        
        $this->container->setModule($this);
        
        $templateName = str_replace('UfoMod', 'UfoTpl', get_class($this));
        $this->loadTemplate($templateName);
        $this->template = new $templateName($this->container);
    }

    /**
     * Генерация основного содержимого страницы.
     * Может быть переопределен в дочерних классах для реализации специфического вывода.
     * @return string
     */
    public function getPage()
    {
        ob_start();
        $this->loadLayout($this->template);
        //$this->loadLayout($this->template, 'print');
        //$this->loadLayout($this->template, 'mobil');
        return ob_get_clean();
    }
    
    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
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
     * Разбор параметров в URL, которые преобразованы в массив.
     * @throws UfoExceptionPathNotexists
     */
    protected function parseParams()
    {
        $pathParams = $this->site->getPathParams();
        
        $class = get_class($this);
        $struct = $class . 'Params';
        $this->loadClass($struct, $this->config->modulesDir . 
                                  $this->config->directorySeparator . 
                                  $class);
        $this->params = new $struct();
        $paramsArray = get_object_vars($this->params);
        
        foreach ($pathParams as $param) {
            $parsed = false;
            foreach ($paramsArray as $paramName => $paramDefaultValue) {
                $value = $this->parseParam($param, 
                                           $paramName, 
                                           gettype($paramDefaultValue), 
                                           $paramDefaultValue);
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
     * Выявление параметра в части URL, определение его значения, приведение значения к нужному типу и диапазону.
     * @param string $paramRaw     часть URL
     * @param string $paramName    имя параметра/поля объекта структуры модуля
     * @param string $paramType    тип значения параметра
     * @param int    $min = 0      минимальное значение параметра, для числовых типов
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
        
        //проверяем состоит ли параметр только из цифр
        //случай, когда идентификатор записи указывается в URL без префикса id
        } else if (10 > strlen($paramRaw) && $this->isInt($paramRaw)) {
            $ret = (int) substr($paramRaw, strlen($paramName));
            return ($ret < $min ? $min : $ret);
        
        //проверяем на дату вида nnnn-nn-nn, 
        //случай идентификатора даты без префикса dt
        } else if (10 == strlen($paramRaw)) {
            $dt = $this->dateFromString($paramRaw);
            if (!is_null($dt)) {
                return $dt;
            }
            return $min;
        }
        return null;
    }
}
