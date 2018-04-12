<?php
// 文章分类模型
class SortModel extends Model
{
    private $str;     
    /*
     * 把sort 及其子级添加到栏目
     * id 当前sort 的id
     * date 为添加的栏目数组
     */
   public  function addSubSortToLanmu($sortid,$lanmu_id,$data) {
      
      $lanmuModel=new LanmuModel("column");
      
       //如果当前的sort所在id还有子级
       $sort=$this->selectByArrayAnd(array("sort_id"=>$sortid));
      
       if(count($sort)>0)
       {
           foreach ($sort as $v)
           {
               //print_r($v);
               $temp_data=$data;
               //将本级sort添加到栏目
               $temp_data['classid']=$lanmu_id;
               $temp_data['u1']=$v['u1'];
               $temp_data['u3']=str_replace("sort_id={$sortid}", "sort_id={$v['id']}", $temp_data['u3']);
               
               $lanmuModel->insert($temp_data);
               $lanmu_id_next=$lanmuModel->select("select * from `sl_column` where  u1 = '{$v['u1']}'   ORDER BY id desc LIMIT 1");
               $lanmu_id_next=$lanmu_id_next[0]['id']==''?$temp_data['classid']:$lanmu_id_next[0]['id'];

               $this->addSubSortToLanmu($v['id'],$lanmu_id_next,$temp_data);
               
           }
          
       }
       
   }

/*
    * 将model 及其所有sort全部添加到栏目
    * 
    * 
    */
  /* public function addModelToLanmu($model_id,$lanmu_id,$data) {
        $lanmuModel=new LanmuModel("column");
        $sort=$this->selectByArrayAnd(array("model_id"=>$model_id,"sort_id"=>0));
        
        //如果这个模型存在分类
        if(count($sort)>0)
        {
            foreach ($sort as $v)
            {
            $temp_date=$data;
            $temp_date['u3']= $temp_date['u3'].$v['id'];
            $temp_date['u1']=$v['u1'];
            $temp_date['classid']=$lanmu_id;
            
            //将第一级sort插入栏目
            $lanmuModel->insert($temp_date);
            $lanmu_id=$lanmuModel->select("select * from `sl_column` where classid = '{$temp_date['classid']}' and u1 = '{$temp_date['u1']}'  ORDER BY id desc LIMIT 1");
            $lanmu_id=$lanmu_id[0]['id'];
            
            $this->addSubSortToLanmu($v['id'], $lanmu_id, $temp_date);
            }
        }
        
        
   } */
   
   
   //得到当前sort_id到顶级的字符串
   public function getSortName($curSortId,$ChildSortNames)
   {
      
       $CurrentSort=$this->select("select * from sl_sort where id={$curSortId}");
       $parentSort=$this->select("select * from sl_sort where id={$CurrentSort[0]['sort_id']}");
       if(count($parentSort)>0)
       {
           //该sort_id的父级存在
           $sortNames="<a href='/index.php?c=list&sort_id={$parentSort[0]['id']}'>".$parentSort[0]['u1']."</a>".">". $CurrentSort[0]['u1'];
          $this->getSortNames($parentSort[0]['id'],$sortNames);
       } else 
       {
           $this->str=$ChildSortNames;
           
       }
   }
   
   
   public function getSortNames($curSortId,$ChildSortNames)
   {
      $this->getSortName($curSortId,$ChildSortNames);
      return $this->str;
   }
   
   
   
}