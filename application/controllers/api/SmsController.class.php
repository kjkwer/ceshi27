<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/1/3
 * Time: 16:45
 */
header("Content-type: text/html; charset=gbk2312");
class SmsController extends BaseController
{
    //>>注册短信验证码
    public function smsAction(){
        header("Content-type: text/html; charset=gbk2312");
        date_default_timezone_set('PRC'); //
        $tel = empty($_REQUEST['tel'])?'':$_REQUEST['tel'];
        if ($tel==''){
            echo "false";exit;
        }
        $uid = 'SLKJ006499';
        $pwd = '123456';
        $message=rand(1000,9000);
        $_SESSION["a".$tel] = $message;
        $msg = rawurlencode(mb_convert_encoding($message, "gb2312", "utf-8"));
        $msg="您的验证码为：".$msg."【成都思乐】";
        $gateway = "http://mb345.com:999/ws/BatchSend2.aspx?CorpID={$uid}&Pwd={$pwd}&Mobile={$tel}&Content={$msg}&SendTime=&cell=";
        $result = file_get_contents($gateway);
        if ($result>0){
            echo json_encode(true);
        }else{
            echo $result;
        }
    }
    //>>找回密码短息验证
    public function lookpwdAction(){
        header("Content-type: text/html; charset=gbk2312");
        date_default_timezone_set('PRC');
        $phone = $_POST["phone"];
        $model = new ModelNew("member");
        $data = $model->where(["yonghuming"=>$phone])->find("yonghuming")->one();
        $tel = !empty($data["yonghuming"])?$data["yonghuming"]:"";
        if ($tel==''){
            echo json_encode(false);
        }
        $uid = 'SLKJ006499';
        $pwd = '123456';
        $message=rand(1000,9000);
        $_SESSION["a".$tel] = $message;
        $msg = rawurlencode(mb_convert_encoding($message, "gb2312", "utf-8"));
        $msg="您的验证码为：".$msg."【成都思乐】";
        $gateway = "http://mb345.com:999/ws/BatchSend2.aspx?CorpID={$uid}&Pwd={$pwd}&Mobile={$tel}&Content={$msg}&SendTime=&cell=";
        $result = file_get_contents($gateway);
        if ($result>0){
            echo json_encode(true);
        }else{
            echo $result;
        }
    }

    static function ajaxJsonAction($message){
        header('Content-type: application/json');
        return json_encode($message);
    }
}