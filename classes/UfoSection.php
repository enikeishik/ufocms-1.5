<?php
/**
 * Класс описывающий раздел сайта.
 *
 * Предоставляет доступ к свойствам раздела сайта через метод, 
 * возвращающий копию поля $fields - агрегированный объект-структуру UfoSectionStruct.
 * 
 * Производит инициализацию модуля (UfoModule), 
 * обслуживающего раздел и генерирует посредством модуля основной контент страницы.
 * 
 * Также предоставляет ряд методов для получения данных родительских, смежных, дочерних разделов.
 * 
 * @author enikeishik
 * 
 */
class UfoSection
{
    use UfoTools;
    
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
     * Ссылка на объект для работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * Ссылка на объект для работы с базой данных.
     * @var UfoDbModel
     */
    protected $dbModel = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Объект-структура для хранения данных раздела сайта.
     * @var UfoSectionStruct
     */
    protected $fields = null;
    
    /**
     * Имя модуля, обслуживающего раздел.
     * @var string
     */
    protected $moduleName = '';
    
    /**
     * Объект модуля, обслуживающего раздел.
     * @var UfoModule
     */
    protected $module = null;
    
    /**
     * Локальное хранилище данных, полученных из базы данных для повторного использования.
     * @var array
     */
    protected $cache = array();
    
    /**
     * Конструктор, формирует объект по идентификатору или пути.
     *
     * @param mixed        $section       идентификатор, путь или данные раздела сайта
     * @param UfoContainer &$container    ссылка на объект-контейнер ссылок на объекты
     * 
     * @throws Exception
     */
    public function __construct($section, UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        $this->setFields($section);
        
        if (0 == $this->fields->moduleid) {
            throw new Exception('Incorrect moduleid');
        }
        if (!$mod = $this->dbModel->getModuleName($this->fields->moduleid)) {
            throw new Exception('Module not found (module uid: ' . $this->fields->moduleid . ')');
        }
        //преобразуем от старого формата 'mod_news.php' к новому 'UfoModNews';
        $mod = substr($mod, strpos($mod, '_') + 1);
        $this->moduleName = 'UfoMod' . ucfirst(substr($mod, 0, strpos($mod, '.')));
    }
    
    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->db =& $this->container->getDb();
        $this->dbModel =& $this->container->getDbModel();
        $this->debug =& $this->container->getDebug();
    }
    
    /**
     * Получение данных раздела в виде объекта-структуры UfoSectionStruct
     * @param mixed $section    идентификатор, путь или данные раздела сайта
     * @throws Exception
     * @todo использовать константу/переменнут вместо строки в throw
     */
    protected function setFields($section)
    {
        $this->loadClass('UfoSectionStruct');
        
        if (is_scalar($section)) {
            if ($fields = $this->dbModel->getSection($section)) {
                $this->fields = new UfoSectionStruct($fields);
            } else {
                throw new Exception('Fields not set');
            }
        } else if (is_array($section)) {
            $this->fields = new UfoSectionStruct($section);
        } else if (is_object($section) && is_a($section, 'UfoSectionStruct')) {
            $this->fields = $section;
        } else {
            throw new Exception('Incorrect $section: ' . var_export($section, true));
        }
    }
    
    /**
     * Инициализация объекта модуля, обслуживающего раздел
     * @todo использовать константу/переменнут вместо строки в throw
     * @throws Exception
     */
    public function initModule()
    {
        $this->container->setSection($this);
        $this->loadModule($this->moduleName);
        $this->module = new $this->moduleName($this->container);
        if (!is_a($this->module, 'UfoModule')) {
            throw new Exception('Module class must extends UfoModule abstract class');
        }
    }
    
    /**
     * Предоставление ссылки на объект модуля
     * @return UfoModule
     */
    public function &getModule()
    {
        return $this->module;
    }
    
    /**
     * Возвращение сгенерированной модулем страницы
     * @return string
     */
    public function getPage()
    {
        return $this->module->getPage();
    }
    
    /**
     * Получение значения поля по имени
     * @param string $field    имя поля
     * @return mixed
     */
    public function getField($field)
    {
    	if (isset($this->fields->$field)) {
	        return $this->fields->$field;
        } else {
            return null;
        }
    }
    
    /**
     * Получение (копии) объекта-структуры, хранящего данные раздела
     * @return UfoSectionStruct
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * Проверка, является ли раздел разделом верхнего уровня
     * @return boolean
     */
    public function isTop()
    {
        return 0 == $this->fields->parentid;
    }
    
    /**
     * Проверка, является ли раздел главной страницей
     * @return boolean
     */
    public function isMain()
    {
        return -1 == $this->fields->id;
    }
    
    /**
     * Получение данных родительского раздела.
     * Данные раздела в виде ассоциативного массива.
     * @return array|null
     */
    public function getParentArray()
    {
        if ($this->isTop() || $this->isMain()) {
            return null;
        }
        if (array_key_exists(__METHOD__, $this->cache)) {
            return $this->cache[__METHOD__];
        }
        if ($parent = $this->dbModel->getSection($this->fields->parentid)) {
            $this->cache[__METHOD__] = $parent;
            return $parent;
        }
        return null;
    }
    
    /**
     * Получение данных родительского раздела.
     * Данные раздела в виде объекта-структуры UfoSectionStruct.
     * @return UfoSectionStruct|null
     */
    public function getParent()
    {
        $section = $this->getParentArray();
        if (!is_null($section)) {
            return new UfoSectionStruct($section);
        }
        return null;
    }

    /**
     * Получение данных раздела верхнего уровня (самого старшего родителя для текущего).
     * Данные раздела в виде ассоциативного массива.
     * @return array|null
     */
    public function getTopArray()
    {
        if ($this->isMain()) {
            return null;
        }
        if ($this->isTop()) {
            return $this->getFields();
        }
        if (array_key_exists(__METHOD__, $this->cache)) {
            return $this->cache[__METHOD__];
        }
        if ($top = $this->dbModel->getSection($this->fields->topid)) {
            $this->cache[__METHOD__] = $top;
            return $top;
        }
        return null;
    }
    
    /**
     * Получение данных раздела верхнего уровня (самого старшего родителя для текущего).
     * Данные раздела в виде объекта-структуры UfoSectionStruct.
     * @return UfoSectionStruct|null
     */
    public function getTop()
    {
        $section = $this->getTopArray();
        if (!is_null($section)) {
            return new UfoSectionStruct($section);
        }
        return null;
    }
    
    /**
     * Получение дочерних разделов для текущего.
     * Данные разделов в виде ассоциативных массивов.
     * @return array[<array>]|null
     */
    public function getChildrenArray()
    {
        if ($this->isMain()) {
            return null;
        }
        if (array_key_exists(__METHOD__, $this->cache)) {
            return $this->cache[__METHOD__];
        }
        if ($children = $this->dbModel->getSection($this->fields->id, true)) {
            $this->cache[__METHOD__] = $children;
            return $children;
        }
        return null;
    }
    
    /**
     * Получение дочерних разделов для текущего.
     * Данные разделов в виде объектов-структур UfoSectionStruct.
     * @return array[<UfoSectionStruct>]|null
     */
    public function getChildren()
    {
        $sections = $this->getChildrenArray();
        if (!is_null($sections)) {
            $arr = array();
            foreach ($sections as $section) {
                $arr[] = new UfoSectionStruct($section); 
            }
            return $arr;
        }
        return null;
    }

    /**
     * Получение смежных разделов для текущего.
     * Данные разделов в виде ассоциативных массивов.
     * @return array[<array>]|null
     */
    public function getNeighborsArray()
    {
        if ($this->isMain()) {
            return null;
        }
        if (array_key_exists(__METHOD__, $this->cache)) {
            return $this->cache[__METHOD__];
        }
        if ($neighbors = $this->dbModel->getSection($this->fields->parentid, true)) {
            $this->cache[__METHOD__] = $neighbors;
            return $neighbors;
        }
        return null;
    }
    
    /**
     * Получение смежных разделов для текущего.
     * Данные разделов в виде объектов-структур UfoSectionStruct.
     * @return array[<UfoSectionStruct>]|null
     */
    public function getNeighbors()
    {
        $sections = $this->getNeighborsArray();
        if (!is_null($sections)) {
            $arr = array();
            foreach ($sections as $section) {
                $arr[] = new UfoSectionStruct($section);
            }
            return $arr;
        }
        return null;
    }
    
    /**
     * Получить родительские разделы текущего.
     * Данные разделов в виде ассоциативных массивов.
     * @param boolean $reversed = true    получить элементы в обратном порядке
     * @return array[<array>]|null
     */
    public function getParentsArray($reversed = true)
    {
        if ($this->isTop() || $this->isMain()) {
            return null;
        }
        $key = __METHOD__ . (string) (int) $reversed;
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }
        $arr = array();
        $parent = $this->dbModel->getSection($this->fields->parentid);
        while ($parent) {
            $arr[] = $parent;
            $parent = $this->dbModel->getSection($parent->fields->parentid);
        }
        if ($reversed) {
            $this->cache[$key] = array_reverse($arr);
            return $this->cache[$key];
        } else {
            $this->cache[$key] = $arr;
            return $arr;
        }
    }
    
    /**
     * Получить родительские разделы текущего.
     * Данные разделов в виде объектов-структур UfoSectionStruct.
     * @param boolean $reversed = true    получить элементы в обратном порядке
     * @return array[<UfoSectionStruct>]|null
     */
    public function getParents($reversed = true)
    {
        if ($sections = $this->getParentsArray($reversed)) {
            $arr = array();
            foreach ($sections as $section) {
                $arr[] = new UfoSectionStruct($section);
            }
            return $arr;
        }
        return null;
    }
}
