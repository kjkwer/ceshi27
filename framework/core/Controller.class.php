<?php
//核心控制器
class Controller {
	//跳转方法
	public function jump($url,$message,$wait = 4){
		if ($wait == 0) {
			header("Location:$url");
		} else {
			
			include CUR_VIEW_PATH . "message.html";
		}
		exit(); //一定要退出 die一样
	}

	//引入工具类模型方法 
	public function library($lib){
		include LIB_PATH . "{$lib}.class.php";
	}
	
	public function library2($lib){
	    include LIB_PATH . "{$lib}.php";
	}

	//引入辅助函数方法
	public function helper($helper){
		include HELPER_PATH . "{$helper}.php";
	}
}