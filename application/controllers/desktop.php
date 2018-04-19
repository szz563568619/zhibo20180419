<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class desktop extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		header("content-Type:text/html; charset=UTF-8");
		$shortcut = '<script>location.href="http://' . $_SERVER["HTTP_HOST"] . '/?shoucangjia"</script>';

		// $shortcut = '[InternetShortcut]
		// URL=http://'.$_SERVER["HTTP_HOST"].'/
		// IDList=
		// [{000214A0-0000-0000-C000-000000000046}]
		// Prop3=19,2
		// ';
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename=瑞丰财经.html;');
		echo $shortcut;
	}

	// public function ins_desktop(){
	// 	$time = time();
	// 	$this->redis->zAdd('desktop', $time, $time);

	// }
	
	// public function from_desktop(){
	// 	$time = time();
	// 	$this->redis->zAdd('from_desktop', $time, $time);
	// }
}