<?php
require_once 'UfoModuleParams.php';
/**
 * јбстрактный класс-структура дл€ определени€ и хранени€ параметров интерактивного модул€.
 * «десь определены общие пол€, которые должны быть определены независимо от конкретного модул€.
 *
 * @author enikeishik
 * 
 */
abstract class UfoInteractiveModuleParams extends UfoModuleParams
{
    /**
     * ¬ыполн€емое действие (0 - выдача данных, 1 - добавление элемента и т.п.).
     * @var int
     */
    public $action = 0;
}
