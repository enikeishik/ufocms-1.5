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
     * Количество дней, в течении которых собирать статистику поисковых запросов.
     * @var int
     */
    const QUERIES_LOG_DAYS = 30;
    
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
     * Время жизни результатов поиска в секундах.
     * @var int
     */
    const RESULTS_EXPIRATION = 600; 
    
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
        
        if (0 == strlen($query)) {
            $this->debug->trace('Search results count, query is empty', __CLASS__, __METHOD__, true);
            return 0;
        }
        $q = $this->safeSql($query, true);
        
        //получаем общее количество записей
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search_results';
        if (0 < strlen($path) && isset($moduleid)) {
            $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                    " AND (Url LIKE '" . $this->safeSql($path) . "%')" . 
                    " AND (Query='" . $q . "')";
        } else if (isset($moduleid)) {
            $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                    " AND (Query='" . $q . "')";
        } else if (0 < strlen($path)) {
            $sql .= " WHERE (Url LIKE '" . $this->safeSql($path) . "%')" . 
                    " AND (Query='" . $q . "')";
        } else {
            $sql .= " WHERE Query='" . $q . "'";
        }
        if ($row = $this->db->getRowByQuery($sql)) {
            $this->count = $row['Cnt'];
        } else {
            $this->count = -1;
        }
        
        $this->debug->trace('Search results count complete', __CLASS__, __METHOD__, true);
        
        return $this->count;
    }
    
    /**
     * Возвращает результаты поиска.
     * @param string $query           поисковый запрос
     * @param int $page = 1           текущая страница вывода
     * @param int $pageLength = 10    количество записей на страницу
     * @param string $path = ''       путь раздела
     * @param int $moduleid = null    идентификатор модуля раздела
     * @return array<array>
     */
    public function getResults($query, $page = 1, $pageLength = 10, $path = '', $moduleid = null)
    {
        if (0 == strlen($query) || 1 > $page || 1 > $pageLength) {
            return false;
        }
        
        $this->debug->trace('Search results', __CLASS__, __METHOD__, false);
        
        $q = $this->safeSql($query, true);
        
        //записываем статистику поисковых запросов
        if (self::QUERIES_LOG_ENABLED) {
            if (1 == $page && !isset($_GET['nolog'])) {
                $this->logSearchQuery($query);
            }
        }
        
        //переводим кирилицу в нижний регистр полностью,
        //поскольку стандартные строковые функции PHP
        //некоректно работают с разными регистрами кирилицы
        $q = mb_strtolower($q);
        
        //проверяем существует ли уже такой запрос в таблице результатов поиска
        //если нет или устарел - делаем "сырой" поиск по индексу
        $rowsFinded = 1;
        if (!$this->isResultsExists($q)) {
            $rowsFinded = $this->rawSearch($q);
        } else if ($this->isResultsExpired($q)) {
            $this->resultsClearOld($q);
            $rowsFinded = $this->rawSearch($q);
        }
        
        $search[] = array();
        //делаем поиск по результатам если имеются результаты
        //или если использовался RawSearch и вернул > 0
        if (0 < $rowsFinded) {
            //делаем вывод из таблицы результатов поиска
            $sql = 'SELECT Relevance, Flag, ModuleId, Url, Title, Descr, Content' . 
                   ' FROM ' . $this->db->getTablePrefix() . 'search_results';
            if (0 < strlen($path) && isset($moduleid)) {
                $sql .= ' WHERE (ModuleId=' . $moduleid . ')' . 
                        " AND (Url LIKE '" . $this->safeSql($path) . "%')" . 
                        " AND (Query='" . $q . "')";
            } else if (isset($moduleid)) {
                $sql .= " WHERE (ModuleId=" . $moduleid . ")" . 
                        " AND (Query='" . $q . "')";
            } else if (0 < strlen($path)) {
                $sql .= " WHERE (Url LIKE '" . $this->safeSql($path) . "%')" . 
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
        return preg_split('/ - |[\s,;:\.\?!=–—]+/', $query, -1, PREG_SPLIT_NO_EMPTY);
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
                    return (time() - $dtm) > self::RESULTS_EXPIRATION;
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
                                ' WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(DateCreate))>' . self::RESULTS_EXPIRATION);
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
    
    /**
     * Возвращает слова больше минимальной длинны.
     * @param array words
     * return array
     */
    protected function getLongWords(array $words)
    {
        $arr = array();
        foreach ($words as $word) {
            if (self::QUERIES_MINWORDLEN <= strlen($word)) {
                $arr[] = $word;
            }
        }
        return $arr;
    }
    
    /**
     * Разбор поискового запроса и поиск по индексу с записью результатов поиска в отдельную таблицу и установкой индекса релевантности для каждого найденного элемента.
     * @param string $query    обработанный поисковый запрос
     * @return int             количество найденных результатов
     * @todo подумать над сохранением пустых результатов, чтобы снова не повторять RawSearch для таких запросов
     */
    protected function rawSearch($query)
    {
        $this->debug->trace('Raw search', __CLASS__, __METHOD__, false);
        
        //количество найденных результатов и флаг,
        //что этих результатов не меньше чем RAWSEARCH_LIMIT
        $rowsFinded = 0;
        $rowsFindedEnough = false;
        
        //разбиваем поисковый запрос на слова, чтобы искать по каждому слову
        $words = array_unique($this->getQueryWords($query));
        $wordsCount = count($words);
        
        //*************************************
        //сначала ищем поисковый запрос целиком
        $rowsFinded += $this->searchWord($query, self::RELEVANCE_PHRASE, $query);
        //если нашли не меньше чем надо, ставим флаг
        $rowsFindedEnough = (self::RAWSEARCH_LIMIT <= $rowsFinded);
        
        //если слово в запросе несколько и еще не нашли достаточно результатов
        //ищем по всем словам в запросе
        if (1 < $wordsCount && !$rowsFindedEnough) {
            $words = $this->getLongWords($words);
            $wordsCount = count($words);
            if (0 == $wordsCount) {
                $this->debug->trace('Raw search complete, all words in query are too small', __CLASS__, __METHOD__, true);
                return $rowsFinded;
            }
            
            if (1 < $wordsCount) {
                //******************************************
                //если слов несколько ищем все слова запроса
                //в произвольном порядке, а не как в поисковом запросе
                $rowsFinded += $this->searchWords($words, self::RELEVANCE_ALLWORDS, $query);
                $rowsFindedEnough = (self::RAWSEARCH_LIMIT <= $rowsFinded);
            } else if ($words[0] != $query) {
                //******************************************************************************
                //если осталось только одно слово, ищем по нему с меньшим индексом релевантности
                //ищем только если исходный запрос содержал мелкие слова, вырезанные выше
                $rowsFinded += $this->searchWord($words[0], self::RELEVANCE_ALLWORDS, $query);
                $rowsFindedEnough = (self::RAWSEARCH_LIMIT <= $rowsFinded);
            }
            
            if (!$rowsFindedEnough) {
                $rowsFinded += $this->rawSearchStemmed($query, $words);
                $rowsFindedEnough = (self::RAWSEARCH_LIMIT <= $rowsFinded);
            }
            
        }
        
        if (0 < $rowsFinded) {
            //********************************************************************
            //удаляем дубли, дубли возникают, поскольку мы понижаем условия поиска
            //и то что уже найдено заведомо содержит более простые формы и войдет в результат
            $ret = $this->resultsClearDoubles();
            if (false !== $ret) {
                $rowsFinded -= $ret;
            }
        }
        
        $this->debug->trace('Raw search complete', __CLASS__, __METHOD__, true);
        
        return $rowsFinded;
    }
    
    /**
     * Поиск по словам из поискового запроса без окончаний.
     * @param string $query    обработанный поисковый запрос
     * @param array $words     массив слов из поискового запроса
     * @return int             количество найденных результатов
     */
    protected function rawSearchStemmed($query, array $words)
    {
        $this->debug->trace('Raw search stemmed', __CLASS__, __METHOD__, false);
        
        $rowsFinded = 0;
        
        //обрезаем окончания и пр. используя класс UfoSearchStemmer
        $this->loadClass('UfoSearchStemmer');
        $stemmer = new UfoSearchStemmer();
        $wordsStemmed = array();
        foreach ($words as $word) {
            $wordsStemmed[] = $stemmer->stem($word);
        }
        if (0 < count($wordsStemmed)) {
            //**********************************
            //ищем по всем словам, без окончаний
            $rowsFinded = $this->searchWords($wordsStemmed, self::RELEVANCE_STEMMEDWORDS, $query, true);
        }
        
        $this->debug->trace('Raw search stemmed complete', __CLASS__, __METHOD__, true);
        
        return $rowsFinded;
    }
    
    /**
     * Поиск слова/фразы.
     * Формирование SQL запросов для поиска в различных полях таблицы поискового индекса и передача этих запросов на выполнение.
     * @param string $word            поисковая фраза или слово из фразы, только слова не меньше заданной длинны
     * @param int $relevanceFactor    множитель для индекса релевантности, для целой фразы больше, для отдельных слов фразы - меньше
     * @param string $query           обработанный поисковый запрос
     * @return int                    количество найденных результатов
     */
    protected function searchWord($word, $relevanceFactor, $query)
    {
        $rowsFinded = 0;
        $sql = 'SELECT Flag, ModuleId, Url, Title, MetaDesc, Content' .
               ' FROM ' . $this->db->getTablePrefix() . 'search';
        
        //ищем сначала записи, где искомое встречается в каждом поле (заголовок, описание, ключевики, контент)
        $sqlWhere = " WHERE Title LIKE '%" . $word . "%'" .
                    " AND MetaDesc LIKE '%" . $word . "%'" .
                    " AND MetaKeys LIKE '%" . $word . "%'" .
                    " AND Content LIKE '%" . $word . "%'";
        $ret = $this->searchWordExec($sql . $sqlWhere, 
                                     ($relevanceFactor * self::RELEVANCE_TDKC), 
                                     $query);
        if (false !== $ret) {
            $rowsFinded += $ret;
        }
        
        //затем ищем записи, где искомое встречается в заголовке и контенте
        $sqlWhere = " WHERE Title LIKE '%" . $word . "%'" .
                    " AND Content LIKE '%" . $word . "%'";
        $ret = $this->searchWordExec($sql . $sqlWhere, 
                                     ($relevanceFactor * self::RELEVANCE_TC), 
                                     $query);
        if (false !== $ret) {
            $rowsFinded += $ret;
        }
        
        //затем ищем записи, где искомое встречается в заголове
        $sqlWhere = " WHERE Title LIKE '%" . $word . "%'";
        $ret = $this->searchWordExec($sql . $sqlWhere, 
                                     ($relevanceFactor * self::RELEVANCE_T), 
                                     $query);
        if (false !== $ret) {
            $rowsFinded += $ret;
        }
        
        //затем ищем записи, где искомое встречается в контенте
        $sqlWhere = " WHERE Content LIKE '%" . $word . "%'";
        $ret = $this->searchWordExec($sql . $sqlWhere, 
                                     ($relevanceFactor * self::RELEVANCE_C), 
                                     $query);
        if (false !== $ret) {
            $rowsFinded += $ret;
        }
        
        return $rowsFinded;
    }
    
    /**
     * Поиск всех слов.
     * Формирование SQL запросов для поиска в различных полях таблицы поискового индекса и передача этих запросов на выполнение.
     * @param string $w                    массив слов из поисковой фразы, только слова не меньше заданной длинны
     * @param int $relevanceFactor         множитель для индекса релевантности, для целой фразы больше, для отдельных слов фразы - меньше
     * @param string $query                обработанный поисковый запрос
     * @param $ignoreMinwordlen = false    игнорировать минимальную длинну слова, используется для слов без окончаний
     * @return int                         количество найденных результатов
     */
    protected function searchWords(array $words, $relevanceFactor, $query, $ignoreMinwordlen = false)
    {
        $this->debug->trace('Search words', __CLASS__, __METHOD__, false);
        
        $rowsFinded = 0;
        $wordsCount = count($words);
        
        $sql = 'SELECT Flag, ModuleId, Url, Title, MetaDesc, Content' . 
               ' FROM ' . $this->db->getTablePrefix() . 'search';
        
        $sqlWhereTdkc = "";
        $sqlWhereTc = "";
        $sqlWhereT = "";
        $sqlWhereC = "";
        foreach ($words as $word) {
            if ($ignoreMinwordlen || self::QUERIES_MINWORDLEN <= strlen($word)) {
                //ищем сначала записи, где искомое встречается в каждом поле (заголовок, описание, ключевики, контент)
                $sqlWhereTdkc .= " AND Title LIKE '%" . $word . "%'" . 
                                 " AND MetaDesc LIKE '%" . $word . "%'" . 
                                 " AND MetaKeys LIKE '%" . $word . "%'" . 
                                 " AND Content LIKE '%" . $word . "%'";
                //затем ищем записи, где искомое встречается в заголовке и контенте
                $sqlWhereTc .= " AND Title LIKE '%" . $word . "%'" . 
                               " AND Content LIKE '%" . $word . "%'";
                //затем ищем записи, где искомое встречается в заголове
                $sqlWhereT .= " AND Title LIKE '%" . $word . "%'";
                //затем ищем записи, где искомое встречается в контенте
                $sqlWhereC .= " AND Content LIKE '%" . $word . "%'";
            }
        }
        if (0 < strlen($sqlWhereTdkc)) {
            $ret = $this->searchWordExec($sql . ' WHERE' . substr($sqlWhereTdkc, 4), 
                                         ($relevanceFactor * self::RELEVANCE_TDKC), 
                                         $query);
            if (false !== $ret) {
                $rowsFinded += $ret;
            }
        }
        if (0 < strlen($sqlWhereTc)) {
            $ret = $this->searchWordExec($sql . ' WHERE' . substr($sqlWhereTc, 4), 
                                         ($relevanceFactor * self::RELEVANCE_TC), 
                                         $query);
            if (false !== $ret) {
                $rowsFinded += $ret;
            }
        }
        if (0 < strlen($sqlWhereT)) {
            $ret = $this->searchWordExec($sql . ' WHERE' . substr($sqlWhereT, 4), 
                                         ($relevanceFactor * self::RELEVANCE_T), 
                                         $query);
            if (false !== $ret) {
                $rowsFinded += $ret;
            }
        }
        if (0 < strlen($sqlWhereC)) {
            $ret = $this->searchWordExec($sql . ' WHERE' . substr($sqlWhereC, 4), 
                                         ($relevanceFactor * self::RELEVANCE_C), 
                                         $query);
            if (false !== $ret) {
                $rowsFinded += $ret;
            }
        }
        
        $this->debug->trace('Search words complete', __CLASS__, __METHOD__, true);
        
        return $rowsFinded;
    }
    
    /**
     * Выполнение SQL запросов к таблице поискового индекса и запись полученных результатов в таблицу результатов поиска для их дальнейшего вывода из этой таблицы.
     * @param string $sql       SQL запрос к таблице поискового индекса
     * @param int $relevance    индекс релевантности
     * @param string $query     обработанный поисковый запрос
     * @return int|false        количество найденных результатов
     */
    protected function searchWordExec($sql, $relevance, $query)
    {
        if (false !== $result = $this->db->query($sql)) {
            $rowsFinded = $result->num_rows;
            if (0 == $rowsFinded) {
                $result->free();
                return 0;
            } else if (self::RAWSEARCH_LIMIT < $rowsFinded) {
                $result->free();
                return false;
            } else if (0 > $rowsFinded) {
                $result->free();
                return false;
            }
            
            $sql = '';
            $cnt = 0;
            while ($row = $result->fetch_assoc()) {
                $sql .= ',(NOW(),' . 
                        $relevance . ',' . 
                        $row['Flag'] . ',' . 
                        $row['ModuleId'] . ',' . 
                        "'" . $query . "'," . 
                        "'" . $this->safeSql($row['Url']) . "'," . 
                        "'" . $this->safeSql($row['Title']) . "'," . 
                        "'" . $this->safeSql($row['MetaDesc']) . "'," . 
                        "'" . $this->safeSql($row['Content']) . "')";
                $cnt++;
                //делаем вставку в таблицу результатов поиска 
                //порциями по RESULTS_INSERT_LIMIT записей
                if (0 == ($cnt % self::RESULTS_INSERT_LIMIT)) {
                    $this->resultsAdd($sql);
                    $sql = '';
                }
            }
            $this->resultsAdd($sql);
            $result->free();
            return $rowsFinded;
        }
        return false;
    }
    
    /**
     * Вставка результатов поиска в таблицу результатов.
     * @param string $results    часть SQL запроса с данными
     * @return boolean
     */
    protected function resultsAdd($results)
    {
        return $this->db->query('INSERT INTO ' . $this->db->getTablePrefix() . 'search_results' .
                                ' (DateCreate,Relevance,Flag,ModuleId,Query,Url,Title,Descr,Content)' .
                                ' VALUES ' . substr($results, 1));
    }
    
    /**
     * Запись поисковых запросов.
     * @param string $query     обработанный поисковый запрос
     * @todo test
     */
    protected function logSearchQuery($query)
    {
        $this->debug->trace('Log search query', __CLASS__, __METHOD__, false);
        
        $prefix = $this->db->getTablePrefix();
        
        //удаляем устаревшие запросы
        $this->db->query('DELETE FROM ' . $prefix . 'search_queries' . 
                         ' WHERE DATEDIFF(dtm, NOW())>' . self::QUERIES_LOG_DAYS);
        
        //добавляем новый запрос (в таблицу текущих запросов)
        $this->db->query('INSERT INTO ' . $prefix . 'search_queries' . 
                         " (dtm, query) VALUES(NOW(), '" . $query . "')");
        
        //проверяем был ли уже такой запрос (в таблице статистики запросов)
        $exists = false;
        $result = $this->db->query('SELECT COUNT(*) AS Cnt' . 
                                   ' FROM ' . $prefix . 'search_queries_stat' . 
                                   " WHERE query='" . $query . "'");
        if (false !== $result) {
            if (0 < $result->num_rows) {
                if ($row = $result->fetch_assoc()) {
                    if (0 < $row['Cnt']) {
                        $exists = true;
                    }
                }
            }
        }
        $result->free();
        
        //добавляем или обновляем статистику запросов
        if ($exists) {
            //получаем количество таких запросов если уже были
            $cnt = 0;
            $result = $this->db->query('SELECT COUNT(*) AS Cnt' . 
                                       ' FROM ' . $prefix . 'search_queries' . 
                                       " WHERE query='" . $query . "'");
            if (false !== $result) {
                if (0 < $result->num_rows) {
                    if ($row = $result->fetch_assoc()) {
                        $cnt = (int) $row['Cnt'];
                    }
                }
            }
            $result->free();
            
            $sql = 'UPDATE ' . $prefix . 'search_queries_stat' . 
                   ' SET dtm=NOW(), cnttmp=' . $cnt . ', cntttl=cntttl+1' . 
                   " WHERE query='" . $query . "'";
        } else {
            $sql = 'INSERT INTO ' . $prefix . 'search_queries_stat' . 
                   ' (dtm, query, cnttmp, cntttl)' . 
                   " VALUES(NOW(), '" . $query . "', 1, 1)";
        }
        $this->db->query($sql);
        
        $this->debug->trace('Log search query complete', __CLASS__, __METHOD__, true);
    }
}
