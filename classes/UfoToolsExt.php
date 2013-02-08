<?php
/**
 * ����� ��������������� ������������.
 *
 * ������������� ������ ��� ���������� ��������� �������� �� �������� � ��������� ������.
 */
trait UfoToolsExt
{
    /**
     * �������� ���������� �������� �� ������������ �����.
     *
     * @param string $str    ����������� ��������
     *
     * @return boolean
     */
    public function isInt($str)
    {
        return ctype_digit((string) $str) && ($str <= PHP_INT_MAX) && ($str > (PHP_INT_MAX * -1));
    }
    
    /**
     * ��������� �������� �� ������ ������ �������� ���� int
     *
     * @param array $arr    ������ ����������� ��������
     *
     * @return boolean
     */
    public function isArrayOfIntegers(array $arr)
    {
        foreach ($arr as $val) {
            if (!$this->isInt($val)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * ��������� �������� �� ������ �������� ���� int ����������� ������������.
     *
     * @param string $str    ������ ��������, ����������� ������������
     * @param string $sep    ������-����������� ��������
     *
     * @return boolean
     */
    public function isStringOfIntegers($str, $sep = ',')
    {
        return $this->isArrayOfIntegers(array_map(function($str) { return trim($str); }, 
                                                  explode($sep, $str)));
    }
    
    /**
     * �������� �� ������������ Email ������.
     *
     * @param string $str    ����������� ��������
     *
     * @return boolean
     */
    public function isEmail($str)
    {
        if (0 == strlen($str)) {
            return false;
        }
        return (bool) preg_match('/[a-z0-9_\-\.]+@[a-z0-9\-\.]{2,}\.[a-z]{2,6}/i', $str);
    }
    
    /**
     * �������������� SQL ��������� � ����������.
     * � ������� ���������� ������ ������������ ��������� ����������� 
     * PHP ������� addslashes.
     *
     * @param string $str    ������������� ������ SQL ���������
     *
     * @return string
     */
    public function safeSql($str)
    {
        return addslashes($str);
    }
    
    /**
     * �������������� JS ��������� � ������.
     * �������������� ������������ �� ���� ������������� 
     * ���� ����. �������� (\, ', \r, \n).
     *
     * @param string  $str                 ������������� ������ JS ���������
     * @param boolean $convertLt = true    ��������������� ����� ������� `<'
     *
     * @return string
     */
    public function jsAsString($str, $convertLt = true)
    {
        if ($convertLt) {
            return str_replace(array("\\", "'", '<', "\r", "\n"),
                               array("\\\\", "\\'", '<!', '\r', '\n'),
                               $str);
        } else {
            return str_replace(array("\\", "'", "\r", "\n"),
                               array("\\\\", "\\'", '\r', '\n'),
                               $str);
        }
    }
    
    /**
     * ������� � ����� ����� ��������� <p> 
     * ������������ �� �������� ����� � �������� ������.
     * �������� ����� ����������� ����� ����������� �����, 
     * ����� ���������� ��������, � ����� ������.
     * ������ ���������, ����������� ��-�� ������ �����, ���������, 
     * ����� ��������� ��������� ������� � �������� ����� (trim).
     *
     * @param string $str            ������������� ������
     * @param string $nl = "\r\n"    ������(�) �������� �����
     *
     * @return string
     */
    public function insertParagraphs($str, $nl = "\r\n")
    {
        if ('' != $nl) {
            return '<p>' . str_replace('<p></p>' . $nl, 
                                       '', 
                                       str_replace($nl, 
                                                   '</p>' . $nl . '<p>', 
                                                   trim($str))) . '</p>';
        } else {
            return '<p>' . trim($str) . '</p>';
        }
    }
    
    /**
     * �������� ����� ��������� �� ������.
     * ����� ������� ������� �������� �����, 
     * ��������� ������� � �������� ����� (trim).
     *
     * @param string $str            ������������� ������
     * @param string $nl = "\r\n"    ������(�) �������� �����
     *
     * @return string
     */
    public function removeParagraphs($str, $nl = "\r\n")
    {
        if ('' != $nl) {
            return trim(preg_replace('/(' . addcslashes($nl, '\0\r\n\f\v\t') . '){2}/', 
                                     $nl, 
                                     str_replace(array('<p>', '</p>'), 
                                                 array('', $nl), 
                                                 $str)));
        } else {
            return trim(str_replace(array('<p>', '</p>'), 
                                    '', 
                                    $str));
        }
    }
    
    /**
     * ��������� ������� ��������� ������.
     * ���� ����� ������ �� ��������� ������ <p>, 
     * ��� ������� ���������� ������ �������� 
     * (������ � ������ ��������� � ���������� ��������� ������).
     * ����� ���������� ������ ������.
     *
     * @param string $str    �������� �����
     *
     * @return string
     */
    public function getFirstParagraph($str)
    {
        return substr($str, 0, stripos($str, '<p>', 3));
    }
    
    /**
     * ��������� ������� ����������� ������.
     * ����� ��������� �� �������� ������, 
     * ������������ �������������� ��������� �� ���������� �������.
     *
     * @param string  $str              �������� �����
     * @param int     $length           ������ ����������� ������
     * @param int     $offset = 0       �������� �� ������ ������
     * @param boolean $offset = true    �������� ����� �� ����� ����� ��������
     *
     * @return string
     */
    public function cutNice($str, 
                            $length, 
                            $offset = 0, 
                            $removeTags = true)
    {
        if (0 >= $length || 0 > $offset || '' == $str) {
            return '';
        }
        if ($removeTags) {
            $str = strip_tags($str);
        }
        $strLength = strlen($str);
        if ($strLength <= $offset) {
            return '';
        }
        return (($strLength > $length) 
                ? substr($str, 
                         $offset, 
                         strrpos(substr($str, $offset, $length), ' ')) 
                : trim($str));
    }
    
    /**
     * ��������� ������ �������� ������ �� ���������, 
     * ������� �� ������ �������� � ������� ���������� �� ��������.
     *
     * @param string $str                �������� �����
     * @param int    $length             ������ ����������� ������
     * @param string $cutStub = '...'    ��� ����� �������� ���������� �����
     *
     * @return string
     */
    public function cutMiddle($str, $length, $cutStub = '...')
    {
        $strLength = strlen($str);
        $cutStubLength = strlen($cutStub);
        if ($length >= $strLength) {
            return $str;
        } else if ($length < $cutStubLength) {
            return '';
        }
        $left = ceil($length / 2);
        $right = $strLength - $left;
        if (0 != ($cutStubLength % 2)) {
            $right++;
            if (0 != ($length % 2)) {
                $left--;
            }
        } else if (0 != ($length % 2)) {
            $right++;
        }
        $cutStubHalfLength = floor($cutStubLength / 2);
        return substr($str, 0, $left - $cutStubHalfLength)
               . $cutStub
               . substr($str, $right + $cutStubHalfLength);
    }
    
    /**
     * ��������� ������ �� �������-����������� � ������.
     * ������������ ������ ��� ��������� ������ �� �����������,
     * ��������� ����������� ����� ���� ������ � ����, 
     * ����� ����� ����������� ����� ���� ������������
     * ��� ������������ ����������� �� ����� � ������
     * (�� �� ���� �������, � � �������� ������),
     * ������� ���� ��������� ����� ����������� ��� 
     * � �������� ����� ����� ����,
     * ���� ���������� �������� $more = true,   
     * ������� �� ����������� ��������� ��������� ����� 
     * �� ���������� ������� ������������ ����
     *
     * @param string  $str             �������� �����
     * @param string  $separator       �����������, �� �������� ���������� ���������
     * @param boolean $more = false    ���������� ��������� ��������� � ������������� ���������, ������ ����� ����������� �� �������� � ������
     *
     * @return string|false
     */
    public function cutBySeparator($str, 
                                   $separator, 
                                   $more = false)
    {
        $pos = strpos($str, $separator);
        if (false === $pos) {
            return $str;
        }
        
        $separatorLength = strlen($separator);
        $left = substr($str, $pos - 1, 1);
        $left2 = substr($str, $pos - 2, 1);
        $right = substr($str, $pos + $separatorLength, 1);
        $right2 = substr($str, $pos + $separatorLength + 1, 1);
        
        /* DEBUG echo "\r\n<br />" . 'left2: ' . $left2 . '; left: ' . $left . '; right: ' . $right . '; right2: ' . $right2 . "<br />\r\n"; */
        
        //���� ����� ����� ����������� ���, ������ ����������� ���, �������� �� ������ ����
        if ('>' == $left && '/' != $left2 && '<' == $right && '/' == $right2) {
            $str = substr($str, 0, $pos);
            $pos2 = strrpos($str, '<');
            if (false === $pos2) {
                return false;
            }
            /* DEBUG echo "\r\n<br />" . 'pos2: ' . $pos2 . "<br />\r\n"; */
            return substr($str, 0, $pos2);
            
        //���� ������ ����������� ���, �������� ���
        } else if ('<' == $right && '/' == $right2) {
            $pos2 = strpos($str, '>', $pos + $separatorLength);
            if (false === $pos2) {
                return false;
            }
            /* DEBUG echo "\r\n<br />" . 'pos2: ' . $pos2 . "<br />\r\n"; */
            return str_replace($separator, '', substr($str, 0, $pos2 + 1));
            
        //���� ����� ��������� �������� ��� (<img />, <br />, ...), �������� ���
        } else if ('>' == $left && '/' == $left2) {
            return substr($str, 0, $pos);
            
        } else {
            $strLeft = substr($str, 0, $pos);
            /* DEBUG echo "\r\n<br />" . 'strLeft: ' . htmlspecialchars($strLeft) . "<br />\r\n"; */
            
            //���� ����� ���, ������� ����� ��� ��� - ����������� ��� �����������
            if ('>' == $left) {
                $pos1 = strrpos($strLeft, '<');
                $pos2 = strrpos($strLeft, '</');
                
                if (false === $pos1) {
                    return false;
                }
                
                //���� ��� ����� �����������, �������� ����� ����� ����
                if ($pos1 == $pos2) {
                    return $strLeft;
                //���� ����� ����������� ���, �������� ��������������� ����� ���
                } else {
                    /* DEBUG echo "\r\n<br />" . 'pos2: ' . $pos2 . "<br />\r\n"; */
                    return substr($str, 0, $pos1);
                }
                
            //���� ����������� � ������ � �� �������� � ������
            } else {
                //���� ��������� ����������� ��� ����� � �������� �� ����
                if (!$more) {
                    $pos2 = strrpos($strLeft, '</');
                    if (false !== $pos2) {
                        $pos2 = strpos($strLeft, '>', $pos2 + 2);
                        if (false === $pos2) {
                            return false;
                        }
                        return substr($strLeft, 0, $pos2 + 1);
                    } else {
                        //����������� ����� ����� ���, ��������� ���� �� ������ ����� ����
                        $pos2 = strrpos($strLeft, '<');
                        //���� ���, ���������� ����� �����
                        if (false === $pos2) {
                            return $strLeft;
                        //����� ���������� ������ ������
                        } else {
                            return '';
                        }
                    }
                    
                //���� ��������� ����������� ��� ������, �������� ���
                } else {
                    $strRight = substr($str, $pos + $separatorLength);
                    /* DEBUG echo "<hr width=\"50%\">\r\nstrRight: " . htmlspecialchars($strRight) . "<br />\r\n"; */
                    $pos2 = strpos($strRight, '</');
                    if (false !== $pos2) {
                        $pos2 = strpos($strRight, '>', $pos2 + 2);
                        if (false === $pos2) {
                            return false;
                        }
                        return $strLeft . substr($strRight, 0, $pos2 + 1);
                    } else {
                        //����� ������ ���, ������ ���������� ����� �����
                        return $strLeft;
                    }
                }
            }
        }
    }
    
    /**
     * ��������� ������ �� ����� �� �������-����������� � ������ 
     * � ������ ������������� �����.
     * ���������� ������� cutBySeparator
     *
     * @param string  $str             �������� �����
     * @param string  $separator       �����������, �� �������� ���������� ���������
     * @param int     $part            ����� ������������� ����� (��������� � ����)
     * @param boolean $more = false    ���������� ��������� ��������� � ������������� ���������, ������ ����� ����������� �� �������� � ������
     *
     * @return string|false
     */
    public function getTextPartBySeparator($str, 
                                           $separator, 
                                           $part = 0, 
                                           $more = false)
    {
        if (0 > $part) {
            return false;
        }
        $arr = explode($separator, $str);
        if (false === $arr) {
            return false;
        }
        $arrCount = count($arr);
        if ($arrCount <= $part) {
            return false;
        }
        
        //���� ������ ���� �������, ���������� ��� �������
        if (1 == $arrCount) {
            return $str;
        }
        
        //��������� �������, ���� ����������� ��� �� ������������ 
        //� ���� �������, �������� ����� ����� ����
        if (($arrCount - 1) == $part) {
            $str = $arr[$part];
            unset($arr);
            
            $pos = strpos($str, '<');
            $pos2 = strpos($str, '</');
            //���� ������ ������������� ��� �������� �����������
            //�������� ����� ����� ����
            if (false !== $pos2 && $pos2 == $pos) {
                $pos2 = strpos($str, '>', $pos2 + 2);
                if (false === $pos2) {
                    return false;
                }
                return substr($str, $pos2 + 1);
            } else {
                return $str;
            }
            
        //�������� ������ � ��������� �� ��� �������� � ��������� ������� api_GetCutTextBySeparator
        } else {
            $str = $arr[$part] . $separator . $arr[$part + 1];
            unset($arr);
            
            return $this->cutBySeparator($str, $separator, $more);
        }
    }
    
    /**
     * ��������� �������� �������� src �� ���� <img>.
     *
     * @param string $str    �������� �����
     *
     * @return string|false
     */
    public function srcFromImg($str)
    {
        $pos = stripos($str, 'src=');
        if (false === $pos) {
            return false;
        }
        
        $str = substr($str, $pos + 4);
        $pos = strpos($str, ' ');
        if (false !== $pos) {
            $str = substr($str, 0, $pos);
        } else {
            $pos = strpos($str, '>');
            if (false === $pos) {
                return false;
            }
            $str = substr($str, 0, $pos - 1);
            if ('/' == substr($str, strlen($str) - 2, 1)) {
                $str = substr($str, 0, strlen($str) - 2);
            }
        }
        
        $str = str_replace(array('"', "'"), '', $str);
        return $str;
    }
    
    /**
     * ���������� ����� � ����� ����� ��� ������ �� �������� ������.
     *
     * @param string|int $num            �������� �����
     * @param int        $digitsTotal    ����� ���������� �������� � �������� ������ (���������� �������� � �������� "�����")
     * @param boolean    $left = true    ��������� ���� ����� (true) ��� ������ (false)
     *
     * @return string
     */
    public function appendDigits($num, $digitsTotal, $left = true)
    {
        return str_pad((string) $num, 
                       $digitsTotal, 
                       '0', 
                       $left ? STR_PAD_LEFT : STR_PAD_RIGHT);
    }
    
    /**
     * ���������, ����� �� ����������������� ������ ������������ ����/�����.
     * 
     * @param string $str                  �������� ������ � �����
     * @param string $formst = 'Y-m-d|'    ������ ����
     * 
     * @return boolean
     */
    public function isDate($str, $format = 'Y-m-d|')
    {
        if (false === $dtm = DateTime::createFromFormat($format, $str)) {
            return false;
        }
        if (false === $str = $dtm->format('Y-m-d')) {
            return false;
        }
        $arr = explode('-', $str);
        if (3 != count($arr)) {
            return false;
        }
        return checkdate($arr[1], $arr[2], $arr[0]);
    }
    
    /**
     * ���������� ������ ����/������� �� ����������������� ������.
     * 
     * @param string $str                  �������� ������ � �����
     * @param string $formst = 'Y-m-d|'    ������ ����
     * 
     * @return DateTime
     */
    public function dateFromString($str, $format = 'Y-m-d|')
    {
        if (false !== $dtm = DateTime::createFromFormat($format, $str)) {
            return $dtm;
        }
        return null;
    }
}
