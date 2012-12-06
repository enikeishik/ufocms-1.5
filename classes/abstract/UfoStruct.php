<?php
/**
 * Абстрактный класс-структура, предназначен для хранения данных.
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
     * @param array $vars = null    ассоциативный массив с данными
     */
    public function __construct(array $vars = null)
    {
        if (is_null($vars)) {
            return;
        }
        foreach ($vars as $key => $val) {
            if (isset($this->$key)) {
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
    }
}
