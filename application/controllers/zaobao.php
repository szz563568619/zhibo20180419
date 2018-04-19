<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class zaobao extends CI_Controller {
	protected $out_data = array();
	function __construct(){
		parent::__construct();
		$this->out_data['tpl'] = base_url().'skin/zaobao/';
	}
	function index()
	{
		$this->load->database();
		$this->load->model('md_zaobao');
		$page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
		$limit = 14;
		$table = $this->db->dbprefix('zaobao');
		$dataa = $this->md_zaobao->get_zaobao_list($table,'',$page,$limit);
		$this->out_data['zaobao_list'] = $dataa['data'];
		$base_url = base_url().'zaobao/?';
		$this->out_data['pagin'] = $this->get_pagin($base_url, $dataa['count'], $limit, 3,  true);
		$this->load->view('zaobao/zaobao_list', $this->out_data);
	}
	
	function zaobao_page($id){
		$id = (int)$id;
		$this->load->database();
		$this->load->model('md_zaobao');
		$this->out_data['art'] = $this->md_zaobao->get_zaobao($id);
		$this->load->view('zaobao/zaobao_page', $this->out_data);
	}
	
	function zaobao_list()
	{
		$this->load->database();
		$this->load->model('md_zaobao');
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		if($page < 1) exit;
		$limit = 10;
		$table = $this->db->dbprefix('zaobao');
		$data = $this->md_zaobao->get_zaobao_list($table,'',$page,$limit);
		$allpage = ceil($data['count']/$limit);
		if($allpage < $page) exit;
		$data['allpage'] = $allpage;
		echo json_encode($data);
	}
	
	protected function get_pagin($base_url, $total_rows, $limit = 10, $uri_segment = 3, $page_query_string = false)
	{
		$this->load->library('pagination');
		$config['base_url'] = $base_url;
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $limit;
		$config['uri_segment'] = $uri_segment;
		$config['use_page_numbers'] = TRUE;
		
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li><a><strong>';
		$config['cur_tag_close'] = '</strong></a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['page_query_string'] = $page_query_string;

		$this->pagination->initialize($config); 

		return $this->pagination->create_links();
	}

}