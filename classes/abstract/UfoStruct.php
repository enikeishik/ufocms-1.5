<?php
/**
 * Абстрактный класс-структура, предназначен для хранения данных.
 * 
 * @author enikeishik
 *
 */
abstract class UfoStruct
{
    /**
     * Конструктор класса-структуры позволяет загрузить поля данными, 
     * передаваемыми посредством ассоциативного массива, 
     * в котором ключи соответствуют именам полей класса.
     * При присваивании полям значения предварительно приводятся 
     * к типу поля, которое определяется значением поля по-умолчанию.
     *
     * @param array $vars = null      ассоциативный массив с данными
     * @param boolean $cast = true    приводить тип переменных в соответствие с типом полей
     */
    public function __construct(array $vars = null, $cast = true)
    {
        if (is_null($vars)) {
            return;
        }
        if ($cast) {
            foreach ($vars as $key => $val) {
                if (property_exists($this, $key)) {
                    if (is_int($this->$key)) {
                    	$this->$key = (int) $val;
                    } else if (is_string($this->$key)) {
                    	$this->$key = (string) $val;
                    } else if (is_bool($this->$key)) {
                    	$this->$key = (bool) $val;
                    } else if (is_float($this->$key)) {
                    	$this->$key = (float) $val;
                    } else {
                		$this->$key = $val;
                    }
                }
            }
        } else {
            foreach ($vars as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
    }
}
