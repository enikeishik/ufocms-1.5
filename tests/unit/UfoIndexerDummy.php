<?php
require_once 'classes/UfoIndexer.php';
/**
 * ����� ��������� ����� UfoIndexer � ������ ��� ���������� ������������ ������ ����������.
 */
class UfoIndexerDummy extends UfoIndexer
{
    public function isIndexExists($url)
    {
        return parent::isIndexExists($url);
    }
    
    public function isIndexChanged($url, $hash)
    {
        return parent::isIndexChanged($url, $hash);
    }
    
    public function getTitle($content)
    {
        return parent::getTitle($content);
    }
    
    public function getMeta($content, $metaname)
    {
        return parent::getMeta($content, $metaname);
    }
}
