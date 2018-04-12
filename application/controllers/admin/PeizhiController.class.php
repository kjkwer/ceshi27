<?php
//品牌控制器
class PeizhiController extends BaseController{
	
	//显示品牌列表
	public function indexAction(){
		
		include CUR_VIEW_PATH ."Speizhi".DS."peizhi.html";
	}

	

	
	//定义update方法，完成品牌的更新
	public function updateAction(){
	    //配置文件地址
	    $filename =  CONFIG_PATH . "config.cache.php";
	    //检查文件是否可写
	    if (is_writable($filename) == false) {
	        $this->jump("index.php?p=admin&c=peizhi&a=index","请检查[]文件权限是否可写！",2);
	    
	    }
	    
		//获取条件及数据
		foreach ($GLOBALS['config_cache'] as $key=>$val)
		{
		    
		    $data[$key] = trim(empty($_POST[$key])?"":$_POST[$key]);
		}
		
		$settingstr = "<?php \n return array(\n";
		foreach ( $data as $key => $v ) {
		    $settingstr .= "\n\t'" . $key . "'=>'" . $v . "',";
		}
		$settingstr .= "\n);\n\n";
		
		
		file_put_contents ( $filename, $settingstr ); // 通过file_put_contents保存setting.config.php文件；
		$this->jump("index.php?p=admin&c=peizhi&a=index","修改成功",2);
		
		
		
	}

	
}