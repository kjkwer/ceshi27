<?php
//商品类型模型
class LanmuModel extends Model {
	//获取所有的商品类型
	public function getLanmuByclassid($classid){
		$sql = "SELECT *,u1 as name,classid as parentid  FROM {$this->table}  " ;
		if($classid!='')
		{
			$sql.=" where classid='$classid' ";
		}
		$sql.=" order by u2 asc,id asc";

		return $this->db->getAll($sql);
		//对获取的分类进行重新排序
		
	}

	//对给定的数组进行重新排序
	public function tree($arr,$pid = 0,$level = 0){
		static $res = array();
		foreach ($arr as $v){
			if ($v['classid'] == $pid) {
				//说明找到，先保存
				$v['level'] = $level; 
				$res[] = $v;
				//改变条件，递归查找
				$this->tree($arr,$v['id'],$level+1);
			}
		}
		return $res;
	}


//将平行的二维数组，转成包含关系的多维数组
	public function child($arr,$pid = 0){
		$res = array();
		foreach ($arr as $v) {
			if ($v['classid'] == $pid) {
				//找到了，继续查找其后代节点
				//$temp = $this->child($arr,$v['cat_id']);
				//将找到的结果作为当前数组的一个元素来保存，其下标是child
				//$v['child'] = $temp;
				$v['child'] = $this->child($arr,$v['id']);
				$res[] = $v;
				unset($v);
			}
			
		}
		return $res;
	}


	public function tree_ghy($arr,$pid = 0,$level = 0){
				//$res = array(); //$res是一个局部变量
		static $res = array();
				//global $res;
		foreach ($arr as $v) {
			if ($v['classid'] == $pid ) {
				//说明找到,首先保存
				$v['level'] = $level;
				$res[] = $v;
				$this->load_lianmuList($v,$level);
				//改变添加，找当前分类的后代分类，就是递归
				$this->tree_ghy($arr,$v['id'],$level + 1);
				//load_html($v,$level);
			}
		}
		
	}
	
	/**
	 * 通过多个关键词 直接查询  and并存
	 * @param  [type] $Col   [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function selectByArrayAndLanmu($arr){
	    $sql = "select * from `{$this->table}`  ";
	    if(count($arr)>0)
	    {
	        $sql.=" where ";
	        foreach ($arr as $key => $value) {
	            $sql.=" {$key} = '{$value}' and ";
	        }
	        $sql.=" 1=1 ";
	        $sql.=" order by u2 asc, id desc ";
	    }
 	    return $this->db->getAll($sql);
	}


	public	function load_lianmuList($column,$level)
	{
		echo "<tr>";
		echo "<td>{$column['id']} </td>";
		echo "<td style='text-align: left;'>";
		
		echo "<a href='index.php?p=admin&c=lanmu&a=add&id={$column['id']}'> ";
		echo str_repeat('----',$level)."{$column['u1']} </a>";
		echo "</td>";
		echo "<td class='hidden-xs'>";
		echo $column['u2'];
		echo "</td>";
		echo "<td class='hidden-xs'>";
		echo $column['u3'] ;
		echo "</td>";
		echo "<td class='hidden-xs'>";
		echo $column['u4'];
		echo "</td>";
		echo "<td>";
		echo "<a href='index.php?p=admin&c=lanmu&a=add&id={$column['id']}' title='添加子类' class='operation'><span class='ficon ficon-tianjia'></span></a>";
		echo "<a href='index.php?p=admin&c=lanmu&a=edit&id={$column['id']}' title='编辑' class='operation'><span class='ficon ficon-xiugai'></span></a>";
		echo "<a href='index.php?p=admin&c=lanmu&a=delete&id={$column['id']}'  onclick=\"if(confirm('确定删除栏目?')==false)return false;\"   ><span class='ficon  ficon-shanchu'></span></a>";
		echo "</td>";

		echo "</tr>";
	}


     public	function load_lianmuLists($column1)
	{

		foreach ($column1 as $column) {
		echo "<tr>";
		echo "<td>{$column['id']} </td>";
		echo "<td style='text-align: left;'>";
		
		echo "<a href='index.php?p=admin&c=lanmu&a=add&id={$column['id']}'> ";
		echo "{$column['u1']} </a>";
		echo "</td>";
		echo "<td class='hidden-xs'>";
		echo $column['u2'];
		echo "</td>";
		echo "<td class='hidden-xs'>";
		echo $column['u3'] ;
		echo "</td>";
		echo "<td class='hidden-xs'>";
		echo $column['u4'];
		echo "</td>";
		echo "<td>";
		echo "<a href='index.php?p=admin&c=lanmu&a=add&id={$column['id']}' title='添加子类' class='operation'><span class='ficon ficon-tianjia'></span></a>";
		echo "<a href='index.php?p=admin&c=lanmu&a=edit&id={$column['id']}' title='编辑' class='operation'><span class='ficon ficon-xiugai'></span></a>";
		echo "<a href='index.php?p=admin&c=lanmu&a=delete&id={$column['id']}'  onclick=\"if(confirm('确定删除栏目?')==false)return false;\"   ><span class='ficon  ficon-shanchu'></span></a>";
		echo "</td>";

		echo "</tr>";
		}
	}



}