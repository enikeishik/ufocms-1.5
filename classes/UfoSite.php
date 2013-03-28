<?php
require_once 'exceptions/UfoExceptionPathBad.php';
require_once 'exceptions/UfoExceptionPathComplex.php';
require_once 'exceptions/UfoExceptionPathEmpty.php';
require_once 'exceptions/UfoExceptionPathFilenotexists.php';
require_once 'exceptions/UfoExceptionPathNotexists.php';
require_once 'exceptions/UfoExceptionPathUnclosed.php';
/**
 * Класс сайта.
 * Предоставляет методы для доступа к параметрам сайта, пути и параметрам пути.
 * 
 * @author enikeishik
 *
 */
class UfoSite
{
    use UfoTools;
    
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
     * @var UfoCoreDbModel
     */
    protected $coreDbModel = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    protected $siteParams = array();
    
    protected $path = '';
    protected $pathRaw = '';
    protected $pathParams = array();
    
    /**
     * Конструктор.
     * @param string $pathRaw             необработанный путь текущего раздела
     * @param string $pathSystem          путь служебного раздела, служебные разделы не присуствуют в БД
     * @param UfoContainer &$container    ссылка на объект-хранилище ссылок на объекты
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists, UfoExceptionPathComplex, UfoExceptionPathNotexists
     */
    public function __construct($pathRaw, $pathSystem, UfoContainer &$container)
    {
        $this->config =& $container->getConfig();
        $this->db =& $container->getDb();
        $this->coreDbModel =& $container->getCoreDbModel();
        
        if ($arr = $this->coreDbModel->getSiteParams()) {
            foreach ($arr as $param) {
                switch($param['PType']) {
                    default:
                        $this->siteParams[$param['PName']] = $param['PValue'];
                }
            }
        }
        
        $this->pathRaw = $pathRaw;
        if ('' == $pathSystem) {
            $this->parsePath();
        } else {
            $this->parsePathSystem($pathSystem);
        }
        if ($this->path != $this->pathRaw) {
            $this->pathParams = explode('/', 
                                        substr($this->pathRaw, 
                                               strlen($this->path), 
                                               -1));
        }
    }
    
    /**
     * Получение значения параметра сайта по имени.
     * @param string $name           имя параметра
     * @param mixed $default = ''    значение по-умолчанию
     * @return mixed
     */
    public function getSiteParam($name, $default = '')
    {
        return (array_key_exists($name, $this->siteParams) ? $this->siteParams[$name] : $default);
    }
    
    /**
     * Возвращает ассоциативный массив параметров сайта.
     * @return array
     */
    public function getSiteParams()
    {
        return $this->siteParams;
    }
    
    /**
     * Возвращает путь с параметрами.
     * @return string
     */
    public function getPathRaw()
    {
        return $this->pathRaw;
    }
    
    /**
     * Возвращает часть пути, соответствующую пути раздела, без параметров.
     * @return string
     */
    public function getPathParsed()
    {
        return $this->path;
    }
    
    /**
     * Возвращает массив параметров, оставшихся в URL после отделения пути раздела.
     * @return array
     */
    public function getPathParams()
    {
        return $this->pathParams;
    }
    
    /**
     * Проверка пути на допустимость.
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists
     */
    protected function checkPath()
    {
        $path = $this->pathRaw;
        
        //main page
        if ('/' == $path) {
            $this->path = $path;
            return;
        }
        if ('' == $path) {
            throw new UfoExceptionPathEmpty('Path empty, main page redirect required');
        }
        
        //если в пути есть недопустимые символы, вызываем ошибку 404
        if (!$this->isPath($path, false)) {
            throw new UfoExceptionPathBad('Bad path');
        }
        
        //дополняем путь правым слешем по необходимости
        //если не имеется правого слеша и есть точка, значит запрашивается файл, выдаем 404
        //иначе добавляем слеш и перенаправляем на скорректированный адрес
        if ('/' != $path{strlen($path) - 1}) {
            if (false === strpos($path, '.')) {
                $path .= '/';
                $this->pathRaw .= '/';
                //перенаправляем только если нет POST запроса, иначе обрабатываем
                if (!isset($_SERVER['REQUEST_METHOD'])) {
                    throw new UfoExceptionPathUnclosed('Closing slash omitted');
                    return;
                } else if (0 != strcasecmp('POST', $_SERVER['REQUEST_METHOD'])) {
                    throw new UfoExceptionPathUnclosed('Closing slash omitted');
                    return;
                }
            } else {
                throw new UfoExceptionPathFilenotexists('Asking for a file which not exists');
                return;
            }
        }
    }
    
    /**
     * Разбор необработанного пути раздела на путь раздела и параметры.
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists, UfoExceptionPathComplex, UfoExceptionPathNotexists
     */
    protected function parsePath()
    {
        $this->checkPath();
        
        $path = $this->pathRaw;
        
        //определяем присутствует ли путь в БД
        if ($this->coreDbModel->isPathExists($path)) {
            $this->path = $path;
        //если нет, разбиваем путь по слэшам, чтобы вычленить параметры в пути
        } else {
            //массив частей пути
            $pathParts = explode('/', $path);
            //убираем крайние слэши, чтобы не было лишних элементов в массиве
            array_shift($pathParts);
            array_pop($pathParts);
            $pathPartsCount = count($pathParts);
        
            //если вложенность больше допустимой, выходим
            if ($this->config->pathNestingLimit < $pathPartsCount) {
                //вызываем ошибку 404
                throw new UfoExceptionPathComplex('Path is too complex');
                return;
            }
            
            //собираем массив вложенных путей
            $paths = array('/');
            for ($i = 0; $i < $pathPartsCount; $i++) {
                $paths[$i + 1] = $paths[$i] . $pathParts[$i] . '/';
            }
            //убираем корневой путь, поскольку корневая (главная) страница всегда присутствует
            array_shift($paths);
            
            if (!$this->path = $this->coreDbModel->getMaxExistingPath($paths)) {
                throw new UfoExceptionPathNotexists('Path not exists');
                return;
            }
        }
    }
    
    /**
     * Разбор необработанного пути раздела на путь раздела и параметры.
     * @param string $pathSystem    путь служебного раздела, без параметров
     * @throws UfoExceptionPathEmpty, UfoExceptionPathBad, UfoExceptionPathUnclosed, UfoExceptionPathFilenotexists, UfoExceptionPathComplex, UfoExceptionPathNotexists
     */
    protected function parsePathSystem($pathSystem)
    {
        $this->checkPath();
        
        $this->path = $pathSystem;
    }
}
