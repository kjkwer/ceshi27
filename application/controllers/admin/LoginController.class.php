<?php
// 后台登录控制器
class LoginController extends Controller
{
    // 显示登录页面
    public function indexAction()
    {
        // 载入登录页面视图
        include CUR_VIEW_PATH . "login.html";
    }
    // 显示登录页面
    public function loginAction()
    {
        // 载入登录页面视图
        include CUR_VIEW_PATH . "login.html";
    }
    
    // 验证用户名和密码
    public function signinAction()
    {
        
        // 1.获取用户名和密码
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        // 对用户名和密码进行转义
        $username = addslashes($username);
        $password = addslashes($password);
        
        // 2.判断登录频率
        $Common = new Common();
        $System = new SystemModel('system');
        $date1 = date('Y-m-d H:i:s', time());
        $date2 = $date1;
        $System1 = new SystemModel('system');
        
        $date2 = $System1->oneRowCol("dtime", " u3='" . $Common->getIP() . "' and u4='管理员登录' order by id desc ");
        $date2 = $date2['dtime'];
        $minute = floor((strtotime($date1) - strtotime($date2)) % 86400 / 60);
        
        // echo $date1." date1<br>";
        // echo $date2." date2<br>";
        // echo $minute."minute3<br>";
        // die();
        
//         if ($minute < 1) {
//             $this->jump('index.php?p=admin&c=login&a=login', '您的操作过于频繁');
//         }
        // 验证和处理
        if (! ($Common->isEmpty($username) || $Common->isEmpty($password))) {
            $this->jump('index.php?p=admin&c=login&a=login', '用户名或密码不能为空');
        }
        if (! ($Common->isName($username) || $Common->isName($password))) {
            
            // 写入日志
            $System->addSystem($username, $username . ":登录失败，用户名或密码不合法。操作页面:" . $Common->getUrl(), $Common->getIP(), "管理员登录");
            $this->jump('index.php?p=admin&c=login&a=login', '用户名或密码不合法');
        }
        
        // 3.调用模型来完成验证操作并给出提示
        $adminModel = new AdminModel('admin');
        $user = $adminModel->checkUser($username, $password);
        if ($user) {
            $groupModel = new Model("group");
            $user["zuming"]=$groupModel->selectByPk($user["group_id"])["zuming"];
            // 登录成功,保存登录标识符
            $admin_arr =$user;
            //$admin_arr =$user;
            
            $_SESSION['admin']['username'] = $admin_arr['username'];
            $_SESSION['admin']['user_id'] = $admin_arr['user_id'];
            $_SESSION['admin']['password'] = $admin_arr['password'];
            $_SESSION['admin']['pic'] = $admin_arr['pic'];
            $_SESSION['admin']['group_id'] = $admin_arr['group_id'];
            $_SESSION['admin']['zuming'] = $admin_arr['zuming'];
            
            //更新登录时间和登录IP
            $data3["last_login_time"]=time();
            $data3["last_login_ip"]=$Common->getIP();
            $data3["user_id"]=$admin_arr['user_id'];
            $adminModel->update($data3);
            
            // 写入日志
            $System->addSystem($username, $username."(". $_SESSION['admin']['zuming'].")" . ":登录成功,操作页面:" . $Common->getUrl(), $Common->getIP(), "管理员登录");
            // 跳转
            $this->jump('index.php?p=admin&c=index&a=index', '', 0);
        } else {
            // 写入日志
            $System->addSystem($username, $username . ":登录失败，用户名或密码错误。操作页面:" . $Common->getUrl(), $Common->getIP(), "管理员登录");
            // 失败
            $this->jump('index.php?p=admin&c=login&a=login', '用户名或密码错误');
        }
    }
    
    // 注销
    public function logoutAction()
    {
        // 删除session中的变量
        unset($_SESSION['admin']);
        // 销毁session
        session_destroy();
        // 跳转
        $this->jump('index.php?p=admin&c=login&a=login', '', 0);
    }
    
    // 生成验证码
    public function captchaAction()
    {
        // 引入验证码类
        $this->library('Captcha');
        // 实例化对象
        $captcha = new Captcha();
        // 生成验证码
        $captcha->generateCode();
        // 将验证码保存到session中
        $_SESSION['captcha'] = $captcha->getCode();
    }
}