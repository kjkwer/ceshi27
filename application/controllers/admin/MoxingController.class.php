<?php
//模型控制器
class MoxingController extends BaseController{
	
	//显示模型列表
	public function indexAction(){
	    $moxingModel = new MoxingModel("moxing");
	    $model=$moxingModel->select("SELECT *,(SELECT u2 from sl_canshu where u1=mx.u3 ) as moxinglianjie from sl_moxing as mx order by id desc"); 
	   // print_r($moxingModel->getFieldsAndTypes());
		include CUR_VIEW_PATH ."Smoxing".DS. "moxing_list.html";
	}

	//载入添加模型页面
	public function addAction(){
	    $canshuModel = new Model("canshu");
	    $u3=$canshuModel->select("SELECT * from sl_canshu where classid='1' order by id desc");
	    $common =new Common();
	    $fileArray=  $common->get_onedirfiles("./application/views/".PLATFORM,"html");
		include CUR_VIEW_PATH . "Smoxing".DS. "moxing_add.html";
	}

	//载入编辑模型页面
	public function editAction(){
		//获取该模型信息
		$moxingModel = new MoxingModel("moxing");
		//条件
		$moxing_id = $_GET['id']; //出于考虑
		//使用模型获取
		$moxing = $moxingModel->selectByPk($moxing_id);
		// var_dump($brand);
		//初始化其他选项
		$canshuModel = new Model("canshu");
		$u3=$canshuModel->select("SELECT * from sl_canshu where classid='1' order by id desc");
		$common =new Common();
		$fileArray=  $common->get_onedirfiles("./application/views/".PLATFORM,"html");
		
		include CUR_VIEW_PATH ."Smoxing".DS. "moxing_edit.html";
	}

	//定义insert方法，完成模型的插入
	public function insertAction(){
	    $moxingModel = new MoxingModel("moxing");
	    //1.收集表单数据
	    $data=$moxingModel->getFieldArray();
	    //2.验证和处理
	    if ($data['u1'] == '') {
	        $this->jump('index.php?p=admin&c=moxing&a=add','模型名不能为空',2);
	    }
	    if ($data['u2'] == '') {
	        $this->jump('index.php?p=admin&c=moxing&a=add','模型别名不能为空',2);
	    }
	    if ($data['u3'] == '') {
	        $this->jump('index.php?p=admin&c=moxing&a=add','模型类型不能为空',2);
	    }
	    $u1u2=$moxingModel->select("SELECT * from sl_moxing where u1='{$data['u1']}' or u2='{$data['u2']}'  ");
	    if(count($u1u2)>0)
	    {
	        $this->jump("index.php?p=admin&c=moxing&a=add","模型名或模型别名已存在",2);
	    }
	    
	    
	    $this->helper('input');
	    $data = deepspecialchars($data);
	    $data = deepslashes($data);
	    
	   $moxingModel->start_T();
	    //3调用模型完成入库并给出提示
	    if ($moxingModel->insert($data)) {
	        //4.创建数据表 
	        $moxingModel->addMoxingByType($data['u3'], $data['u1']);
	        $this->jump('index.php?p=admin&c=moxing&a=index','添加成功',2);
	    } else {
	        $moxingModel->roll_T();
	        $this->jump('index.php?p=admin&c=moxing&a=add','添加失败');
	    }
	    $moxingModel->comit_T();
	}

	//定义update方法，完成模型的更新
	public function updateAction(){
	    $moxingModel = new MoxingModel("moxing");
	    //1.收集表单数据
	    $data=$moxingModel->getFieldArray();
	    //2.验证和处理
	    if ($data['u1'] == '') {
	        $this->jump('index.php?p=admin&c=moxing&a=add','模型名不能为空',2);
	    }
	    if ($data['u2'] == '') {
	        $this->jump('index.php?p=admin&c=moxing&a=add','模型别名不能为空',2);
	    }
	    if ($data['u3'] == '') {
	        $this->jump('index.php?p=admin&c=moxing&a=add','模型类型不能为空',2);
	    }

		
		//调用模型完成更新
		
		if($moxingModel->update($data)){
			$this->jump("index.php?p=admin&c=moxing&a=index","更新成功",2);
		}else{
			$this->jump("index.php?p=admin&c=moxing&a=edit&id=".$data['id'],"更新失败",2);
		}
		
		
	}

	//定义delete方法，完成模型的删除
	public function deleteAction(){
		//获取brand_id
		$moxing_id = $_GET['id'];
		$moxingModel = new MoxingModel("moxing");
		$moxing_name=$moxingModel->oneRowCol("u1", "id='{$moxing_id}'")['u1'];
		//删除表和sl_fileds 里对应的字段
		$moxingModel ->deleteModel($moxing_id,$moxing_name);
		
		if ($moxingModel->delete($moxing_id)){ 
			$this->jump("index.php?p=admin&c=moxing&a=index","删除成功",2);
		}else{
			$this->jump("index.php?p=admin&c=brand&a=index","删除失败",3);
		}
	}
}