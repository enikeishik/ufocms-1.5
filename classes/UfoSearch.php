<?php
/**
 * Класс поиска.
 * 
 * @author enikeishik
 *
 */
class UfoSearch
{
    use UfoTools;
    
    /**
     * Записывать статистику поисковых запросов.
     * @var boolean
     */
    const QUERIES_LOG_ENABLED = true;
    
    /**
     * Максимальное количество слов в поисковом запросе.
     * @var int
     */
    const QUERIES_MAXWORDS = 5;
    
    /**
     * Минимальная длинна слова.
     * @var int
     */
    const QUERIES_MINWORDLEN = 3;
    
    /**
     * Ограничение на количество получаемых результатов поиска.
     * @var int
     */
    const RAWSEARCH_LIMIT = 1000;
    
    /**
     * Ограничение на вставку результатов поиска (за один проход) в кэш.
     * @var int
     */
    const RESULTS_INSERT_LIMIT = 100;
    
    /**
     * Вес в выдаче всей фразы.
     * @var int
     */
    const RELEVANCE_PHRASE = 1000;
    
    /**
     * Вес в выдаче всех слов.
     * @var int
     */
    const RELEVANCE_ALLWORDS = 100;
    
    /**
     * Вес в выдаче слов без окончаний.
     * @var int
     */
    const RELEVANCE_STEMMEDWORDS = 10;
    
    /**
     * Вес в выдаче каждого слова.
     * @var int
     */
    const RELEVANCE_WORD = 1;
    
    /**
     * Вес в выдаче, если искомое найдено в Title, MetaDescription, MetaKeywords и содержимом страницы.
     * @var int
     */
    const RELEVANCE_TDKC = 8;
    
    /**
     * Вес в выдаче, если искомое найдено в Title и содержимом страницы.
     * @var int
     */
    const RELEVANCE_TC = 6;
    
    /**
     * Вес в выдаче, если искомое найдено в Title.
     * @var int
     */
    const RELEVANCE_T = 4;
    
    /**
     * Вес в выдаче, если искомое найдено в содержимом страницы.
     * @var int
     */
    const RELEVANCE_C = 2;
    
    /**
     * Ссылка на объект-контейнер ссылок на объекты.
     * @var UfoContainer
     */
    protected $container = null;
    
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
     * Количество найденных результатов.
     * @var int
     */
    protected $count = -1;
    
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
        $this->debug =& $this->container->getDebug();
        $this->db =& $this->container->getDb();
    }
    
    /**
     * Возвращает количество найденных результатов.
     * Выборка производится по таблице готовых результатов, поэтому предварительно необходимо осуществить собственно поиск.
     * @param string $query           поисковый запрос
     * @param string $path = ''       путь раздела
     * @param int $moduleid = null    идентификатор модуля раздела
     * @return int
     */
    public function getResultsCount($query, $path = '', $moduleid = null)
    {
        if (-1 != $this->count) {
            return $this->count;
        }
        
        $this->debug->trace('Search results count', __CLASS__, __METHOD__, false);
        
        $ret = 0;
        if (0 == strlen($query)) {
            return $ret;
        }
        $q = $this->safeSql($query, true);
        
        //получаем общее количество записей
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search_results';
        if (0 < strlen($path) && isset($moduleid)) {
            $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                    " AND (Url LIKE '" . api_GetSafePath($path) . "%')" . 
                    " AND (Query='" . $q . "')";
        } else if (isset($moduleid)) {
            $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                    " AND (Query='" . $q . "')";
        } else if (0 < strlen($path)) {
            $sql .= " WHERE (Url LIKE '" . api_GetSafePath($path) . "%')" . 
                    " AND (Query='" . $q . "')";
        } else {
            $sql .= " WHERE Query='" . $q . "'";
        }
        if ($row = $this->db->getRowByQuery($sql)) {
            $ret = $row['Cnt'];
        }
        
        $this->debug->trace('Search results count complete', __CLASS__, __METHOD__, true);
        
        return $ret;
    }
    
    /**
     * Возвращает результаты поиска.
     * @param string $query           поисковый запрос
     * @param int $page = 1           текущая страница вывода
     * @param int $pageLength = 10    количество записей на страницу
     * @param string $path = ''       путь раздела
     * @param int $moduleid = null    идентификатор модуля раздела
     * @return array<array>
     * @todo заменить строку "Windows-1251" на константу или поле объекта конфигурации
     */
    public function getResults($query, $page = 1, $pageLength = 10, $path = '', $moduleid = null)
    {
        if (0 == strlen($query) || 1 > $page || 1 > $pageLength) {
            return false;
        }
        
        $this->debug->trace('Search results', __CLASS__, __METHOD__, false);
        
        //записываем статистику поисковых запросов
        if (self::QUERIES_LOG_ENABLED) {
            if (1 == $page && !isset($_GET['nolog'])) {
                $this->logSearchQuery($query);
            }
        }
        
        $q = $this->safeSql($query, true);
        //переводим кирилицу в нижний регистр полностью,
        //поскольку стандартные строковые функции PHP
        //некоректно работают с разными регистрами кирилицы
        $q = mb_strtolower($q, "Windows-1251");
        
        //проверяем существует ли уже такой запрос в таблице результатов поиска
        //если нет или устарел - делаем "сырой" поиск по индексу
        $rowsFinded = 1;
        if (!$this->isResultsExists($q)) {
            $rowsFinded = $this->rawSearch($q);
        } else if ($this->isResultsExpired($q)) {
            $this->resultsClearOld($q);
            $rowsFinded = $this->rawSearch($q);
        }
        
        /* DEBUG api_DebugInfo('Search query start'); */
        $search[] = array();
        //делаем поиск по результатам если имеются результаты
        //или если использовался RawSearch и вернул > 0
        if (0 < $rowsFinded) {
            //делаем вывод из таблицы результатов поиска
            $sql = 'SELECT Relevance, Flag, ModuleId, Url, Title, Descr, Content' . 
                   ' FROM ' . $this->db->getTablePrefix() . 'search_results';
            if (0 < strlen($path) && isset($moduleid)) {
                $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                        " AND (Url LIKE '" . api_GetSafePath($path) . "%')" . 
                        " AND (Query='" . $q . "')";
            } else if (isset($moduleid)) {
                $sql .= " WHERE (ModuleId=" . $moduleid . ")" . 
                        " AND (Query='" . $q . "')";
            } else if (0 < strlen($path)) {
                $sql .= " WHERE (Url LIKE '" . api_GetSafePath($path) . "%')" . 
                        " AND (Query='" . $q . "')";
            } else {
                $sql .= " WHERE Query='" . $q . "'";
            }
            $sql .= ' ORDER BY Flag DESC, Relevance DESC' . 
                    ' LIMIT ' . (($page - 1) * $pageLength) . ', ' . $pageLength;
            
            //для увеличения быстродействия не используем обертку getRowsByQuery
            $result = $this->db->query($sql);
            if (false === $result) {
                return $search;
            }
            if (0 < $result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    $search[] = $row;
                }
            }
            $result->free();
        }
        
        $this->debug->trace('Search results complete', __CLASS__, __METHOD__, true);
        
        return $search;
    }
    
    /**
     * Возвращает массив слов из поискового запроса.
     * @param string $query    поисковый запрос
     * @return array<string>
     */
    protected function getQueryWords($query)
    {
        //return explode(' ', $query);
        return preg_split('/[\s,;:\.\?!=–—]+/', $query, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * Проверка наличия сохраненного результата поиска.
     * @param string $query    поисковый запрос
     * @return boolean
     */
    protected function isResultsExists($query)
    {
        $this->debug->trace('Search results exists', __CLASS__, __METHOD__, false);
        
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search_results' . 
               " WHERE Query='" . $query . "'";
        $result = $this->db->query($sql);
        if (false !== $result) {
            if (0 < $result->num_rows) {
                if ($row = $result->fetch_assoc()) {
                    $this->debug->trace('Search results exists complete', __CLASS__, __METHOD__, true);
                    return 0 != $row['Cnt'];
                }
            }
            $result->free();
        }
        
        $this->debug->trace('Search results exists complete', __CLASS__, __METHOD__, true);
        
        return false;
    }
    
    /**
     * Проверка актуальности сохраненного результата поиска.
     * @param string $query    поисковый запрос
     * @return boolean
     */
    protected function isResultsExpired($query)
    {
        $this->debug->trace('Search results expired', __CLASS__, __METHOD__, false);
        
        $sql = 'SELECT UNIX_TIMESTAMP(DateCreate) AS dtm' .
               ' FROM ' . $this->db->getTablePrefix() . 'search_results' .
               " WHERE Query='" . $query . "'" .
               ' LIMIT 1';
        $result = $this->db->query($sql);
        if (false !== $result) {
            if (0 < $result->num_rows) {
                if ($row = $result->fetch_assoc()) {
                    $dtm = $row['dtm'];
                    $this->debug->trace('Search results expired complete', __CLASS__, __METHOD__, true);
                    return (time() - $dtm) > C_EXE_SEARCH_RESULTS_EXPIRATION;
                }
            }
            $result->free();
        }
        
        $this->debug->trace('Search results expired complete', __CLASS__, __METHOD__, true);
        
        return true;
    }
    
    /**
     * Удаление результатов поиска определенного запроса.
     * @param string $query    поисковый запрос
     * @return boolean
     */
    protected function resultsDelete($query)
    {
        return $this->db->query('DELETE FROM ' . $this->db->getTablePrefix() . 'search_results' . 
                                " WHERE Query='" . $query . "'");
    }
    
    /**
     * Удаление устаревших результатов поиска.
     * @param string $query    поисковый запрос
     * @return boolean
     */
    protected function resultsClearOld($query)
    {
        //удаляем устаревшие результаты ВСЕХ запросов,
        //какой смысл их хранить если они все равно пересоздаются
        //да и условие при этом получается "легче"
        //так что может и запрос быстрее сработает
        //и база при этом не будет чрезмерно распухать
        return $this->db->query('DELETE FROM ' . $this->db->getTablePrefix() . 'search_results' . 
                                ' WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(DateCreate))>' . C_EXE_SEARCH_RESULTS_EXPIRATION);
    }
    
    /**
     * Удаление дублей в результатах поиска.
     * @return int
     */
    protected function resultsClearDoubles()
    {
        $sql = 'DELETE t2 FROM ' . $this->db->getTablePrefix() . 'search_results AS t1, ' . 
                                   $this->db->getTablePrefix() . 'search_results AS t2' . 
               ' WHERE t1.Query=t2.Query AND t1.Url=t2.Url AND t1.Relevance>t2.Relevance';
        $this->db->query($sql);
        $this->debug->trace('Clear doubles, affected rows: ' . $this->db->affected_rows, __CLASS__, __METHOD__, false);
        return $this->db->affected_rows;
    }
}
