<?php
require_once 'classes/abstract/UfoTemplate.php';
/**
 * ����� �������� ������� ������ ���������� ������.
 * ��� ������ �������� ������� ����� ����������� ���� �����.
 * ������ ������ ����� ���� �������������� � �������� �������
 * ��� ���������� �������������� ������.
 * 
 * @author enikeishik
 *
 */
abstract class UfoTemplateGlobal extends UfoTemplate
{
    /**
     * ����� ���������, ������������� � ��������� ���������.
     */
    public function drawHeadTitle()
    {
        echo '<title>' . $this->sectionFields->title . '</title>' . "\r\n";
    }
    
    /**
     * ����� ���� �����.
     */
    public function drawMetaTags()
    {
    
    }
    
    /**
     * ����� ��������������� ���� (JS, CSS, ...) � ��������� ���������.
     */
    public function drawHeadCode()
    {
    
    }
    
    /**
     * ����� ���������, ������������� �� ��������.
     */
    public function drawBodyTitle()
    {
        echo '<h1>' . $this->sectionFields->title . '</h1>' . "\r\n";
    }
    
    /**
     * ����� ������� ���������� �� ��������.
     * @param array $params = null    ��������� �������, �������������� ������, ������������ ������ ������� �������
     */
    public function drawInsertion(array $params = null)
    {
        echo $this->core->insertion($params);
    }
    
    /**
     * ����� ���������� ������� (� ����� ��������, � ���� ����������� HTML).
     */
    public function drawDebug()
    {
        if (is_null($this->debug)) {
            return;
        }
        echo '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->' . "\r\n";
    }
}
