<?php
// 模型类基类
class Model
{

    protected $db;
 // 数据库连接对象
    protected $table;
 // 表名
    protected $fields = array();
 // 字段列表
    public function __construct($table)
    {
        $table= str_replace($GLOBALS['config']['prefix'],"" ,$table);
        $dbconfig['host'] = $GLOBALS['config']['host'];
        $dbconfig['user'] = $GLOBALS['config']['user'];
        $dbconfig['password'] = $GLOBALS['config']['password'];
        $dbconfig['dbname'] = $GLOBALS['config']['dbname'];
        $dbconfig['port'] = $GLOBALS['config']['port'];
        $dbconfig['charset'] = $GLOBALS['config']['charset'];
        
        $this->db = new Mysql($dbconfig);
        $this->table = $GLOBALS['config']['prefix'] . $table;
        // 调用getFields字段
        $this->getFields();
    }

    /**
     * 获取表字段列表
     */
    private function getFields()
    {
        $sql = "DESC " . $this->table;
        $result = $this->db->getAll($sql);
        foreach ($result as $v) {
            $this->fields[] = $v['Field'];
            if ($v['Key'] == 'PRI') {
                // 如果存在主键的话，则将其保存到变量$pk中
                $pk = $v['Field'];
            }
        }
        // 如果存在主键，则将其加入到字段列表fields中
        if (isset($pk)) {
            $this->fields['pk'] = $pk;
        }
    }

    /**
     * 获取表字段和类型列表
     */
    public function getFieldsAndTypes()
    {
        $sql = "SELECT ORDINAL_POSITION as id , column_name ,data_type ,character_maximum_length ,numeric_precision,numeric_scale,is_nullable ,column_default ,column_comment
                   FROM
                Information_schema.columns 
                   WHERE TABLE_NAME='{$this->table}'";
        $result = $this->db->getAll($sql);
        /*
         * foreach ($result as $v) {
         * $this->fieldsandtpyes[]=array("{$v['Field']}"=>array("type"=>"{$v['Type']}"));
         *
         * }
         */
        return $result;
    }

    /**
     * 根据传递的参数，返回接收数组
     */
    public function getFieldArray()
    {
        $fields_temp = $this->fields;
        $fields_temp = array_unique($fields_temp);
        foreach ($fields_temp as $k => $v) {
            if (isset($_REQUEST[$v]) && $_REQUEST[$v] != '') {
                $data[$v] = $_REQUEST[$v];
            }
        }
        
        return $data;
    }

    /**
     * 根据传递的参数，自动获得SQL的条件语句
     */
    public function getSqlWhereStr()
    {
        $sql="";
        $fields_temp = $this->fields;
        $fields_temp = array_unique($fields_temp);
        foreach ($fields_temp as $k => $v) {
            if (isset($_REQUEST[$v]) && $_REQUEST[$v] != '') {
                $sql = $sql . $v . "='{$_REQUEST[$v]}' and ";
            }
        }
        $sql .= " 1=1 ";
        return $sql;
    }

    public function getDtimeSql($time_str = 'dtime')
    {
        $sql=" ";
        if (isset($_REQUEST['dtime_str']) && $_REQUEST['dtime_str'] != '') {
            $dtime_str = $_REQUEST['dtime_str']."  00:00:00";
        }
        if (isset($_REQUEST['dtime_end']) && $_REQUEST['dtime_end'] != '') {
            $dtime_end = $_REQUEST['dtime_end']."  23:59:59";
        }
        if ((isset($dtime_str) && $dtime_str != '') && (! isset($dtime_end) && $dtime_end == '')) {
            $sql = " dtime >= '{$dtime_str}' and  dtime <= NOW() ";
        } else 
            if ((isset($dtime_str) && $dtime_str != '') && (isset($dtime_end) && $dtime_end != '')) {
                $sql = "  {$time_str} >= '{$dtime_str}' and  dtime <= '{$dtime_end}'  ";
            }
        return $sql;
    }

    /**
     * 自动插入记录[单条]
     * 
     * @access public
     * @param $list array
     *            关联数组
     * @return mixed 成功返回插入的id，失败则返回false
     */
    public function insert($list)
    {
        $field_list = ''; // 字段列表字符串
        $value_list = ''; // 值列表字符串
        foreach ($list as $k => $v) {
            if (in_array($k, $this->fields)) {
                $field_list .= "`" . $k . "`" . ',';
                $value_list .= "'" . $v . "'" . ',';
            }
        }
        // 去除右边的逗号
        $field_list = rtrim($field_list, ',');
        $value_list = rtrim($value_list, ',');
        // 构造sql语句
        $sql = "INSERT INTO `{$this->table}` ({$field_list}) VALUES ($value_list)";
        
        if ($this->db->query($sql)) {
            // 插入成功,返回最后插入的记录id
            return $this->db->getInsertId();
            // return true;
        } else {
            // 插入失败，返回false
            return false;
        }
    }

    /**
     * 自动更新记录
     * 
     * @access public
     * @param $list array
     *            需要更新的关联数组
     * @return mixed 成功返回受影响的记录行数，失败返回false
     */
    public function update($list)
    {
        $uplist = ''; // 更新列表字符串
        $where = 0; // 更新条件,默认为0
        foreach ($list as $k => $v) {
            if (in_array($k, $this->fields)) {
                if ($k == $this->fields['pk']) {
                    // 是主键列，构造条件
                    $where = "`$k`=$v";
                } else {
                    // 非主键列，构造更新列表
                    $uplist .= "`$k`='$v'" . ",";
                }
            }
        }
        // 去除uplist右边的
        $uplist = rtrim($uplist, ',');
        // 构造sql语句
        $sql = "UPDATE `{$this->table}` SET {$uplist} WHERE {$where}";
       
        if ($this->db->query($sql)) {
            // 成功，并判断受影响的记录数
            if ($rows = mysql_affected_rows()) {
                // 有受影响的记录数
                return $rows;
            } else {
                // 没有受影响的记录数，没有更新操作
                return false;
            }
        } else {
            // 失败，返回false
            return false;
        }
    }

    /**
     * 自动删除
     * 
     * @access public
     * @param $pk mixed
     *            可以为一个整型，也可以为数组
     * @return mixed 成功返回删除的记录数，失败则返回false
     */
    public function delete($pk)
    {
        $where = 0; // 条件字符串
                    // 判断$pk是数组还是单值，然后构造相应的条件
        if (is_array($pk)) {
            // 数组
            $where = "`{$this->fields['pk']}` in (" . implode(',', $pk) . ")";
        } else {
            // 单值
            $where = "`{$this->fields['pk']}`=$pk";
        }
        // 构造sql语句
        $sql = "DELETE FROM `{$this->table}` WHERE $where";
        
        if ($this->db->query($sql)) {
            // 成功，并判断受影响的记录数
            if ($rows = mysql_affected_rows()) {
                // 有受影响的记录
                return $rows;
            } else {
                // 没有受影响的记录
                return false;
            }
        } else {
            // 失败返回false
            return false;
        }
    }

    /**
     * 通过SQL语句直接查询
     */
    public function select($sql)
    {
        return $this->db->getAll($sql);
    }
    
    /*
     *执行SQL语句不返回数据 
     * 
     */
    public function query($sql) {
         return $this->db->query($sql);
    }

    /**
     * 通过主键获取信息
     * 
     * @param $pk int
     *            主键值
     * @return array 单条记录
     */
    public function selectByPk($pk)
    {
        $sql = "select * from `{$this->table}` where `{$this->fields['pk']}`=$pk";
        return $this->db->getRow($sql);
    }

    /**
     * 通过非主键获取信息
     * 
     * @param $pk int
     *            主键值
     * @return array 单条记录
     */
    public function selectByCol($Col, $value)
    {
        $sql = "select * from `{$this->table}` where `$Col`='$value'";
        return $this->db->getRow($sql);
    }

    /**
     * 通过多个关键词 模糊查询
     * 
     * @param [type] $Col
     *            [description]
     * @param [type] $value
     *            [description]
     * @return [type] [description]
     */
    public function selectByArray($arr)
    {
        $sql = "select * from `{$this->table}`  ";
        if (count($arr) > 0) {
            $sql .= " where ";
            foreach ($arr as $key => $value) {
                $sql .= " {$key} like '%{$value}%' or ";
            }
            $sql .= " 1=2 ";
        }
        return $this->db->getAll($sql);
    }

    /**
     * 通过多个关键词 直接查询 or并存
     * 
     * @param [type] $Col
     *            [description]
     * @param [type] $value
     *            [description]
     * @return [type] [description]
     */
    public function selectByArrayOr($arr)
    {
        $sql = "select * from `{$this->table}`  ";
        if (count($arr) > 0) {
            $sql .= " where ";
            foreach ($arr as $key => $value) {
                $sql .= " {$key} = '{$value}' or ";
            }
            $sql .= " 1=2 ";
        }
        return $this->db->getAll($sql);
    }

    /**
     * 通过多个关键词 直接查询 and并存
     * 
     * @param [type] $Col
     *            [description]
     * @param [type] $value
     *            [description]
     * @return [type] [description]
     */
    public function selectByArrayAnd($arr)
    {
        $sql = "select * from `{$this->table}`  ";
        if (count($arr) > 0) {
            $sql .= " where ";
            foreach ($arr as $key => $value) {
                $sql .= " {$key} = '{$value}' and ";
            }
            $sql .= " 1=1 order by {$this->fields['pk']} desc";
        }
       
        return $this->db->getAll($sql);
    }

    /**
     * 获取总的记录数
     * 
     * @param string $where
     *            查询条件，如"id=1"
     * @return number 返回查询的记录数
     */
    public function total($where)
    {
        if (empty($where)) {
            $sql = "select count(*) from {$this->table}";
        } else {
            $sql = "select count(*) from {$this->table} where $where";
        }
        return $this->db->getOne($sql);
    }

    /**
     * 获取一列的值
     * 
     * @param string $where
     *            查询条件，如"id=1"
     * @return number 返回查询的记录数
     */
    public function oneRowCol($col, $where)
    {
        if (empty($where)) {
            $sql = "select $col from {$this->table}";
        } else {
            $sql = "select $col from {$this->table} where $where";
        }
        return $this->db->getRow($sql);
    }

    /**
     * 分页获取信息
     * 
     * @param $offset int
     *            偏移量
     * @param $limit int
     *            每次取记录的条数
     * @param $where string
     *            where条件,默认为空
     */
    public function pageRows($offset, $limit, $where = '', $orderby = 'id')
    {
        if($orderby!="id")
        {
            $temp_orderby=$orderby ." desc,";
        }else 
        {
            $temp_orderby="";
        }
        
        if (empty($where)) {
            $sql = "select * from {$this->table} " . "  order by $temp_orderby id desc  " . " limit $offset, $limit";
        } else {
            $sql = "select * from {$this->table}  where $where " . "  order by  $temp_orderby id desc " . " limit $offset, $limit";
        }
        
        return $this->db->getAll($sql);
    }
    
    // 对给定的数组进行重新排序
    public function tree($arr, $pid = 0, $level = 0)
    {
        static $res = array();
        foreach ($arr as $v) {
            if ($v['classid'] == $pid) {
                // 说明找到，先保存
                $v['level'] = $level;
                $res[] = $v;
                // 改变条件，递归查找
                $this->tree($arr, $v['id'], $level + 1);
            }
        }
        return $res;
    }
    
    // 将平行的二维数组，转成包含关系的多维数组
    public function child($arr, $pid = 0)
    {
        $res = array();
        foreach ($arr as $v) {
            if ($v['classid'] == $pid) {
                $v['child'] = $this->child($arr, $v['id']);
                $res[] = $v;
            }
        }
        return $res;
    }
    
    
    
    public function start_T() {
      $this->db->start_TRANSACTION();//开始事务
    }
    
    public function roll_T() {
        $this->db->roll_TRANSACTION();//回滚事务
    }
    
    public function comit_T() {
        $this->db->comit_TRANSACTION();//提交事务
    }
    
    
}