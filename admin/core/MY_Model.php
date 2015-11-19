<?php
class MY_Model extends CI_Model{
	public function __construct()
	{
        parent::__construct();
    }
	
	/**
	 * 从数据表中查找一条记录
	 
	 * @param table 表名
	 * @param conditions    查找条件，数组array("字段名"=>"查找值")或字符串
	 * @param sort    排序，等同于“ORDER BY ”
	 * @param fields    返回的字段范围，默认为返回全部字段的值
	 */
	public function find($table = null,$conditions = null, $sort = null, $fields = null)
	{
		if(!is_string($table)){
			echo "Don't have table name";
			return FALSE;
		}
		
		if( $record = $this->findAll($table ,$conditions, $sort, $fields, 1) ){
			return array_pop($record);
		}else{
			return FALSE;
		}
	}
	
	
	/**
	 * 从数据表中查找记录
	 
	 * @param table 表名
	 * @param conditions    查找条件，数组array("字段名"=>"查找值")或字符串
							使用字符串时将需要开发者自行使用escape来对输入值进行过滤
							
	 * @param sort    排序，等同于“ORDER BY ”
	 * @param fields    返回的字段范围，默认为返回全部字段的值
	 * @param limit    返回的结果数量限制，等同于“LIMIT ”，如$limit = " 3, 5"，即是从第3条记录（从0开始计算）开始获取，共获取5条记录
	 *                 如果limit值只有一个数字，则是指代从0条记录开始。
	 */
	function findAll($table = null , $conditions = null,$sort = null,$fields = null,$limit = null){
		if(!is_string($table)){
			echo "Don't have table name";
			return FALSE;
		}
		//$this->load->helper("dump");
		$where = "";
		$fields = empty($fields) ? "*" : $fields;
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->db->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		if(null != $sort){
			$sort = "ORDER BY {$sort}";
		}
		
		
		$sql = "SELECT {$fields} FROM {$this->db->dbprefix($table)} {$where} {$sort}";
		if(null != $limit)$sql.= " Limit {$limit}";
		return $this->db->query($sql)->result_array();
		
	}
	
	/**
	 * 计算符合条件的记录数
	 
	 * @param table 表名
	 * @param conditions    查找条件，数组array("字段名"=>"查找值")或字符串
	                        使用字符串时将需要开发者自行使用escape来对输入值进行过滤
	 */
	function findCount($table= null , $conditions = null){
		if(!is_string($table)){
			echo "Don't have table name";
			return FALSE;
		}
		$where = "";
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->db->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		$sql = "SELECT COUNT(*) AS COUNTER FROM {$this->db->dbprefix($table)} {$where}";
		$result = $this->db->query($sql)->result_array();
		return $result[0]['COUNTER'];
	}
	/**
	#添加一条数据 
	# @param $row 数组 数组的键是数据表中的字段名，键对应的值是需要新增的数据
	**/
	public function create($table,$row)
	{
		if(!is_string($table)){
			echo "Don't have table name";
			return FALSE;
		}
		$row = $this->__prepera_format($table,$row);
		if(!is_array($row))return FALSE;
		foreach($row as $key => $value){
			$cols[] = $key;
			$vals[] = $this->db->escape($value);
		}
		$col = join(',', $cols);
		$val = join(',', $vals);

		$sql = "INSERT INTO {$this->db->dbprefix($table)} ({$col}) VALUES ({$val})";
		if($this->db->query($sql)){
			return $this->db->insert_id();
		}else{
			return FALSE;
		}
	}
	
	
	/**
	 * 修改数据，该函数将根据参数中设置的条件而更新表中数据
	 * 
	 * @param conditions    数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
	 * @param row    数组形式，修改的数据，
	 *  此参数的格式用法与create的$row是相同的。在符合条件的记录中，将对$row设置的字段的数据进行修改。
	 */
	public function update($table,$conditions, $row)
	{
		if(!is_string($table)){
			echo "Don't have table name";
			return FALSE;
		}
		$where = "";
		$row = $this->__prepera_format($table,$row);
		if(!is_array($row))return FALSE;
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->db->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		foreach($row as $key => $value){
			$value = $this->db->escape($value);
			$vals[] = "{$key} = {$value}";
		}
		$values = join(", ",$vals);
		$sql = "UPDATE {$this->db->dbprefix($table)} SET {$values} {$where}";
		return $this->db->query($sql);
	}
	
	/**
	 * 按条件删除记录
	 *
	 * @param conditions 数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
	 */
	public function delete($table,$conditions)
	{
		if(!is_string($table)){
			echo "Don't have table name";
			return FALSE;
		}
		$where = "";
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->db->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ( ".join(" AND ",$join). ")";
		}else{
			if(null != $conditions)$where = "WHERE ( ".$conditions. ")";
		}
		$sql = "DELETE FROM {$this->db->dbprefix($table)} {$where}";
		return $this->db->query($sql);
	}
	
	
    /**
	 * 魔术函数，执行模型扩展类的自动加载及使用
	 */
	public function __call($name, $args)
	{
		if($name == 'linker'){
			$this->load->model($name."_model");
			return $this->linker_model->__input($this,$args);
		}else{
			show_error($name." method not found");
		}
	}
	
	
	/**
	 * 按表字段调整适合的字段
	 * @param rows    输入的表字段
	 */
	private function __prepera_format($table,$rows)
	{
		$columns = $this->db->list_fields($table);
		
		$newcol = array();
		foreach( $columns as $col ){
			$newcol[$col] = $col;
		}
		return array_intersect_key($rows,$newcol);
	}

}