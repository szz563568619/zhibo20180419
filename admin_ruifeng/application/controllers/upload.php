<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class upload extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('base');
	}

	public function index()
	{
		show_404();
	}

	function upload_img()
	{
		$site_url = parent::get_site_url();
		$result = array('status' => false, 'img' => '', 'msg' => '');
		$allowed = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/bmp', 'image/png');
		if (in_array($_FILES['file']['type'], $allowed))
		{
			if ($_FILES["file"]["error"] === 0)
			{
				$ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
				$img = time().rand(100,999).'.'.$ext;
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

	function upload_album()
	{
		$site_url = parent::get_site_url();
		$result = array('status' => false, 'msg' => '');
		$allowed = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/bmp', 'image/png');
		if (in_array($_FILES['file']['type'], $allowed))
		{
			if ($_FILES["file"]["error"] === 0)
			{
				$ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
				$img = time().rand(100,999).'.'.$ext;
				move_uploaded_file($_FILES["file"]["tmp_name"], '../upload/img/'.$img);
				$result['status'] = true;
				$result['img'] = $img;

				$this->db->insert('cate_album', array('cate_id' => (int)$_POST['cate_id'], 'img' => $img, 'text' => $_POST['text11']));
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

	function upload_file()
	{
		$site_url = parent::get_site_url();
		$result = array('status' => false, 'file' => '', 'msg' => '');

		if ($_FILES["file"]["error"] === 0)
		{
			$ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
			$file = time().rand(100,999).'.'.$ext;
			move_uploaded_file($_FILES["file"]["tmp_name"], '../upload/temp/'.$file);
			$result['status'] = true;
			$result['file'] = $file;
		}
		else
		{
			$result['msg'] = $_FILES["file"]["error"];
		}

		echo json_encode($result);
	}
	
	/* 上传pdf */
	function upload_pdf()
	{
		$result = array('status' => false, 'file' => '', 'msg' => '');
		if ($_FILES["file"]["error"] === 0)
		{
			$ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
			if($ext != 'pdf'){
				$result['msg'] = '只支持pdf格式的文件！';
			}else{
				$file = time().rand(100,999);
				move_uploaded_file($_FILES["file"]["tmp_name"], '../upload/zhanfa/'.$file.'.'.$ext);
				$result['status'] = true;
				$result['file'] = $file;
			}
			
		}
		else
		{
			$result['msg'] = $_FILES["file"]["error"];
		}

		echo json_encode($result);
	}

}