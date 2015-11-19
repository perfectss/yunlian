<?php 
class Linker_model extends CI_Model{
	private $model_obj = null;
	private $run_result = null;
	private $linker = null;
	private $methods = array('find','findAll','create','delete','update');
	
	public function __construct()
	{
        parent::__construct();
		$this->load->config("linker");
		$this->linker = $this->config->item("linker");
		
    }
	
	public function __input(& $obj, $args = null){
		$this->model_obj = $obj;
		return $this;
	}
	/** 
	 * 魔术函数，支持多重函数式使用类的方法
	 * @param func_name    需要执行的函数名称
	 * @param func_args    函数的参数
	 */
	public function __call($func_name, $func_args){
		if(in_array( $func_name, $this->methods )){
			if( !$run_result = call_user_func_array(array($this->model_obj, $func_name), $func_args) ){
				if( 'update' != $func_name )return FALSE;
			}
			
			if (array_key_exists($func_args[0], $this->linker)) {
				$linker = $this->linker[$func_args[0]];
			}else{
				return $run_result;
			}
			
			
			if( null != $linker && is_array($linker) ){
				foreach( $linker as $linkey => $thelinker ){
					if( !isset($thelinker['map']) )$thelinker['map'] = $linkey;
					if( FALSE == $thelinker['enabled'] )continue;
					$thelinker['type'] = strtolower($thelinker['type']);
					if( 'find' == $func_name ){
						$run_result[$thelinker['map']] = $this->do_select( $thelinker, $run_result );
					}elseif( 'findAll' == $func_name){
						foreach( $run_result as $single_key => $single_result )
							$run_result[$single_key][$thelinker['map']] = $this->do_select( $thelinker, $single_result);
					}elseif( 'create' == $func_name ){
						$this->do_create( $thelinker, $run_result, $func_args );
					}elseif( 'update' == $func_name ){
						$this->do_update( $thelinker, $func_args );
					}elseif( 'delete' == $func_name){
						$this->do_delete( $thelinker, $func_args );
					}
				}
			}
			return $run_result;
		}else{
			return FALSE;
		}
	}

	/** 
	 * 私有函数，进行关联删除数据操作
	 * @param thelinker    关联的描述
	 * @param func_args    进行操作的参数
	 */
	private function do_delete( $thelinker, $func_args ){
		if( !$maprecords = $this->model_obj->findAll($thelinker['ftable'],$func_args[1]))return FALSE;
		foreach( $maprecords as $singlerecord ){
			if(!empty($thelinker['condition'])){
				if( is_array($thelinker['condition']) ){
					$fcondition = array($thelinker['fkey']=>$singlerecord[$thelinker['mapkey']]) + $thelinker['condition'];
				}else{
					$fcondition = "{$thelinker['fkey']} = '{$singlerecord[$thelinker['mapkey']]}' AND {$thelinker['condition']}";
				}
			}else{
				$fcondition = array($thelinker['fkey']=>$singlerecord[$thelinker['mapkey']]);
			}
			$returns = $this->model_obj->delete($thelinker['ftable'],$fcondition);
		}
		return $returns;
	}
	/** 
	 * 私有函数，进行关联更新数据操作
	 * @param thelinker    关联的描述
	 * @param func_args    进行操作的参数
	 */
	private function do_update( $thelinker, $func_args ){
		if( !is_array($func_args[2][$thelinker['map']]) )return FALSE;
		if( !$maprecords = $this->model_obj->findAll($thelinker['ftable'],$func_args[1]))return FALSE;
		foreach( $maprecords as $singlerecord ){
			if(!empty($thelinker['condition'])){
				if( is_array($thelinker['condition']) ){
					$fcondition = array($thelinker['fkey']=>$singlerecord[$thelinker['mapkey']]) + $thelinker['condition'];
				}else{
					$fcondition = "{$thelinker['fkey']} = '{$singlerecord[$thelinker['mapkey']]}' AND {$thelinker['condition']}";
				}
			}else{
				$fcondition = array($thelinker['fkey']=>$singlerecord[$thelinker['mapkey']]);
			}
			$returns = $this->model_obj->update($thelinker['ftable'],$fcondition, $func_args[2][$thelinker['map']]);
		}
		return $returns;
	}
	/** 
	 * 私有函数，进行关联新增数据操作
	 * @param thelinker    关联的描述
	 * @param newid    主表新增记录后的关联ID
	 * @param func_args    进行操作的参数
	 */
	private function do_create( $thelinker, $newid, $func_args ){
		if( !is_array($func_args[1][$thelinker['map']]) )return FALSE;
		if('hasone'==$thelinker['type']){
			$newrows = $func_args[1][$thelinker['map']];
			$newrows[$thelinker['fkey']] = $newid;
			return $this->model_obj->create($thelinker['ftable'],$newrows);
		}elseif('hasmany'==$thelinker['type']){
			if(array_key_exists(0,$func_args[1][$thelinker['map']])){ // 多个新增
				foreach($func_args[1][$thelinker['map']] as $singlerows){
					$newrows = $singlerows;
					$newrows[$thelinker['fkey']] = $newid;
					$returns = $this->model_obj->create($thelinker['ftable'],$newrows);	
				}
				return $returns;
			}else{ // 单个新增
				$newrows = $func_args[1][$thelinker['map']];
				$newrows[$thelinker['fkey']] = $newid;
				return $this->model_obj->create($thelinker['ftable'],$newrows);
			}
		}
	}
	/** 
	 * 私有函数，进行关联查找数据操作
	 * @param thelinker    关联的描述
	 * @param run_result    主表执行查找后返回的结果
	 * 
	 */
	private function do_select( $thelinker, $run_result){
		if(empty($thelinker['mapkey']))return FALSE;
		if( 'manytomany' == $thelinker['type'] ){
			$do_func = 'findAll';
			$midcondition = array($thelinker['mapkey']=>$run_result[$thelinker['mapkey']]);
			if( !$midresult = $this->model_obj->findAll($thelinker['ftable'],$midcondition,null,$thelinker['fkey']) )return FALSE;
			$tmpkeys = array();foreach( $midresult as $val )$tmpkeys[] = "'".$val[$thelinker['fkey']]."'";
			if(!empty($thelinker['condition'])){
				if( is_array($thelinker['condition']) ){
					$fcondition = "{$thelinker['fkey']} in (".join(',',$tmpkeys).")";
					foreach( $thelinker['condition'] as $tmpkey => $tmpvalue )$fcondition .= " AND {$tmpkey} = '{$tmpvalue}'";
				}else{
					$fcondition = "{$thelinker['fkey']} in (".join(',',$tmpkeys).") AND {$thelinker['condition']}";
				}
			}else{
				$fcondition = "{$thelinker['fkey']} in (".join(',',$tmpkeys).")";
			}
		}else{
			$do_func = ( 'hasone' == $thelinker['type'] ) ? 'find' : 'findAll';
			if(!empty($thelinker['condition'])){
				if( is_array($thelinker['condition']) ){
					$fcondition = array($thelinker['fkey']=>$run_result[$thelinker['mapkey']]) + $thelinker['condition'];
				}else{
					$fcondition = "{$thelinker['fkey']} = '{$run_result[$thelinker['mapkey']]}' AND {$thelinker['condition']}";
				}
			}else{
				$fcondition = array($thelinker['fkey']=>$run_result[$thelinker['mapkey']]);
			}
		}
		return $this->model_obj->$do_func($thelinker['ftable'],$fcondition, $thelinker['sort'], $thelinker['field'], $thelinker['limit'] );
	}
}