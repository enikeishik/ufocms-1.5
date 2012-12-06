<?php
require_once 'classes/abstract/UfoTemplate.php';

class UfoTplDocuments extends UfoTemplate
{
    public function getMetaTags()
    {
        
    }
    
    public function getHeadTitle()
    {
        return '<title>' . $this->fields->title . '</title>' . "\r\n";
    }
    
    public function getBodyTitle()
    {
        return '<h1>' . $this->fields->title . '</h1>' . "\r\n";
    }
    
    public function getBodyContent()
    {
        ob_start();
?>
<table border="1" style="width: 100%;">
<tr>
<td width="200">
<?php
$parent = $this->section->getParent();
if (!is_null($parent)) {
    echo $parent->title;
} else {
    echo 'parent not exists';
}
?>
</td>
<td>
<p><?php echo $this->module->getContent(); ?></p>
</td>
</tr>
</table>
<?php
        return ob_get_clean();
    }
    
    public function getDebug()
    {
        if (is_null($this->debug)) {
            return;
        }
        echo '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->';
    }
}
