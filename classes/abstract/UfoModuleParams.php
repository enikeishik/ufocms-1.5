<?php
require_once 'UfoStruct.php';
/**
 * јбстрактный класс-структура дл€ определени€ и хранени€ параметров модул€.
 * «десь определены общие пол€, которые должны быть определены независимо от конкретного модул€.
 *
 * @author enikeishik
 * 
 */
abstract class UfoModuleParams extends UfoStruct
{
    /**
     * »дентификатор конкретного элемента.
     * @var int
     */
    public $id = 0;

    /**
     * Ќомер страницы комментариев при постраничном выводе.
     * @var int
     */
    public $comments = 1;

    /**
     * ¬ывод данных в формате RSS.
     * @var boolean
     */
    public $rss = false;
}
