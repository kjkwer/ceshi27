<?php
//首页控制器
class SingleController extends  BaseController {
	//index方法
	public function indexAction(){
	    $Common=new Common();
	    $moban=$Common->SafeFilterStr(trim($_REQUEST['m'])).'.html';
	    
	    
		//$this->smarty->display("defualt/".$moban);
		//加载对应的页面（微信端或者电脑端）
		$pageFilePath=CUR_VIEW_PATH."templates".DS.$this->templates;
		if(file_exists($pageFilePath.DS.$moban))
		{
		    //echo $this->templates.DS.$moban;die();
		    $this->smarty->display($this->templates.DS.$moban);
		}else
		{
		    $this->smarty->display("defualt".DS.$moban);
		}
	}
	
}