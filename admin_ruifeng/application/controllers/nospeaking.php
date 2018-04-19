<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class nospeaking extends MY_Controller {

	private $redis;
	function __construct()
	{
		parent::__construct();
		parent::check_permission('base,customer');
		$this->redis = parent::redis_conn();
	}

	public function index()
	{
		$this->nospeaking_list();
	}

	function nospeaking_list()
	{
		$keys = $this->redis->keys('nospeaking_*');//无值，返回空数组
		$values = $this->redis->getMultiple($keys);//无值，返回false
		$this->out_data['nospeaking_list'] = array_combine($keys, my_echo($values, array()));//合并一个数组的键和另一个数组的值
		if(!$this->out_data['nospeaking_list']) $this->out_data['nospeaking_list'] = array();
		$this->out_data['con_page'] = 'nospeaking_list';
		$this->load->view('default', $this->out_data);
	}

	function nospeaking_del()
	{
		$forbidden = $this->input->post('forbidden');
		$this->redis->del('nospeaking_'.$forbidden);
	}

	//屏蔽ip
	function nospeaking_ban()
	{
		$result = array('status' => false, 'msg' => '');
		$forbidden = $this->input->post('forbidden');
		$key = 'nospeaking_'.$forbidden;
		
		$admin_name = $this->session->userdata('nick'); //管理员,屏蔽人

		//公司ip
		$company_ip = explode(',', $this->config->item('company_ip'));

		if(in_array($forbidden, $company_ip))
		{
			$result['msg'] = '该IP是公司IP，不能屏蔽！';
		}
		else
		{
			$this->redis->setex($key, 3600*24*15, $admin_name); /*立马屏蔽掉*/
			/*如果上面被屏蔽的是用户名，那同时再把他的IP也屏蔽掉，如果能找到他的IP的话*/
			if(!filter_var($forbidden, FILTER_VALIDATE_IP))
			{
				$this->load->database();
				$ip = '';
				if(strpos($forbidden, '游客') == 0)
				{
					/*该用户名是游客，去游客表中找*/
					$ip_info = $this->db->query("select ip from {$this->db->dbprefix('visitor')} where name = '{$forbidden}' limit 1");
				}
				else
				{
					$ip_info = $this->db->query("select ip from {$this->db->dbprefix('member')} where name = '{$forbidden}' limit 1");
				}
				if($ip_info->num_rows() > 0) $ip = $ip_info->row()->ip;

				if($ip != '' AND !in_array($forbidden, $company_ip))
				{
					$this->redis->setex('nospeaking_'.$ip, 3600*24*15, $admin_name);
				}
			}
			$result['status'] = true;
		}
        echo json_encode($result);

	}
	
	function ip_search()
    {
        $all_ip = array();
        $name = trim($this->input->post('name'));
        if($name != '')
        {
			$this->load->database();
            $tb_vis = $this->db->dbprefix('visitor');
            $tb_mem = $this->db->dbprefix('member');
            $vis = $this->db->query("select ip from {$tb_vis} where name='{$name}'")->result_array();
            $mem = $this->db->query("select ip from {$tb_mem} where name='{$name}'")->result_array();
            $all_ip = array_merge($vis, $mem);
        }
        echo json_encode($all_ip);
    }

}