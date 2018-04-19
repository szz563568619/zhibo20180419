<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class upload extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		show_404();
	}

	function upload_img()
	{
		if($_SERVER['REQUEST_METHOD' ] !== 'POST') show_404();

		$result = array('status' => false, 'msg' => '');

		$allowed = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/bmp', 'image/png', 'image/jpg', 'image/x-png');
		if (in_array(strtolower($_FILES['upload_img']['type']), $allowed))
		{
			if ($_FILES["upload_img"]["error"] === 0)
			{
				if($_FILES["upload_img"]['size'] > 200*1024){
					$result['msg'] = '图片限制200k以内！';
					echo json_encode($result);
					exit;
				}
				$ext = pathinfo($_FILES["upload_img"]["name"], PATHINFO_EXTENSION);
				$code = '';
				for($i=1;$i<=10;$i++){
					$code .= chr(rand(97,122));
				}
				$img = $code.rand(100, 999).'.'.$ext;
				$floder = 'upload/chat/'.date("Y-m-d").'/';
				if(!file_exists($floder)) mkdir($floder, 0777, true);

				move_uploaded_file($_FILES["upload_img"]["tmp_name"], $floder.$img);
				
				
				//curl上传图片
				$curl = curl_init();
				//$data = array('img'=>'@'.dirname(__FILE__).'/'.$floder.$img);
				$data = array('img'=>new CurlFile(str_replace('\\', '/', FCPATH).$floder.$img));//PHP 5.5   
				//curl_setopt($curl, CURLOPT_URL, "http://www.severupload.com/upload.php");
				curl_setopt($curl, CURLOPT_URL, "http://img.huijinyjs.com/upload.php");
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				$res = curl_exec($curl);
				curl_close($curl);
				
				$result = json_decode($res);
				
				//删除文件
				unlink($floder.$img);
			}
			else
			{
				$result['msg'] = $_FILES["upload_img"]["error"];
			}
		}
		else
		{
			$result['msg'] = '不支持的图片格式';
		}

		echo json_encode($result);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */