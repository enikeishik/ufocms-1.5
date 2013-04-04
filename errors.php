<?php
/**
 * Класс содержащий набор текстовых описаний различных ошибок.
 * 
 * @author enikeishik
 *
 */
final class UfoErrors
{
    /**
     * Ошибка удаления файла.
     * @var string
     */
    public $fsUnlink = 'Can not unlink file %1s';
    
    /**
     * Ошибка открытия папки.
     * @var string
     */
    public $fsOpenDir = 'Can not open dir %1s';
    
    /**
     * Передан пустой путь раздела.
     * @var string
     */
    public $pathEmpty = 'Path empty, main page redirect required';
    
    /**
     * Путь содержит недопустимые символы.
     * @var string
     */
    public $pathBad = 'Bad path';
    
    /**
     * Путь не содержит закрывающего слэша '/'.
     * @var string
     */
    public $pathUnclosed = 'Closing slash omitted';
    
    /**
     * Путь содержит имя файла, который не существует.
     * @var string
     */
    public $pathFileNotExists = 'Asking for a file which not exists';
    
    /**
     * Путь слишком длинный или содержит слишком много уровней вложенности.
     * @var string
     */
    public $pathComplex = 'Path is too complex';
    
    /**
     * Раздела с таким путем не существует.
     * @var string
     */
    public $pathNotExists = 'Path not exists';
    
    /**
     * Не удалось получить данные раздела.
     * @var string
     */
    public $sectionFieldsUnset = 'Fields not set ($section: %1s)';
    
    /**
     * Некоректный раздел (путь, идентификатор или данные).
     * @var string
     */
    public $sectionIncorrect = 'Incorrect $section: %1s';
    
    /**
     * Некоректный идентификатор модуля раздела.
     * @var string
     */
    public $sectionModuleIdIncorrect = 'Incorrect moduleid';
    
    /**
     * Модуль не найден по идентификатору.
     * @var string
     */
    public $sectionModuleNotFound = 'Module not found (module uid: %1s)';
    
    /**
     * Некоректный тип модуля (не является потомком UfoModule).
     * @var string
     */
    public $sectionModuleIncorrect = 'Module class must extends UfoModule abstract class';
    
    /**
     * Класс модуля служебного раздела не определен (служебные разделы и обслуживающие их модули перечислены в конфигурации).
     * @var string
     */
    public $syssectModuleNotDefined = 'Module class not defined';
    
    /**
     * Некоректный тип модуля служебного раздела (не является потомком UfoSystemModule).
     * @var string
     */
    public $syssectModuleIncorrect = 'Module class must extends UfoSystemModule abstract class';
    
    /**
     * Переданный в URL параметр не определен в модуле (класс UfoModuleParams или его потомки).
     * @var string
     */
    public $moduleParamNotDefined = 'Parameter %1s not identified';
}
