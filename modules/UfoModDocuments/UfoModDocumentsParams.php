<?php
require_once 'classes/abstract/UfoStruct.php';
/**
 *  ласс-структура дл€ хранени€ данных модул€.
 * @author enikeishik
 */
class UfoModDocumentsParams extends UfoStruct
{
    /**
     * Ќомер страницы при постраничном выводе.
     * @var int
     */
    public $page = 1;
}
