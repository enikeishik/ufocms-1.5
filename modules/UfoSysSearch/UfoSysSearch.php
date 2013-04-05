<?php
require_once 'classes/abstract/UfoSystemModule.php';
/**
 * Служебный модуль Карта сайта.
 *
 * @author enikeishik
 *
 */
class UfoSysSearch extends UfoSystemModule
{
    public function getContent()
    {
        if (isset($_GET['q'])) {
            $query = (string) $_GET['q'];
            $this->loadClass('UfoSearch');
            $search = new UfoSearch();
            
            $path = '';
            if (isset($_GET['p'])) {
                $path = (string) $_GET['p'];
            }
            $moduleid = null;
            if (isset($_GET['m'])) {
                $moduleid = (int) $_GET['m'];
                if (0 >= $moduleid) {
                    $moduleid = null;
                }
            }
            $page = 1;
            if (isset($_GET['page'])) {
                $page = (int) $_GET['page'];
                if (0 >= $page) {
                    $page = 1;
                }
            }
            $pageLength = 1;
            if (isset($_GET['psize'])) {
                $pageLength = (int) $_GET['psize'];
                if (0 >= $pageLength) {
                    $pageLength = 1;
                }
            }
            
            $results = $search->getResults($query, $page, $pageLength, $path, $moduleid);
            $resultsCount = $search->getResultsCount($query, $path, $moduleid);
        }
        
    }
}
