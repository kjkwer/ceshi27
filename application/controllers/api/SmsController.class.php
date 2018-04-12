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
        $msg="������֤��Ϊ:".$msg."���ɶ�˼�֡�";
        $gateway = "http://mb345.com:999/ws/BatchSend2.aspx?CorpID={$uid}&Pwd={$pwd}&Mobile={$tel}&Content={$msg}&SendTime=&cell=";
        $result = file_get_contents($gateway);
        if ($result>0){
            echo json_encode(true);
        }else{
            echo $result;
        }
    }
}