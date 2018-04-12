<?php
class BaseController extends Controller {
    // protected $smarty;
    //构造方法
    public function __construct(){
        //$this->checkRequest();
    }
    
    //验证接口合法性
    public function checkRequest(){
        //接收token的值
        //验证token是否合法（存在，超时）
        //查询该表是否可以查询
        
        //写入系统日志
        $type=$_GET["type"];
        $t=$_GET["t"];
        $yonghuming=$_GET["xt_yhm"];
        $type_str="";
        if($type=="search")
        {
            $type_str="查询"; 
        }else if($type=="login")
        {
            $type_str="登录";
        }else if($type=="add")
        {
            $type_str="添加";
        }else if($type=="edit")
        {
            $type_str="修改";
        }else 
        {
            $type_str=$type;
        }
        
        $Common = new Common();
        $System = new SystemModel('System');
        $sysData["u1"]=$yonghuming;
        $sysData["yonghuming"]=$yonghuming;
        $sysData["u4"]=$type_str;
        $sysData["u3"]=$Common->getIP();
        if(!empty($yonghuming)){
            $sysData["u2"]=$username . "操作类型: {$type_str}，相关数据表 {$t}";
            $System->insert($sysData);
        }
        
        
    }
    
    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data,$type='') {
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        //header('Access-Control-Allow-Origin:*');
        exit(json_encode($data));
    }
    
    
    /**
     * 处理查询表名称，防止跨脚本攻击
     */
    protected function TablenameFilter($t) {
        //调用common类
        $commonClass=new Common();
        $table_arr = explode(',',$t);
        if(count($table_arr)==0)
        {
            //待处理的表名
            $t=$commonClass->SafeFilterStr($t);
            //增加权限判断
            
        }else
        {
            $t="";
            for($i=0;$i<count($table_arr);$i++)
            {
                //增加权限判断
                
                if($t=="")
                {
                    $t="sl_".$commonClass->SafeFilterStr($table_arr[$i]);
                }else
                {
                    $t= $t ." , ". "sl_".$commonClass->SafeFilterStr($table_arr[$i]);
                }
                
            }
            
        }
        
        return $t;
        
    }
    
    /**
     * 处理查询表的查询字段，自动生成查询sql
     */
    protected function FiledFilter($t) {
        //调用common类
        $commonClass=new Common();
        if(strpos($t,",")=="")
        {
            $t=$commonClass->SafeFilterStr($t);
            $temp_model=new Model($t);
            //echo $temp_model->getSqlWhereStr();die();
            return $temp_model->getSqlWhereStr();
        }
        
        
    }
    
    
    /**
     * 处理返回的列名称，用于多表查询
     */
    protected function LiemingchengFilter($liemingcheng) {
        //调用common类
        $commonClass=new Common();
        if($liemingcheng=="")
            return "*";
            $liemingcheng_arr = explode(',',$liemingcheng);
            if(count($liemingcheng_arr)==0)
            {
                //待处理的列名称
                $liemingcheng=$commonClass->SafeFilterStr($liemingcheng);
                
                
            }else
            {
                $liemingcheng="";
                for($i=0;$i<count($liemingcheng_arr);$i++)
                {
                    if($liemingcheng=="")
                    {
                        $liemingcheng=$commonClass->SafeFilterStr($liemingcheng_arr[$i]);
                    }else
                    {
                        $liemingcheng= $liemingcheng." , ". $commonClass->SafeFilterStr($liemingcheng_arr[$i]);
                    }
                    
                }
                
            }
            
            return $liemingcheng;
            
    }
    
    /**
     * 返回条数
     */
    protected function NumberFilter($number) {
        //调用common类
        $commonClass=new Common();
        if ($number=="")
        {
            $number="0";
        }
        else if(!$commonClass->isNumber($number))
        {
            $number="0";
        }
        return  $number;
    }
    
    /**
     * 默认查询方式
     * 如果有多个用逗号分隔，“|”会替换成=号
     * 多个字使用逗号“，”分开
     * 如果是or，比如sqlvalue=biaoti|'姓名/shouji|‘15982851365’，使用“/”分隔
     */
    protected function SqlvalueFilter($sqlvalue) {
        //调用common类
        $commonClass=new Common();
        $sqlvalue=str_replace("|"," = ",$sqlvalue);
        $sqlvalue=str_replace(","," and ",$sqlvalue);
        $sqlvalue=str_replace("/"," or ",$sqlvalue);
        $sqlvalue=str_replace("dh"," , ",$sqlvalue);//把逗号替换为，
        $sqlvalue=str_replace("{bdy}"," <> ",$sqlvalue);//替换不等于
        //处理模糊查询
        $sqlvalue=str_replace("{like}"," like ",$sqlvalue);
        $sqlvalue=str_replace("{bfb}","%",$sqlvalue);
        
        // dtime between '2017-08-27 00:00' and '2017-08-27 23:59'
        $sqlvalue=str_replace("{between}"," between ",$sqlvalue);
        $sqlvalue=str_replace("{and}"," and ",$sqlvalue);
        
        //验证字符合法性
        
        if($sqlvalue!="")
        {
            $sqlvalue=" (".$sqlvalue.") ";
        }
        return $sqlvalue;
    }
    
    /**
     * 自动拼接sql 语句
     *
     */
    protected function getSql($t,$liemingcheng,$number,$page=0,$ordertype,$orderby,$sqlvalue) {
        //调用common类
        $commonClass=new Common();
        
        $_sql="select ";
        if($sqlvalue=="")
        {
            $_sql=$_sql." ".$liemingcheng." from {$t} ";
        }else
        {
            $_sql=$_sql." ".$liemingcheng." from {$t} where {$sqlvalue}  ";
        }
        
        
        
        if($this->FiledFilter($t)!="")
        {
            if($sqlvalue=="")
            {
                $_sql=$_sql." where  ".$this->FiledFilter($t);
            }else
            {
                
                $_sql=$_sql." and  ".$this->FiledFilter($t);
            }
        }
        
        
        
        if($ordertype!="")
        {
            if($ordertype=="id")
            {
                $_sql=$_sql." order by ".$ordertype." ".$orderby ;
            }else if(strlen(strstr($t, ','))>0)
            {
                
                $_sql=$_sql." order by ".$ordertype." ".$orderby ;
                
            }else
            {
                $_sql=$_sql." order by ".$ordertype." ".$orderby ."  , id desc ";
            }
        }else
        {
            $_sql=$_sql." order by id desc ";
        }
        
        if($number!="0")
        {
            if($page<=1)
            {
                $a=0;
            }else
            {
                $a=$number * ($page-1);
            }
            
            $b=$number ;
            $_sql=$_sql . " limit  $a , $b ";
        }
        
        //$_sql="select {$liemingcheng} from {$t} where {$sqlvalue} order by id desc";
        
        //验证字符合法性
        //echo $_sql;die();
        return $_sql;
    }
    
    
    
}