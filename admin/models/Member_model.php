<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Member_model extends MY_Model{

    function __construct(){
        parent::__construct();
    }

    public $mainTable = 'wl_member';
    /**
     * 关联表
     * @var array
     */
    protected $_link = array(
        'MemberGroup'=>array(
            'table'=>'wl_member_group',
            'selfKey'=>'groupid',
            'otherKey'=>'groupid'
        )
    );

    /**
     * 创建 主表匹配数组
     * @return array|bool
     */
    public function creatData($data){
        return $this->createDateCommon($data,$this->mainTable);
    }

    /**
     * 查询sell公共方法
     * @param string $files  查询字段
     * @param string $where  条件
     * @param string $limit  limit
     * @param string $order  排序
     * @param int    $type   1：返回一条一维数据 0:默认返回二维数组
     * @return array 查询结果
     */
    public function getMemberCommon($files='*',$where='',$order='',$limit='',$type=0){
        $sql = "SELECT ".$files;
        $sql .= " FROM ".$this->mainTable;
        if($where){
            $sql .= " WHERE ".$where;
        }

        if($order){
            $sql .= " ORDER BY ".$order;
        }

        if($limit){
            $sql .= " LIMIT ".$limit;
        }

        $query = $this->db->query($sql);

        if($query->num_rows>0){
            if(!$type){
                return $query->result_array();
            }else{
                return $query->row_array();
            }
        }else{
            return array();
        }

    }


    /**
     * 连表查询 公共方法
     * @param string $files    查询字段
     * @param array $manTable  主表 array('表名'=>'别名')
     * @param array $link      关联表 array('$_link'=>'别名')
     * @param string $where    查询条件
     * @param string $order    排序
     * @param string $limit    limit
     * @param int $type        1：返回一条一维数据 0:默认返回二维数组
     * @return array
     */
    public function getMemberCommonLink($files='*',$manTable,$link,$where='',$order='',$limit='',$type=0){
        $manTableName = key($manTable);
        $manTableAlse = $manTable[$manTableName];
        $sql = "SELECT ".$files;
        $sql .= " FROM ".$manTableName." AS ".$manTableAlse;

        if($link){
            while($key = key($link)){
                $sql .= " LEFT JOIN ".$this->_link[$key]['table']." AS ".$link[$key]." ON ".$link[$key].".".$this->_link[$key]['otherKey']." = ".$manTableAlse.".".$this->_link[$key]['selfKey'];
                next($link);
            }
        }

        if($where){
            $sql .= " WHERE ".$where;
        }

        if($order){
            $sql .= " ORDER BY ".$order;
        }

        if($limit){
            $sql .= " LIMIT ".$limit;
        }

        $query = $this->db->query($sql);

        if($query->num_rows>0){
            if(!$type){
                return $query->result_array();
            }else{
                return $query->row_array();
            }
        }else{
            return array();
        }

    }


    /**
     * 获取所有企业会员
     * @return array
     */
    public function getCompanyMember(){
        return $this->getMemberCommon('userid,username','groupid=6');
    }

    /**
     * 获取会员详细信息
     * @param $userid  用户id
     * @return array
     */
    public function getMemberDetail($userid){
        return $this->getMemberCommon('*',"userid='{$userid}'",'','',1);
    }



    /**
	member表的多表查询记录
	前面的字段跟MY_Model中的findAll方法一样
	@company_conditions 主要针对company表 传进的参数是 array("condition"=>"","sort"=>"","fields"=>"","limit"=>"")
	
	**/
	
	function member_findAll($conditions = null, $sort = null, $fields = null,$limit = null,$company_conditions = array("condition"=>"","sort"=>"","fields"=>"","limit"=>"")){
		$result = $this->findAll("member",$conditions,$sort,$fields,$limit);
		if($result){
			foreach($result as $k=>$v){
				//$result[$k]['member'] = $result[$k];
				if(!empty($company_conditions['condition'])){
						if(is_array($company_conditions['condition'])){
							$fcondition = array('userid'=>$v['userid']) + $company_conditions['condition'];
						}else{
							$fcondition = "userid = '{$v['userid']}' AND {$company_conditions['condition']}";
						}
				}else{
					$fcondition = array('userid'=>$v['userid']);
				}
				$result[$k]['mcompany'] = $this->find("company",$fcondition,$company_conditions['sort'],$company_conditions['fields']);
				$result[$k]['company_data'] = $this->find("company_data",$fcondition);
			}
		}else{			
			return FALSE;
		}
		
		
		return $result;
	}
	
	function member_find($conditions = null, $sort = null, $fields = null,$company_conditions = array("condition"=>"","sort"=>"","fields"=>"","limit"=>"")){
		if( $record = $this->member_findAll($conditions, $sort, $fields, 1,$company_conditions) ){
			return array_pop($record);
		}else{
			return FALSE;
		}
	}
    /**
     *  根据username获得用户信息
     * @param string $username  用户名称
     * @return array  返回信息
     */
    public function member_user($username){
        $arr = $this->find("member",array("username"=>$username),"","username,credit,loginip,logintime,logintimes");
        return $arr;
    }

}
