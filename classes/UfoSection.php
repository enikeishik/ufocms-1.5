<?php
/**
 * ����� ����������� ������ �����.
 *
 * ������������� ������ � ��������� ������� ����� 
 * ����� �����, ������������ ����� ���� $fields
 * - �������������� ������-��������� UfoSectionStruct.
 * ���������� ������������� ������ (UfoModule), 
 * �������������� ������ � ���������� ����������� ������ 
 * �������� ������� ��������.
 * ����� ������������� ��� ������� ��� ��������� ������ ������������, 
 * �������, �������� ��������.
 * 
 * @author enikeishik
 * 
 */
class UfoSection
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
     * ������ �� ������ ��� ������ � ����� ������.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * ����� SQL �������, ���������� ������ ����� ������� � ������� ���� ������.
     * @var string
     */
    protected $fieldsSql = '';
    
    /**
     * ������-��������� ��� �������� ������ ������� �����.
     * @var UfoSectionStruct
     */
    protected $fields = null;
    
    /**
     * ��� ������, �������������� ������.
     * @var string
     */
    protected $moduleName = '';
    
    /**
     * ������ ������, �������������� ������.
     * @var UfoModule
     */
    protected $module = null;
    
    /**
     * ��������� ��������� ������, ���������� �� ���� ������ ��� ���������� �������������.
     * @var array
     */
    protected $cache = array();
    
    /**
     * �����������, ��������� ������ �� �������������� ��� ����.
     *
     * @param mixed        $section       �������������, ���� ��� ������ ������� �����
     * @param UfoContainer &$container    ������ �� ������-��������� ������ �� �������
     * 
     * @throws Exception
     */
    public function __construct($section, UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
        
        $this->setFields($section);
        
        $this->moduleName = $this->getModuleName();
    }
    
    /**
     * ������������� ������ �������� ���������� ��������� ����������.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->db =& $this->container->getDb();
        $this->debug =& $this->container->getDebug();
    }
    
    /**
     * ��������� ������ ������� � ���� �������-��������� UfoSectionStruct
     * @param mixed $section    �������������, ���� ��� ������ ������� �����
     * @throws Exception
     * @todo ������������ ���������/���������� ������ ������ � throw
     */
    protected function setFields($section)
    {
        $this->loadClass('UfoSectionStruct');
        
        if (is_scalar($section)) {
            //�������� ���� ������� �� ����� ������-���������
            $arr = get_class_vars('UfoSectionStruct');
            $sql = '';
            foreach ($arr as $fld => $val) {
                $sql .= ',`' . $fld . '`';
            }
            $this->fieldsSql = substr($sql, 1);
            
            $sql = 'SELECT ' . $this->fieldsSql .
                   ' FROM ' . $this->db->getTablePrefix() . 'sections' .
                   ' WHERE ';
            if (is_int($section)) {
                $sql .= 'id=' . $section;
            } else if (is_string($section) && '/' == $section) {
                $sql .= 'id=-1';
            } else if (is_string($section) && $this->isPath($section)) {
                $sql .= "path='" . $section . "'";
            } else {
                throw new Exception('Incorrect $section: ' . var_export($section, true));
            }
            
            if ($fields = $this->db->getRowByQuery($sql)) {
                $this->fields = new UfoSectionStruct($fields);
            } else {
                throw new Exception('Fields not set');
            }
            
        } else if (is_array($section)) {
            $sql = '';
            $this->fields = new UfoSectionStruct($section);
            
        } else if (is_object($section) && is_a($section, 'UfoSectionStruct')) {
            $sql = '';
            $this->fields = $section;
            
        } else {
            throw new Exception('Incorrect $section: ' . var_export($section, true));
        }
    }
    
    /**
     * ������������� ������� ������, �������������� ������
     * @todo ������������ ���������/���������� ������ ������ � throw
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
     * �������������� ������ �� ������ ������
     * @return UfoModule
     */
    public function &getModule()
    {
        return $this->module;
    }
    
    /**
     * ����������� ��������������� ������� ��������
     * @return string
     */
    public function getPage()
    {
        return $this->module->getPage();
    }
    
    /**
     * ��������� �������� ���� �� �����
     * @param string $field    ��� ����
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
     * ��������� (�����) �������-���������, ��������� ������ �������
     * @return UfoSectionStruct
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * ��������, �������� �� ������ �������� �������� ������
     * @return boolean
     */
    public function isTop()
    {
        return 0 == $this->fields->parentid;
    }
    
    /**
     * ��������, �������� �� ������ ������� ���������
     * @return boolean
     */
    public function isMain()
    {
        return -1 == $this->fields->id;
    }
    
    /**
     * ��������� ������ ������������� ������� � ���� �������������� �������.
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
        if ($parent = $this->getSectionById($this->fields->parentid)) {
            $this->cache[__METHOD__] = $parent[0];
            return $parent[0];
        }
        return null;
    }
    
    /**
     * ��������� ������ ������������� ������� � ���� �������-���������.
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
     * ��������� ������ ������� �������� ������ 
     * � �������� ������������ �������� 
     * � ���� �������������� �������.
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
        if ($top = $this->getSectionById($this->fields->topid)) {
            $this->cache[__METHOD__] = $top[0];
            return $top[0];
        }
        return null;
    }
    
    /**
     * ��������� ������ ������� �������� ������ 
     * � �������� ������������ �������� 
     * � ���� �������-���������.
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
     * @return array|null
     */
    public function getChildrenArray()
    {
        if ($this->isMain()) {
            return null;
        }
        if (array_key_exists(__METHOD__, $this->cache)) {
            return $this->cache[__METHOD__];
        }
        if ($children = $this->getSectionById($this->fields->id, true)) {
            $this->cache[__METHOD__] = $children;
            return $children;
        }
        return null;
    }
    
    /**
     * @return array|null
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
     * @return array|null
     */
    public function getNeighborsArray()
    {
        if ($this->isMain()) {
            return null;
        }
        if (array_key_exists(__METHOD__, $this->cache)) {
            return $this->cache[__METHOD__];
        }
        if ($neighbors = $this->getSectionById($this->fields->parentid, true)) {
            $this->cache[__METHOD__] = $neighbors;
            return $neighbors;
        }
        return null;
    }
    
    /**
     * @return array|null
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
     * @return array|null
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
        $parent = $this->getSectionById($this->fields->parentid);
        while ($parent) {
            $arr[] = $parent;
            $parent = $this->getSectionById($parent->fields->parentid);
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
     * @return array|null
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
    
    /**
     * @return array|false
     */
    protected function getSectionById($id, $isParentId = false)
    {
        $sql = 'SELECT ' . $this->fieldsSql . 
               ' FROM ' . $this->config->dbTablePrefix . 'sections' . 
               ' WHERE ' . ($isParentId ? 'parentid' : 'id') . '=' . $id;
        return $this->db->getRowsByQuery($sql);
    }
    
    /**
     * ��������� ����� ������, �������������� ������ �� �������������� ������.
     * @throws Exception
     * @todo ����� ������������ "������ ��-���������" ������ ����������?
     */
    protected function getModuleName()
    {
        if (0 == $this->fields->moduleid) {
            throw new Exception('Incorrect moduleid');
        }
        /*
        $sql = 'SELECT ...name' . 
               ' FROM ' . $this->config->dbTablePrefix . 'modules' . 
               ' WHERE ...id=' . $this->fields->moduleid;
        if ($row = $this->db->getRowByQuery($sql)) {
            return $row['...name'];
        }
        throw new Exception('Module not found ($moduleid=' . $moduleid . ')');
        */
        return 'UfoModDocuments';
    }
}
