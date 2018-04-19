<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class caichuanhai extends CI_Controller {

	private $redis = null;
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function index()
	{
		$redis = self::_redis_conn();
		$redis->zremrangebyrank("room", 0, -1);
		$redis->delete("g_innnerpeo_list");

		$yesterday = date("Y-m-d",strtotime('-1 month'));
		$this->db->query("delete from {$this->db->dbprefix('chat_list')} where date_format(time,'%Y-%m-%d')<='{$yesterday}'");

		$day5ago = date("Y-m-d",strtotime('-10 day')); /*5天前*/
		$this->db->query("delete from {$this->db->dbprefix('visitor')} where date_format(time,'%Y-%m-%d')<='{$day5ago}'");
		$this->db->query("delete from {$this->db->dbprefix('visitor_chat')} where date_format(time,'%Y-%m-%d')<='{$day5ago}'");
	}

	/*连接redis*/
	private function _redis_conn()
	{
		if($this->redis != null) return $this->redis;

		$this->redis = new Redis();
		$redis_conf = $this->config->item('redis');
		$this->redis->connect($redis_conf['host'], $redis_conf['port']);
		if($redis_conf['auth'] != '') $this->redis->auth($redis_conf['auth']);
		$this->redis->select($redis_conf['db']);
		return $this->redis;
	}

	/* 前台管理员发送聊天信息 */
	function admin_send_msg()
	{
		$redis = self::_redis_conn();
		$auto_adminmsg = $this->db->query("select auto_adminmsg from {$this->db->dbprefix('room')} limit 1")->row()->auto_adminmsg;//默认取出第一条发送

		if($auto_adminmsg)
		{
			$pre = $redis->get('send_msg_admin_id');
			$next = $this->db->query("select id,name,say,qq from {$this->db->dbprefix('member')} where say <> '' AND gid = 0 order by id limit 1")->row_array();//默认取出第一条发送
			//如果redis里面有上一次的值
			if($pre)
			{
				$next = $this->db->query("select id,name,say,qq from {$this->db->dbprefix('member')} where id > {$pre}  AND say <> '' AND gid = 0 order by id limit 1")->row_array();
				if(!$next)
				{
					$next = $this->db->query("select id,name,say,qq from {$this->db->dbprefix('member')} where say <> '' AND gid = 0 order by id limit 1")->row_array();
				}
			}
			
			$redis->set('send_msg_admin_id', $next['id']);
			
			$forbidden = explode('|', $this->forbiddenword());
			$content = str_replace($forbidden, '**', $next['say']);

			//$content = is_shouji(is_qq($content));
			if($next['qq'])
			{
				$content = $content.'<a href="javascript:;"  onclick="open_qq('.$next['qq'].')" style="margin-left:10px;text-decoration:none;color:red;"><img src="'.base_url().'skin/'.$this->config->item('tpl').'/images/webchat/qq.png" align="absmiddle"></a>';
			}
			$info = array('gid' => 0, 'time' => date('Y-m-d H:i:s'), 'content' => $content, 'name' => $next['name'], 'types' => 1, 'score' => str_pad(str_replace('.', '', microtime(true)),14,0));
			send_websocket(array('type' => 'public_msg', 'content' => json_encode($info)));
			$this->db->insert('chat_list', $info);
		}
	}

	/* 屏蔽关键词 */
	protected function forbiddenword(){
		$res = array();
		if($this->redis->exists('forbiddenword')){
			$res = $this->redis->get('forbiddenword');
		}else{
			$res = $this->db->query("select forbidden from {$this->db->dbprefix('room')} limit 1")->row()->forbidden;
			$this->redis->set('forbiddenword',$res);
		}	
		
		return $res;
	}
}