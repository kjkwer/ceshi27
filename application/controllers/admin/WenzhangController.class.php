<?php
// 文章模型控制器
class WenzhangController extends BaseController
{
    
    // 显示文章模型列表
    public function indexAction()
    {
        $u6="";
        $model_id="";
        $sort_id="";
        if(!empty($_REQUEST['model_id']))
        {
            $model_id = $_REQUEST['model_id'];
        }
         
        if(!empty( $_REQUEST['sort_id']))
        {
            $sort_id = $_REQUEST['sort_id'];
        }
        if(!empty( $_REQUEST['u6']))
        {
            $u6 = $_REQUEST['u6'];
        }
         
        $sortModel=new SortModel("sort");
        //当前路径
        $curSortNames=$sortModel->getSortNames($sort_id,"");
        
        $where = " 1=1 ";
        // 获得当前表名
        $moxingModel = new MoxingModel("moxing");
        $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
        //先获取文章信息
        $tableModel = new Model($tableName);
        //查询条件
        if(trim(str_replace("1=1", " ", $tableModel->getSqlWhereStr()))!="")
        {
            $where .= " and ".$tableModel->getSqlWhereStr();
        }
        if(trim($tableModel->getDtimeSql())!='')
        {
            $where .=" and ".$tableModel->getDtimeSql();
        }
        
        //得到字段模型
        $filedModel=new FiledModel("filed");
        if(trim($u6)!='')
        {
            $filedListU6=$filedModel->select("select * from sl_filed where model_id='{$model_id}' and u6='是' ");//模糊查询字段
            if(count($filedListU6)>0)
            {
                $where=$where." and ";
                foreach ($filedListU6 as $v)
                {
                $where=$where."  {$v['u1']} like '%{$u6}%' or ";
                }
                $where=$where." 1=2 ";
            }
            
        }
        //需要显示的字段
        $filedLists=$filedModel->select("select * from sl_filed where model_id='{$model_id}' and u5='是' order by u10 asc ");//显示查询字段
        
        //获得状态栏目 status
        $statusList =$tableModel->select("select status from {$tableName} where sort_id='{$sort_id}' group by status ");
        // 载入分页类
        include LIB_PATH . "Page.class.php";
        // 获取wenzhang总的记录数
        $total = $tableModel->total($where);
        // 指定分页数，每一页显示的记录数
        $pagesize = 10;
        // $pagesize = $GLOBALS['config']['pagesize'];
        // 获取当前页数，默认是1
        $current = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($current - 1) * $pagesize;
        // 使用模型完成数据的查询
        $tableModel = $tableModel->pageRows($offset, $pagesize, $where);
        // 使用分页类获取分页信息
        $page = new Page($total, $pagesize, $current, "index.php", array(
            "p" => "admin",
            "c" => "wenzhang",
            "a" => "index",
            "model_id" => "{$model_id}",
            "sort_id" => "{$sort_id}"
        ));
        $pageinfo = $page->showPage();
       //var_dump($tableModel);die();
        include CUR_VIEW_PATH . "Swenzhang" . DS . "wenzhang_list.html";
    }
    
    // 载入添加文章模型页面
    public function addAction()
    {
        $model_id = $_REQUEST['model_id'];
        $sort_id = $_REQUEST['sort_id'];
        //得到字段模型
        $filedModel=new Model("filed");
        $filedLists=$filedModel->select("select * from sl_filed where model_id='{$model_id}'  order by  u10 asc,id desc ");//显示查询字段
        
        include CUR_VIEW_PATH . "Swenzhang" . DS ."wenzhang_add.html";
    }
    
    // 载入编辑文章模型页面
    public function editAction()
    {
        $model_id = $_REQUEST['model_id'];
        $sort_id = $_REQUEST['sort_id'];
        // 获取wenzhang_id
        $wenzhang_id = $_GET['id'] ;
        //得到字段模型
        $filedModel=new Model("filed");
        $filedLists=$filedModel->select("select * from sl_filed where model_id='{$model_id}'  order by  u10 asc,id desc ");//显示查询字段
        
        // 获得当前表名
        $moxingModel = new MoxingModel("moxing");
        $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
        //先获取文章信息
        $tableModel = new Model($tableName);
        
        $wenzhang = $tableModel->selectByPk($wenzhang_id);
        include CUR_VIEW_PATH . "Swenzhang" . DS . "wenzhang_edit.html";
    }
    
    // 定义insert方法，完成文章模型的插入
    public function insertAction()
    {
        $model_id = $_REQUEST['model_id'];
        $sort_id = $_REQUEST['sort_id'];
        // 获得当前表名
        $moxingModel = new MoxingModel("moxing");
        $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
        //先获取文章信息
        $tableModel = new Model($tableName);
        
        //1.收集表单数据
        $data=$tableModel->getFieldArray();
        
        //2.验证和处理

        $this->helper('input');
        $data = deepspecialchars($data);
        //$data = deepslashes($data);
        
        
            //如果字段设置为当前时间
            $filedModel=new Model("filed");
            $filedAraay=$filedModel->select("select u1 from sl_filed where model_id='{$model_id}' ");
            foreach ($filedAraay as $v)
            {
                $filedList=$filedModel->select("select * from sl_filed where  u7='时间框' and u8='CURRENT_TIMESTAMP' and model_id='{$model_id}' and u1='{$v['u1']}' ");
               // echo "select * from sl_filed where  u7='时间框' and u8='CURRENT_TIMESTAMP' and model_id='{$model_id}' and u1='{$v['u1']}' <br>";
                if(count($filedList)>0)
                {
                    $data[$v['u1']]= date('Y-m-d H:i:s',time());
                }
            }
            //如果字段设置为文件和图片
            foreach ($filedAraay as $v)
            {
                $filedList=$filedModel->select("select * from sl_filed where  u7='图片'  and model_id='{$model_id}' and u1='{$v['u1']}' ");
                //判断是否为图片，请图片参数不为空
                if(count($filedList)>0 )
                {
                    //echo "select * from sl_filed where  u7='图片'  and model_id='{$model_id}' and u1='{$v['u1']}'  <br>";
                    //$data[$v['u1']]= date('Y-m-d H:i:s',time());
                    //处理文件上传,需要使用到Upload.class.php
                    $this->library("Upload"); //载入文件上传类
                    $upload = new Upload(); //实例化上传对象
                    if ($filename = $upload->up($_FILES[$v['u1']])){
                        //成功
                        $data[$v['u1']] = $filename;
                        //调用模型完成入库操作，并给出相应的提示
                       
                    }else {
                        //print_r($_FILES[$v['u1']]);
                        //$this->jump('index.php?p=admin&c=wenzhang&a=add&model_id='.$model_id."&sort_id=".$sort_id.$data['sort_id'],$upload->error(),3);
                    }
                    
                }
            }
            
        
        
	    
	    //3调用模型完成入库并给出提示
	    if ($tableModel->insert($data)) {
	        $this->jump('index.php?p=admin&c=wenzhang&a=index&model_id='.$model_id."&sort_id=".$sort_id,'添加成功',2);
	    } else {
	        $this->jump('index.php?p=admin&c=wenzhang&a=add&model_id='.$model_id."&sort_id=".$sort_id.$data['sort_id'],'添加失败');
	    }
	    
	    
    }
    
    // 定义update方法，完成文章模型的更新
    public function updateAction()
    {
        $model_id = $_REQUEST['model_id'];
        $sort_id = $_REQUEST['sort_id'];
        
        // 获得当前表名
        $moxingModel = new MoxingModel("moxing");
        $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
        //先获取文章信息
        $tableModel = new Model($tableName);
        
        //1.收集表单数据
        $data=$tableModel->getFieldArray();
        //var_dump($data["content"]);die();
        //2.验证和处理

        $this->helper('input');
        $data = deepspecialchars($data);
            //如果字段设置为文件和图片
        $filedModel=new Model("filed");
        $filedAraay=$filedModel->select("select u1 from sl_filed where model_id='{$model_id}' ");
        foreach ($filedAraay as $v)
        {
            $filedList=$filedModel->select("select * from sl_filed where  u7='图片'  and model_id='{$model_id}' and u1='{$v['u1']}' ");
            //判断是否为图片，请图片参数不为空
            if(count($filedList)>0 )
            {
                //echo "select * from sl_filed where  u7='图片'  and model_id='{$model_id}' and u1='{$v['u1']}'  <br>";
                //$data[$v['u1']]= date('Y-m-d H:i:s',time());
                //处理文件上传,需要使用到Upload.class.php
                $this->library("Upload"); //载入文件上传类
                $upload = new Upload(); //实例化上传对象
                if ($filename = $upload->up($_FILES[$v['u1']])){
                    //成功
                    $data[$v['u1']] = $filename;
                    //调用模型完成入库操作，并给出相应的提示
                     
                }else {
                    //print_r($_FILES[$v['u1']]);
                    //$this->jump('index.php?p=admin&c=wenzhang&a=add&model_id='.$model_id."&sort_id=".$sort_id.$data['sort_id'],$upload->error(),3);
                }
        
            }
        }
        //调用模型完成更新
        
        if($tableModel->update($data)){
            $this->jump('index.php?p=admin&c=wenzhang&a=index&model_id='.$model_id."&sort_id=".$sort_id,"更新成功",2);
        }else{
            $this->jump('index.php?p=admin&c=wenzhang&a=edit&id='.$data['id'].'&model_id='.$model_id."&sort_id=".$sort_id.$data['sort_id'],"更新失败",2);
        }
    }
    
    // 定义delete方法，完成文章模型的删除
    public function deleteAction()
    {
        $model_id = $_REQUEST['model_id'];
        $sort_id = empty($_REQUEST['sort_id'])?'':$_REQUEST['sort_id'];
        $c=$sort_id==""?"autotable":"wenzhang";
        // 获取wenzhang_id
        if($_REQUEST['id']=='')
        {
            $this->jump('index.php?p=admin&c='.$c.'&a=index&model_id='.$model_id."&sort_id=".$sort_id,"删除失败，参数不能为空",3);
        }
        $sys_id = $_REQUEST['id'];
        $array_id=explode(",", $sys_id);
        $array_id=array_unique($array_id);
        
        // 获得当前表名
        $moxingModel = new MoxingModel("moxing");
        $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
        //先获取文章信息
        $tableModel = new Model($tableName);
        
        if ($tableModel->delete($array_id)!="false") {
            $this->jump('index.php?p=admin&c='.$c.'&a=index&model_id='.$model_id."&sort_id=".$sort_id, "删除成功", 2);
        } else {
            $this->jump('index.php?p=admin&c='.$c.'&a=index&model_id='.$model_id."&sort_id=".$sort_id, "删除失败", 3);
        }
    }
    
    // 定义批量更新某列值方法
    public function updatemoreAction()
    {
        $model_id = $_REQUEST['model_id'];
        $sort_id = $_REQUEST['sort_id'];
        $status = $_REQUEST['status'];
        // 获取wenzhang_id
        if($_REQUEST['id']=='')
        {
            $this->jump('index.php?p=admin&c=wenzhang&a=index&model_id='.$model_id."&sort_id=".$sort_id,"更新失败，参数不能为空",3);
        }
        $sys_id = $_REQUEST['id'];
        $array_id=explode(",", $sys_id);
        $array_id=array_unique($array_id);
        foreach ($array_id as $k=>$v)
        {
            $ids.=",".$v;
        }
        $ids=substr($ids,1);
        // 获得当前表名
        $moxingModel = new MoxingModel("moxing");
        $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
        //先获取文章信息
        $tableModel = new Model($tableName);
    
        if ($tableModel->query(" UPDATE {$tableName} SET `status` ='{$status}' where id in ({$ids})")) {
            $this->jump('index.php?p=admin&c=wenzhang&a=index&model_id='.$model_id."&sort_id=".$sort_id, "更新成功", 2);
        } else {
            $this->jump('index.php?p=admin&c=wenzhang&a=index&model_id='.$model_id."&sort_id=".$sort_id, "更新失败", 3);
        }
    }
    
    //百度推送
    public function postToBaiduAction() {
        $model_id = $_REQUEST['model_id'];
        $sort_id = $_REQUEST['sort_id'];
        // 获得当前表名
        $moxingModel = new MoxingModel("moxing");
        $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
        //先获取文章信息
        $tableModel = new Model($tableName);
        $table=$tableModel->select("select * from {$tableName} where sort_id={$sort_id}");
        
        foreach ($table as $t)
        {
            $urls[]="http://www.cdweni.com/index.php?c=page&id=".$t['id'];
        }
       /*  print_r($urls);
        die(); */
        $api = 'http://data.zz.baidu.com/urls?site=www.cdweni.com&token=4d1ZsftIGsqu4yed';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo $result;
        ob_flush();
        flush();
        
    }
    
    //百度推送  逐条推送
    public function postToBaiduByIdAction() {
        $ids = $_REQUEST['id'];
        $array_id=explode(",", $ids);
        $array_id=array_unique($array_id);
        foreach ($array_id as $k=>$v)
        {
            $urls[]="http://www.cdweni.com/index.php?c=page&id=".$v;
        }
        //print_r($urls);
        $api = 'http://data.zz.baidu.com/urls?site=www.cdweni.com&token=4d1ZsftIGsqu4yed';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo $result;
        ob_flush();
        flush();

// $urls = array(
//     'http://www.cdweni.com/index.php?c=page&id=2566',
//     'http://www.cdweni.com/index.php?c=page&id=2565',
// );
// $api = 'http://data.zz.baidu.com/urls?site=www.cdweni.com&token=4d1ZsftIGsqu4yed';
// $ch = curl_init();
// $options =  array(
//     CURLOPT_URL => $api,
//     CURLOPT_POST => true,
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_POSTFIELDS => implode("\n", $urls),
//     CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
// );
// print_r($options);
// curl_setopt_array($ch, $options);
// $result = curl_exec($ch);
// echo $result;
    
    }
    
    
}