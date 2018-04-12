<?php
//品牌控制器
class SystemController extends BaseController{
	
	//显示品牌列表
	public function indexAction(){
	    $Common= new Common();
	    $where=" 1=1 ";
	    //获取请求参数

	    $u2=empty($_REQUEST['u2_str'])? "" : $_REQUEST['u2_str'];

		//先获取品牌信息
		$systemModel = new Model("system");
		//获得操作类型u4的列表  SELECT u4 from sl_system GROUP BY u4
		$u4Lists=$systemModel->select("SELECT u4 from sl_system GROUP BY u4");
		//查询条件 
		if($u2!='')
		{
		    $where .=" and (u1 like '%{$u2}%' or u2 like '%{$u2}%' or u3 like '%{$u2}%') ";
		}
		if(trim(str_replace("1=1", " ", $systemModel->getSqlWhereStr()))!="")
		{
		    $where .= " and ".$systemModel->getSqlWhereStr();
		}
		if(trim($systemModel->getDtimeSql())!='')
		{
		    $where .=" and ".$systemModel->getDtimeSql();
		}
		//echo $systemModel->getDtimeSql();
		
		
		//载入分页类
		include LIB_PATH . "Page.class.php";
		//获取brand总的记录数
		$total = $systemModel->total($where);
		//指定分页数，每一页显示的记录数
		 $pagesize = 20;
		// $pagesize = $GLOBALS['config']['pagesize'];
		//获取当前页数，默认是1
		$current = isset($_GET['page']) ? $_GET['page'] : 1;
		$offset = ( $current - 1 ) * $pagesize;
		//使用模型完成数据的查询
		$systems = $systemModel->pageRows($offset,$pagesize,$where);
		//使用分页类获取分页信息
		//echo $total." | ".$pagesize." | ".$current;die();
		$page = new Page($total,$pagesize,$current,"index.php",array("p"=>"admin","c"=>"system","a"=>"index"));
		$pageinfo = $page->showPage();
		
	    foreach ($systems as $k=>$v)
	    {
	        //查询ip的地址
	        //$systems[$k]['address']=$Common->getIPAddress(trim($systems[$k]['u3']));
	        $systems[$k]['address']="";
	    }
		include CUR_VIEW_PATH ."Ssystem".DS."system_list.html";
	}

	
	//定义delete方法，完成品牌的删除
	public function deleteAction(){
		//获取brand_id
		if($_REQUEST['id']=='')
		{
		    $this->jump("index.php?p=admin&c=system&a=index","删除失败，参数不能为空",3);
		}
		$sys_id = $_REQUEST['id'];
		$array_id=explode(",", $sys_id);
		$array_id=array_unique($array_id);
		
		$systemModel = new Model("system");
		if($systemModel->delete($array_id)!="false")
		{
		    $this->jump("index.php?p=admin&c=system&a=index","删除成功",2);
		}
		else 
		{
		    $this->jump("index.php?p=admin&c=system&a=index","删除失败",3);
		}
		
	}
	
}