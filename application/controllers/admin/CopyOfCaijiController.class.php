<?php
// 采集控制器
class caijiController extends BaseController
{
    
    // 显示采集列表
    public function indexAction()
    {
        $common = new Common();
        $this->library("Snoopy"); // 载入snoopy类
        $this->library2("phpQuery"); // 载入phpQuery类
        $snoopy = new Snoopy();
        $page_num=0;//当前页数
        //读取第page_num页链接
        if(isset($_GET['page_num']))
        {
            $url=$common->format("http://www.iteye.com/news/category/industry?page={0}",$_GET['page_num']);
            $page_num=$_GET['page_num'];
        }
        else //读取第一页数据
        {
            
            $url="http://www.iteye.com/news/category/industry";
           
        }
        
        $snoopy->fetchlinks($url);
        
        $snoopy->expandlinks = true; // 是否补全链接 在采集的时候经常用到
        foreach ($snoopy->results as $k => $v) {
            $url[] = $common->getLisrUrl("/http:\/\/www.iteye.com\/news\/[0-9]{5}/", $v); // 得到所有链接url
        }
        // 去除空和重复的url
        $url = array_filter($url);
        $url = array_unique($url);
        
        // $common->GrabImage("http://www.jb51.net/images/logo.gif", "public/images/1.jpg");//下载图片
        // 遍历所有链接
        ob_end_clean();
        $articleModel = new Model("article");
        $this->helper('input');
        foreach ($url as $k => $v) {
            phpQuery::newDocumentFile($v);
            
           
            // 加载一条url的详细页，存入数据库
            
            // 将数据组成对象
            
            $data['sort_id'] = "19";
            $data['status'] = "终审";
            $data['biaoti'] = pq(".title h3 a")->html();
            $data['dtime'] = pq(".news_info .date")->html();
            $data['update_time'] = $data['dtime'];
            $data['laiyuan'] = "来源";
            $data['content'] = pq("#news_content")->html();
            // 下载图片  并替换路径
            $img = pq("#news_content")->find("img");
            foreach ($img as $i) {
                $imgSrc = pq($i)->attr("src");
                $common->GrabImage($imgSrc, "public/images/" . basename($imgSrc)); // 下载图片
                $imgDir=str_replace(basename($imgSrc), "", $imgSrc);
                //替换原来图片途径为本地路径
                $data['content']=str_replace($imgDir, "public/images/", $data['content']);
            }
            
            
            $data = deepspecialchars($data);
            $data = deepslashes($data);
            if (count($articleModel->selectByArray(array(
                "biaoti" => $data['biaoti']
            ))) == 0) {
                $articleModel->insert($data);
                echo "采集成功" . $v . "<br>";
            } else {
                $articleModel->insert($data);
                echo "已经采集，不重复采集" . $v . "<br>";
            }
            
            flush();
        }
        
        //跳转到下一页
       $page_num= $page_num+1;
        if($page_num==1)
        {
            $url=$common->format("http://www.iteye.com/news/category/industry?page={0}","2");
            header($url);
        }
        if($page_num<=3) 
        {
            $url=$common->format("http://www.iteye.com/news/category/industry?page={0}",$page_num);
            header($url);
        }else 
        {
            echo "采集完成";
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
}