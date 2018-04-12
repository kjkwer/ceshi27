<?php
//用户组控制器
class GroupController extends BaseController{
	
	//显示用户组列表
	public function indexAction(){
		//先获取用户组信息
		$groupModel = new Model("group");
		// $groups = $groupModel->getgroups();
		//载入分页类
		$where = ""; //查询条件
		$this->library("Page");
		//获取group总的记录数
		$total = $groupModel->total($where);
		//指定分页数，每一页显示的记录数
		$pagesize = 10;
		// $pagesize = $GLOBALS['config']['pagesize'];
		//获取当前页数，默认是1
		$current = isset($_GET['page']) ? $_GET['page'] : 1;
		$offset = ( $current - 1 ) * $pagesize;
		//使用模型完成数据的查询
		$groups= $groupModel->pageRows($offset, $pagesize, $where);
		foreach ($groups as $k=>$v)
		{
		    $adminModel = new Model("admin");
		    $groups[$k]["memberNum"]=$adminModel->select("select count(*) as count_id from sl_admin where group_id={$v['id']} ")[0]['count_id'];
		}
		//使用分页类获取分页信息
		$page = new Page($total,$pagesize,$current,"index.php",array("p"=>"admin","c"=>"group","a"=>"index"));
		$pageinfo = $page->showPage();
		include CUR_VIEW_PATH . "Sgroup".DS."group_list.html";
	}

	//载入添加用户组页面
	public function addAction(){
	    //记录单选框的数量
	    $che_list=0;
	    $LanmuModel = new LanmuModel('column');
	    $classidArray=$LanmuModel->getLanmuByclassid("");
	    $lanmu= $LanmuModel->child($classidArray);

	    $data = $LanmuModel->select(" select id,u1,u2,u3,u4,classid,u1 as name,classid as parentid,(@rowNum:=@rowNum+1) as rowNo from sl_column as a ,(Select (@rowNum :=0) ) b order by a.id asc,a.u2 asc");
	   
	    $che_list = count($data);
	    //挂载 TREE 类
	    $this->helper('tree');
	    $tree =new Tree() ;
	    $tree->tree($data);
	    //var_dump($data);die;
	    //$str = "<option value=\$id \$select>\$spacer\$name</option>";
	    $str="<tr>
    	         <td style='font-weight: 70; font-size: 14px'>
	                <div class='checkbox checkbox-inline checkbox-success'><input type='checkbox' data-id='qx_box_\$rowNo' name='colid[\$rowNo]' value='\$id'><label>\$spacer\$u1</label></div>
    				<input name='classid[\$rowNo]' type='text' value='\$classid' class='yincang'>
    				<input name='u1[\$rowNo]' type='text' value='\$u1' class='yincang'>
    				<input name='u2[\$rowNo]' type='text' value='\$u2' class='yincang'>
    				<input name='u3[\$rowNo]' type='text' value='\$u3' class='yincang'>
    				<input name='u4[\$rowNo]' type='text' value='\$u4' class='yincang'> 
                 </td>
	          </tr>
	        
	        ";
				
 
	    $html='';
	    $html .= $tree->get_tree(0,$str,-1);
	    
	    $html="
	        <table border='0' cellpadding='0' cellspacing='0' style='width: 100%; text-align: left'>
				<tbody>
					
	                   ".$html."
    	           
    			</tbody>
    		</table>
	        ";
	    
	    //var_dump($lanmu);
	    include CUR_VIEW_PATH . "Sgroup".DS."group_add.html";
	}

	//载入编辑用户组页面
	public function editAction(){
	    //获取该用户组信息
	    $groupModel = new Model("group");
	    //条件
	    $group_id = $_GET['id'] + 0; //出于考虑
	    //使用模型获取
	    $group = $groupModel->selectByPk($group_id);
	    $column_groupModel = new ModelNew("column_group");
	    $group_id_data["group_id"]=$group_id;
	    //当前用户组拥有的栏目ID
	    $column_group_data = $column_groupModel->find("id")->where($group_id_data)->all();
	    //var_dump($column_group_data);die;
	    //记录单选框的数量
	    $che_list=0;
	    $LanmuModel = new LanmuModel('column');
	    $classidArray=$LanmuModel->getLanmuByclassid("");
	    $lanmu= $LanmuModel->child($classidArray);

	    $data = $LanmuModel->select(" select id,u1,u2,u3,u4,classid,u1 as name,classid as parentid,(@rowNum:=@rowNum+1) as rowNo from sl_column as a ,(Select (@rowNum :=0) ) b order by a.id asc,a.u2 asc");
	   
	    $che_list = count($data);
	    //挂载 TREE 类
	    $this->helper('tree');
	    $tree =new Tree() ;
	    $tree->tree($data);
	    //var_dump($data);die;
	    //$str = "<option value=\$id \$select>\$spacer\$name</option>";
	    $str="<tr>
    	         <td style='font-weight: 70; font-size: 14px'>
	                <div class='checkbox checkbox-inline checkbox-success'><input type='checkbox' data-id='qx_box_\$rowNo' name='colid[\$rowNo]' value='\$id' id='col_\$id'><label>\$spacer\$u1</label></div>
    				<input name='classid[\$rowNo]' type='text' value='\$classid' class='yincang'>
    				<input name='u1[\$rowNo]' type='text' value='\$u1' class='yincang'>
    				<input name='u2[\$rowNo]' type='text' value='\$u2' class='yincang'>
    				<input name='u3[\$rowNo]' type='text' value='\$u3' class='yincang'>
    				<input name='u4[\$rowNo]' type='text' value='\$u4' class='yincang'> 
                 </td>
	          </tr>
	        
	        ";
				
 
	    $html='';
	    $html .= $tree->get_tree(0,$str,-1);
	    
	    $html="
	        <table border='0' cellpadding='0' cellspacing='0' style='width: 100%; text-align: left'>
				<tbody>
					
	                   ".$html."
    	           
    			</tbody>
    		</table>
	        ";
	    
		include CUR_VIEW_PATH . "Sgroup".DS."group_edit.html";
	}

	//定义insert方法，完成用户组的插入
	public function insertAction(){
	    //获取该用户组信息
	    $groupModel = new Model("group");
	    $Common = new Common();
	    $column_groupModel = new model("column_group");
		//接受表单提交过来的数据
		$col_id = $_REQUEST['colid'];
		$classid= $_POST['classid'];
		$u1= $_POST['u1'];
		$u2= $_POST['u2'];
		$u3= $_POST['u3'];
		$u4= $_POST['u4'];
		//$group_id=$_SESSION["admin"]["group_id"];

		// 1.收集表单数据
		$data = $groupModel->getFieldArray();
		$this->helper("input");
		$data = deepspecialchars($data); //实体转义处理
		//var_dump($data);die();
		$group_id=$groupModel->insert($data);
		if ($group_id){
		    //写入另外权限操作表
		    foreach ($col_id as $k=>$v ){
		        $controller=$Common->getUrlParams($u3[$k])["c"];
		        $data1["id"]=$col_id[$k];
		        $data1["classid"]=$classid[$k];
		        $data1["u1"]=$u1[$k];
		        $data1["u2"]=$u2[$k];
		        $data1["u3"]=$u3[$k];
		        $data1["u4"]=$u4[$k];
		        $data1["laiyuan"]=$_SESSION["admin"]["username"];
		        $data1["group_id"]=$group_id;
		        $data1["controller"]=$controller;
		        
		        $column_groupModel->insert($data1);
		        
		        //var_dump($controller);
		    }
		    //添加成功
		    $this->jump("index.php?p=admin&c=group&a=index","添加用户组成功",2);
		}else {//添加失败
		    $this->jump("index.php?p=admin&c=group&a=add","添加用户组失败",3);
		}

		
	}

	//定义update方法，完成用户组的更新
	public function updateAction(){
	    //获取该用户组信息
	    $groupModel = new Model("group");
	    $Common = new Common();
	    $column_groupModel = new model("column_group");
	    //接受表单提交过来的数据
	    $col_id = $_REQUEST['colid'];
	    $classid= $_POST['classid'];
	    $u1= $_POST['u1'];
	    $u2= $_POST['u2'];
	    $u3= $_POST['u3'];
	    $u4= $_POST['u4'];
	    $group_id=trim($_REQUEST['id']);
	    
	   // var_dump($col_id);die();
		//删除用户组原有数据
		$column_groupModel->select("delete from sl_column_group where group_id={$group_id} ");
	    foreach ($col_id as $k=>$v ){
	        $controller=$Common->getUrlParams($u3[$k])["c"];
	        $data1["id"]=$col_id[$k];
	        $data1["classid"]=$classid[$k];
	        $data1["u1"]=$u1[$k];
	        $data1["u2"]=$u2[$k];
	        $data1["u3"]=$u3[$k];
	        $data1["u4"]=$u4[$k];
	        $data1["laiyuan"]=$_SESSION["admin"]["username"];
	        $data1["group_id"]=$group_id;
	        $data1["controller"]=$controller;
	        
	        $column_groupModel->insert($data1);
	        
	       // var_dump($data1);
	    }
	    $data = $groupModel->getFieldArray();
	    //var_dump($data);die();
	    if ($groupModel->update($data)>0){//添加成功
	        $this->jump("index.php?p=admin&c=group&a=index","修改用户组成功",2);
	    }else {//添加失败  
	        $this->jump("index.php?p=admin&c=group&a=edit&id={$data["id"]}","修改用户组失败",3);
	    }
	    
	}

	//定义delete方法，完成用户组的删除
	public function deleteAction(){
		//获取group_id
		$group_id = $_GET['id'] + 0;
		$groupModel = new Model("group");
		$group = $groupModel->selectByPk($group_id);
		
		if ($groupModel->delete($group_id)){
		    $column_groupModel = new model("column_group");
		    $column_groupModel->select("delete from sl_column_group where group_id={$group_id} ");
			$this->jump("index.php?p=admin&c=group&a=index","删除成功",2);
		}else{
			$this->jump("index.php?p=admin&c=group&a=index","删除失败",3);
		}
	}
}