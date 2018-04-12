<?php
// 采集控制器
class caijiController extends BaseController
{
    
    // 显示采集列表
    public function indexAction()
    {
        if($_REQUEST['id']=='')
        {
            $this->jump('index.php?p=admin&c=autotable&a=index&model_id=54',"参数不能为空",3);
        }
        $id = $_REQUEST['id'];
        //加载采集类数据
        $caijiModel = new Model("caiji");
        $caiji=$caijiModel->selectByPk($id);
       
        
        $common = new Common();
        $this->library("Snoopy"); // 载入snoopy类
        $this->library2("phpQuery"); // 载入phpQuery类
        $snoopy = new Snoopy();
        $page_num=trim($caiji['page_start'])==''?0:$caiji['page_start'];//当前页数
       
        //读取第page_num页链接
        if(isset($_GET['page_num']))
        {
            $url_caiji=str_replace("{0}", $_GET['page_num'], $caiji['url_list']);
            $page_num=$_GET['page_num'];
            
        }
        else //读取第一页数据
        {
            if($page_num==0)
            {
                $url_caiji=$caiji['url_first'];
                
            }
            else 
            {
                $url_caiji=str_replace("{0}", $page_num, $caiji['url_list']);
               
            }
            
            
        }
       
       
        $snoopy->fetchlinks($url_caiji);
        $snoopy->expandlinks = true; // 是否补全链接 在采集的时候经常用到
		
        foreach ($snoopy->results as $k => $v) {
           
              if($common->getListUrl("{$caiji[url_list_zz]}", $v)!='')
             {
                 $temp1= $common->getListUrl("{$caiji[url_list_zz]}", $v); // 得到所有链接url
                 if(strpos( $temp1,"http")=="")
                 {
                     //不包含http
                     //echo "1";die();
                     $temp1=$caiji['sitedir']. $temp1;
                 }else
                 {
                     //包含http
                     //echo $imgSrc;die();
                 }
                
                 $url[] =$temp1;
                 
             } 
        }
        
        if(count($url)==0)
        {
			echo $url_caiji."\n";
            echo "访问受限"."\n";
			print_r($snoopy->results)."\n";
			Header("Location: {$url_caiji}");
            die();
        }
        // 去除空和重复的url
        $url = array_filter($url);
        $url = array_unique($url);
        //print_r($url);die();
        
        $articleModel = new Model("article");
        $this->helper('input');
     foreach ($url as $k => $v) {
        
         
         
                phpQuery::newDocumentFile($v);
            
                // 加载一条url的详细页，存入数据库
            
				if($caiji['bianma']=="utf-8")
				{
					$data['sort_id'] = $caiji['sort_id'];
					$data['status'] = $caiji['status'];
					$biaotiStr=preg_replace ( "/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', pq("{$caiji['biaoti']}")->html() );
					$description=preg_replace ( "/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', pq("{$caiji['content']}")->html() );
					//$description=substr(trim($description), 0, 2000);
                    $data['description']=$description;
					if(strlen($biaotiStr)>90)
					{
						$data['biaoti'] = substr($biaotiStr, 4, 50);
					}else
					{
						$data['biaoti'] =$biaotiStr;
					}
					
					if(empty($caiji['shijian']))
					{
						$data['dtime'] =date("Y-m-d H:i",time());
					}else
					{
						$data['dtime'] = pq("{$caiji['shijian']}")->html();
						
					}
					$data['update_time'] = $data['dtime'];
					$data['laiyuan'] = $caiji['laiyuan'];
					$data['content'] = pq("{$caiji['content']}")->html();
					// 下载图片  并替换路径
					$img = pq("{$caiji['content']}")->find("img");
					$img_num=0;
					foreach ($img as $i) {
						$imgSrc = pq($i)->attr("src");
						$imgDir=str_replace(basename($imgSrc), "", $imgSrc);
						if(strpos($imgSrc,"http")=="")
						{
							//不包含http  
							//echo "1";die();
							$imgSrc=$caiji['sitedir'].$imgSrc;
						}else 
						{
							//包含http  
							//echo $imgSrc;die();
						}
					   
						$common->GrabImage($imgSrc, "{$caiji['dir']}" . basename($imgSrc)); // 下载图片
						
						
						//替换原来图片途径为本地路径
						$data['content']=str_replace($imgDir, "{$caiji['dir']}", $data['content']);
						//第一张图片设置为新闻封面
						if($img_num==0 and $data['suolutu']=='')
						{
							$data['suolutu']=$caiji['dir'] . basename($imgSrc);
				
						}
						$img_num++;
					}
				
				   //替换相关词 
                    $data['biaoti']=str_replace("方维","维尼",$data['biaoti']);
					$data['biaoti']=str_replace("深圳","成都",$data['biaoti']);
					$data['biaoti']=str_replace("szfangwei.cn","cdweni.com",$data['biaoti']); 
 
                     
					$data = deepspecialchars($data);
					$data = deepslashes($data);
					if (count($articleModel->selectByArrayAnd(array(
						"biaoti" => $data['biaoti'], "sort_id" => $caiji['sort_id']
					))) == 0) {
						$articleModel->insert($data);
						echo "采集成功" . $v . "<br>";
						ob_flush();
						flush();
					} else {
						echo "已经采集，不重复采集" . $v . "<br>";
						ob_flush();
						flush();
					}
					unset($data);
				}
				else
				{
					// 将数据组成对象
				
					$data['sort_id'] = $caiji['sort_id'];
					$data['status'] = $caiji['status'];
					$biaotiStr=preg_replace ( "/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', pq("{$caiji['biaoti']}")->html() );
					$biaotiStr=iconv('gbk', 'utf-8', $biaotiStr);

					//描述
					$description=preg_replace ( "/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', pq("{$caiji['content']}")->html() );
					$description=iconv('gbk', 'utf-8', $description);
					//$description=substr(trim($description), 0, 2000);
					$data['description'] = $description;
	//$test=pq(".article h1")->html();
	//$test=iconv('gbk', 'utf-8', $test);
	//echo  $test;die();

					if(strlen($biaotiStr)>90)
					{
						$data['biaoti'] = substr($biaotiStr, 4, 50);
					}else
					{
						$data['biaoti'] =$biaotiStr;
					}
					
					
					if(empty($caiji['shijian']))
					{
						$data['dtime'] =date("Y-m-d H:i",time());
					}else
					{
						$data['dtime'] = pq("{$caiji['shijian']}")->html();
						
					}
					$data['update_time'] = $data['dtime'];
					$data['laiyuan'] = $caiji['laiyuan'];
					$data['content'] =iconv('gbk', 'utf-8',pq("{$caiji['content']}")->html()); 
					// 下载图片  并替换路径
					
					$img = iconv('gbk', 'utf-8',pq("{$caiji['content']}")->find("img"));
					$img_num=0;
					foreach ($img as $i) {
						$imgSrc = pq($i)->attr("src");
						$imgDir=str_replace(basename($imgSrc), "", $imgSrc);
						if(strpos($imgSrc,"http")=="")
						{
							//不包含http  
							//echo "1";die();
							$imgSrc=$caiji['sitedir'].$imgSrc;
						}else 
						{
							//包含http  
							//echo $imgSrc;die();
						}
					   
						$common->GrabImage($imgSrc, "{$caiji['dir']}" . basename($imgSrc)); // 下载图片
						
						
						//替换原来图片途径为本地路径
						$data['content']=str_replace($imgDir, "{$caiji['dir']}", $data['content']);
						//第一张图片设置为新闻封面
						if($img_num==0 and $data['suolutu']=='')
						{
							$data['suolutu']=$caiji['dir'] . basename($imgSrc);
				
						}
						$img_num++;
					}
				
				
					$data = deepspecialchars($data);
					$data = deepslashes($data);
					if (count($articleModel->selectByArrayAnd(array(
						"biaoti" => $data['biaoti'], "sort_id" => $caiji['sort_id']
					))) == 0) {
						$articleModel->insert($data);
						echo "采集成功" . $v . "<br>";
						ob_flush();
						flush();
					} else {
						echo "已经采集，不重复采集" . $v . "<br>";
						ob_flush();
						flush();
					}
					unset($data);
				}
                
               
            }
        
        //跳转到下一页
       $page_num= $page_num+1;
        if($page_num==1)
        {
            //$url_caiji=$common->format("Location:http://www.ghycms.com:8080/index.php?p=admin&c=caiji&a=index&page_num={0}","2");
            //header("Location:http://www.ghycms.com:8080/index.php?p=admin&c=caiji&a=index&page_num=2");
            $this->jump(str_replace("{0}", $page_num, "index.php?p=admin&c=caiji&a=index&id={$id}&page_num={0}"),"采集第".$page_num."页",1);
        }
        if($page_num<=$caiji['page_end']) 
        {
           // $url_caiji=$common->format("Location:http://www.ghycms.com:8080/index.php?p=admin&c=caiji&a=index&page_num={0}",$page_num);
            //header("Location:http://www.ghycms.com:8080/index.php?p=admin&c=caiji&a=index&page_num=".$page_num);
            $this->jump(str_replace("{0}", $page_num, "index.php?p=admin&c=caiji&a=index&id={$id}&page_num={0}"),"采集第".$page_num."页",1);
        }else 
        {
            echo "采集完成";  
            $caijiModel ->select("UPDATE `sl_article` SET biaoti = REPLACE ( biaoti, '方维', '维尼' )");
			$caijiModel ->select("UPDATE `sl_article` SET biaoti = REPLACE ( biaoti, '深圳', '成都' )");
			$caijiModel ->select("UPDATE `sl_article` SET biaoti = REPLACE ( biaoti, 'szfangwei.cn', 'cdweni.com' )");
			$caijiModel ->select("UPDATE `sl_article` SET content = REPLACE ( content, '方维', '维尼' )");
			$caijiModel ->select("UPDATE `sl_article` SET content = REPLACE ( content, '深圳', '成都' )");
			$caijiModel ->select("UPDATE `sl_article` SET content = REPLACE ( content, 'szfangwei.cn', 'cdweni.com' )");
            echo "替换完成";  
        }
        
        
    }
    
    // 载入添加采集页面
    public function addAction()
    {}
    
    // 载入编辑采集页面
    public function editAction()
    {}
    
    // 定义insert方法，完成采集的插入
    public function insertAction()
    {}
    
    // 定义update方法，完成采集的更新
    public function updateAction()
    {}
    
    // 定义delete方法，完成采集的删除
    public function deleteAction()
    {}
    
    
    // 压力测试
    public function testAction()
    {
        $this->library2("phpQuery"); // 载入phpQuery类
        for ($i=0 ;i<1000;$i++)
        {
            //暂停 10 秒
            sleep(0.01);
            phpQuery::newDocumentFile("http://jnc.66nsn.com/?c=single&m=act_dzp");
            
        }
        
        
    }
    
}