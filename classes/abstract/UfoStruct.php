<?php
/**
 * ����������� �����-���������, ������������ ��� �������� ������.
 * 
 * @author enikeishik
 *
 */
abstract class UfoStruct
{
    /**
     * ����������� ������-��������� ��������� ��������� ���� �������, 
     * ������������� ����������� �������������� �������, 
     * � ������� ����� ������������� ������ ����� ������.
     * ��� ������������ ����� �������� �������������� ���������� 
     * � ���� ����, ������� ������������ ��������� ���� ��-���������.
     *
     * @param array|object $vars = null    ������������� ������ ��� ������-��������� � �������
     * @param boolean $cast = true         ��������� ��� ���������� � ������������ � ����� �����
     */
    public function __construct($vars = null, $cast = true)
    {
        if (is_array($vars)) {
            $this->setValues($vars, $cast);
        } else if (is_object($vars) && is_a($vars, __CLASS__)) {
            $this->setFields($vars);
        }
    }
    
    public function __toString()
    {
        $out = '';
        $vars = get_object_vars($this);
        foreach ($vars as $key => $val) {
            $out .= "\t" . $key . ': ' . $val;
        }
        return get_class($this) . ' {' . substr($out, 1) . '}';
    }
    
    /**
     * ������������ ����� ��������� ������ �� ������������� �������-���������.
     * @param UfoStruct $struct    ������-���������, ������ �������� ����� �������������
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
     * ������������ ����� ��������� ������ �� ������������� �������������� ������� (����� ������������� ������ �����).
     * @param array $vars             ������������� ������ � �������
     * @param boolean $cast = true    ��������� ��� ���������� � ������������ � ����� �����
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
}
