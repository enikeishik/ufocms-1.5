<?php
/**
 * Класс описывающий раздел сайта.
 *
 * Предоставляет доступ к свойствам раздела сайта 
 * и ряд вспомогательных методов.
 */
class UfoSection
{
    
    public $id = 0;
    public $topId = 0;
    public $parentId = 0;
    public $orderId = 0;
    public $levelId = 0;
    public $isParent = false;
    public $moduleId = 0;
    public $designId = 0;
    public $mask = '';
    public $path = '';
    public $image = '';
    public $timage = '';
    public $indic = '';
    public $title = '';
    public $metaDesc = '';
    public $metaKeys = '';
    public $isEnabled = false;
    public $inSearch = false;
    public $inMenu = false;
    public $inLinks = false;
    public $inMap = false;
    public $shTitle = 0;
    public $shMenu = 0;
    public $shLinks = 0;
    public $shComments = 0;
    public $shRating = 0;
    public $flSearch = 0;
    public $flCache = 0;
    
    public function __construct()
    {
        
    }
    
    public function getParent()
    {
        return UfoCore::getSectionById(this.parentId);
    }
    
    public function getChildren()
    {
        return UfoCore::getSectionsByParentId(this.id);
    }
    
    public function getNeighbors()
    {
        return UfoCore::getSectionsByParentId(this.parentId);
    }
    
    public function getTop()
    {
        return UfoCore::getSectionById(this.topId);
    }
    
    public function getParents($reversed = true)
    {
        $arr = array();
        $parent = UfoCore::getSectionById(this.parentId);
        while ($parent) {
            $arr[] = $parent;
            $parent = UfoCore::getSectionById($parent.parentId);
        }
        if ($reversed) {
            return array_reverse($arr);
        } else {
            return $arr;
        }
    }
}
