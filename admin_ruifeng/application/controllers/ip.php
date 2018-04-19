<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ip extends MY_Controller {

	private $redis;
	function __construct()
	{
		parent::__construct();
		parent::check_permission('base,customer');
		$this->redis = parent::redis_conn();
	}

	public function index()
	{
		$this->ip_list();
	}

	function ip_list()
	{
		$keys = $this->redis->keys('ipban_*');//无值，返回空数组
		$values = $this->redis->getMultiple($keys);//无值，返回false
		$this->out_data['ip_list'] = array_combine($keys, my_echo($values, array()));//合并一个数组的键和另一个数组的值
		if(!$this->out_data['ip_list']) $this->out_data['ip_list'] = array();
		$this->out_data['con_page'] = 'ip_list';
		$this->load->view('default', $this->out_data);
	}

	function ip_del()
	{
		$forbidden = $this->input->post('forbidden');
		$this->redis->del('ipban_'.$forbidden);
	}

	//屏蔽ip
	function ip_ban()
	{
		$result = array('status' => false, 'msg' => '');
		$forbidden = $this->input->post('forbidden');
		$key = 'ipban_'.$forbidden;
		
		$admin_name = $this->session->userdata('nick'); //管理员,屏蔽人

		/*先判断是否已经在redis中被禁过了*/
		/* if($this->redis->exists($key))
		{
			$result['msg'] = "'{$forbidden}'已经被屏蔽过，不需要再次屏蔽！";
		}
		else
		{ */
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
						$this->redis->setex('ipban_'.$ip, 3600*24*15, $admin_name);
					}
				}
				$result['status'] = true;
			}

		/* } */
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