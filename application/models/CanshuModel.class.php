<?php
// 文章分类模型
class CanshuModel extends Model
{
    private $str;     
   
   //得到当前Canshu_id到底层的json
   public function getCanshuName($canshuId,$temp_arr)
   {
      
       $CurrentCanshu=$this->select("select id as value, u1 as text from sl_canshu where id={$canshuId}");
       $ChildrenCanshu=$this->select("select * from sl_canshu where classid={$CurrentCanshu[0]['value']}");
       if($temp_arr=="")
       {
           $temp_arr=$CurrentCanshu;
       }
       if(count($ChildrenCanshu)>0)
       {
           //该Canshu_id的父级存在
           $temp_arr["children"]=$ChildrenCanshu;
           $this->getCanshuNames($parentCanshu[0]['value'],$temp_arr);
       } else 
       {
           $this->str=$temp_arr;
           
       }
   }
   
   
   public function getCanshuNames($curCanshuId,$ChildCanshuNames)
   {
      $this->getCanshuName($curCanshuId,$ChildCanshuNames);
      return $this->str;
   }
   
   
   
}