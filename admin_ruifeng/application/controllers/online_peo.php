<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class online_peo extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->out_data['socket'] = $this->config->item('socket');

		$this->out_data['con_page'] = 'online_list';
		$this->load->view('default', $this->out_data);
	}
	
	/* 获取在线用户列表 */
	function get_all_visitor()
	{
		$this->load->database();
		$data = json_decode($this->input->post('data'), true);
		$result = array('count' => 0, 'data' => array());
		foreach($data as $k => $v)
		{
			$k = explode('_', $k);
			if(isset($k[1]))
			{
				$mid = $k['0'];//会员或游客id
				$gid = $k['1'];//所属组
				if($gid == 1){
					//游客
					$data = $this->db->query("select a.name,b.nick from {$this->db->dbprefix('visitor')} as a left join {$this->db->dbprefix('admin')} as b on a.cid = b.id where a.id='{$mid}' limit 1")->row_array();
					if($data){
						$data['from'] = my_echo($v['from'], 0);
						$result['data'][] = $data;
					}
				}else{
					//会员
					$data = $this->db->query("select a.name,b.nick from {$this->db->dbprefix('member')} as a left join {$this->db->dbprefix('admin')} as b on a.cid = b.id where a.id='{$mid}' limit 1")->row_array();
					if($data){
						$data['from'] = my_echo($v['from'], 0);
						$result['data'][] = $data;
					}
				}
			}
		}
		$result['count'] = count($result['data']);
		echo json_encode($result);
	}

}