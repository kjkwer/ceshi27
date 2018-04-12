<?php
//导入数据控制器
class DaoruController extends BaseController{
	
	 
	//载入添加导入数据页面
	public function addAction(){
	    
	    include CUR_VIEW_PATH ."Sdaoru".DS. "daoru_add.html";
	}

	 

	//定义insert方法，完成导入数据的插入
	public function insertAction(){
	    require_once(LIB_PATH."PHPExcel-1.8".DS."Classes".DS."PHPExcel.php");
	    $filename="";
	    //上传文件到服务器
	    $this->library("Upload"); //载入文件上传类
	    $upload = new Upload(); //实例化上传对象
	    if ($filename = $upload->up($_FILES["wenjian"])){
	        //成功
	        echo "上传文件成功<br/>";
	        flush();
	         
	    }
        
	    //导入的表名
        $t = $_REQUEST["t"];	
        if(empty($t))
        {
            echo "请选择需要导入的表";
            die();
        }
        
       
        //文件名为文件路径和文件名的拼接字符串
        //$objReader = \PHPExcel_IOFactory::createReader('Excel5');//创建读取实例
        $objPHPExcel = PHPExcel_IOFactory::load($filename);//加载文件
        $sheet = $objPHPExcel->getSheet(0);//取得sheet(0)表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        
        //设置村关系
        //如果是村管理员1
        $adminModel = new AdminModel('admin');
        $user = $adminModel->selectByPk($_SESSION['admin']['user_id']);
        //村ID
        $cun_id =  $user["cun_id"];
        
        //导入员工
        if($t="user")
        {
            $userModel = new Model("user");
            for($i=2;$i<=$highestRow;$i++)
            {
                $data['yonghuming']=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                $data['xingming'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                $data['mima'] =md5(substr($data['yonghuming'],5,6));
                $data['suoshucun']=$cun_id;
                $data['suoshuzu']=$cun_id;
                $data['daorurenxinxi']="姓名:".$user["nickname"]." 用户名:".$user["username"];
                
                if(count($userModel->select("select * from sl_user where yonghuming='{$data['yonghuming']}' "))>0)
                {
                    //用户名已存在
                    echo $data['yonghuming']."导入失败：用户名已存在<br/>";
                    continue;
                }else
                {
                    $userModel->insert($data);
                    echo $data['yonghuming']."导入成功<br/>";
                    flush();
                }
                
            }
        }else if($t="renyuan") //导入人员
        {
           $renyuanModel = new model("renyuan");
           for($i=2;$i<=$highestRow;$i++)
           {
               $data['yonghuming']=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
               $data['xingming'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
              
               
               if(count($userModel->select("select * from sl_user where yonghuming='{$data['yonghuming']}' "))>0)
               {
                   //用户名已存在
                   echo $data['yonghuming']."导入失败：用户名已存在<br/>";
                   continue;
               }else
               {
                   $userModel->insert($data);
                   echo $data['yonghuming']."导入成功<br/>";
                   flush();
               }
               
           }
           
           
        }else if($t="huji") //导入户籍
        {
            
        }
       
	       

		
	}
 
}