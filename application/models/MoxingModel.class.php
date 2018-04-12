<?php
// 商品类型模型
class MoxingModel extends Model
{
    
    // 删除表和sl_fileds 里对应的字段
    function deleteModel($model_id, $model_name)
    {
        $filedModel = new Model("filed");
        $this->db->exec_update_delete("drop table $model_name ");
        $this->db->exec_update_delete("delete from sl_filed where model_id='{$model_id}' ");
    }
    
    // 根据模型类型，添加模型
    public function addMoxingByType($type, $tableName)
    {
        switch (trim($type)) {
            case "文章模型":
                $this->createArticleModelTable($type, $tableName);
                $this->addFiled($tableName);
                break;
            case "表单模型":
                $this->createBiaodanModelTable($type, $tableName);
                $this->addFiled($tableName);
                break;
                
            case "用户模型":
                $this->createYonghuModelTable($type, $tableName);
                $this->addFiled($tableName);
                break;
            
            default:
                echo "没有这个模型!";
        }
    }

    /*
     * `model_id` varchar(50) CHARACTER SET utf8 DEFAULT ' ' COMMENT '模型id',
     * `u1` varchar(50) CHARACTER SET utf8 DEFAULT ' ' COMMENT '数据库字段名',
     * `u2` varchar(50) CHARACTER SET utf8 DEFAULT ' ' COMMENT '字段名称',
     * `u3` varchar(50) CHARACTER SET utf8 DEFAULT ' ' COMMENT '字段提示',
     * `u4` varchar(10) CHARACTER SET utf8 DEFAULT '否' COMMENT '是否必填',
     * `u5` varchar(10) CHARACTER SET utf8 DEFAULT '否' COMMENT '是否显示',
     * `u6` varchar(10) CHARACTER SET utf8 DEFAULT '否' COMMENT '是否检索',
     *
     * `u7` varchar(50) CHARACTER SET utf8 DEFAULT '文本框' COMMENT '字段类型',
     * `u8` varchar(50) CHARACTER SET utf8 DEFAULT ' ' COMMENT '默认值',
     * `u9` varchar(50) CHARACTER SET utf8 DEFAULT '10%' COMMENT '列表CSS',
     * `u10` varchar(10) CHARACTER SET utf8 DEFAULT '0' COMMENT '排序',
     */
    public function addFiled($tableName)
    {
        $filed = new Model("filed");
        $table = new Model($tableName);
        $model_id = $this->oneRowCol("id", "u1='{$tableName}'")['id'];
        $array = $table->getFieldsAndTypes();
        foreach ($array as $v) {
            if ($v['column_name'] != 'id' and $v['column_name'] != 'sort_id') {
                
                $data['model_id'] = $model_id;
                $data['u1'] = $v['column_name'];
                $data['u2'] = $v['column_comment'];
                $data['u3'] = "";
                $data['u4'] = ($v['is_nullable'] == 'YES') ? '否' : '是';
                $data['u5'] = '否';
                $data['u6'] = '否';
                $data['u8'] = $v['column_default'];
                $data['u9'] = "10%";
                $data['u10'] = $v['id'];
                
                // 得到字段的类型
                $filed_type = "文本框";
                switch ($v['data_type']) {
                    case "int":
                        $filed_type = "数字";
                        break;
                    case "char":
                        if($data['u2']=="密码")
                        {
                            $filed_type = "密码";
                        }else 
                        {
                            $filed_type = "文本框";
                        }
                        
                        break;
                    case "mediumtext":
                        $filed_type = "文本编辑器";
                        break;
                    case "varchar":
                        $filed_type = "文本域";
                        break;
                    case "timestamp":
                            $filed_type = "时间框";
                            break;
                }
                $data['u7'] = $filed_type;
                
                $filed->insert($data);
            }
        }
    }
    
    // 创建文章模型表的SQL
    public function createArticleModelTable($type, $tableName)
    {
        $sql = "
        CREATE TABLE `{$tableName}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `sort_id` char(100) DEFAULT '0' COMMENT '分类id',
        `biaoti` char(100) NOT NULL DEFAULT '' COMMENT '标题',
        `laiyuan` char(100)  NULL DEFAULT '' COMMENT '来源',
        `status`char(100) NULL DEFAULT '终审' COMMENT '状态：待审,终审,回收站',
        `paixu` int(10) unsigned DEFAULT '1' COMMENT '排序',
        `url` char(100) DEFAULT NULL COMMENT '链接',
        `suolutu` char(100) DEFAULT NULL COMMENT '缩略图',
        `description` varchar(250) DEFAULT NULL COMMENT '文章摘要',
        `content` mediumtext COMMENT '详细内容',
        `seokeywords` varchar(250) DEFAULT NULL COMMENT 'seo关键字',
        `seodescription` varchar(250) DEFAULT NULL COMMENT 'seo描述',
        `seotitle` varchar(250) DEFAULT NULL COMMENT 'SEO标题',
        `username` char(100) DEFAULT NULL COMMENT '用户名',
        `update_time` timestamp  NULL DEFAULT 0 COMMENT '更新时间',
        `dtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '添加时间',
        PRIMARY KEY (`id`)
        ) ENGINE=INNODB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1 COMMENT='{$tableName}模型主表';";
        
        if ($this->db->exec_update_delete($sql)) {
            // 成功，并判断受影响的记录数
            if ($rows = mysql_affected_rows()) {
                // 有受影响的记录
                return true;
            }
        } else {
            // 失败返回false
            return false;
        }
    }
    
    // 创建表单模型表的SQL
    public function createBiaodanModelTable($type, $tableName)
    {
        $sql = "
        CREATE TABLE `{$tableName}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        
        `dtime` timestamp NULL DEFAULT now() COMMENT '添加时间',
        PRIMARY KEY (`id`)
        ) ENGINE=INNODB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1 COMMENT='{$tableName}模型主表';";
        
        if ($this->db->exec_update_delete($sql)) {
            // 成功，并判断受影响的记录数
            if ($rows = mysql_affected_rows()) {
                // 有受影响的记录
                return true;
            }
        } else {
            // 失败返回false
            return false;
        }
    }
    
    
    // 创建用户模型表的SQL
    public function createYonghuModelTable($type, $tableName)
    {
        $sql = "
        CREATE TABLE `{$tableName}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `yonghuming` char(100) NOT NULL DEFAULT '' COMMENT '用户名',
        `mima` char(200) NOT NULL DEFAULT '' COMMENT '密码',
        `dtime` timestamp NULL DEFAULT now() COMMENT '添加时间',
        PRIMARY KEY (`id`)
        ) ENGINE=INNODB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1 COMMENT='{$tableName}模型主表';";
        
        if ($this->db->exec_update_delete($sql)) {
            // 成功，并判断受影响的记录数
            if ($rows = mysql_affected_rows()) {
                // 有受影响的记录
                return true;
            }
        } else {
            // 失败返回false
            return false;
        }
    }
    
    
}