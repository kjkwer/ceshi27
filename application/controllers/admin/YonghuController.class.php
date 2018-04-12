<?php
// 品牌控制器
class YonghuController extends BaseController
{
    //显示管理员列表
    public function indexAction(){
        //先获取管理员信息
        $adminModel = new Model("admin");
        // $admins = $adminModel->getadmins();
        //载入分页类
        $where = ""; //查询条件
        $this->library("Page");
        //获取admin总的记录数
        $total = $adminModel->total($where);
        //指定分页数，每一页显示的记录数
        $pagesize = 10;
        // $pagesize = $GLOBALS['config']['pagesize'];
        //获取当前页数，默认是1
        $current = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ( $current - 1 ) * $pagesize;
        //使用模型完成数据的查询
        $admins = $adminModel->pageRows($offset,$pagesize);
        foreach ($admins as $k=>$v)
        {
            $groupModel = new Model("group");
            $admins[$k]["zuming"]=$groupModel->selectByPk($v["group_id"])["zuming"];
        }
        //使用分页类获取分页信息
        $page = new Page($total,$pagesize,$current,"index.php",array("p"=>"admin","c"=>"yonghu","a"=>"index"));
        $pageinfo = $page->showPage();
        include CUR_VIEW_PATH . "Syonghuguanli".DS."yonghu_list.html";
    }
    
    //载入添加管理员页面
    public function addAction(){
        $groupModel = new Model("group");
        $cunModel = new Model("canshu");
        $cun =$cunModel->select("select * from sl_canshu where classid=211 ");
        $group=$groupModel->select("select * from sl_group ");
        include CUR_VIEW_PATH . "Syonghuguanli".DS."yonghu_add.html";
    }
    
    //载入编辑管理员页面
    public function editAction(){
        // 先获取品牌信息
        $adminModel = new Model("admin");
        $groupModel = new Model("group");
        $cunModel = new Model("canshu");
        $group=$groupModel->select("select * from sl_group ");
        $cun =$cunModel->select("select * from sl_canshu where classid=211 ");
        $user_id=trim($_REQUEST["id"]);
        $adminDetail = $adminModel->selectByPk($user_id);
        // print_r($adminDetail);
        include CUR_VIEW_PATH . "Syonghuguanli" . DS . "yonghu_edit.html";
    }
    
    //定义insert方法，完成管理员的插入
    public function insertAction(){
        // //获取条件及数据
        $adminModel = new AdminModel('admin');
        $data['username'] = trim($_POST['username']);
        $data['password'] = trim($_POST['password']);
        $data['pic'] = trim(empty($_POST['pic'])?"":$_POST['pic']);
        $data['group_id'] = trim($_POST['group_id']);
        $data['nickname'] = trim($_POST['nickname']);
        $data['create_time'] = time();
        if(empty( $data['username']))
        {
            $this->jump("index.php?p=admin&c=yonghu&a=add", "用户名不能为空", 3);
        }
        if(empty( $data['password']))
        {
            $this->jump("index.php?p=admin&c=yonghu&a=add", "密码不能为空", 3);
        }else 
        {
            $data['password']=md5($data['password']);
        }
        
        //处理文件上传,需要使用到Upload.class.php
        if($data['pic']!="")
        {
            $this->library("Upload"); //载入文件上传类
            $upload = new Upload(); //实例化上传对象
            if ($filename = $upload->up($_FILES["pic"])){
                //成功
                $data['pic']= $filename;
                //调用模型完成入库操作，并给出相应的提示
            
            }else {
                //print_r($_FILES[$v['u1']]);
                //$this->jump("index.php?p=admin&c=yonghu&a=edit&id={$data['user_id']}", "头像上传失败", 2);
            } 
        }
        
        // 调用模型完成更新
        $Common = new Common();
        $System = new SystemModel('system');
        if ($adminModel->insert($data)) {
            // 写入日志
            $System->addSystem($_SESSION['admin']['username'], $_SESSION['admin']['username'] . ":增加用户信息：成功。操作页面:" . $Common->getUrl(), $Common->getIP(), "用户管理");
            $this->jump("index.php?p=admin&c=yonghu&a=index", "添加成功", 2);
        } else {
            $System->addSystem($_SESSION['admin']['username'], $_SESSION['admin']['username'] . ":增加用户信息：失败。操作页面:" . $Common->getUrl(), $Common->getIP(), "用户管理");
            $this->jump("index.php?p=admin&c=yonghu&a=index", "添加失败", 2);
        }
        
        
    }
    
    //定义update方法，完成管理员的更新
    public function updateAction(){
        // //获取条件及数据
        $adminModel = new AdminModel('admin');
        $data = $adminModel->getFieldArray();
        $data['new_password'] = empty($_POST['new_password'])?"":$_POST['new_password'];
        $data['re_password'] = empty($_POST['re_password'])?"":$_POST['re_password'];
        //var_dump($data);die();
//         $data['username'] = trim($_POST['username']);
//         $data['email'] = trim($_POST['email']);
//         $data['password'] = trim($_POST['password']);
//         $data['new_password'] = trim($_POST['new_password']);
//         $data['re_password'] = trim($_POST['re_password']);
//         $data['user_id'] = trim($_POST['user_id']);
//         $data['pic'] = trim($_POST['pic']);
//         $data['group_id'] = trim($_POST['group_id']);
//         $data['cun_id'] = trim($_POST['cun_id']);
//         $data['nickname'] = trim($_POST['nickname']);
        if( $data['password'] == '' && $data['new_password'] == '' && $data['re_password'] == '')
        {
            //不修改密码
            //1.收集表单数据
            $data=$adminModel->getFieldArray();
        }else 
        {
            if ($data['username'] == '' || $data['password'] == '' || $data['new_password'] == '' || $data['re_password'] == '') {
                $this->jump("index.php?p=admin&c=yonghu&a=edit&id={$data['user_id']}", "用户名或当前密码或新密码或确认密码为空", 2);
            }
            if ($data['new_password'] != $data['re_password']) {
                $this->jump("index.php?p=admin&c=yonghu&a=edit&id={$data['user_id']}", "新密码与确认密码不相同", 2);
            }
            
            $user = $adminModel->checkUser($data['username'], $data['password']);
            if ($user) {
                // 当前密码正确
                $data['password'] = md5($data['new_password']);
            } else {
                $this->jump("index.php?p=admin&c=yonghu&a=edit&id={$data['user_id']}", "当前密码不正确", 2);
            }
        }
        
       //处理文件上传,需要使用到Upload.class.php
        if($data['pic']!="")
        {
            $this->library("Upload"); //载入文件上传类
            $upload = new Upload(); //实例化上传对象
            if ($filename = $upload->up($_FILES["pic"])){
                //成功
                $data['pic']= $filename;
                //调用模型完成入库操作，并给出相应的提示
            
            }else {
                //print_r($_FILES[$v['u1']]);
                //$this->jump("index.php?p=admin&c=yonghu&a=edit&id={$data['user_id']}", "头像上传失败", 2);
            } 
        }
        
        // 调用模型完成更新
        $Common = new Common();
        $System = new SystemModel('system');
       // var_dump($data);die();
        if ($adminModel->update($data)) {
            
//             $_SESSION['admin']['group_id']=trim($_POST['group_id']);
//             $_SESSION['admin']['pic']=$data['pic'];
//             $_SESSION['admin']['username']=$data['username'];
            // 写入日志
            $System->addSystem($_SESSION['admin']['username'], $_SESSION['admin']['username'] . ":更改用户信息：成功。操作页面:" . $Common->getUrl(), $Common->getIP(), "用户管理");
            $this->jump("index.php?p=admin&c=yonghu&a=edit&id={$data['user_id']}", "更新成功", 2);
        } else {
            $System->addSystem($_SESSION['admin']['username'], $_SESSION['admin']['username'] . ":更改用户信息：失败。操作页面:" . $Common->getUrl(), $Common->getIP(), "用户管理");
            $this->jump("index.php?p=admin&c=yonghu&a=edit&id={$data['user_id']}", "更新失败", 2);
        }
    }
    
    //定义delete方法，完成管理员的删除
    public function deleteAction(){
        //获取admin_id
        $admin_id = $_GET['id'] + 0;
        $adminModel = new Model("admin");
        $admin = $adminModel->selectByPk($admin_id);
        //得到图片的全路径
        $img = UPLOAD_PATH . $admin['pic'];
        if ($adminModel->delete($admin_id)){
            //成功的同时删除对应的图片
            @unlink($img);
            $this->jump("index.php?p=admin&c=yonghu&a=index","删除成功",1);
        }else{
            $this->jump("index.php?p=admin&c=yonghu&a=index","删除失败",3);
        }
    }
    
}