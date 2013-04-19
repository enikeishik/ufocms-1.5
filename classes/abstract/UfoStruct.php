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
     * @param array|object $vars = null    ассоциативный массив или объект-структура с данными
     * @param boolean $cast = true         приводить тип переменных в соответствие с типом полей
     */
    public function __construct($vars = null, $cast = true)
    {
        if (is_array($vars)) {
            $this->setValues($vars, $cast);
        } else if (is_object($vars) && is_a($vars, __CLASS__)) {
            $this->setFields($vars);
        }
    }
    
    /**
     * Волшебный метод, для формирования представления экземпляра класса (объекта) в виде строки.
     * @return string
     */
    public function __toString()
    {
        $out = '';
        $vars = get_object_vars($this);
        foreach ($vars as $key => $val) {
            try {
                if (is_scalar($val)) {
                    $out .= "\t" . $key . ': ' . (string) $val;
                } else if (is_null($val)) {
                    $out .= "\t" . $key . ': <null>';
                } else if (is_array($val)) {
                    $out .= "\t" . $key . ': <array>';
                } else if (is_object($val)) {
                    $out .= "\t" . $key . ': <object>';
                } else if (is_resource($val)) {
                    $out .= "\t" . $key . ': <resource>';
                } else {
                    $out .= "\t" . $key . ': <unknown type>';
                }
            } catch (Exception $e) {
                $out .= "\t" . $key . ': <unconvertable to string>';
            }
        }
        return get_class($this) . ' {' . substr($out, 1) . '}';
    }
    
    /**
     * Присваивание полям структуры данных из передаваемого объекта-структуры.
     * @param UfoStruct $struct    объект-структура, данные которого нужно импортировать
     */
    public function setFields(UfoStruct $struct)
    {
        $vars = get_object_vars($struct);
        foreach ($vars as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }
    
    /**
     * Присваивание полям структуры данных из передаваемого ассоциативного массива (ключи соответствуют именам полей).
     * @param array $vars             ассоциативный массив с данными
     * @param boolean $cast = true    приводить тип переменных в соответствие с типом полей
     */
    public function setValues(array $vars, $cast = true)
    {
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
    
    /**
     * Возвращает ассоциативный массив полей.
     * @return array($key => $value)
     */
    public function getValues()
    {
        return get_object_vars($this);
    }
    
    /**
     * Возвращает массив имен полей.
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->getValues());
    }
}
