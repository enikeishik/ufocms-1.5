<?php
require_once 'classes/abstract/UfoDbModel.php';
/**
 * ����� �������������� ������ ������ � ��.
 * ������������� ������ ��� ���������� ����������� ��� ������� �������� � ��, ����������� ��������� ������.
 * 
 * @author enikeishik
 *
 */
class UfoCoreDbModel extends UfoDbModel
{
    /**
     * ��������� ������ ������� �����.
     * @param mixed $section    ������������� ��� ���� ������� �����
     * @throws Exception
     * @return array|false
     */
    public function getSection($section, $isParentId = false)
    {
        $sql = 'SELECT ' . $this->getSectionFields() . 
               ' FROM ' . $this->db->getTablePrefix() . 'sections' . 
               ' WHERE ';
        if (is_int($section)) {
            $sql .= ($isParentId ? 'parentid' : 'id') . '=' . $section;
        } else if (is_string($section) && '/' == $section) {
            $sql .= 'id=-1';
        } else if (is_string($section) && $this->isPath($section)) {
            $sql .= "path='" . $section . "'";
        } else {
            throw new Exception('Incorrect $section: ' . var_export($section, true));
        }
        if (!$isParentId) {
            return $this->db->getRowByQuery($sql);
        } else {
            return $this->db->getRowsByQuery($sql);
        }
    }
    
    /**
     * ��������� ����� ������ �� ��� ��������������.
     * @param int $moduleId    ������������� ������
     * @return string|false;
     */
    public function getModuleName($moduleId)
    {
        $sql = 'SELECT mfile' . 
               ' FROM ' . $this->db->getTablePrefix() . 'modules' . 
               ' WHERE muid=' . $moduleId;
        if ($row = $this->db->getRowByQuery($sql)) {
            return $row['mfile'];
        }
        return false;
    }
    
    /**
     * ��������� ������ �������.
     * @param int $targetId      ������������� ������� � ������� ��������� �������
     * @param int $placeId       ������������� ����� � ������� ��������� �������
     * @param int $offset = 0    �������� �������� ������� � $offset
     * @param int $limit = 0     ������� ����� $limit ��������� (���� $limit > 0)
     * @return array(string $mfileins, string $path, UfoInsertionItemStruct)|false
     */
    public function getInsertionItems($targetId, $placeId, $offset = 0, $limit = 0)
    {
        $prefix = $this->db->getTablePrefix();
        $sql = '';
        $arr = get_class_vars('UfoInsertionItemStruct');
        foreach ($arr as $fld => $val) {
            $sql .= 'i.`' . $fld . '`,';
        }
        unset($arr);
        $sql = 'SELECT ' . $sql . 's.path,m.mfileins' . 
               ' FROM ' . $prefix . 'insertions AS i' . 
               ' INNER JOIN ' . $prefix . 'sections AS s ON s.id=i.SourceId' . 
               ' INNER JOIN ' . $prefix . 'modules AS m ON m.muid=s.moduleid' . 
               ' WHERE (i.TargetId=' . $targetId . ' OR i.TargetId=0)' . 
               ' AND i.PlaceId=' . $placeId . 
               ' AND s.isenabled!=0 AND m.isenabled!=0' . 
               ' ORDER BY i.OrderId';
        if (0 !=$offset && 0 != $limit) {
            $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        } else if (0 != $limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        $arr = array();
        while ($row = $result->fetch_assoc()) {
            $arr[] = array(new UfoInsertionItemSettings(array('mfileins' => array_pop($row), 'path' => array_pop($row), false)), 
                           new UfoInsertionItemStruct($row, false));
        }
        $result->free();
        return $arr;
    }
    
    /**
     * �������� ������������� ��������� ���� � ��.
     * @param string $path    ����������� ���� (������ �� ����������� �� ������������!)
     * @return boolean
     */
    public function isPathExists($path)
    {
        $sql = 'SELECT COUNT(*) AS Cnt' . 
               ' FROM ' . $this->db->getTablePrefix() . 'sections' .
               " WHERE path='" . $path . "'";
        $row = $this->db->getRowByQuery($sql);
        return 0 < $row['Cnt'];
    }
    
    /**
     * ��������� ����������� ������� ���� �� ����������� ������ �����.
     * @param array $paths    ����������� ����� ����� (������ �� ����������� �� ������������!)
     * @return string|false
     */
    public function getMaxExistingPath(array $paths)
    {
        $sql = 'SELECT path FROM ' . $this->db->getTablePrefix() . 'sections' . 
               " WHERE path IN('" . implode("','", $paths) . "')" . 
               ' ORDER BY path DESC' . 
               ' LIMIT 1';
        if ($row = $this->db->getRowByQuery($sql)) {
            return $row['path'];
        }
        return false;
    }
    
    /**
     * ��������� ������ ���������� �����.
     * @return array|false
     */
    public function getSiteParams()
    {
        $sql = 'SELECT PType,PName,PValue,PDefault' .
               ' FROM ' . $this->db->getTablePrefix() . 'siteparams';
        return $this->db->getRowsByQuery($sql);
    }
    
    /**
     * ��������� ��������� ����������� ������������������ ������������� �����.
     * @param array $fields    ����� �����
     * @return array|false
     */
    public function getUsersSettings(array $fields)
    {
        $sql = 'SELECT ' . implode(',', $fields) .
               ' FROM ' . $this->db->getTablePrefix() . 'users_params';
        return $this->db->getRowByQuery($sql);
    }
    
    /**
     * ��������� ������ �������� ������������.
     * @param string $ticket    ����� ������������
     * @return array|false
     */
    public function getUsersCurrent($ticket)
    {
        $sql = 'SELECT ' . implode(',', $this->item->getFields()) .
        ' FROM ' . $this->db->getTablePrefix() . 'users' .
        " WHERE IsDisabled=0' .
                   ' AND Ticket='" . $this->safeSql($ticket, true) . "'";
        return $this->db->getRowByQuery($sql);
    }
    
    /**
     * ��������� ������ � �������������� ������������ � �������.
     * @param int $userId    ������������� ������������
     * @return array|false
     */
    public function getUserGroups($userId)
    {
        $sql = 'SELECT GroupId' .
               ' FROM ' . $this->db->getTablePrefix() . 'users_groups_relations' .
               ' WHERE UserId=' . $userId;
        return $this->db->getRowsByQuery($sql);
    }
    
    /**
     * �������� ������������� ������������������� ������������ �� ������.
     * @param string $login    ����� ������������
     * @return boolean
     */
        public function isLoginExists($login)
    {
        $sql = 'SELECT COUNT(*) AS Cnt FROM ' . $this->db->getTablePrefix() . 'users' .
               " WHERE Login='" . $this->safeSql($login, true) . "'";
        if ($row = $this->db->getRowByQuery($sql)) {
            return 0 < $row['Cnt'];
        }
        return false;
    }
}