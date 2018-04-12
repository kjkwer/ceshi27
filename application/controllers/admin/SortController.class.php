<?php
//分类控制器
class SortController extends BaseController{
	
	//显示分类列表
	public function indexAction(){
	    $model_id=empty($_REQUEST['model_id'])? "" : $_REQUEST['model_id']; //
	    $sort_id=empty($_REQUEST['sort_id'])? "" : $_REQUEST['sort_id'];
	    $sortModel = new SortModel("sort");
	    if($sort_id=='')
	    {
	        $sort=$sortModel->select("select *,(SELECT u1 FROM sl_sort as s1 where s1.id=s.sort_id ) as sort_name from sl_sort as s where model_id='{$model_id}' and sort_id='0' order by paixu asc, id desc ");
	    }else {
	        $sort=$sortModel->select("select *,(SELECT u1 FROM sl_sort as s1 where s1.id=s.sort_id ) as sort_name from sl_sort as s where model_id='{$model_id}' and sort_id='{$sort_id}' order by paixu asc, id desc ");
	    }
	    //如果是栏目的最后一层，则跳转到文章列表
	    $count_sort=$sortModel->select("SELECT * from sl_sort where sort_id='{$sort_id}' and model_id='{$model_id}' order by id desc ");
	    if(count($count_sort)==0&&$sort_id!='')
	    {
	       header("Location:/index.php?p=admin&c=wenzhang&a=index&model_id={$model_id}&sort_id={$sort_id}");
	    }
	    
	    
		include CUR_VIEW_PATH  ."Ssort". DS . "sort_list.html";
	}

	//载入添加分类页面
	public function addAction(){
        $model_id=empty($_REQUEST['model_id'])? "" : $_REQUEST['model_id']; //
        $sort_id=empty($_REQUEST['sort_id'])? "" : $_REQUEST['sort_id'];
	    $sortModel = new SortModel("sort");
	    $sort=$sortModel->select("select * from sl_sort where model_id='{$model_id}' ");
	    
	    $common =new Common();
	    $fileArray=  $common->get_onedirfiles("./application/views/home/templates/defualt/");
	    
	    $moxingModel=new MoxingModel("moxing");
	    $moxing=$moxingModel->selectByPk($model_id);
	    
		include CUR_VIEW_PATH  ."Ssort". DS . "sort_add.html";
	}

	//载入编辑分类页面
	public function editAction(){
        $model_id=empty($_REQUEST['model_id'])? "" : $_REQUEST['model_id']; //
        $sort_id=empty($_REQUEST['sort_id'])? "" : $_REQUEST['sort_id'];
	    $sortModel = new SortModel("sort");
	    $sort_array=$sortModel->select("select * from sl_sort where model_id='{$model_id}' ");
	    $common =new Common();
	    $fileArray=  $common->get_onedirfiles("./application/views/home/templates/defualt/");
	    //分类的id
	    $id=$_GET['id'];
	    if($_REQUEST['id']=='')
	    {
	        $this->jump("index.php?p=admin&c=sort&a=index&model_id=".$model_id."&sort_id=".$sort_id,"修改失败，参数不能为空",3);
	    }
	    $sort=$sortModel->selectByPk($id);
		include CUR_VIEW_PATH ."Ssort" . DS . "sort_edit.html";
	}

	//定义insert方法，完成分类的插入
	public function insertAction(){
	    $sortModel = new SortModel("sort");
	    //1.收集表单数据
	    $data=$sortModel->getFieldArray();
	    //2.验证和处理
 	    if ($data['u1'] == '') {
 	        $this->jump('index.php?p=admin&c=sort&a=add&model_id='.$data['model_id'].'&sort_id='.$data['sort_id'],'列表标题不能为空',2);
 	    }
// 	    if ($data[u2] == '') {
// 	        $this->jump('index.php?p=admin&c=sort&a=add&model_id='.$data[model_id].'&sort_id='.$data[sort_id],'列表页模版不能为空',2);
// 	    }
// 	    if ($data[u3] == '') {
// 	        $this->jump('index.php?p=admin&c=sort&a=add&model_id='.$data[model_id].'&sort_id='.$data[sort_id],'介绍页模板不能为空',2);
// 	    }
	    if ($data['model_id'] == '') {
	        $this->jump('index.php?p=admin&c=sort&a=add&model_id='.$data['model_id'].'&sort_id='.$data['sort_id'],'model_id不能为空',2);
	    }
	    
	    //var_dump($data)  ;die();
	    $u1u2=$sortModel->select("SELECT * from sl_sort where u1='{$data['u1']}' and model_id='{$data['model_id']}' and sort_id='{$data['sort_id']}' order by id desc ");
	    if(count($u1u2)>0)
	    {
	        $this->jump("index.php?p=admin&c=moxing&a=add","列表标题已存在",2);
	    }
	   
	    //判断是否为图片，请图片参数不为空
	    if(($_FILES["u5"]["size"]>0))
	    {
	       
	        $this->library("Upload"); //载入文件上传类
	        $upload = new Upload(); //实例化上传对象
	        if ($filename =$upload->up($_FILES['u5'])){
	            //成功
	            $data['u5'] = $filename;
	            //调用模型完成入库操作，并给出相应的提示
	             
	        }else {
	            //print_r($_FILES[$v['u1']]);
	            $this->jump('index.php?p=admin&c=sort&a=index&model_id='.$data['model_id']."&sort_id=".$data['sort_id'],$upload->error(),3);
	        }
	    
	    }
	     
	    $this->helper('input');
	    //$data = deepspecialchars($data);
	    //$data = deepslashes($data);
	     
	    
	    //3调用模型完成入库并给出提示
	    if ($sortModel->insert($data)) {
	       
	        $this->jump('index.php?p=admin&c=sort&a=index&model_id='.$data['model_id']."&sort_id=".$data['sort_id'],'添加成功',2);
	    } else {
	       
	        $this->jump('index.php?p=admin&c=sort&a=index&model_id='.$data['model_id']."&sort_id=".$data['sort_id'],'添加失败');
	    }
	    
		
	}

	//定义update方法，完成分类的更新
	public function updateAction(){
	    $sortModel = new SortModel("sort");
	    //1.收集表单数据
	    $data=$sortModel->getFieldArray();
	    //2.验证和处理
// 	    if ($data[u1] == '') {
// 	        $this->jump('index.php?p=admin&c=sort&a=edit&model_id='.$data[model_id].'&sort_id='.$data[sort_id],'列表标题不能为空',2);
// 	    }
// 	    if ($data[u2] == '') {
// 	        $this->jump('index.php?p=admin&c=sort&a=edit&model_id='.$data[model_id].'&sort_id='.$data[sort_id],'列表页模版不能为空',2);
// 	    }
// 	    if ($data[u3] == '') {
// 	        $this->jump('index.php?p=admin&c=sort&a=edit&model_id='.$data[model_id].'&sort_id='.$data[sort_id],'介绍页模板不能为空',2);
// 	    }
	    if ($data['model_id'] == '') {
	        $this->jump('index.php?p=admin&c=sort&a=edit&model_id='.$data[model_id].'&sort_id='.$data[sort_id],'model_id不能为空',2);
	    }
	     
	    //判断是否为图片，请图片参数不为空
	    if(($_FILES["u5"]["size"]>0))
	    {
	    
	        $this->library("Upload"); //载入文件上传类
	        $upload = new Upload(); //实例化上传对象
	        if ($filename =$upload->up($_FILES['u5'])){
	            //成功
	            $data['u5'] = $filename;
	            //调用模型完成入库操作，并给出相应的提示
	    
	        }else {
	            //print_r($_FILES[$v['u1']]);
	            $this->jump('index.php?p=admin&c=sort&a=index&model_id='.$data['model_id']."&sort_id=".$data['sort_id'],$upload->error(),3);
	        }
	         
	    }
	    $this->helper('input');
// 	    $data = deepspecialchars($data);
// 	    $data = deepslashes($data);
	    
	     
	    //3调用模型完成入库并给出提示
	    if ($sortModel->update($data)) {
	    
	        $this->jump('index.php?p=admin&c=sort&a=index&model_id='.$data['model_id']."&sort_id=".$data['sort_id'],'修改成功',2);
	    } else {
	    
	        $this->jump('index.php?p=admin&c=sort&a=index&model_id='.$data['model_id']."&sort_id=".$data['sort_id'],'修改失败');
	    }
	}

	//定义delete方法，完成分类的删除
	public function deleteAction(){
        $model_id=empty($_REQUEST['model_id'])? "" : $_REQUEST['model_id']; //
        $sort_id=empty($_REQUEST['sort_id'])? "" : $_REQUEST['sort_id'];
	    //获取sort_id
	    if($_REQUEST['id']=='')
	    {
	        $this->jump("index.php?p=admin&c=sort&a=index&model_id=".$model_id."&sort_id=".$sort_id,"删除失败，参数不能为空",3);
	    }
	    $id = $_GET['id'];
	    $array_id=explode(",", $id);
	    $sortModel = new sortModel("sort");
	   
	    if ($sortModel->delete($array_id)!="false"){
	        $this->jump("index.php?p=admin&c=sort&a=index&model_id=".$model_id."&sort_id=".$sort_id,"删除成功",2);
	    }else{
	        $this->jump("index.php?p=admin&c=sort&a=index&model_id=".$model_id."&sort_id=".$sort_id,"删除失败",3);
	    }
	    
	}
}