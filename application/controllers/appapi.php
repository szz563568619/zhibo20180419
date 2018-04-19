<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class appapi extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function get_socket_info()
	{
		$socket = $this->config->item('socket');
		$socket['url'] = 'http://'.$socket['url'].':'.$socket['send_port'];
		echo json_encode($socket);
	}
	
}