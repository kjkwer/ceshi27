<?php
// 模型类基类
class ModelNew
{
    // 数据库连接对象
    protected $db;

   
    // 表的主键
    protected $pk = "id";
    // 字段列表
    protected $fields = array();
    
     /**
     * 20180122
     * ghy
     * 链式查询参数
     */
    protected $table;

    protected $column = "*";

    protected $condition = array();

    protected $group;

    protected $order;

    protected $having;

    protected $startSet;

    protected $endSet;
    
    
    //查询返回结果
    protected $result = array();
    

    public function __construct($table="")
    {
        $dbconfig['host'] = $GLOBALS['config']['host'];
        $dbconfig['user'] = $GLOBALS['config']['user'];
        $dbconfig['password'] = $GLOBALS['config']['password'];
        $dbconfig['dbname'] = $GLOBALS['config']['dbname'];
        $dbconfig['port'] = $GLOBALS['config']['port'];
        $dbconfig['charset'] = $GLOBALS['config']['charset'];
        // var_dump($dbconfig);die();
        $this->db = new CPdo(false, $dbconfig);
        if($table!="")
        {
            $this->M($table);
        }
    }

    /**
     * 获取表主键
     */
    private function getPK()
    {
        if ($this->table != $GLOBALS['config']['prefix'] && ! empty($this->table)) {
            $sql = "DESC " . $this->table;
            
            $result = $this->db->getValueBySelfCreateSql($sql);
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
                $this->pk = $pk;
            }
            
        }
    }
    
    
    

    /**
     * 设置模型的表明
     * 
     * @param unknown $table            
     */
    public function M($table)
    {
        $table = str_replace($GLOBALS['config']['prefix'], "", $table);
        $this->table = $GLOBALS['config']['prefix'] . $table;
        // 设置主键
        $this->getPK();
        return $this;
    }

    /**
     * 筛选返回字段
     * @param string $column
     */
    public function find($column = '*')
    {
         if($column!="" || !empty($this->column ))
         {
             $this->column = $column;
         }
        
        return $this;
    }

    /**
     *  增加条件
     * @param unknown $condition
     */
    public function where($condition = array())
    {
        if (count($this->condition) == 0) {
            $this->condition = $condition;
        } else {
            $this->condition = $this->condition + $condition;
        }
        
        return $this;
    }
    
    

    /**
     * 增加条件
     * @param unknown $condition
     */
//     public function andwhere($condition = array())
//     {
//         if (count($this->$condition) == 0) {
//             $this->condition = $condition;
//         } else {
//             $this->condition = $this->$condition + $condition;
//         }
        
//         return $this;
//     }

    /**
     * having字句可以让我们筛选成组后的各种数据，where字句在聚合前先筛选记录，也就是说作用在group by和having字句前。而 having子句在聚合后对组记录进行筛选。
     * 
     * @param string $column            
     */
    public function having($having)
    {
        if($this->having=="" || empty($this->having ))
        {
            $this->having = $having;
        }else 
        {
            $this->having =$this->having. " and " . $having;
            
        }
       
        return $this;
    }

    /**
     * 此方法是排序查询；
     * @param string $order
     */
    public function orderBy($order = '{$this->pk} desc')
    {
        if($this->order=="" || empty($this->order ))
        {
            $this->order = $order;
        }else
        {
            $this->order =$this->order .",". $order;
        }
       
        return $this;
    }
    
    /**
     * 此方法是group查询；
     * @param string $order
     */
    public function groupBy($group = '')
    {
        if($this->group=="" || empty($this->group ))
        {
             $this->group = $group;
             $this->column = $group;
        }else
        {
            $this->group =$this->group .",". $group;
            $this->column =$this->group .",". $group;
        }
        
       
        return $this;
    }

    /**
     * 设置返回数量
     * 
     * @param number $startSet            
     * @param number $endSet            
     */
    public function limit($startSet = 0, $endSet = 10)
    {
        $this->startSet = $startSet;
        $this->endSet = $endSet;
        return $this;
    }

    /**
     * 返回所有
     */
    public function all()
    {      
        if(count($this->result)==0)
        {
            $result = $this->do_sql();
        
        }else
        {
            $result=$this->result;
        
        }
        
        return $result;
    }
    
    /**
     * 执行查询
     */
    private function do_sql()
    {
        
        $result = $this->db->query($this->table, $this->column, $this->condition, $this->group, $this->order, $this->having, $this->startSet, $this->endSet, "assoc",null);
        return $result;
    }
    
    
    /**
     * 返回主键的值等于 $pk_val的记录
     * @param unknown $pk_val
     */
    public function findOne($pk_val)
    {
        $this->condition = [$this->pk => $pk_val];
        $result = $this->all();
        return $result[0];
    }
    
    
    
    /**
     * 此方法返回记录的数量
     */
    public function count()
    {
        if(count($this->result)==0)
        {
            $result = $this->all();
            
        }else
        {
            $result=$this->result;
        
        }
        
        $num = count($result);
        return $num;
        
    }
    
    /**
     * 此方法返回一条数据
     */
    public function one()
    {
        if(count($this->result)==0)
        {
            $this->startSet = 0;
            $this->endSet = 1;
            $result = $this->all();
        }else 
        {
            $result=$this->result;
            
        }
        
        return isset($result[0])?$result[0]:$result;
    }
    
    
    /**
     * 此方法是用 sql  语句查询 ；
     * @param unknown $sql_str
     */
    public function findBySql($sql_str)
    {
        //echo "<br>".$sql_str;flush();
        $result = $this->db->getValueBySelfCreateSql($sql_str);
        $this->result = $result;
        return $this->result;
    }
    
    
    /**
     *  此方法是用 like , in , <> 查询 数据
     * @param unknown $condition
     */
    public function andFilterWhere($condition = array())
    {
       //['like', 'name', '小伙儿']
       $_condition = $condition[0];
       $_k = $condition[1];
       $_v = $condition[2];
       
       if($_condition=="like")
       {
       $_having_str = " {$_k} {$_condition} '%{$_v}%' ";    
       }
       
       if($_condition=="<>")
       {
           $_having_str = " {$_k} {$_condition} '{$_v}' ";
       }
       
       if($_condition=="in")
       {
           $_having_str = " {$_k} {$_condition} ({$_v}) ";
       }
       $this->having($_having_str);
       
        return $this;
    }
    
    /**
     *  此方法是用 sql 直接 查询 数据
     * @param unknown $condition
     */
    public function andStringWhere($condition = "")
    { 
        
        $this->having($condition);
         
        return $this;
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
    
        if ($this->db->exec($sql)) {
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
        
        //链式查询where start
        if (count($this->condition)>0) {
            foreach ($this->condition as $key => $value) {
                if($where=='0')
                {
                    $where = " {$key} = '{$value}'  ";
                }
                else {
                    $where = $where . " and {$key} = '{$value}'  ";
                }
                
            }
           
        }
        
        if ($this->having != "") {
            if($where=='0')
            {
                $where = "  having {$this->having}  ";
            }
            else {
                $where = $where ." and   having {$this->having}   ";
            }
             
        }
        //链式查询where end
        
        // 去除uplist右边的
        $uplist = rtrim($uplist, ',');
        // 构造sql语句
        $sql = "UPDATE `{$this->table}` SET {$uplist} WHERE {$where}";
        // echo $sql;die();
        if ($rows = $this->db->exec_update_delete($sql)) {
            // 成功，并判断受影响的记录数
            if ($rows ) {
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
    public function delete($pk="")
    {
        $where = 0; // 条件字符串
        
        if(!empty($pk) && $pk!="")
        {
            // 判断$pk是数组还是单值，然后构造相应的条件
            if (is_array($pk)) {
                // 数组
                $where = "`{$this->fields['pk']}` in (" . implode(',', $pk) . ")";
            } else {
                // 单值
                $where = "`{$this->fields['pk']}`=$pk";
            }  
        }
        
        
        
        //链式查询where start
        if (count($this->condition)>0) {
            foreach ($this->condition as $key => $value) {
                if($where=='0')
                {
                    $where = " {$key} = '{$value}'  ";
                }
                else {
                    $where = $where . " and {$key} = '{$value}'  ";
                }
        
            }
             
        }
        
        if ($this->having != "") {
            if($where=='0')
            {
                $where = "  having {$this->having}  ";
            }
            else {
                $where = $where ." and   having {$this->having}   ";
            }
             
        }
        //链式查询where end
        
        
        
        // 构造sql语句
        $sql = "DELETE FROM `{$this->table}` WHERE $where";
    
        if ($rows =$this->db->exec_update_delete($sql)) {
            // 成功，并判断受影响的记录数
            if ($rows) {
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
     * 根据传递的参数，返回接收数组
     */
    public function getFieldArray()
    {
        $fields_temp = $this->fields;
        $fields_temp = array_unique($fields_temp);
        foreach ($fields_temp as $k => $v) {//
            if (empty($_REQUEST[$v])? "" : $_REQUEST[$v] != '') {
                $data[$v] = $_REQUEST[$v];
            }else if( $k != $this->pk)
            {
                $data[$v]='';
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
    
    /**
     * 此方法是用 sql  语句查询 ；
     * @param unknown $sql_str
     */
    public function query($sql_str)
    {
        $result = $this->db->exec_update_delete($sql_str);
       return $result;
    }
    
    public function start_T() {
        $this->db->begin();//开始事务
    }
    
    public function roll_T() {
        $this->db->rollback();//回滚事务
    }
    
    public function comit_T() {
        $this->db->commit();//提交事务
    }
    
    
}