<?php
// 字段类型模型
class FiledModel extends Model
{

    PUBLIC function getFiledDefaultValue($filed_id)
    {
        $defaultValueArray=array();
        $filedDetail=$this->selectByPk($filed_id);
        $defaultValue=$this->oneRowCol("u8", "id={$filed_id}")['u8'];
        
        if($filedDetail["u7"]=="单选"||$filedDetail["u7"]=="多选")
        {
            //匹配正则查询
            $result = array();
            $defaultValueArray=explode("\r\n", $defaultValue);
           // echo count($defaultValueArray);die();
            if(count($defaultValueArray)>0)
            {
                $defaultValueArray=explode("\r\n", $defaultValue);
               
            }else
            {
                preg_match_all("/(?:sql{)(.*)(?:})/i",$defaultValue, $result);
                $sql_string=$result[1][0];
                //sl_qudao|where id>0|order by id desc|ziyuanmingcheng
                $sql_array=explode("|", $sql_string);
                $table_name=$sql_array[0];
                $temp_model = new Model($table_name);
                $temp_array=$temp_model->select(" select ".$sql_array[3]." from  "." ".$sql_array[0]." ".$sql_array[1]." ".$sql_array[2] );
                foreach ($temp_array as $k=>$v)
                {
                    $defaultValueArray[$k]=$v[$sql_array[3]];
                
                }
                //var_dump($defaultValueArray);die();
                
            }
            //var_dump($defaultValueArray);die();
            return $defaultValueArray;
        }else  if($filedDetail["u7"]=="下拉框")
        {
            
            //匹配正则查询
            $result = array();
            //var_dump($defaultValueArray);die();
            $temp_defaultValueArray = explode("\r\n", $defaultValue);
            if(count($temp_defaultValueArray)>=2)
            {
                foreach ($temp_defaultValueArray as $k=>$v)
                {
                     
                    $defaultValueArray[$k]["key"]=$v;
                    $defaultValueArray[$k]["value"]=$v;
                }
                
            }else
            {

                preg_match_all("/(?:sql{)(.*)(?:})/i",$defaultValue, $result);
                $sql_string=$result[1][0];
                // sql{sl_zhuanjia|where 1=1|order by id desc|id,xingming}
                //sl_qudao|where id>0|order by id desc|ziyuanmingcheng
                $sql_array=explode("|", $sql_string);
                $table_name=$sql_array[0];
                $temp_model = new Model($table_name);
                //判断返回的select的 key 和 value
                $key_array=explode(",", $sql_array[3]);//查询的返回字段
                if(count($key_array)==0)
                {
                    $sql_array[3]=$sql_array[3].','.$sql_array[3];
                }
                $temp_array=$temp_model->select(" select ".$sql_array[3]." from  "." ".$sql_array[0]." ".$sql_array[1]." ".$sql_array[2] );
                
                foreach ($temp_array as $k=>$v)
                {
                     
                    $defaultValueArray[$k]["key"]=$v[$key_array[0]];
                    $defaultValueArray[$k]["value"]=$v[$key_array[1]];
                }
                //var_dump($defaultValueArray);die();
                
            }
            return $defaultValueArray;
        }else  if($filedDetail["u7"]=="多条记录")
        {
            $tempArray=explode("|", $defaultValue);
            //多条字段默认通过 laiyuanbianhao 进行关联，也可自定义
            if(count($tempArray)==0)
            {
               $defaultValueArray["modelid"]=$tempArray[0];
               $defaultValueArray["fieldname"]="laiyuanbianhao";
            }else 
            {
                $defaultValueArray["modelid"]=$tempArray[0];
                $defaultValueArray["fieldname"]=$tempArray[1];
            }
            return $defaultValueArray;
              
        }else 
        {
            
            $defaultValueArray=$defaultValue;
            
        }
        
       
        
    }
}