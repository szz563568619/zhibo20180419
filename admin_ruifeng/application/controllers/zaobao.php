<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class zaobao extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('customer,admin');
		$this->load->database();
		self::check_login_admin();
		$this->load->model('md_zaobao');
		$this->out_data['site_url'] = parent::get_site_url();
	}

	public function index()
	{
		$this->zaobao_list();
	}

	private function check_login_admin()
	{
		$admin_login = $this->db->where('id',$this->session->userdata('id'))->limit(1)->get('admin')->num_rows();
		if(!$admin_login)
		{
			header("Location:".base_url()."login");
		}
	}

	function zaobao_list()
	{
		$page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
		$limit = 20;
		$table = $this->db->dbprefix('zaobao');
		$dataa = $this->md_zaobao->get_zaobao_list($table,'',$page,$limit);
		$this->out_data['zaobao_list'] = $dataa['data'];
		$base_url = base_url().'zaobao/zaobao_list/?';
		$this->out_data['pagin'] = parent::get_pagin($base_url, $dataa['count'], $limit, 3,  true);
		$this->out_data['con_page'] = 'zaobao_list';
		$this->load->view('default', $this->out_data);
	}

	function zaobao_del($id)
	{
		$this->md_zaobao->del_zaobao($id);
	}

	function edit_zaobao($id = 0)
	{
		$this->out_data['zaobao_info'] = $this->md_zaobao->get_zaobao($id);
		if( ! $this->out_data['zaobao_info'])
		{
			$this->out_data['zaobao_info']['id'] = 0;
		}
		$this->out_data['con_page'] = 'zaobao_edit';
		$this->load->view('default', $this->out_data);
	}

	function zaobao_update()
	{
		$info = array('title' => $this->input->post('title'),
            'intro' => $this->input->post('intro'),
			'content' => $this->input->post('content'));
		$id = $this->input->post('id');
		$result = array('status' => false, 'msg' => '');
		if(empty($info['title'])){
			$result['msg'] = "文章标题不能为空！";
			echo json_encode($result);
			exit;
		}
		if(empty($info['content'])){
			$result['msg'] = "文章内容不能为空！";
			echo json_encode($result);
			exit;
		}
		if($id == 0)
		{
			$info['time'] = date('Y-m-d H:i:s');
			$this->md_zaobao->add_zaobao($info);
		}
		else
		{
			$this->md_zaobao->update_zaobao($id, $info);
		}
		$result['status'] = true;
	
		echo json_encode($result);
	}

}