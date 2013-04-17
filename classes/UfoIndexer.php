<?php
/**
 * Класс индексации содержимого страниц.
 * 
 * @author enikeishik
 *
 */
class UfoIndexer
{
    use UfoTools;
    
    /**
     * Метка в коде страницы, определяющая начало индексируемого содержимого.
     * @var string
     */
    const MARK_INDEX_START = '<!--content-->';
    
    /**
     * Метка в коде страницы, определяющая конец индексируемого содержимого.
     * @var string
     */
    const MARK_INDEX_STOP = '<!--/content-->';
    
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
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Ссылка на объект для работы с базой данных.
     * @var UfoDb
     */
    protected $db = null;
    
    /**
     * Конструктор.
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
     * @throws Exception
     */
    public function __construct(UfoContainer &$container)
    {
        $this->container =& $container;
        $this->unpackContainer();
    }
    
    /**
     * Присванивание ссылок объектов контейнера локальным переменным.
     */
    protected function unpackContainer()
    {
        $this->config =& $this->container->getConfig();
        $this->debug =& $this->container->getDebug();
        $this->db =& $this->container->getDb();
    }
    
    /**
     * Индексирование контента.
     * @param string $url                  URL индексируемой страницы
     * @param string $content = null       индексируемый контент, если отсутствует, берется из буфера вывода
     * @param boolean $insearch = false    флаг, определяет должен ли индексироваться контент данной страницы, если нет и индекс имеется, то индекс удаляется
     * @param int $flsearch = 0            дополнительная метка, может использоваться при поиске, для выведения некоторых страниц вверх (при выдаче используется обратная сортировка по этому полю), или для поиска только по конкретным меткам
     * @param int $moduleid = 0            идентификатор модуля используемого на индексируемой странице, может использоваться для поиска только по определенным модулям
     * @return boolean
     */
    public function index($url, $content = null, $insearch = false, $flsearch = 0, $moduleid = 0)
    {
        if ('/' != substr($url, strlen($url) - 1, strlen($url))) {
            $url .= '/';
        }
        
        if (!$insearch) {
            //проверяем есть ли индекс
            $cnt = 0;
            $sql = 'SELECT COUNT(*) AS Cnt FROM ' . $this->db->getTablePrefix() . 'search' .
                   " WHERE Url='" . $this->safeSql($url) . "'";
            $result = $this->db->query($sql);
            if ($row = $result->fetch_assoc()) {
                $cnt = $row['Cnt'];
            }
            $result->free();
            if ($cnt > 0) {
                $sql = 'DELETE FROM ' . $this->db->getTablePrefix() . 'search' .
                       " WHERE Url='" . $this->safeSql($url) . "'";
                $this->db->query($sql);
            }
            return false;
        }
        
        if (!isset($content)) {
            $content = ob_get_contents();
        }
        $contentRaw = $content;
        
        //если есть метки ограничения индексирования контента, то обрезаем по этим меткам
        if (false !== stripos($content, self::MARK_INDEX_START)) {
            $content = substr($content, 
                              stripos($content, 
                                      self::MARK_INDEX_START) + strlen(self::MARK_INDEX_START));
            if (false !== stripos($content, self::MARK_INDEX_STOP)) {
                $content = substr($content, 
                                  0, 
                                  stripos($content, 
                                          self::MARK_INDEX_STOP));
            }
        }
        $title = $this->getTitle($contentRaw);
        $descr = $this->getMeta($contentRaw, 'description');
        $keys  = $this->getMeta($contentRaw, 'keywords');
        $index = trim(preg_replace('/\s{2,}|\r|\n|\t/',
                                   ' ',
                                   preg_replace('/&[^;]*;/', 
                                                ' ',
                                                preg_replace('/<[^>]*>/', 
                                                             ' ',
                                                             preg_replace('/<style[^>]*?>.*?<\/style>/si', 
                                                                          '',
                                                                          preg_replace('/<script[^>]*?>.*?<\/script>/si', 
                                                                                       '', 
                                                                                       $content))))));
        $hash  = md5($title . $descr . $keys . $index);
        
        if ($this->isIndexExists($url)) {
            if (!$this->isIndexChanged($url, $hash)) {
                return false;
            }
            $sql = 'UPDATE ' . $this->db->getTablePrefix() . 'search' .
                   " SET Flag=" . $flsearch . ", " .
                   " ModuleId=" . $moduleid . ", " .
                   " Title='" . $this->safeSql($title) . "', " .
                   " MetaDesc='" . $this->safeSql($descr) . "', " .
                   " MetaKeys='" . $this->safeSql($keys) . "', " .
                   " Content='" . $this->safeSql($index) . "', " .
                   " Hash='" . $hash . "', " .
                   " DateIndex=NOW() " .
                   " WHERE Url='" . $this->safeSql($url) . "'";
        } else {
            $sql = 'INSERT INTO ' . $this->db->getTablePrefix() . 'search' .
                   ' (Flag,ModuleId,Url,Title,MetaDesc,MetaKeys,Content,Hash,DateIndex)' .
                   " VALUES(" . $flsearch . "," . $moduleid . 
                   ",'" . $this->safeSql($url) .
                   "','" . $this->safeSql($title) .
                   "','" . $this->safeSql($descr) .
                   "','" . $this->safeSql($keys) .
                   "','" . $this->safeSql($index) .
                   "','" . $hash . "',NOW())";
        }
        return $this->db->query($sql);
    }
    
    /**
     * Проверка существования индекса для URL.
     * @param string $url    URL страницы для которой проверяется наличие индекса
     * @return boolean
     */
    protected function isIndexExists($url)
    {
        $sql = 'SELECT COUNT(*) AS Cnt' .
               ' FROM ' . $this->db->getTablePrefix() . 'search' .
               " WHERE Url='" . $this->safeSql($url) . "'";
        $result = $this->db->query($sql);
        if (false === $result) {
            return false;
        }
        if (0 == $result->num_rows) {
            return false;
        }
        if (!$row = $result->fetch_assoc()) {
            return false;
        }
        $result->free();
        return 0 != (int) $row['Cnt'];
    }
    
    /**
     * Определение изменился ли индекс URL.
     * @param string $url      URL страницы
     * @param $string $hash    хэш содержимого индекса
     * @return boolean
     */
    protected function isIndexChanged($url, $hash)
    {
        $sql = 'SELECT COUNT(*) AS Cnt' .
               ' FROM ' . $this->db->getTablePrefix() . 'search' .
               " WHERE Url='" . $this->safeSql($url) . "'" .
               " AND Hash!='" . $hash . "'";
        $result = $this->db->query($sql);
        if (false === $result) {
            return false;
        }
        if (0 == $result->num_rows) {
            return false;
        }
        if (!$row = $result->fetch_assoc()) {
            return false;
        }
        $result->free();
        return 0 != (int) $row['Cnt'];
    }
    
    /**
     * Получение содержимого HTML тэга TITLE.
     * @param string $content    содержимое (HTML код) страницы
     * @return string 
     */
    protected function getTitle($content)
    {
        $start = stripos($content, '<title');
        if (false === $start) {
            return '';
        }
    
        $startTitle = strpos($content, '>', $start + 6);
        if (false === $startTitle) {
            return '';
        }
    
        $stop = stripos($content, '</title>', $startTitle + 1);
        if (false === $stop) {
            return '';
        }
    
        $len = $stop - $startTitle - 1;
        if ($this->config->dbVarcharLengthLimit < $len) {
            $len = $this->config->dbVarcharLengthLimit;
        }
        return substr($content, $startTitle + 1, $len);
    }
    
    /**
     * Получение содержимого мета тэга.
     * @param string $content     содержимое (HTML код) страницы
     * @param string $metaname    имя метатэга
     * @return string 
     */
    protected function getMeta($content, $metaname)
    {
        $offset = 0;
        $length = strlen($content);
        do {
            $start = stripos($content, '<meta', $offset);
            if (false === $start) {
                return '';
            }
    
            $stop = strpos($content, '>', $start + 5);
            if (false === $stop) {
                return '';
            }
    
            $meta = substr($content, $start + 5, $stop - $start - 5);
            
            // проверяем нестрого, поскольку все равно после '<meta'
            // еще должен быть пробел, так что stripos > 0 если нашла
            if (stripos($meta, 'name=' . $metaname) 
            || stripos($meta, 'name="' . $metaname)
            || stripos($meta, "name='" . $metaname)) {
                $start = stripos($meta, ' content=');
                if (false !== $start) {
                    $quot = substr($meta, $start + 9, 1);
                    if ('"' != $quot && "'" != $quot) {
                        //если значение атрибута не заключено в кавычки, возвращаем пустую строку
                        return '';
                    }
                    $stop = strpos($meta, $quot, $start + 10);
                    if (false !== $stop) {
                        $len = $stop - $start - 10;
                        if ($this->config->dbVarcharLengthLimit < $len) {
                            $len = $this->config->dbVarcharLengthLimit;
                        }
                        return substr($meta, $start + 10, $len);
                    } else {
                        $len = $this->config->dbVarcharLengthLimit;
                        return substr($meta, $start + 10, $len);
                    }
                }
            }
            $offset = $stop;
        } while ($offset < $length);
    
        return '';
    }
}
