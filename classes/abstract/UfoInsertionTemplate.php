<?php
require_once 'UfoInsertionTemplateInterface.php';
require_once 'classes/UfoToolsExt.php';
/**
 * Абрстрактный класс шаблона вставки, 
 * дочерние классы должны реализовывать 
 * интерфейс UfoInsertionTemplateInterface или быть абстрактными.
 * 
 * @author enikeishik
 *
 */
abstract class UfoInsertionTemplate implements UfoInsertionTemplateInterface
{
    use UfoToolsExt;
}
