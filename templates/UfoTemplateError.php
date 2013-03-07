<?php
require_once 'classes/abstract/UfoTemplate.php';
/**
 * ����� �������� ������ ���������� ������� ������.
 * 
 * @author enikeishik
 *
 */
class UfoTemplateError extends UfoTemplate
{
    /**
     * ����� ���������, ������������� � ��������� ���������.
     */
    public function drawHeadTitle()
    {
        echo '<title>' . $this->error->text . '</title>' . "\r\n";
    }
    
    /**
     * ����� ���� �����.
     */
    public function drawMetaTags()
    {
        echo '<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">';
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
        echo '<h1>' . $this->error->text . '</h1>' . "\r\n";
    }
    
    /**
     * ����� ������� ���������� �� ��������.
     * @param array $params = null    ��������� �������, �������������� ������, ������������ ������ ������� �������
     */
    public function drawInsertion(array $params = null)
    {
        
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
