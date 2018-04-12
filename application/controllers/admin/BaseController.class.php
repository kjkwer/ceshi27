<?php
//后台基础控制器
class BaseController extends Controller {
	//构造方法
	public function __construct(){
	    @header('Content-type: text/html;charset=UTF-8');
		$this->checkLogin();
	}
	
	//验证用户是否登录
	public function checkLogin(){
        $a=empty($_REQUEST['a'])? "" : $_REQUEST['a'];
        if ($a == "daochu" ||$a == "fileupload" ) {
            //导出功能不用判断登录；
        } else {
            //注意，此处的admin是我在登录成功时保存的登录标识符
            if (!isset($_SESSION['admin'])) {
                $this->jump('index.php?p=admin&c=login&a=login','你还没有登录呢');
            }
        }

// 	    //注意，此处的admin是我在登录成功时保存的登录标识符
// 	    if (!isset($_SESSION['admin'])) {
// 	        $this->jump('index.php?p=admin&c=login&a=login','你还没有登录呢');
// 	    }
	                
		
		
	}
	
	
	//返回联动空间选中的下一项html
	public function getLiandongHtml($classid,$filedName,$selected_val="",$canshu_id=""){
	    $CanshuModel = new Model("canshu");
	    $Canshu = $CanshuModel->select("select * from sl_canshu where classid=".$classid);
	    $temp_html='';
	    if(count($Canshu)>0)
	    {
	        //$temp_html.='<div id=div_'.$classid.' class="liandong" >';
	        $temp_html.='<select  name="'.$filedName.$classid.'" onchange="$(this).nextAll().remove() ;change(this.value,\'liandong_'.$filedName.'\',\''.$filedName.'\');"   id="'.$filedName.$classid.'" class="select-container2">';
	        $temp_html.='<option value="">请选择</option>';
	       
	       //有子栏目
	       foreach ($Canshu as $k=>$v)
	       {
	           //<option value="323">武汉</option>
	           if($canshu_id==$v["id"])
	           {
	               $temp_html.='<option value="'.$v["id"].'"  selected = "selected"  >'.$v["u1"].'</option>';
	           }else
	           {
	               $temp_html.='<option value="'.$v["id"].'">'.$v["u1"].'</option>';
	           }
	           
	           
	       }
	       $temp_html.='</select>';
	       //$temp_html.='</div>';
	       
	       
	       
	       
	    }
	    else
	    {
	        //无子栏目
	        $temp_html='';
	    }
	    return $temp_html;
	   
	}
	
    //返回全部联动控件选html
	public function getAllLiandongHtml($canshu_id,$filedName,$classid,$temp1_html){
        	$canshuModel=new Model("canshu");
        	$canshu = $canshuModel->select("select * from sl_canshu where id=".$canshu_id);
        	
        	if($canshu_id <= $classid)
        	{
        	    $temp1_html=$this->getLiandongHtml($canshu_id,$filedName,"","").$temp1_html;
        	    return $temp1_html;
            }else
            {
                $temp1_html=$this->getLiandongHtml($canshu[0]["classid"],$filedName,$canshu[0]["classid"],$canshu_id).$temp1_html;
                return $this->getAllLiandongHtml($canshu[0]["classid"],$filedName,$classid,$temp1_html);
                
            }
            //return "123";
            
             
    }
	
	/*
	 * 加载自定义控件方法
	 * $kjName        $v['u2']
	 * $filedName     $v['u1']
	 * $selectValue   $wenzhang[$v['u1']]
	 * $tipString     $v['u3']
	 * $filedId
	 * */
	public function getKj($type,$kjName,$filedName,$selectValue,$tipString,$filedId){
	    if($type=="文本域")
	    {
	        return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>
				    			<textarea name="'.$filedName.'" class="input" style="height: 127px; margin: 0px; width: 100%;">'.$selectValue.'</textarea><i>'.$tipString.'</i>
				    		    </td>
				    	     </tr>';
	        
	    }else if($type=="图片")
	    {
	        $_js_str="
	            <script type='text/javascript'>
	               //iframe窗
	               function upload_".$filedName."()
                   {
                       layer.open({
                          type: 2,
                          title: '上传图片 ',
                          shadeClose: true,
                          shade: false,
                          maxmin: true, //开启最大化最小化按钮
                          area: ['893px', '600px'],
                          content: '/index.php?p=admin&c=inc&a=showWebUploader&type=image&field=".$filedName."'
                       }); 
                   }
                 </script>
	               ";
	        return $_js_str.' <tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>
				    		    <input type="hidden" name="'.$filedName.'"  id="'.$filedName.'"  >
				    		    <a id="img_a_'.$filedName.'"  href="'.$selectValue.'"  target="_blank" >
				    		       <img id="img_'.$filedName.'" src="'.$selectValue.'" style="max-width: 200px;max-height: 100px;overflow: hidden;" onerror="javascript:this.src=\'application/views/admin/images/nopic.jpg\';" /> 
				    		    </a>
                                
                                <a onclick="upload_'.$filedName.'()"  class="btn btn-blue"><em class="ficon  ficon-uploading"></em> 上传图片</a>
                                <i>'.$tipString.'</i>
				    		    </td>
				    	     </tr>';

	        
	    }else if($type=="单选")
	    {
             $filedModel1=new FiledModel("filed");
             $filedVal=$filedModel1->getFiledDefaultValue($filedId);
             $temp_radio_html=' ';
             $i=1; 
             
             if($filedVal==$selectValue || $selectValue=="")
             {
                 $temp_radio_html.='<input id="'.$filedName.'" type="radio" name="'.$filedName.'" checked="checked" value=" ">请选择  ';
             }
             
             foreach($filedVal as $key=>$val)
             {
                 if($selectValue==trim($val) ) 
                 { 
                     $temp_radio_html.='<input id="'.$filedName.'" type="radio" name="'.$filedName.'" checked="checked" value="'.$val.'">'.$val;
                 }else{ 
                     $temp_radio_html.='<input id="'.$filedName.'" type="radio" name="'.$filedName.'"  value="'.$val.'">'.$val;
                  }
               $i++;
              }
             
	        return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
								<td>
								
								<!-- 循环默认值 -->
								'.$temp_radio_html.'
								</td>

							</tr>  ';
	        
	        
	    }else if($type=="文本编辑器")
	    {
	        return ' <div class="bjq">
                       <p class="title" style="font-size: 15px;font-weight: bold;color: #555;"><em class="txt-blue ficon ficon-deng"></em> '.$kjName.'</em></p>
                          <script id="'.$filedName.'" name="'.$filedName.'" type="text/plain">'.html_entity_decode($selectValue).'</script>
                          <script type="text/javascript">
                            var ue_'.$filedName.' = UE.getEditor("'.$filedName.'");
                          </script>
                      </div>';
	        
	    }else if($type=="时间框")
	    {
	        return '  <div class="marb-20">
					        <p class="title"><em class="txt-blue ficon ficon-riqi"></em> '.$kjName.'</p>
					        <p><input name="'.$filedName.'" type="text" id="'.$filedName.'" class="dinput" style="width: 80%;"  value="'.$selectValue.'" onClick="WdatePicker({skin:\'whyGreen\',dateFmt:\'yyyy-MM-dd HH:mm:ss\',minDate:\'1900-01-01 00:00:00\',maxDate:\'2117-01-01 00:00:00\'})" /></p>
					    </div>';
	        
	    }else if($type=="金额")
	    {
	        return ' <div class="marb-20">
					        <p class="title"><em class="txt-blue ficon ficon-deng"></em>'.$kjName.'</p>
					        <p> <input type="text" class="input" style="width: 50px;"  name="'.$filedName.'"  id="'.$filedName.'"  value="'.$selectValue.'"/> </p>
				        </div>';
	        
	    }else if($type=="数字")
	    {
	        return ' <div class="marb-20">
					        <p class="title"><em class="txt-blue ficon ficon-deng"></em>'.$kjName.'</p>
					        <p> <input type="text" class="input" style="width: 50px;"  name="'.$filedName.'"  id="'.$filedName.'"  value="'.$selectValue.'"/> <span class="txt-blue">数值越小越前面</span></p>
				        </div>';
	        
	    }
	    else if($type=="密码")
	    {
	        return '<tr>
				    	 <th>'.$kjName.'</th>
    				    <td>
                            <input name="'.$filedName.'" type="password" class="input" /><i>密码为空则不修改'.$tipString.'</i>
    				    </td>
				   </tr>'; 
	    } else if($type=="联动")
	    {
	        if(strlen($selectValue)==0)
	        {
	            $filedModel1=new FiledModel("filed");
	            $filedVal=$filedModel1->getFiledDefaultValue($filedId);
	           
	            return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td><div id="liandong_'.$filedName.'" class="liandong1" >'.
				    		    $this->getLiandongHtml($filedVal,$filedName).
				    		    '</div><input name="'.$filedName.'" id="'.$filedName.'" type="hidden"  value="'.$selectValue.'">'.
				    		    '</td>
                        </tr>';
	        }else
	        {
	            $filedModel1=new FiledModel("filed");
	            $filedVal=$filedModel1->getFiledDefaultValue($filedId);
	            
	            //默认值
	            $classid=$filedVal;
	            //已选中，递归全部选中项
	            //$temp_html=$this->getAllLiandongHtml($selectValue,$filedName,$classid,"");
	            return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td><div id="liandong_'.$filedName.'" class="liandong1" >'.
				    		    $this->getAllLiandongHtml($selectValue,$filedName,$classid,"").
				    		    '</div><input name="'.$filedName.'" id="'.$filedName.'" type="hidden"  value="'.$selectValue.'">'.
				    		    '</td>
                        </tr>';
	        }
	        
	        
	        
	    }else if($type=="组图")
	    {
	        $filedModel1=new FiledModel("filed");
	        $filedVal=$filedModel1->getFiledDefaultValue($filedId);
	        return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>'.
				    		    '<input name="'.$filedName.'" id="'.$filedName.'" type="hidden"  value="'.$selectValue.'">'.
				    		    '	       
                                <iframe width="100%" onload="this.height=50" src="/index.php?p=admin&c=Inc&a=addWebuploader&field='.$filedName.'" scrolling="no" frameborder="0" id="if'.$filedName.'" ></iframe>
                    	        <script>
                    	        function reinitIframe(){
                    	            var iframe = document.getElementById("if'.$filedName.'");
                    	            try{
                    	                var bHeight = iframe.contentWindow.document.body.scrollHeight;
                    	                var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
                    	                var height = Math.max(bHeight, dHeight);
                    	                iframe.height = height;
                    	                //console.log(height);
                    	            }catch (ex){}
                    	        }
                    	        window.setInterval("reinitIframe()", 200);
                    	        </script>'.
                    	        '<i>'.$tipString.'</i>'.
				    		    '</td>
                        </tr>';
	        
	        
	    }else if($type=="文件")
	    {
	        $_js_str="
	            <script type='text/javascript'>
	               //iframe窗
	               function upload_".$filedName."()
                   {
                       layer.open({
                          type: 2,
                          title: '上传文件 ',
                          shadeClose: true,
                          shade: false,
                          maxmin: true, //开启最大化最小化按钮
                          area: ['893px', '600px'],
                          content: '/index.php?p=admin&c=inc&a=showWebUploader&type=file&field=".$filedName."' 
                       });
                   }
                 </script>
	               ";
	        return $_js_str.' <tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>
				    		    <input type="hidden" name="'.$filedName.'"  id="'.$filedName.'"  >
				    		    <a id="file_'.$filedName.'"  href="'.$selectValue.'"  target="_blank" >'.$selectValue.'</a>
                                <a onclick="upload_'.$filedName.'()"  class="btn btn-blue"><em class="ficon  ficon-uploading"></em> 上传文件</a>
                                <i>'.$tipString.'</i>
				    		    </td>
				    	     </tr>';
	        
	        
	    }else if($type=="下拉框")
	    {
	        $filedModel1=new FiledModel("filed");
	        //$filedVal=$filedModel1->getFiledDefaultValue($filedId);
	        return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>'.
				    		    '<input name="'.$filedName.'" id="'.$filedName.'" type="hidden"  value="'.$selectValue.'">'.
				    		    '	       
                                <iframe max-width="250px"  height="20px"  src="/index.php?p=admin&c=Inc&a=addSelect&field='.$filedName.'&field_id='.$filedId.'" scrolling="no"  frameborder="0" id="if'.$filedName.'" ></iframe>
                    	         <script>
                        	        function reinitIframe'.$filedName.'(){
                        	            var iframe = document.getElementById("if'.$filedName.'");
                        	            try{
                        	                var bHeight = iframe.contentWindow.document.body.scrollHeight;
                        	                var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
                        	                var height = Math.max(bHeight, dHeight);
                        	                
                        	                var bWidth = iframe.contentWindow.document.body.scrollWidth;
                        	                var dWidth = iframe.contentWindow.document.documentElement.scrollWidth;
                        	                var width = Math.min(bWidth, width);
                        	                iframe.height = height;
                        	                 iframe.width = width;
                        	                //console.log(height);
                        	            }catch (ex){}
                        	        }
                        	        window.setInterval("reinitIframe'.$filedName.'()", 200);
                    	        </script>
                                 </td>
                        </tr>';
	        
	        
	    }else if($type=="多条记录")
	    {
	        $commomClass = new Common();
	        $filedModel1=new FiledModel("filed");
	        $filedValArray=$filedModel1->getFiledDefaultValue($filedId);
	        $_model_id=$filedValArray["modelid"];
	        $_guanlianziduan=$filedValArray["fieldname"];//两张表通过关联的字段
            if($selectValue=="" || empty($selectValue))
            {
                $selectValue = $commomClass->getOrderId();
            }
	        //echo '/index.php?p=admin&c=Inc&a=showDuotiaojilu&model_id='.$_model_id.'&guanlianziduan_val='.$selectValue.'&guanlianziduan='.$_guanlianziduan.'&field='.$filedName.'&field_id='.$filedId.'';die();
	        return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>'.
				    		    '<input name="'.$filedName.'" id="'.$filedName.'" type="hidden"  value="'.$selectValue.'">'.
				    		    '	       
                                <iframe width="100%" onload="this.height=50" src="/index.php?p=admin&c=Inc&a=showDuotiaojilu&model_id='.$_model_id.'&guanlianziduan_val='.$selectValue.'&guanlianziduan='.$_guanlianziduan.'&field='.$filedName.'&field_id='.$filedId.'" scrolling="no" frameborder="0" id="if'.$filedName.'" ></iframe>
                    	        <script>
                    	        function reinitIframe'.$filedName.'(){
                    	            var iframe = document.getElementById("if'.$filedName.'");
                    	            try{
                    	                var bHeight = iframe.contentWindow.document.body.scrollHeight;
                    	                var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
                    	                var height = Math.max(bHeight, dHeight);
                    	                iframe.height = height;
                    	                //console.log(height);
                    	            }catch (ex){}
                    	        }
                    	        window.setInterval("reinitIframe'.$filedName.'()", 200);
                    	        </script>'.
				    		    '</td>
                        </tr>';
	        
	        
	    }else if($type=="省市县三级联动")
	    {
	        $filedModel1=new FiledModel("filed");
	        //$filedVal=$filedModel1->getFiledDefaultValue($filedId);
	        return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>'.
				    		    '<input name="'.$filedName.'" id="'.$filedName.'" type="hidden"  value="'.$selectValue.'">'.
				    		    '	       
                                <iframe width="100%" onload="this.height=50" src="/index.php?p=admin&c=Inc&a=showdiqu&field='.$filedName.'&field_id='.$filedId.'" scrolling="no" frameborder="0" id="if'.$filedName.'" ></iframe>
                    	        <script>
                    	        function reinitIframe'.$filedName.'(){
                    	            var iframe = document.getElementById("if'.$filedName.'");
                    	            try{
                    	                var bHeight = iframe.contentWindow.document.body.scrollHeight;
                    	                var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
                    	                var height = Math.max(bHeight, dHeight);
                    	                iframe.height = height;
                    	                //console.log(height);
                    	            }catch (ex){}
                    	        }
                    	        window.setInterval("reinitIframe'.$filedName.'()", 200);
                    	        </script>'.
				    		    '</td>
                        </tr>';
	        
	        
	    }else if($type=="城市选择器(多选)")
	    {
	        $_js_str="
	            <script type='text/javascript'>
	               //iframe窗
	               function upload_".$filedName."()
                   {
                       layer.open({
                          type: 2,
                          title: '城市选择器 ',
                          shadeClose: true,
                          shade: false,
                          maxmin: true, //开启最大化最小化按钮
                          area: ['893px', '600px'],
                          content: '/index.php?p=admin&c=inc&a=SelectCity&type=duoxuan&field=".$filedName."'
                       }); 
                   }
                 </script>
	               ";
	        $selectValue_array = explode("|",$selectValue);
	        if(count($selectValue_array)<2)
	        {
	            $selectValue1=$selectValue;
	            $selectValue2=$selectValue;
	        }else 
	        {
	            $selectValue1=$selectValue_array[0];
	            $selectValue2=$selectValue_array[1];
	        }
	        return $_js_str.' <tr style="display: table-row;">
				    		    <th>'.$kjName.'</th> 
				    		    <td>
				    		    <input type="hidden"  name="temp_'.$filedName.'"  data-value="'.$selectValue1.'"  value="'.$selectValue2.'"  id="temp_'.$filedName.'"  >
				    		    <input  type="hidden"   class="input" type="text"  readonly="readonly"  name="'.$filedName.'"    value="'.$selectValue.'"  id="'.$filedName.'"  >
                                <a id="city_a_'.$filedName.'"  target="_blank" >
				    		    '.$selectValue2.'
                                </a>
				    		    <a onclick="upload_'.$filedName.'()"  class="btn btn-blue"><em class="ficon  ficon-uploading"></em> 选择城市</a>
                                <i>'.$tipString.'</i>
				    		    </td>
				    	     </tr>';

	        
	    }else if($type=="城市选择器(单选)")
	    {
	        $_js_str="
	            <script type='text/javascript'>
	               //iframe窗
	               function upload_".$filedName."()
                   {
                       layer.open({
                          type: 2,
                          title: '城市选择器 ',
                          shadeClose: true,
                          shade: false,
                          maxmin: true, //开启最大化最小化按钮
                          area: ['893px', '600px'],
                          content: '/index.php?p=admin&c=inc&a=SelectCity&type=danxuan&field=".$filedName."'
                       }); 
                   }
                 </script>
	               ";
	        $selectValue_array = explode("|",$selectValue);
	        if(count($selectValue_array)<2)
	        {
	            $selectValue1=$selectValue;
	            $selectValue2=$selectValue;
	        }else 
	        {
	            $selectValue1=$selectValue_array[0];
	            $selectValue2=$selectValue_array[1];
	        }
	        return $_js_str.' <tr style="display: table-row;">
				    		    <th>'.$kjName.'</th> 
				    		    <td>
				    		    <input type="hidden"  name="temp_'.$filedName.'"  data-value="'.$selectValue1.'"  value="'.$selectValue2.'"  id="temp_'.$filedName.'"  >
				    		    <input  type="hidden"   class="input" type="text"  readonly="readonly"  name="'.$filedName.'"    value="'.$selectValue.'"  id="'.$filedName.'"  >
                                <a id="city_a_'.$filedName.'"  target="_blank" >
				    		    '.$selectValue2.'
                                </a>
				    		    <a onclick="upload_'.$filedName.'()"  class="btn btn-blue"><em class="ficon  ficon-uploading"></em> 选择城市</a>
                                <i>'.$tipString.'</i>
				    		    </td>
				    	     </tr>';

	        
	    }else if($type=="批量上传")
	    {
	        $_js_str="
	            <script type='text/javascript'>
	               //iframe窗
	               function upload_".$filedName."()
                   { 
                       var _path=$('#".$filedName."').val();
                       if(_path=='' || _path ==undefined)
                       {
                            alert('请选择保存路径');
                            return false;
                        }
                       layer.open({
                          type: 2,
                          title: '批量上传',
                          shadeClose: true,
                          shade: false,
                          maxmin: true, //开启最大化最小化按钮
                          area: ['893px', '600px'],
                          content: '/index.php?p=admin&c=inc&a=BatchUpload&field=".$filedName."&path='+_path
                       });
                   }
                              
                  
                              
                    $(function() {
                      $('img').lazyload({ 
                      placeholder : 'application/views/admin/images/nopic.jpg',
                             effect: 'fadeIn'
                       });  
                    });           
                 </script>
	               ";
            $commomClass = new Common();
            $my_filepath=(empty($selectValue) || $selectValue=="" )?"":"/".$selectValue;//ghy 默认保存路径
            $uploadDir = $GLOBALS['config_cache']['UPLOAD_DIR'].'upload'.$my_filepath;
            $files = $commomClass->getFileNameByDir($uploadDir);
            //<img id="img_'.$filedName.'" src="'.$selectValue.'" style="max-width: 200px;max-height: 100px;overflow: hidden;" onerror="javascript:this.src=\'application/views/admin/images/nopic.jpg\';" /> 
            $img_html ="";
            foreach ($files as $k=>$v)
            {
                if($my_filepath=="")
                    break;
                $img_html.='<img src="application/views/admin/images/loading.gif"  data-original="'.$v.'" style="max-width: 200px;max-height: 100px;overflow: hidden;"   /> ';
                
            }
	        return $_js_str.' <tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>
				    		    <input type="text" name="'.$filedName.'"  id="'.$filedName.'" value="'.$selectValue.'"  >
                                <a onclick="upload_'.$filedName.'()"  class="btn btn-blue"><em class="ficon  ficon-uploading"></em> 上传图片</a>
                                <i>上传路径:'.$uploadDir.$tipString.'</i>
                                <div>
                                '. $img_html.'
                                </div>
				    		    </td>
				    	     </tr>';
	        
	        
	    }else  {
	       
	        if($filedName=="laiyuanbianhao" && !empty($_GET['guanlianziduan_val']))
	        {
	            $selectValue=$_GET['guanlianziduan_val'];
	        }
	       return '<tr>
				    	 <th>'.$kjName.'</th>
    				    <td>
                            <input name="'.$filedName.'"  id="'.$filedName.'" type="text" class="input" value="'.$selectValue.'"/><i>'.$tipString.'</i>
    				    </td>
				   </tr>'; 
	    }
	    
	}
	
	
	/*
	 * 显示自定义控件方法
	 * $kjName        $v['u2']
	 * $filedName     $v['u1']
	 * $selectValue   $wenzhang[$v['u1']]
	 * $tipString     $v['u3']
	 * $filedId
	 * */
	public function showKj($type,$kjName,$filedName,$selectValue,$tipString,$filedId){
	    if($type=="图片")
	    {
	        return ' <img src="'.$selectValue.'" style="max-width: 200px;max-height: 100px;overflow: hidden;" onerror="javascript:this.src=\'application/views/admin/images/nopic.jpg\';" />';
	         
	    }else if($type=="单选")
	    {
	        $filedModel1=new FiledModel("filed");
	        $filedVal=$filedModel1->getFiledDefaultValue($filedId);
	        $temp_radio_html=' ';
	        $i=1;
	         
	        if($filedVal==$selectValue || $selectValue=="")
	        {
	            $temp_radio_html.='无';
	        }
	         
	        foreach($filedVal as $key=>$val)
	        {
	            if($selectValue==trim($val) )
	            {
	                $temp_radio_html=$val;
	            } 
	            
	        }
	         
	        return $temp_radio_html;
	         
	         
	    }else if($type=="文本编辑器")
	    {
	        return ' <div class="bjq">
                      '.html_entity_decode($selectValue).'
                      </div>';
	         
	    }else if($type=="时间框")
	    {
	        //return '<input name="'.$filedName.'" type="text" id="'.$filedName.'" class="dinput" value="'.$selectValue.'" onClick="WdatePicker({skin:\'whyGreen\',dateFmt:\'yyyy-MM-dd HH:mm:ss\',minDate:\'1900-01-01 00:00:00\',maxDate:\'2117-01-01 00:00:00\'})" />';
	        return $selectValue;
	        
	    }else if($type=="数字")
	    {
	       // return '<input type="text" class="input" style="width: 50px;"  name="'.$filedName.'"  id="'.$filedName.'"  value="'.$selectValue.'"/>';
	        return $selectValue;
	    }
	    else if($type=="密码")
	    {
	        return '<input name="'.$filedName.'" type="password" class="input" />';
	    } else if($type=="联动")
	    {
	        if(strlen($selectValue)==0)
	        {
	            $filedModel1=new FiledModel("filed");
	            $filedVal=$filedModel1->getFiledDefaultValue($filedId);
	
	            //如果是村管理员1
	            if($_SESSION['admin']['zuming']=="村管理员")
	            {
	                $adminModel = new AdminModel('admin');
	                $canshuModel =new Model("canshu");
	                $user = $adminModel->selectByPk($_SESSION['admin']['user_id']);
	                $canshu = $canshuModel->selectByPk($user["cun_id"]);
	                //村ID
	                $cun_classid=  $canshu["classid"];
	                //只查询本村
	                if(!empty($cun_classid))
	                {
	                    $filedVal=$cun_classid;
	                }
	            }
	             
	            return $this->getLiandongHtml($filedVal,$filedName);
	        }else
	        {
	            $filedModel1=new FiledModel("filed");
	            $filedVal=$filedModel1->getFiledDefaultValue($filedId);
	             
	            //默认值
	            $classid=$filedVal;
	            //已选中，递归全部选中项
	            //$temp_html=$this->getAllLiandongHtml($selectValue,$filedName,$classid,"");
	            return $this->getAllLiandongHtml($selectValue,$filedName,$classid,"");
	        }
	         
	         
	         
	    }else if($type=="组图")
	    {
	        $randNumber=rand(1,10000);
	        $filedModel1=new FiledModel("filed");
	        $filedVal=$filedModel1->getFiledDefaultValue($filedId);
	        return  '<input name="'.$filedName.'" id="'.$filedName.'" type="hidden"  value="'.$selectValue.'">'.
					    		    '
                                <iframe width="100%" onload="this.height=50" src="/index.php?p=admin&c=Inc&a=addWebuploader&field='.$filedName.'" scrolling="no" frameborder="0" id="if'.$filedName.'" ></iframe>
                    	        <script>
                    	        function reinitIframe'.$randNumber.'(){
                    	            var iframe = document.getElementById("if'.$filedName.'");
                    	            try{
                    	                var bHeight = iframe.contentWindow.document.body.scrollHeight;
                    	                var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
                    	                var height = Math.max(bHeight, dHeight);
                    	                iframe.height = height;
                    	                console.log(height);
                    	            }catch (ex){}
                    	        }
                    	        window.setInterval("reinitIframe'.$randNumber.'()", 200);
                    	        </script>';
	         
	         
	    }else if($type=="文件")
	    {
	         
	        return '<a href="'.$selectValue.'">'.$selectValue.'</a>';
	         
	    }else if($type=="下拉框")
	    {
	        $randNumber=rand(1,10000);
	        $filedModel1=new FiledModel("filed");
	        //$filedModel1->selectByPk($pk)
	        //$filedVal=$filedModel1->getFiledDefaultValue($filedId);
	        return     '<input name="'.$filedName.$randNumber.'" id="'.$filedName.$randNumber.'" type="hidden"  value="'.$selectValue.'">'.
					    		    '
                                <iframe  onload="this.height=25;this.width=150;" src="/index.php?p=admin&c=Inc&a=showSelect&field='.$filedName.$randNumber.'&field_id='.$filedId.'" scrolling="no" frameborder="0" id="if'.$filedName.'" ></iframe>
                    	        <script>
                    	        function reinitIframe'.$randNumber.'(){
                    	            var iframe = document.getElementById("if'.$filedName.'");
                    	            try{
                    	                var bHeight = iframe.contentWindow.document.body.scrollHeight;
                        	                var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
                        	                var height = Math.max(bHeight, dHeight);
                        	                
                        	                //var bWidth = iframe.contentWindow.document.body.scrollWidth;
                        	                //var dWidth = iframe.contentWindow.document.documentElement.scrollWidth;
                        	                //var width = Math.min(bWidth, width);
                        	                iframe.height = height;
                        	                 //iframe.width = width;
                    	            }catch (ex){}
                    	        }
                    	        //window.setInterval("reinitIframe'.$randNumber.'()", 200);
                    	        </script>';
	         
	         
	    }else if($type=="城市选择器(多选)" || $type=="城市选择器(单选)")
	    {
	        
	        $selectValue_array = explode("|",$selectValue);
	        if(count($selectValue_array)<2)
	        {
	            $selectValue1=$selectValue;
	            $selectValue2=$selectValue;
	        }else 
	        {
	            $selectValue1=$selectValue_array[0];
	            $selectValue2=$selectValue_array[1];
	        }
	        return   '  <a id="city_a_'.$filedName.'"  target="_blank" >
				    		    '.$selectValue2.'
                         </a>';

	        
	    }else  {
	         
	        return $selectValue;
	    }
	     
	}
	
	
}