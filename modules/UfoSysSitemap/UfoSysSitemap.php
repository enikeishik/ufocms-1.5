<?php
require_once 'classes/abstract/UfoSystemModule.php';
/**
 * Служебный модуль Карта сайта.
 * 
 * @author enikeishik
 *
 */
class UfoSysSitemap extends UfoSystemModule
{
    public function getContent()
    {
        return 'sitemap';
    }
}
