<?php
// 品牌控制器
class DaobiaoController extends BaseController
{
    //显示管理员列表
    public function indexAction()
    {
        include CUR_VIEW_PATH . "Sdaobiao" . DS . "daobiao_list.html";
    }

    //yhmd
    public function yhmdAction(){

        $filename ="./public/uploads/".time().$_FILES['file']['name'];
        $filename=iconv("UTF-8","gb2312", $filename);
        move_uploaded_file($_FILES["file"]["tmp_name"],$filename);//将临时地址移动到指定地址
        $filename=iconv("gb2312","UTF-8", $filename);
        $data['url']=$filename;
        echo json_encode($data);
    }
   public function textAction(){
        $data=[
          ['a'=>1,'b'=>2,'c'=>3],
          ['a'=>4,'b'=>5,'c'=>6],
          ['a'=>4,'b'=>8,'c'=>9],
          ['a'=>10,'b'=>11,'c'=>12],
        ];

        foreach ($data as $key=>$v){
            $a=$key-1;
            if ($a>=0){
                if ($data[$a]['a']==$v['a']){
                    echo $v['a'];
                    echo '<br>';
                    echo $data[$a]['b'];
                    echo '<br>';
                    echo $v['b'];
                }
            }

        }
   }

    public function imgAction(){
        $zip=new ZipArchive();
        $rs=$zip->open('/public/uploads/zp/1517462276webuploader-0.1.5.zip');
        var_dump($rs);
       if ($rs){
           echo '成功';
       }else{
           echo '失败';
       }
die;
        $filename ="./public/uploads/zp/".time().$_FILES['file']['name'];
        move_uploaded_file($_FILES["file"]["tmp_name"],$filename);//将临时地址移动到指定地址
        $data['url']=$filename;

        echo json_encode($data);
    }
}