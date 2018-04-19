<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class elements extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		show_404();
	}

	/*先将图片上传到temp文件夹中*/
	function upload_img()
	{
		$result = array('status' => false, 'img' => '', 'msg' => '');
		$allowed = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/bmp', 'image/png');
		if (in_array($_FILES['file']['type'], $allowed))
		{
			if ($_FILES["file"]["error"] === 0)
			{
				$ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
				$img = time().rand(100,999).'.'.$ext;
				if(!file_exists('../upload/temp/')) mkdir('../upload/temp/');
				move_uploaded_file($_FILES["file"]["tmp_name"], '../upload/temp/'.$img);
				$result['status'] = true;
				$result['img'] = $img;
			}
			else
			{
				$result['msg'] = $_FILES["file"]["error"];
			}
		}
		else
		{
			$result['msg'] = '不支持的图片格式';
		}
		echo json_encode($result);
	}

}