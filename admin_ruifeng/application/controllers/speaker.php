<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class speaker extends MY_Controller{

	private $redis;
	function __construct()
	{
		parent::__construct();
		$this->redis = parent::redis_conn();
	}
	public function index()
	{
		$this->out_data['con_page'] = 'speaker';
		$this->load->view('default',$this->out_data);

	}

	private function _get_rid_list()
	{
		return explode(',', $this->session->userdata('rid'));
	}

	function get_check_data()
	{
		$score = $this->input->post('score');
		if( ! $score ) $score = 0;
		$room = $this->_get_rid_list();
		$this->out_data['chat_list'] = array(); /*未审核的聊天记录*/
		$this->out_data['chat_list']['score'] = $score;
		$this->out_data['chat_list']['data_list'] = array();

		for($i = 0; $i < 13; $i++)
		{
			foreach($room as $v)
			{
				$list = $this->redis->zRangeByScore('examine_record_'.$v, '('.$score, '+inf', array('withscores' => TRUE));
				foreach($list as $k => $lv)
				{
					$this->out_data['chat_list']['data_list'][$lv] = $k;
					/*每次都取最大的score*/
					$this->out_data['chat_list']['score'] = $this->out_data['chat_list']['score'] > $lv ? $this->out_data['chat_list']['score'] : $lv;
				}
			}
			if( ! empty($this->out_data['chat_list']['data_list']) )
			{
				/*判断有没有数据，有，输出，没有，继续循环*/
	 			echo json_encode($this->out_data['chat_list']);
				ob_flush();
				flush();
				break;
			}
			sleep(2);
			clearstatcache();
		}
		exit();
	}
}