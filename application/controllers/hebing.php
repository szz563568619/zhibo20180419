<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/***
 *这个页面在huijin的直播室
 */
class hebing extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
	function index()
	{
		header("Content-type:text/html;charset=utf-8");
		
		//连接要合并过来的数据库，就是鼎汇的
		$yingfeng_data = $this->get_yingfeng_data();
		$this->db = $this->load->database($yingfeng_data, TRUE);
		
		$yingfeng_admin = $this->db->query("select a.*,b.qq,b.phone,b.intro from `zhibo_admin` as a left join `zhibo_admin_extra` as b on a.id = b.aid where find_in_set('customer', a.permission)")->result_array();//下面开始查询yingfeng下面的admin列表和 admin_extra
		$yingfeng_member = $this->db->query("select a.*,b.name as cname from `zhibo_member` as a left join `zhibo_admin` as b on a.cid = b.id")->result_array();//会员表外部
		
		echo "yingfeng有 ".count($yingfeng_member)." 个会员等待处理完毕！<br /><hr />";
		$yingfeng_member_alias = $this->db->query("select * from `zhibo_member_alias`")->result_array();//内部会员小号表
		
		//下面开始把管理员的数据插入到ninggui里面
		$this->db = $this->load->database('default', TRUE);
		
		//这部分处理admin和admin_extra
		$new_admin = array();//新插入的id和老id
		foreach($yingfeng_admin as $v){
			$admin_info = array('name'=>$v['name'],'nick'=>$v['nick'],'password'=>$v['password'],'permission'=>$v['permission'],'rid'=>$v['rid'],'wellcome'=>$v['wellcome'],'login_status'=>$v['login_status']);
			$this->db->insert('admin', $admin_info);//插入admin表
			$new_aid = $this->db->insert_id();
			$admin_extra_info = array('aid'=>$new_aid,'qq'=>$v['qq'],'phone'=>$v['phone'],'intro'=>$v['intro']);//插入admin_extra表
			$this->db->insert('admin_extra', $admin_extra_info);
			$new_admin[$v['id']] = $new_aid;
		}
		
		echo "admin和admin_extra表处理完毕！<br /><hr />";
		
		//这部分处理会员
		$new_member = array();//新插入的id和老id
		foreach($yingfeng_member as $k => $v){
			$v['cid'] = isset($new_admin[$v['cid']]) ? $new_admin[$v['cid']] : $v['cid'];
			$member_info = array('cid'=>$v['cid'],'gid'=>$v['gid'],'name'=>$v['name'],'password'=>$v['password'],'phone'=>$v['phone'],'re_time'=>$v['re_time'],'login_time'=>$v['login_time'],'ip'=>$v['ip'],'is_open'=>$v['is_open'],'is_company'=>$v['is_company'],'source'=>$v['source'],'keyword'=>$v['keyword'],'qq'=>$v['qq'],'account'=>$v['account'],'say'=>$v['say'],'is_mobile_reg'=>$v['is_mobile_reg']);
			
			//看看宁贵里面有没有这个会员
			$res = $this->db->where('name',$v['name'])->limit(1)->get('member')->num_rows();
			if(!$res){
				$this->db->insert('member', $member_info);//插入member表
				$new_mid = $this->db->insert_id();
				$new_member[$v['id']] = $new_mid;
				echo "{$k}不重复会员id：{$v['id']},会员名：{$v['name']},处理结束！<br />";
			}else{
				echo "{$k}重复-会员id：{$v['id']},会员名：{$v['name']},处理结束！<br />";
			}
			
		}
		echo "<hr />";
		echo "member表处理完毕！<br /><hr />";
		
		 //这部分处理小号
		foreach($yingfeng_member_alias as $v){
			if(isset($new_member[$v['mid']])){
				$v['mid'] = $new_member[$v['mid']];
				$member_alias_info = array('mid'=>$v['mid'],'gid'=>$v['gid'],'name'=>$v['name']);
				$this->db->insert('member_alias', $member_alias_info);//插入member表
			}
		}
		
		
		
		echo "member_alias表处理完毕！<br />"; 
		
		
		
	}
	
	protected function get_yingfeng_data()
	{
		$db['default']['hostname'] = 'localhost';
		$db['default']['username'] = 'root';
		$db['default']['password'] = 'Sj4k_JqD82_2Jle903jKJ_kOh3fH2SD_Ft2ls_tux';
		$db['default']['database'] = 'yiducaijing';
		$db['default']['dbdriver'] = 'mysql';
		$db['default']['dbprefix'] = 'zhibo_';
		$db['default']['pconnect'] = TRUE;
		$db['default']['db_debug'] = TRUE;
		$db['default']['cache_on'] = FALSE;
		$db['default']['cachedir'] = 'data';
		$db['default']['char_set'] = 'utf8';
		$db['default']['dbcollat'] = 'utf8_general_ci';
		$db['default']['swap_pre'] = '';
		$db['default']['autoinit'] = TRUE;
		$db['default']['stricton'] = FALSE;
		return $db['default'];
	}

}