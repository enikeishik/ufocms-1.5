<?php
require_once 'UfoModule.php';
require_once 'UfoIneractiveModuleInterface.php';
/**
 * Абрстрактный класс интерактивного модуля, обслуживающего раздел, 
 * дочерние классы должны реализовывать интерфейс UfoIneractiveModuleInterface или быть абстрактными.
 * Все классы интерактивных модулей должны наследовать этот класс.
 * 
 * @author enikeishik
 *
 */
abstract class UfoIneractiveModule extends UfoModule implements UfoIneractiveModuleInterface
{
    
}
