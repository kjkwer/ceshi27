<?php

class Common
{

    public function isEmpty($val)
    {
        if (! is_string($val))
            return false; // 是否是字符串类型
        
        if (empty($val))
            return false; // 是否已设定
        
        if ($val == '')
            return false; // 是否为空
        
        return true;
    }

    /*
     * -----------------------------------------------------------
     * 函数名称：isNumber
     * 简要描述：检查输入的是否为数字
     * 输入：string
     * 输出：boolean
     * 修改日志：------
     * -----------------------------------------------------------
     */
    public function isNumber($val)
    {
        if (preg_match("/^[0-9]+$/", $val))
            return true;
        return false;
    }

    /*
     * -----------------------------------------------------------
     * 函数名称：isPhone
     * 简要描述：检查输入的是否为电话
     * 输入：string
     * 输出：boolean
     * 修改日志：------
     * -----------------------------------------------------------
     */
    public function isPhone($val)
    {
        // eg: xxx-xxxxxxxx-xxx | xxxx-xxxxxxx-xxx ...
        if (preg_match("/^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/", $val))
            return true;
        return false;
    }

    /*
     * -----------------------------------------------------------
     * 函数名称：isPostcode
     * 简要描述：检查输入的是否为邮编
     * 输入：string
     * 输出：boolean
     * 修改日志：------
     * -----------------------------------------------------------
     */
    public function isPostcode($val)
    {
        if (preg_match("/^[0-9]{4,6}$/", $val))
            return true;
        return false;
    }

    /*
     * -----------------------------------------------------------
     * 函数名称：isEmail
     * 简要描述：邮箱地址合法性检查
     * 输入：string
     * 输出：boolean
     * 修改日志：------
     * -----------------------------------------------------------
     */
    public function isEmail($val, $domain = "")
    {
        if (! $domain) {
            if (preg_match("/^[a-z0-9-_ www.cshangzj.com .]+@[\da-z][\.\w-]+\.[a-z]{2,4}$/i", $val)) {
                return true;
            } else
                return false;
        } else {
            if (preg_match("/^[a-z0-9-_.]+@" . $domain . "$/i", $val)) {
                return true;
            } else
                return false;
        }
    }
    // end func
    
    /*
     * -----------------------------------------------------------
     * 函数名称：isName
     * 简要描述：姓名昵称合法性检查，只能输入中文英文
     * 输入：string
     * 输出：boolean
     * 修改日志：------
     * -----------------------------------------------------------
     */
    public function isName($val)
    {
        if (preg_match("/^[\x80-\xffa-zA-Z0-9]{3,60}$/", $val) || preg_match("/^[0-9]+$/", $val)) // 2008-7-24
        {
            return true;
        }
        
        return false;
    }
    // end func
    
    /*
     * -----------------------------------------------------------
     * 函数名称:isStrLength($theelement, $min, $max)
     * 简要描述:检查字符串长度是否符合要求
     * 输入:mixed (字符串，最小长度，最大长度)
     * 输出:boolean
     * 修改日志:------
     * -----------------------------------------------------------
     */
    public function isStrLength($val, $min, $max)
    {
        $theelement = trim($val);
        if (preg_match("/^[a-zA-Z0-9]{" . $min . "," . $max . "}$/", $val))
            return true;
        return false;
    }

    /*
     * -----------------------------------------------------------
     * 函数名称:isNumberLength($theelement, $min, $max)
     * 简要描述:检查字符串长度是否符合要求
     * 输入:mixed (字符串，最小长度，最大长度)
     * 输出:boolean
     * 修改日志:------
     * -----------------------------------------------------------
     */
    public function isNumLength($val, $min, $max)
    {
        $theelement = trim($val);
        if (preg_match("/^[0-9]{" . $min . "," . $max . "}$/", $val))
            return true;
        return false;
    }
    
    // 定义一个函数getIP()
    public function getIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else 
            if (getenv("HTTP_X_FORWARDED_FOR"))
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            else 
                if (getenv("REMOTE_ADDR"))
                    $ip = getenv("REMOTE_ADDR");
                else
                    $ip = "Unknow";
        return $ip;
    }
    
    //查询IP地址
    public function getIPAddress($ipadd)
    {
        if(filter_var($ipadd, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = @file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".trim($ipadd));
            $ip = json_decode($ip,true);
            $address= $ip['data']['country']."-".$ip['data']['area']."-". $ip['data']['region']."-". $ip['data']['city'];
            return $address;
        }else 
        {
            return "本地";
        }
        
       
    }
    
    
    // 得到完整的当前url
    public function getUrl()
    {
        return $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
    }
    
    // 获得两个时间的差
    public function timediff($begin_time, $end_time)
    {
        // $startdate=$date2;
        // $enddate=$date1;
        // $date=floor((strtotime($enddate)-strtotime($startdate))/86400);
        // $hour=floor((strtotime($enddate)-strtotime($startdate))%86400/3600);
        // $minute=floor((strtotime($enddate)-strtotime($startdate))%86400/60);
        // $second=floor((strtotime($enddate)-strtotime($startdate))%86400%60);
        // echo $date."天<br>";
        // echo $hour."小时<br>";
        // echo $minute."分钟<br>";
        // echo $second."秒<br>";
    }
    
    // 遍历所有文件夹下的文件 start
    public function getFileNameByDir($Mydir)
    {
        $dir = $Mydir;
        $filenames = $this->get_filenamesbydir($dir);
        // 返回所有文件名，包括路径
        return $filenames;
    }

    public function get_allfiles($path, &$files)
    {
        if (is_dir($path)) {
            $dp = dir($path);
            while ($file = $dp->read()) {
                if ($file != "." && $file != "..") {
                    $this->get_allfiles($path . "/" . $file, $files);
                }
            }
            $dp->close();
        }
        if (is_file($path)) {
            $files[] = $path;
        }
    }

    public function get_filenamesbydir($dir)
    {
        $files = array();
        $this->get_allfiles($dir, $files);
        return $files;
    }
    // 遍历所有文件夹下的文件 end
    
    // 遍历单个文件夹下的所有文件 start
    public function get_onedirfiles($path)
    {
        $dp = dir($path);
        while ($file = $dp->read()) {
            if ($file != "." && $file != "..") {
                $files[] = $path . "/" . $file;
            }
        }
        $dp->close();
        return $files;
    }
    // 遍历单个文件夹下的所有文件 end
    
    // 根据字段的类型名称得到类型
    public function getFiledType($filedName)
    {
        switch (trim($filedName)) {
            
            case "文本框":
                return "char(100)";
                break;
            
            case "文本编辑器":
                return "mediumtext";
                break;
            
            case "文本域":
                return "varchar(250)";
                break;
            
            case "时间框":
                return "timestamp";
                break;
            
            case "单选":
                return "varchar(250)";
                break;
            
            case "多选":
                return "varchar(250)";
                break;
            
            case "图片":
                return "varchar(250)";
                break;
            
            case "组图":
                return "varchar(250)";
                break;
                
            case "密码":
                return "varchar(250)";
                break;
            
            default:
                return "char(100)";
                break;
        }
    }

    /*
     * 采集相关函数
     * 得到符合正则的文章详情链接
     */
    public function getListUrl($rule1, $str)
    {
        // http://www.iteye.com/news/31395
        $rule = $rule1;
        
        preg_match($rule, $str, $result);
       
        if (count($result) > 0) {
            return $result[0];
        } else {
            return "";
        }
    }
    
    // URL是远程的完整图片地址，不能为空, $filename 是另存为的图片名字
    // 默认把图片放在以此脚本相同的目录里
    public function GrabImage($url, $filename = "")
    {
        // $url 为空则返回 false;
        if ($url == "") {
            return false;
        }
        $ext = strrchr($url, "."); // 得到图片的扩展名
        if ($ext != ".gif" && $ext != ".jpg" && $ext != ".bmp" && $ext != ".png") {
            echo "格式不支持！";
            return false;
        }
        if ($filename == "") {
            $filename = time() . "$ext";
        } // 以时间戳另起名
                  
        // 开始捕捉
        ini_set(“max_execution_time”,3000); 
        set_time_limit(3000); 
        
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
        $size = strlen($img);
        $fp2 = fopen($filename, "a");
        fwrite($fp2, $img);
        fclose($fp2);
        return $filename;
    }

    public function format()
    {
        $args = func_get_args();
        
        if (count($args) == 0) {
            return;
        }
        
        if (count($args) == 1) {
            return $args[0];
        }
        
        $str = array_shift($args);
        
        $str = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', create_function('$match', '$args = ' . var_export($args, true) . '; return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];'), $str);
        
        return $str;
    }
    //php防注入和XSS攻击通用过滤.==>数组
    
   public function SafeFilterArray (&$arr)
    {
         
        $ra=Array('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/','/script/','/javascript/','/vbscript/','/expression/','/applet/','/meta/','/xml/','/blink/','/link/','/style/','/embed/','/object/','/frame/','/layer/','/title/','/bgsound/','/base/','/onload/','/onunload/','/onchange/','/onsubmit/','/onreset/','/onselect/','/onblur/','/onfocus/','/onabort/','/onkeydown/','/onkeypress/','/onkeyup/','/onclick/','/ondblclick/','/onmousedown/','/onmousemove/','/onmouseout/','/onmouseover/','/onmouseup/','/onunload/');
         
        if (is_array($arr))
        {
            foreach ($arr as $key => $value)
            {
                if (!is_array($value))
                {
                    if (!get_magic_quotes_gpc())             //不对magic_quotes_gpc转义过的字符使用addslashes(),避免双重转义。
                    {
                        $value  = addslashes($value);           //给单引号（'）、双引号（"）、反斜线（\）与 NUL（NULL 字符）加上反斜线转义
                    }
                    $value       = preg_replace($ra,'',$value);     //删除非打印字符，粗暴式过滤xss可疑字符串
                    $arr[$key]     = htmlentities(strip_tags($value)); //去除 HTML 和 PHP 标记并转换为 HTML 实体
                }
                else
                {
                    SafeFilter($arr[$key]);
                }
            }
        }
    }
    
    //php防注入和XSS攻击通用过滤.==>单个变量
    
    public function SafeFilterStr ($Str)
    {
        $ra=Array('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/','/script/','/javascript/','/vbscript/','/expression/','/applet/','/meta/','/xml/','/blink/','/link/','/style/','/embed/','/object/','/frame/','/layer/','/title/','/bgsound/','/base/','/onload/','/onunload/','/onchange/','/onsubmit/','/onreset/','/onselect/','/onblur/','/onfocus/','/onabort/','/onkeydown/','/onkeypress/','/onkeyup/','/onclick/','/ondblclick/','/onmousedown/','/onmousemove/','/onmouseout/','/onmouseover/','/onmouseup/','/onunload/');
        if (!get_magic_quotes_gpc())             //不对magic_quotes_gpc转义过的字符使用addslashes(),避免双重转义。
        {
            $value  = addslashes($Str);           //给单引号（'）、双引号（"）、反斜线（\）与 NUL（NULL 字符）加上反斜线转义
        }
        $value       = preg_replace($ra,'',$value);     //删除非打印字符，粗暴式过滤xss可疑字符串
        $key     = htmlentities(strip_tags($value)); //去除 HTML 和 PHP 标记并转换为 HTML 实体
         return $key;
        
    }
    
    

    //php判断是否为手机浏览器
    function is_mobile(){
    
        // returns true if one of the specified mobile browsers is detected
        // 如果监测到是指定的浏览器之一则返回true
    
        $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
    
        $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
    
        $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
    
        $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
    
        $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
    
        $regex_match.=")/i";
    
        // preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true
        return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }
    
    /*
     * php计算时间几分钟前、几小时前、几天前函数
     * 
     * */
    public function time_tran($the_time) {
        $now_time = date("Y-m-d H:i:s", time());
        //echo $now_time;
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $dur = $now_time - $show_time;
        if ($dur < 0) {
            return $the_time;
        } else {
            if ($dur < 60) {
                return $dur . '秒前';
            } else {
                if ($dur < 3600) {
                    return floor($dur / 60) . '分钟前';
                } else {
                    if ($dur < 86400) {
                        return floor($dur / 3600) . '小时前';
                    } else {
                        if ($dur < 259200) {//3天内
                            return floor($dur / 86400) . '天前';
                        } else {
                            return $the_time;
                        }
                    }
                }
            }
        }
    }  
    
    
    // 严格按照离当前时间的间隔来输出
    
    function formatTime($time)
    {
        $now_time = date("Y-m-d H:i:s", time());
        $the_time=$time;
        
        //echo $time."<br/>";
        //flush();
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $t= $now_time - $show_time;
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                $m = floor($t % $k);
                foreach ($f as $x => $y) {
                    if (0 != $r = floor($m / (int)$x)) {
                        return $c.$v.$r.$y.'前';
                    }
                }
                return $c.$v.'前';
            }
        }
    }
    
    //加密算法
    function encrypt($data, $key)
    {
        $key    =   md5($key);
        $x      =   0;
        $len    =   strlen($data);
        $l      =   strlen($key);
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        for ($i = 0; $i < $len; $i++)
        {
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        return base64_encode($str);
    }  
    
    //解密算法
    function decrypt($data, $key)
    {
        $key = md5($key);
        $x = 0;
        $data = base64_decode($data);
        $len = strlen($data);
        $l = strlen($key);
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++)
        {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
            {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }
            else
            {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    } 
    
    
    //分解url里面的参数，将参数返回数组
    function getUrlParams($url)
    {
        
        $refer_url = parse_url($url);
        
        $params = empty($refer_url["query"])?"":$refer_url["query"];
        
        $arr = array();
        if($params!="")
        {
            $paramsArr = explode('&',$params);
            
            foreach($paramsArr as $k=>$v)
            {
                $a = explode('=',$v);
                $arr[$a[0]] = $a[1];
            }
        }else 
        {
            $arr["c"]="未找到 controller";
        }
        return $arr;
    }
    
    function getOrderId() {
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
    
    
    
}