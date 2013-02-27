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
     * @param array $vars = null      ������������� ������ � �������
     * @param boolean $cast = true    ��������� ��� ���������� � ������������ � ����� �����
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
