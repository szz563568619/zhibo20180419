<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class zhibo extends MY_Controller {
	protected $redis;
	function __construct(){
		parent::__construct();
		$this->redis = parent::redis_conn();
	}
	function index()
	{
		$this->load->database();
		$video_code = null;
		$is_obs_video = $this->input->get('code');
		if($is_obs_video == 0){//如果是展视的
			$video_code = $this->redis->get('video_zhnashi');
		}else{
			$video_code = $this->redis->get('video_obs');
		}
		if(!$video_code){//如果redis没有就查数据库
			$res = $this->db->query("select video,obs_video from {$this->db->dbprefix('room')} where id='001' limit 1")->row_array();
			if($is_obs_video == 0){//如果是展视的
				$video_code = $res['video'];
			}else{
				$video_code = $res['obs_video'];
			}
		}

		$host = $_SERVER['HTTP_HOST'];
		$source = '';
		$source_info = $this->db->query("select source from {$this->db->dbprefix('source')} where host = '{$host}' limit 1");
		if($source_info->num_rows() > 0) $source = '_'.$source_info->row()->source;

		$this->out_data['source'] = $source;
		$this->out_data['video_code'] = $video_code;
		$this->out_data['is_obs_video'] = $is_obs_video;
		$this->load->view('zhibo', $this->out_data);
	}

}