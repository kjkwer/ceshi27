<?php
//日志模型
class SystemModel extends Model {
	//获取所有的日志
	public function getSystems(){
		$sql = "SELECT * FROM {$this->table}";
		return $this->db->getAll($sql);
		
	}

    //添加日志
	public function addSystem($u1,$u2,$u3,$u4)
	{
		$data['u1'] = $u1;
		$data['u2'] = $u2;
		$data['u3'] = $u3;
		$data['u4'] = $u4;
		//2.验证和处理
		include HELPER_PATH . "input.php";
		$data = deepspecialchars($data);
		$data = deepslashes($data);

		$this->insert($data);
	}


}