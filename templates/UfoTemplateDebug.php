<?php
/**
 * ����� �������� ������ ���������� ������ ���������� ����������.
 * ������ ����� ������������ ��������������� ������� ������� UfoDebug 
 * � �������� ������ ���� ����� drawDebug ��� ���������� ������.
 * ���� ������������ ����� ���������� ���������� ����� ������� 
 * ������������ �������� � ���������� ���� ��������� ��������.
 * 
 * @author enikeishik
 *
 */
class UfoTemplateDebug
{
    /**
     * ������ �� ������ ������������.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * ������ �� ������ �������.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * �����������.
     * @param UfoConfig &$config    ������ �� ������ ������������
     * @param UfoDebug $debug       ������ �� ������ �������
     */
    public function __construct(UfoConfig &$config, UfoDebug &$debug)
    {
        $this->config =& $config;
        $this->debug =& $debug;
    }
    
    /**
     * ����� ���������� ������� (� ����� ��������).
     */
    public function drawDebug()
    {
        echo '<!-- Execution time: ' . $this->debug->getPageExecutionTime() . ' -->' . "\r\n";
        
        if (2 < $this->config->debugLevel) {
            echo '<pre>' . "\r\n";
            $debug = $this->debug->getBuffer();
            foreach ($debug as $debugData) {
                $data = (array) $debugData;
                $blockEnd = false;
                foreach ($data as $key => $val) {
                    echo $key . ': ' . $val . "\r\n";
                    if ('blockTime' == $key && 0 != $val) {
                        $blockEnd = true;
                    }
                }
                echo "\r\n";
                if ($blockEnd) {
                    echo str_repeat('-', 72) . "\r\n";
                }
            }
            echo '</pre>' . "\r\n";
        }
        
        echo '<p>Script time: ' . $this->debug->getPageExecutionTime() .
             '; SQL queries counter: ' . $this->debug->getDbQueriesCounter() .
             '; memory max usage: ' . $this->debug->getMemoryUsedMax() .
             ' (' . $this->debug->getMemoryUsedTotalMax() . ')</p>' . "\r\n";
        
        if (null != $ds = $this->debug->getBlockMaxExecutionTime()) {
            echo '<p>Block with max execition time: ' . $ds . '</p>' . "\r\n";
        }
    }
}
