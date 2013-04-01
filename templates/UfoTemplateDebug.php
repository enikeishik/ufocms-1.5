<?php
/**
 * Класс содержит методы оформления вывода отладочной информации.
 * Данный класс используется непосредственно классом отладки UfoDebug 
 * и содержит только один метод drawDebug для оформления вывода.
 * Ядро осуществляет вывод отладочной информации после полного 
 * формирования страницы и выполнения всех служебных процедур.
 * 
 * @author enikeishik
 *
 */
class UfoTemplateDebug
{
    /**
     * Ссылка на объект конфигурации.
     * @var UfoConfig
     */
    protected $config = null;
    
    /**
     * Ссылка на объект отладки.
     * @var UfoDebug
     */
    protected $debug = null;
    
    /**
     * Конструктор.
     * @param UfoConfig &$config    ссылка на объект конфигурации
     * @param UfoDebug $debug       ссылка на объект отладки
     */
    public function __construct(UfoConfig &$config, UfoDebug &$debug)
    {
        $this->config =& $config;
        $this->debug =& $debug;
    }
    
    /**
     * Вывод информации отладки (в конце страницы).
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
