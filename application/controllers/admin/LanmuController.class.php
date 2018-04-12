<?php
// 商品类型控制器
class LanmuController extends BaseController
{
    // 显示商品类型
    public function indexAction()
    {
        
        // 使用模型获取所有栏目
        $LanmuModel = new LanmuModel('column');
        $classidArray = $LanmuModel->getLanmuByclassid("");
        $queryStr = trim( empty($_REQUEST['u1'])? "" : $_REQUEST['u1'] );
        
        $Common = new Common();
        if ($queryStr != '') {
            if (!($Common->isName($queryStr))) {
                
                $this->jump('index.php?p=admin&c=lanmu&a=index', '输入值不合法');
            } else 
            {
                $data = $LanmuModel->select(" select id,u1 as name ,u2,u3,classid as parentid from sl_column where u1 like '%{$queryStr}%' order by id asc,u2 asc ");
                
            }
        }else 
        {
            $data = $LanmuModel->select(" select *,u1 as name,classid as parentid from sl_column  order by id asc,u2 asc");
            
        }
        //挂载 TREE 类
        $this->helper('tree');
        $tree =new Tree() ;
        $tree->tree($data);
        
        // 如果使用数组, 请使用 getArray方法
        //$tree->getArray();
        // 下拉菜单选项使用 get_tree方法
        
        //$str = "<option value=\$id \$select>\$spacer\$name</option>";
        $str = "<tr class=''>
                     <td>\$id </td>
                            <td style='text-align: left;'>
                            <a href='index.php?p=admin&c=lanmu&a=add&id=\$id'>\$spacer \$name </a>
                            </td>
                            <td class='hidden-xs'>0</td>
                            <td class='hidden-xs'>\$u3</td>
                            <td class='hidden-xs'>\$u4</td>
                            <td>    
                                    <a href='index.php?p=admin&c=lanmu&a=add&id=\$id' title='添加子类' class='operation'>
                                    <span class='ficon ficon-tianjia'></span>
                                    </a>
                                    <a href='index.php?p=admin&c=lanmu&a=edit&id=\$id' title='编辑' class='operation'>
                                    <span class='ficon ficon-xiugai'></span>
                                    </a>
                                    <a href='index.php?p=admin&c=lanmu&a=delete&id=\$id' onclick='if(confirm('确定删除栏目?')==false)return false;'>
                                    <span class='ficon ficon-shanchu'></span>
                                    </a>
                             </td>
                </tr>";
        $html='';
        $html .= $tree->get_tree(0,$str,-1);
         
        
        // 载入模板页面
        include CUR_VIEW_PATH . "Slanmu/lanmu_list.html";
    }

    public function addAction()
    {
        $LanmuModel = new LanmuModel('column');
        $id = trim(isset($_GET['id']) ? $_GET['id'] : "0");
        $type= trim(empty($_GET['type']) ? "" : $_GET['type']);
        $model_id= trim(empty($_GET['model_id']) ? "" : $_GET['model_id']);
        $sortid= trim(empty($_GET['sortid']) ? "" : $_GET['sortid']);
        
        $classidArray = $data = $LanmuModel->select(" select *,u1 as name,classid as parentid from sl_column  order by id asc,u2 asc");
        //挂载 TREE 类
        $this->helper('tree');
        $tree =new Tree() ;
        $tree->tree($classidArray);
        $str = "<option value=\$id >\$spacer\$name</option>";
        $html_option='';
        $html_option .= $tree->get_tree(0,$str,-1);
        
        $lianjie = empty($_GET['url'])?"":$_GET['url'];
        $lanmuName =  empty($_GET['name'])?"":$_GET['name'] ;
        $lianjie = str_replace("ghy123", "?", $lianjie);
        $lianjie = str_replace("ghy321", "&", $lianjie);
        
        include CUR_VIEW_PATH . "Slanmu" . DS . "lanmu_add.html";
    }
    // 完成类型入库操作
    public function insertAction()
    {
        $LanmuModel = new LanmuModel('column');
        $data = $LanmuModel->getFieldArray();
        // 1.收集表单数据
         $data['classid'] = $data['classid']=='' ? '0': $data['classid'];
         $data['u2'] = $data['u2']=='' ? '0' : $_POST['u2'];
        
        // 2.验证和处理
        if ($data['u1'] == '') {
            $this->jump('index.php?p=admin&c=lanmu&a=add', '栏目名称不能为空');
        }
        if ($data['classid'] == '') {
            $this->jump('index.php?p=admin&c=lanmu&a=add', '所属栏目不能为空');
        }
        
        $this->helper('input');
        $data = deepspecialchars($data);
        $data = deepslashes($data);
        
        // 3.调用模型完成入库并给出提示
       
        if ($LanmuModel->insert($data)) {
            // 如果是$_GET['type']=wenzhang 文章模型，将sortid及其子分类都添加到栏目
            if (isset($_REQUEST['type']) and $_REQUEST['type'] != '') {
                $model_id = $_REQUEST['model_id'];
                $sortid = $_REQUEST['sortid'];
                $sortModel = new SortModel("sort");
                $lanmu_id = $LanmuModel->select("select * from `sl_column` where classid = '{$data['classid']}' and u1 = '{$data['u1']}'  ORDER BY id desc LIMIT 1");
                $lanmu_id = $lanmu_id[0]['id'];
                $sortModel->addSubSortToLanmu($sortid, $lanmu_id, $data);
            }
            
            $this->jump('index.php?p=admin&c=lanmu&a=index', '添加成功', 2);
        } else {
            $this->jump('index.php?p=admin&c=lanmu&a=add', '添加失败');
        }
    }

    public function editAction()
    {
        $LanmuModel = new LanmuModel('column');
        $id = trim(isset($_GET['id']) ? $_GET['id'] : "");
        $classidArray = $LanmuModel->getLanmuByclassid("");
        //挂载 TREE 类
        $this->helper('tree');
        $tree =new Tree() ;
        $tree->tree($classidArray);
        $str = "<option value=\$id >\$spacer\$name</option>";
        $html_option='';
        $html_option .= $tree->get_tree(0,$str,-1);
        
        $lanmu_detail = $LanmuModel->selectByPk($id);
        include CUR_VIEW_PATH . "Slanmu" . DS . "lanmu_edit.html";
    }

    public function updateAction()
    {
        
        // 1.收集表单数据
        $data['id'] = trim($_POST['id']);
        $data['classid'] = trim($_POST['classid']);
        $data['u1'] = trim($_POST['u1']);
        $data['u2'] = empty($_POST['u2']) ? '0' : $_POST['u2'];
        $data['u3'] = trim($_POST['u3']);
        $data['u4'] = trim($_POST['u4']);
        // 2.验证和处理
        if ($data['id'] == '') {
            $this->jump('index.php?p=admin&c=lanmu&a=add', 'id不能为空');
        }
        if ($data['u1'] == '') {
            $this->jump('index.php?p=admin&c=lanmu&a=add', '栏目名称不能为空');
        }
        if ($data['classid'] == '') {
            $this->jump('index.php?p=admin&c=lanmu&a=add', '所属栏目不能为空');
        }
        
        $this->helper('input');
        $data = deepspecialchars($data);
        $data = deepslashes($data);
        // 3.调用模型完成入库并给出提示
        $LanmuModel = new LanmuModel('column');
        if ($LanmuModel->update($data) > 0) {
            $this->jump('index.php?p=admin&c=lanmu&a=index', '修改成功', 3);
        } else {
            $this->jump('index.php?p=admin&c=lanmu&a=edit&id=' . $data['id'], '修改失败');
        }
    }

    public function deleteAction()
    {
        // 1.收集表单数据
        $data['id'] = trim($_GET['id']);
        // 2.验证和处理
        $Common = new Common();
        
        if (! $Common->isNumber($data['id'])) {
            $this->jump('index.php?p=admin&c=lanmu&a=index', 'ID不合法');
        }
        
        // 3.调用模型完成入库并给出提示
        $LanmuModel = new LanmuModel('column');
        $lanmu_detail = $LanmuModel->selectByPk($data['id']);
        
        // 如果存在子栏目，则不能删除
        $classidArray = $LanmuModel->getLanmuByclassid($data['id']);
        if (count($classidArray) > 0) {
            $this->jump('index.php?p=admin&c=lanmu&a=index', '该栏目含有子栏目，请从最底层的栏目开始删除');
        }
        if ($lanmu_detail['laiyuan'] == "系统") {
            $this->jump('index.php?p=admin&c=lanmu&a=index', '系统栏目，不能删除');
        } else {
            if ($LanmuModel->delete($data['id']) > 0) {
                $this->jump('index.php?p=admin&c=lanmu&a=index', '删除成功');
            } else {
                $this->jump('index.php?p=admin&c=lanmu&a=index', '删除失败');
            }
        }
    }
}