<?php

/**
 * Created by PhpStorm.
 * User: lilin
 * Date: 2018/4/15
 * Time: 18:51
 */
class CommonController extends Controller
{
    //>>省市县三级联查方法
        //>>通过父id查找下级省份
        public function selectAreaAction(){
            $pid = $_POST['pid'];
            $model = new ModelNew("area");
            $datas = $model->where(["area_parent_id"=>$pid])->find("id,area_name")->all();
            echo json_encode($datas);
        }
}