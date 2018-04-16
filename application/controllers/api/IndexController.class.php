<?php

// 文章模型控制器
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
class IndexController extends BaseController

{
    function __construct()
    {
        ob_end_clean();
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Headers", "content-type");
        header("Access-Control-Allow-Methods", "POST,GET,PUT,OPTIONS");
   

    }


    public function  indexAction(){

    }
    //万能的方法
    public function wnAction(){
        $table=empty($_REQUEST['table'])?'':$_REQUEST['table'];
        $table="sl_".$table;
        $tiaojian=empty($_REQUEST['tiaojian'])?'':$_REQUEST['tiaojian'];
        $orderbytype=empty($_REQUEST['orderbytype'])?'DESC':$_REQUEST['orderbytype'];
        $orderby=empty($_REQUEST['orderby'])?'id':$_REQUEST['orderby'];
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $act=($page-1)*$num;
        $model=new  ModelNew($table);
        $sql="select *from {$table}  ";

        if ($tiaojian!=''){
            $sql=$sql."where ";
            $_tiaojian=explode(',',$tiaojian);
            foreach ($_tiaojian as $value){
                $sql.=" $value and";
            }
            $sql=substr($sql,0,-3);
        }
//              $sql=$sql."  limit {$page},{$num}";
        $sql=$sql." ORDER BY {$orderby} {$orderbytype} limit {$act},{$num}";
//              var_dump($sql);die;
//              var_dump($sql);die;
        $msg=$model->findBysql($sql);
        echo self::returnAction($msg);
    }
    //查询风采的功能
    function fengcaiAction(){
        $model=new ModelNew('fengcai');
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $xiangzhen=$_REQUEST['xiangzhen'];
        $fenlei=$_REQUEST['fenlei'];
        $act=($page-1)*$num;
        $orderbytype=empty($_REQUEST['orderbytype'])?'DESC':$_REQUEST['orderbytype'];
        $orderby=empty($_REQUEST['orderby'])?'id':$_REQUEST['orderby'];
        $sql="select *from sl_fengcai WHERE xiangzhen='".$xiangzhen."' and fenlei='".$fenlei."' ORDER BY {$orderby} {$orderbytype} limit {$act},{$num}";
//              var_dump($sql);die;
        $msg=$model->findBysql($sql);
        foreach ($msg as $key=>$v){
            $id=$v['id'];
            $_model=new ModelNew('pinglun');
            $_num=$model->findBysql("select count(*) from sl_pinglun WHERE wid=$id AND cengji=1")[0]['count(*)'];
            $msg[$key]['zhuitie']=$_num;
        }

//              var_dump($msg);die;
        echo self::returnAction($msg);

    }
    //查询评论的功能
    function  pinglunAction(){
        $wid=$_REQUEST['wid'];
        $xiangzhen=$_REQUEST['xiangzhen'];
        $fenlei=$_REQUEST['fenlei'];
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $act=($page-1)*$num;
        $model=new ModelNew('pinglun');
        $rs=$model->findBysql("select *from sl_pinglun WHERE wid={$wid} AND xiangzhen='".$xiangzhen."' and fenlei='".$fenlei."' and cengji=1 order by id desc limit {$act},{$num} ");
        $_model=new ModelNew('member');
        foreach ($rs as $key=>$value){
            $name=$_model->findBysql("select *from sl_member WHERE id={$value['uid']}");
            $rs[$key]['touxiang']=empty($name[0]['touxiang'])?'':$name[0]['touxiang'];
            $rs[$key]['mingcheng']=empty($name[0]['mingcheng'])?'':$name[0]['mingcheng'];
            $rs1=self::huanAction($model->findBysql("select *from sl_pinglun WHERE pid={$value['id']}"));
            if ($rs1) {
                foreach ($rs1 as $k => $v) {
                    $_name = $_model->findBysql("select *from sl_member WHERE id={$v['uid']}");
                    $rs1[$k]['touxiang'] = $_name[0]['touxiang'];
                    $rs1[$k]['mingcheng'] = $_name[0]['mingcheng'];
                }
            }
            $rs[$key]['two']=$rs1;
        }
//              var_dump($rs);die;
        echo self::returnAction($rs);
    }
    //注册的方法
    function resAction(){
        header('Content-type: application/json');
        $yonghuming=empty($_REQUEST['yonghuming'])?'':$_REQUEST['yonghuming'];
        $yanzhengma=empty($_REQUEST['yanzhengma'])?'':$_REQUEST['yanzhengma'];
        $mima=empty($_REQUEST['mima'])?'':$_REQUEST['mima'];
        $model=new ModelNew('member');
        $msg=$model->where(['yonghuming'=>$yonghuming])->find()->one();
        if ($msg){
            $result['status']=false;
            $result['msg']="用户名已经被注册";
            echo json_encode($result);exit;
        }
        if ($yonghuming==''){
            $result['status']=false;
            $result['msg']="用户名为空";
            echo json_encode($result);exit;
        }
        if ($yanzhengma==''){
            $result['status']=false;
            $result['msg']="验证码为空";
            echo json_encode($result);exit;
        }
        if ($mima==''){
            $result['status']=false;
            $result['msg']="密码为空";
            echo json_encode($result);exit;
        }
        if ($_SESSION["a".$yonghuming]=!$yanzhengma){
            $result['status']=false;
            $result['msg']="验证码错误";
            echo json_encode($result);exit;
        }
        $model=new ModelNew('member');
        $data['yonghuming']=$yonghuming;
        $data['mima']=md5($mima);
        $data['mingcheng']=rand(00000,99999);
        $msg=$model->insert($data);
        if ($msg){
            $result['status']=true;
            $result['msg']=$msg;
        }else{
            $result['status']=false;
        }
        echo self::returnAction($result);
    }
    //登陆的方法
    function loginAction(){
        header('Content-type: application/json');
        $yonghuming=empty($_POST['yonghuming'])?'':$_POST['yonghuming'];
        $mima=empty($_POST['mima'])?'':$_POST['mima'];
        if ($yonghuming==''){
            $result['status']=false;
            $result['msg']="用户名为空";
            echo json_encode($result);exit;
        }
        if ($mima==''){
            $result['status']=false;
            $result['msg']="密码为空";
            echo json_encode($result);exit;
        }
        $model=new ModelNew('member');
        $rs=$model->where(['yonghuming'=>$yonghuming])->find()->one();
        if (!$rs){
            $result['status']=false;
            $result['msg']="用户名不存在";
            echo json_encode($result);exit;
        }
        if ($rs['mima']!=md5($mima)){
            $result['status']=false;
            $result['msg']="密码错误";
            echo json_encode($result);exit;
        }
        $result['status']=true;

        $msg['yonghuming']=$rs['yonghuming'];
        $msg['uid']=$rs['id'];
        $msg['touxiang']=$rs['touxiang'];
        $msg['mingcheng']=$rs['mingcheng'];
        $msg['id']=$rs['id'];
        $result['msg']=$msg;
        echo json_encode($result);exit;

    }
    //添加的方法
    function addAction(){
        $table=$_GET['table'];
        $model=new ModelNew($table);
        $data=$_POST;
        $msg=$model->insert($data);
        echo self::returnAction($msg);
    }
    //删除的方法
    function deleteAction(){
        $table=$_GET['table'];
        $tiaojian=$_POST['tiaojian'];
        $zhi=$_POST['zhi'];
        $model=new ModelNew($table);
        $msg=$model->where([$tiaojian=>$zhi])->delete();
        echo self::returnAction($msg);
    }

    function ktxAction(){
        $model=new ModelNew('zonghe');
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $fenlei=$_REQUEST['fenlei'];
        $act=($page-1)*$num;
        $orderbytype=empty($_REQUEST['orderbytype'])?'DESC':$_REQUEST['orderbytype'];
        $orderby=empty($_REQUEST['orderby'])?'id':$_REQUEST['orderby'];
        $sql="select *from sl_zonghe WHERE  fenlei='".$fenlei."' ORDER BY {$orderby} {$orderbytype} limit {$act},{$num}";
        //              var_dump($sql);die;
        $msg=$model->findBysql($sql);
        foreach ($msg as $key=>$v){
            $id=$v['id'];
            $_model=new ModelNew('pinglun');
            $_num=$model->findBysql("select count(*) from sl_pinglun WHERE wid=$id AND cengji=1")[0]['count(*)'];
            $msg[$key]['zhuitie']=$_num;
        }
        echo self::returnAction($msg);

    }

    //接口补充
    function uploadAction(){
        require 'public/qn/autoload.php';
        // 用于签名的公钥和私钥
        $file=$_FILES["file"]["tmp_name"];
//            $file="public/movie.ogg";
        $accessKey = 'thCRyOAFJrdk34OTqJ4zplzp4PZQiszrWGoiSzA2';
        $secretKey = 'VE5HTug-eIlV1PezbFonOK_wtbfBdKf23BpP36l_';
        $auth = new Auth($accessKey, $secretKey);

        $bucket="jncsp";
        $domin='p4tafmzgc.bkt.clouddn.com';
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径
        $filePath=$file;
        // 上传到七牛后保存的文件名
        $key = time().$file;
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//                           echo "\n====> putFile result: \n";
        if ($err !== null) {
            $result['status']=$err;
            echo self::returnAction($result);
        } else {
            $result['status']=true;
            $result['msg']='http://'.$domin.'/'.$file;
            echo self::returnAction($result);
        }
    }
    //评论转发的功能
    function  add1Action(){
        $table=$_REQUEST['table'];
        $id=$_REQUEST['wid'];
        $uid=$_REQUEST['uid'];
        $lianjie=self::huanAction($_REQUEST['lianjie']);
        $model=new ModelNew($table);
        $_rs=$model->where(['id'=>$id])->find()->one();
        $_model=new ModelNew('sjx');
        $data['jianjie']=self::huanAction($_rs['jianjie']);
        $data['biaoti']=self::huanAction($_rs['biaoti']);
        $data['shipin']=self::huanAction($_rs['shipin']);
        $data['shipindizhi']=self::huanAction($_rs['shipindizhi']);
        $data['zutu']=self::huanAction($_rs['zutu']);
        $data['zhuanfa']='是';
        $data['uid']=$uid;
        $data['wid']=self::huanAction($_rs['id']);
        $data['fenlei']=self::huanAction($_rs['fenlei']);
        $data['leixin']=$table;


        $uid=self::huanAction($_REQUEST['uid']);
        if (!$uid){
            $result['status']=false;
            echo self::returnAction($result);exit;
        }
        $data['uid']=$uid;
        $msg=$_model->insert($data);
        if ($msg){
            $result['status']=true;
            $result['msg']=$msg;
        }else{
            $result['status']=false;
        }
        echo self::returnAction($result);
    }
    //晒家乡
    function sjxAction(){
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $act=($page-1)*$num;
        $model=new ModelNew('sjx');
        $result=$model->findBySql("select *from sl_sjx ORDER BY id DESC limit {$act},{$num}");
//            $id=1;
        $id=$_REQUEST['uid'];
        $_model=new ModelNew('guanzhu');
        $ids=$_model->findBySql("select buid from sl_guanzhu WHERE uid={$id}");
        $_ids=[];
        foreach ($ids as $v){
            $_ids[]=$v['buid'];
        }
        $m=new ModelNew('pinglun');
        $m1=new ModelNew('collect');
        $m2=new ModelNew('member');
        foreach ($result as $key=>$value){
//            $people=$m2->findOne($value['uid']);
//            var_dump($people);die;

              $people=$m2->findBySql("select *from sl_member WHERE id={$value['uid']}")[0];


            if (in_array($value['uid'],$_ids)){
                $result[$key]['guanzhu']=1;
            }else{
                $result[$key]['guanzhu']=2;
            }
            $result[$key]['mingcheng']=$people['mingcheng'];
            $result[$key]['touxiang']=$people['touxiang'];

            if ($value['zhuanfa']=='否'){
                $result[$key]['gentie']=self::huanAction($m->findBysql("select count(*) from sl_pinglun WHERE wid={$value['id']} AND fenlei='晒家乡'")[0]['count(*)']);
                $result[$key]['shoucang']=self::huanAction($m1->findBysql("select count(*) from sl_collect WHERE wid={$value['id']} AND fenlei='晒家乡'")[0]['count(*)']);
                $result[$key]['zhuanfa']=self::huanAction($model->findBysql("select count(*) from sl_sjx WHERE wid={$value['id']} AND zhuanfa='是'")[0]['count(*)']);
            }


        }

        echo self::returnAction($result);
    }
    //个人中心
    function zhuangtaiAction(){
        $uid=$_REQUEST['uid'];
//                   $uid=1;
        $_1_model=new ModelNew('sjx');
        $_2_model=new ModelNew('collect');
        $_3_model=new ModelNew('guanzhu');
        $_4_model=new ModelNew('xiaoxi');
        $fabu=$_1_model->findBySql("select count(*) from sl_sjx WHERE uid={$uid}")[0]['count(*)'];
        $fensi=$_3_model->findBySql("select count(*) from sl_guanzhu WHERE buid={$uid}")[0]['count(*)'];
        $shoucang=$_2_model->findBySql("select count(*) from sl_collect WHERE uid={$uid}")[0]['count(*)'];
        $guanzhu=$_3_model->findBySql("select count(*) from sl_guanzhu WHERE uid={$uid}")[0]['count(*)'];
        $xiaoxi=$_4_model->findBySql("select count(*) from sl_xiaoxi WHERE buid={$uid} and zhuangtai='未读'")[0]['count(*)'];


        $msg['fabu']=$fabu;
        $msg['shoucang']=$shoucang;
        $msg['guanzhu']=$guanzhu;
        $msg['xiaoxi']=$xiaoxi;
        $msg['fensi']=$fensi;
        echo self::returnAction($msg);
    }
    //我的关注
    function wdgzAction(){
//                $uid=$_SESSION['id'];
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $act=($page-1)*$num;

        $uid=$_REQUEST['uid'];
        $model=new  ModelNew('guanzhu');
//        $ids=$model->where(['uid'=>$uid])->find()->all();
        $ids=$model->findBySql("select *from sl_guanzhu WHERE uid={$uid} limit {$act},{$num}");
        $result=[];
        $_model=new  ModelNew('member');
        foreach ($ids as $key=>$v){
            $buid=$v['buid'];
            $msg=$_model->findBySql("select *from sl_member WHERE id={$buid}");
            if ($msg){
                $msg=$msg[0];
            }
            $result[$key]=$msg;
            $result[$key]['wid']=$v['id'];
        }
        echo self::returnAction($result);
    }
    //阅读历史
    function ydlsAction(){
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $act=($page-1)*$num;


        $id=$_REQUEST['uid'];
        $model=new ModelNew('ydls');
//              $rs=$model->where(['uid'=>$id])->find()->all();
        $rs=$model->findBySql("select *from sl_ydls WHERE uid={$id} ORDER BY id desc limit {$act},{$num}");
        $msg='';
        foreach ($rs as $key=>$value){
            if ($value['leixing']!=''){
                $_model=new ModelNew($value['leixing']);
                $xinxi=self::huanAction($_model->where(['id'=>$value['wid']])->find()->one());
                $msg[$key]=$xinxi;
                $msg[$key]['dtime']=$value['dtime'];
                $msg[$key]['wid']=$value['id'];
                $msg[$key]['leixing']=$value['leixing'];
            }

        }
        echo self::returnAction($msg);
    }
    function  upload1Action(){
        $filename ="public/webuploader/upload/".time().$_FILES['file']['name'];
        move_uploaded_file($_FILES["file"]["tmp_name"],$filename);//将临时地址移动到指定地址
        $data['url']="http://".$_SERVER['HTTP_HOST']."/".$filename;
        echo json_encode($data);
    }
    function ldfgAction(){
        $model=new ModelNew('ldfg');
        $xiangzhen=self::huanAction($_REQUEST['xiangzhen']);
        $msg=$model->where(['xiangzhen'=>$xiangzhen])->find()->all();
        echo self::returnAction($msg);
    }
    //名片
    function mingpianAction(){
        $model=new ModelNew('mingpian');
        $xiangzhen=$_REQUEST['xiangzhen'];
        $msg=$model->where(['xiangzhen'=>$xiangzhen])->find()->one();
        echo self::returnAction($msg);
    }
    //修改密码
    function gmmAction(){
        header('Content-type: application/json');
        $id=$_POST['id'];
        $mima=$_POST['mima'];
        $miman=$_POST['mima1'];
        $model=new ModleNew('member');
        $msg=$model->where(['id'=>$id])->find()->one();
        if ($msg!=md5($mima)){
            $msg['status']=false;
            $mag['msg']="密码错误";
            echo json_encode($msg);exit;
        }else{
            $data['mima']=md5($miman);
            $model->where(['id'=>$id])->update($data);
            $msg['status']=true;
            $msg['msg']="修改成功";
            echo  json_encode($msg);
        }
    }
    //找回密码
    function zhmmAction(){
        header('Content-type: application/json');
        $yonghuming=$_REQUEST['yonghuming'];
        $yanzhengma=$_REQUEST['yanzhengma'];
        if ($_SESSION['a'.$yonghuming]!=$yanzhengma){
            $result['status']=false;
            $result['msg']="验证码不对";
            echo json_encode($result);exit;
        }else{
            $result['status']=true;
            $result['msg']="验证码为正确";
            echo json_encode($result);exit;
        }
    }
    //我的发布
    function wdfbAction(){
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $act=($page-1)*$num;

        $model=new ModelNew('sjx');
        $id=$_REQUEST['uid'];
//        $msg=$model->where(['uid'=>$id])->find()->all();
        $msg=$model->findBySql("select *from sl_sjx WHERE uid={$id} limit {$act},{$num}");
        echo self::returnAction($msg);
    }
    //意见反馈
    function yjfkAction(){
        $zhuti=$_REQUEST['zhuti'];
        $neirong=$_REQUEST['neirong'];
        $uid=$_REQUEST['uid'];
        $model=new ModelNew('yjfk');
        $data['zhuti']=$zhuti;
        $data['neirong']=$neirong;
        $data['uid']=$uid;
        $msg=$model->insert($data);
        echo self::returnAction($msg);

    }
    //我的收藏
    function wdscAction(){
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $act=($page-1)*$num;
        $id=$_REQUEST['uid'];
        $model=new ModelNew('collect');
//        $msg1=$model->where(['uid'=>$id])->find()->all();
        $msg1=$model->findBySql("select *from sl_collect WHERE uid={$id} limit {$act},{$num}");
        $msg='';
        foreach ($msg1 as  $key=>$value){
            if ($value['leixing']!=''){
                $_model=new ModelNew($value['leixing']);
                $xinxi=self::huanAction($_model->where(['id'=>$value['wid']])->find()->one());
                $msg[$key]=$xinxi;
            }
            $msg[$key]['wid']=$value['id'];
            $msg[$key]['leixing']=$value['leixing'];
        }

        echo self::returnAction($msg);

    }

    function p1Action(){
        $id=$_REQUEST['wid'];
        $fenlei=$_REQUEST['fenlei'];
        $model=new ModelNew('pinglun');
        $number=$model->findBysql("select count(*) from sl_pinglun WHERE wid={$id} and fenlei='".$fenlei."'")[0]['count(*)'];
        echo self::returnAction($number);
    }
    //我的消息
    function  wdxxAction(){
        $num=empty($_REQUEST['num'])?10:$_REQUEST['num'];
        $page=empty($_REQUEST['page'])?1:$_REQUEST['page'];
        $act=($page-1)*$num;
        $uid=$_REQUEST['uid'];
        $model=new ModelNew('xiaoxi');
//               $rs=$model->where(['uid'=>$uid])->find()->all();
        $rs=$model->findBySql("select *from sl_xiaoxi WHERE buid={$uid} ORDER BY id desc limit {$act},{$num}");
        $__model=new  ModelNew('member');
        foreach ($rs as $key=>$value){

            $people=$__model->where(['id'=>$value['buid']])->find()->one();

            if ($value['leixing_x']!=''){
                $_model=new ModelNew($value['leixing_x']);
                $xinxi=self::huanAction($_model->where(['id'=>$value['wid']])->find()->one());
                if ($xinxi!=''){
                    $rs[$key]['fenlei']=$xinxi['fenlei'];
                }
                $rs[$key]['mingcheng']=$people['mingcheng'];
            }
        }
        echo self::returnAction($rs);
    }

    function  add2Action(){
        $leixing_x=$_REQUEST['leixing_x'];
        $zhuanfa=$_REQUEST['zhuanfa'];
        $fenlei=self::huanAction($_REQUEST['fenlei']);
        $wid=$_REQUEST['wid'];
        $uid=$_REQUEST['uid'];
        $cengji=$_REQUEST['cengji'];
        $xiangzhen=$_REQUEST['xiangzhen'];
        $neirong=$_REQUEST['neirong'];
        $model=new ModelNew('pinglun');
        $_model=new ModelNew('xiaoxi');
        $__model=new ModelNew('member');
        if ($cengji==1){
            $data['leixing']=$leixing_x;
            $data['xiangzhen']=$xiangzhen;
            $data['cengji']=1;
            $data['fenlei']=$fenlei;
            $data['neirong']=$neirong;
            $data['wid']=$wid;
            $data['uid']=$uid;
            if($zhuanfa==1){
                self::adAction($leixing_x,$wid,$uid);
            }
            if ($leixing_x=='sjx'){
                $leixing=$_REQUEST['leixing'];
                $_data['buid']=$_REQUEST['buid'];
                $pepple=$__model->where(['id'=>$_data['buid']])->find()->one();
                $name=$pepple['mingcheng'];
                $_data['leixing_x']=$leixing_x;
                if ($leixing=='回复'){
                    $_data['xinxi']=$leixing."了你:".$neirong;
                }elseif ($leixing=='收藏'){
                    $_data['xinxi']=$leixing."了你的文章";
                }else{
                    $_data['xinxi']=$leixing."了你:";
                }
                $_data['zhuangtai']="未读";
                $_data['fenlei']=$fenlei;
                $_data['wid']=$wid;
                $_data['leixing']=$leixing;
                $_data['uid']=$uid;
                $msg=$model->insert($data);
                $msg1=$_model->insert($_data);
            }else{
                $msg=$model->insert($data);
            }
        }elseif ($cengji==2){
            if($zhuanfa==1){
                self::adAction($leixing_x,$wid,$uid);
            }
            $data['leixing']=$leixing_x;
            $data['xiangzhen']=$xiangzhen;
            $data['cengji']=2;
            $data['fenlei']=$fenlei;
            $data['neirong']=$neirong;
            $data['wid']=$wid;
            $data['uid']=$uid;
            $data['pid']=$_REQUEST['pid'];

            $leixing=$_REQUEST['leixing'];
            $_data['buid']=$_REQUEST['buid'];
            $pepple=$__model->where(['id'=>$_data['buid']])->find()->one();
            $name=$pepple['mingcheng'];
            $_data['leixing_x']=$leixing_x;
            if ($leixing=='回复'){
                $_data['xinxi']=$leixing."了你:".$neirong;
            }elseif ($leixing=='收藏'){
                $_data['xinxi']=$leixing."了你的文章";
            }else{
                $_data['xinxi']=$leixing."了你:";
            }
            $_data['zhuangtai']="未读";
            $_data['fenlei']=$fenlei;
            $_data['wid']=$wid;
            $_data['leixing']=$leixing;
            $_data['uid']=$uid;
            $msg=$model->insert($data);
            $msg1=$_model->insert($_data);
        }
        echo self::returnAction(1);
    }
    //>>用户修改资料
    function upateUserDateAction(){
        $userId = !empty($_REQUEST["userId"])?$_REQUEST["userId"]:"";  //用户ID
        $nickName = !empty($_REQUEST["nickName"])?$_REQUEST["nickName"]:""; //昵称ID
        $phone = !empty($_REQUEST["phone"])?$_REQUEST["phone"]:"";  //手机号
        $headPicture = !empty($_REQUEST["headPicture"])?$_REQUEST["headPicture"]:"";//头像
        if ($nickName == ""){
            $result['status']=false;
            $result['msg']="昵称不能为空！";
            echo json_encode($result);exit;
        }
        if ($phone == ""){
            $result['status']=false;
            $result['msg']="电话不能为空！";
            echo json_encode($result);exit;
        }
        $model=new ModelNew('member');
        $msg=$model->where(['yonghuming'=>$phone])->find()->one();
        if (!empty($msg)){
            if ($msg["id"] != $userId){
                $result['status']=false;
                $result['msg']="电话已经被注册";
                echo json_encode($result);exit;
            }
        }
        $data["mingcheng"] = $nickName;
        $data["yonghuming"] = $phone;
        $data["touxiang"] = $headPicture;
        $data["dtime"] = date("Y-m-d H:i:s");
        $_model=new ModelNew('member');
        $rs = $_model->where(["id"=>$userId])->update($data);
        if ($rs){
            $result['status']=true;
            $result['msg']="修改成功";
        }else{
            $result['status']=false;
            $result['msg']="修改失败";
        }
        echo self::ajaxJsonAction($result);
    }
    //>>获取首页滚动图
    function rollPictureAction(){
        $model = new ModelNew("roll_picture");
        $data = $model->findBySql("select * from sl_roll_picture");
        if ($data){
            $result['status']=true;
            $result['msg']=$data;
        }else{
            $result['status']=false;
            $result['msg']="没有数据";
        }
        echo self::ajaxJsonAction($result);
    }
    //>>上传图片
    function uploadPictureAction(){


        $result = array("status"=>false,"msg"=>"上传失败");
        if (!empty($_FILES["file"])){
            $pictureData = $_FILES["file"];
            if ($pictureData["error"]==0){
                if (explode("/",$pictureData["type"])[0]=="image"){
                    //>>移动图片
                    //设置图片保存路径
                    $filePath = "public/images/".date("Ymd",time());
                    if (!is_dir($filePath)){
                        mkdir ($filePath,0777,true);
                    }
                    //>>移动文件
                    $file = $filePath."/".uniqid().stristr($pictureData['name'],'.');
                    if(move_uploaded_file($pictureData["tmp_name"],$file)){
                        $result = array("status"=>true,"url"=>"http://".$_SERVER['SERVER_NAME'].$file);
                    }
                }
            }
        }
        echo self::ajaxJsonAction($result);
    }
    //>>验证找回密码短信
    function checkLookPwdAction(){
        $result = array("status"=>false,"msg"=>"验证失败");
        $userId = $_POST["userId"];
        $authCode = $_POST["authCode"];
        $model = new ModelNew("member");
        $data = $model->where(["id"=>$userId])->find("yonghuming")->one();
        $tel = !empty($data["yonghuming"])?$data["yonghuming"]:"";
        if ($tel==""){
            $result = array("status"=>false,"msg"=>"电话号码为空");
        }else{
            if (empty($_SESSION["a".$tel])){
                $result = array("status"=>false,"msg"=>"验证码已过期");
            }else{
                if ($_SESSION["a".$tel]==$authCode){
                    $result = array("status"=>true,"msg"=>"验证通过");
                }else{
                    $result = array("status"=>false,"msg"=>"验证失败");
                }
            }
        }
        echo self::ajaxJsonAction($result);
    }
    //>>找回密码设置新密码
    function setNewPwdAction(){
        $userId = $_REQUEST["userId"];
        $pwd = $_REQUEST["newPwd"];
        $model = new ModelNew("member");
        $data["mima"] = md5($pwd);
        if ($model->where(["id"=>$userId])->update($data)){
            $result = array("status"=>true,"msg"=>"新密码设置成功");
        }else{
            $result = array("status"=>false,"msg"=>"新密码设置失败");
        }
        echo self::ajaxJsonAction($result);
    }
    //>>切换城市查找省份
    function selectProvinceAction(){
        $model = new ModelNew("city");
        $data = $model->findBySql("select b.id as value,b.area_name as text from sl_city as a JOIN sl_area as b on a.province_id = b.id GROUP BY a.province_id");
        if ($data){
            $result['status']=true;
            $result['msg']=$data;
        }else{
            $result['status']=false;
            $result['msg']="没有数据";
        }
        echo self::ajaxJsonAction($result);
    }
    //>>切换城市查找城市
    function selectCityAction(){
        $pid = $_REQUEST["pid"];
        $model = new ModelNew("city");
        $data = $model->findBySql("select b.id as value,b.area_name as text from sl_city as a JOIN sl_area as b ON a.city_id = b.id WHERE b.area_parent_id=$pid GROUP BY a.city_id");
        if ($data){
            $result['status']=true;
            $result['msg']=$data;
        }else{
            $result['status']=false;
            $result['msg']="没有数据";
        }
        echo self::ajaxJsonAction($result);
    }
    //>>切换城市查找区/县
    function selectAreaAction(){
        $pid = $_REQUEST["pid"];
        $model = new ModelNew("city");
        $data = $model->findBySql("select b.id as value,b.area_name as text from sl_city as a JOIN sl_area as b ON a.area_id = b.id WHERE b.area_parent_id=$pid GROUP BY a.area_id");
        if ($data){
            $result['status']=true;
            $result['msg']=$data;
        }else{
            $result['status']=false;
            $result['msg']="没有数据";
        }
        echo self::ajaxJsonAction($result);
    }
    //>>切换城市查找景点
    function selectSpotAction(){
        $pid = $_REQUEST["pid"];
        $model = new ModelNew("city");
        $data = $model->findBySql("select id as value,scenic_spot as text,identification_code as code from sl_city WHERE area_id=$pid");
        if ($data){
            $result['status']=true;
            $result['msg']=$data;
        }else{
            $result['status']=false;
            $result['msg']="没有数据";
        }
        echo self::ajaxJsonAction($result);
    }
    //>>切换城市搜索景点
    function searchSpotAction(){
        $word = $_REQUEST["word"];
        $model = new ModelNew("city");
        $datas = $model->findBySql("select * from sl_city WHERE scenic_spot like '%$word%'");
        $array = [];
        $arr = [];
        if (!empty($datas)){
            foreach ($datas as $data){
                $arr["id"] = $data["id"];
                $arr["name"] = $data["scenic_spot"];
                $arr["code"] = $data["identification_code"];
                $arr["province"] = self::selectDressAction($data["province_id"]);
                $arr["province_id"] = $data["province_id"];
                $arr["city"] = self::selectDressAction($data["city_id"]);
                $arr["city_id"] = $data["city_id"];
                $arr["area"] = self::selectDressAction($data["area_id"]);
                $arr["area_id"] = $data["area_id"];
                $array[] = $arr;
            }
        }
        if ($array){
            $result['status']=true;
            $result['msg']=$array;
        }else{
            $result['status']=false;
            $result['msg']="没有数据";
        }
        echo self::ajaxJsonAction($result);
    }
    //>>测试接口
    function testAction(){
        echo self::ajaxJsonAction($_SESSION["a13551275272"]);
    }


    static function  adAction($leixing,$wid,$_uid){
        $table=$leixing;
        $id=$wid;
        $uid=$_uid;

        $model=new ModelNew($table);
        $_rs=$model->where(['id'=>$id])->find()->one();
        if (!$_rs){
            return;
        }
        $_model=new ModelNew('sjx');
        $data['jianjie']=self::huanAction($_rs['jianjie']);
        $data['biaoti']=self::huanAction($_rs['biaoti']);
        $data['shipin']=self::huanAction($_rs['shipin']);
        $data['shipindizhi']=self::huanAction($_rs['shipindizhi']);
        $data['zutu']=self::huanAction($_rs['zutu']);
        $data['zhuanfa']='是';
        $data['uid']=$uid;
        $data['wid']=self::huanAction($_rs['id']);
        $data['fenlei']=self::huanAction($_rs['fenlei']);

        $data['leixing']=$table;

        $uid=self::huanAction($_REQUEST['uid']);
        if (!$uid){
            $result['status']=false;
            echo self::returnAction($result);exit;
        }
        $data['uid']=$uid;
        $_model->insert($data);
    }

    function wdfsAction(){
            $id=$_REQUEST['uid'];
            $model=new ModelNew('guanzhu');
            $rs=$model->where(['buid'=>$id])->find()->all();
            $_model=new ModelNew('member');
            $result='';
            foreach ($rs as $key=>$va){

//                $people=$_model->where(['id'=>$va['uid']])->find()->one();
                $people=$_model->findBySql("select *from sl_member WHERE id={$va['uid']}")[0];

                $result[$key]['mingcheng']=$people['mingcheng'];
                $result[$key]['uid']=$people['id'];
                $result[$key]['touxiang']=$people['touxiang'];
//                $msg=$model->where(['uid'=>$id])->where(['buid'=>$people['id']])->find()->one();
                $msg=$model->findBySql("select *from sl_guanzhu WHERE uid={$id} and buid={$people['id']}");
                if ($msg){
                    $result[$key]['guanzhu']=1;
                }else{
                    $result[$key]['guanzhu']=0;
                }
            }

            echo self::returnAction($result);
        }

    function  changeAction(){
            $uid=$_REQUEST['uid'];
            $pid=$_REQUEST['buid'];
            $model=new ModelNew('guanzhu');
            $msg=$model->findBySql("select *from sl_guanzhu WHERE uid={$uid} and buid={$pid}");
            if ($msg){
                $model->query("delete from sl_guanzhu WHERE uid={$uid} and buid={$pid}");
                $result['status']=true;
                $result['msg']='取消关注';
                echo json_encode($result);exit;
            }else{
                $data['uid']=$uid;
                $data['buid']=$pid;
                $model->insert($data);
                $result['status']=true;
                $result['msg']='关注成功';
                echo json_encode($result);exit;
            }
        }

    static function huanAction($msg){
        $a=empty($msg)?'':$msg;
        return $a;
    }

    static function returnAction($msg){
        header('Content-type: application/json');
        if ($msg){
            $result['status']=true;
            $result['msg']=$msg;
        }else{
            $result['status']=false;
            $result['msg']="没有数据了";
        }
        return json_encode($result);
    }
    static function ajaxJsonAction($message){
        header('Content-type: application/json');
        return json_encode($message);
    }

    //>>通过id查找地区
    static function selectDressAction($id){
        $model = new ModelNew("area");
        $name = $model->where(["id"=>$id])->find("area_name")->one()["area_name"];
        return $name;
    }
}