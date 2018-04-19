<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class tactics extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('customer,admin');
		$this->load->database();
		self::check_login_admin();
		$this->out_data['site_url'] = parent::get_site_url();
		$this->out_data['teachers'] = $this->db->query("select id,name from {$this->db->dbprefix('specialist')}")->result_array();
	}

	public function index()
	{
		$this->tactics_list();
	}

	private function check_login_admin()
	{
		$admin_login = $this->db->where('id',$this->session->userdata('id'))->limit(1)->get('admin')->num_rows();
		if(!$admin_login)
		{
			header("Location:".base_url()."login");
		}
	}

	function tactics_list()
	{
		$query_tid = '';
		$page_tid = '';
		$table = $this->db->dbprefix('tactics');
		$page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
		if($this->input->get('tid')){
			$query_tid = ' where tid='.$this->input->get('tid').' ';
			$page_tid = 'tid='.$this->input->get('tid').'&';
		}
		if($query_tid){
			$this->out_data['curteacher'] = $this->db->query("select id,name from {$this->db->dbprefix('specialist')} where id = {$this->input->get('tid')}")->row_array();
		}
		$limit = 10;
		$start = ($page - 1)*$limit;
		$this->out_data['tactics_list'] = $this->db->query("select id,title,create_time from {$table} {$query_tid} order by create_time desc limit {$start},{$limit}")->result_array();
		$count = $this->db->query("select count(1) as num from {$table} {$query_tid}")->row()->num;
		$base_url = base_url().'tactics/tactics_list/?'.$page_tid;
		$this->out_data['pagin'] = parent::get_pagin($base_url, $count, $limit, 3,  true);
		$this->out_data['con_page'] = 'tactics_list';
		$this->load->view('default', $this->out_data);
	}

	function tactics_del($id)
	{
		$this->db->simple_query("delete from {$this->db->dbprefix('tactics')} where id={$id}");
	}

	function edit_tactics($id = 0)
	{
		$this->out_data['tactics_info'] = $this->db->query("select * from {$this->db->dbprefix('tactics')} where id={$id} limit 1")->row_array();
		if( ! $this->out_data['tactics_info'])
		{
			$this->out_data['tactics_info']['id'] = 0;
		}
		$this->out_data['con_page'] = 'tactics_edit';
		$this->load->view('default', $this->out_data);
	}

	function tactics_update()
	{
		$info = array('title' => $this->input->post('title'),
			'intro' => $this->input->post('intro'),
			'tid' => $this->input->post('tid'),
			'fname' => $this->input->post('fname'),
			'content' => $this->input->post('content'));
		$id = $this->input->post('id');
		$result = array('status' => false, 'msg' => '');
		if(empty($info['tid'])){
			$result['msg'] = "老师不能为空！";
			echo json_encode($result);
			exit;
		}
		if(empty($info['title'])){
			$result['msg'] = "战法标题不能为空！";
			echo json_encode($result);
			exit;
		}
		if(empty($info['fname'])){
			$result['msg'] = "上传pdf文件不能为空！";
			echo json_encode($result);
			exit;
		}
		if($id == 0)
		{
			$info['create_time'] = date('Y-m-d H:i:s');
			$this->db->insert('tactics', $info);
		}
		else
		{
			$this->db->where('id', $id);
			$this->db->update('tactics', $info);
		}
		
		//生成swf文件
		$pdf_folder = dirname(BASEPATH).'/upload/zhanfa/';
		$res = exec('/usr/local/swftools/bin/pdf2swf '.$pdf_folder.$info['fname'].'.pdf -o '.$pdf_folder.$info['fname'].'.swf', $output, $return_var);
		$res = exec('/usr/local/swftools/bin/swfcombine /usr/local/swftools/share/swftools/swfs/default_viewer.swf viewport='.$pdf_folder.$info['fname'].'.swf -o '.$pdf_folder.$info['fname'].'.swf', $output, $return_var);
		
		$result['status'] = true;
	
		echo json_encode($result);
	}
}