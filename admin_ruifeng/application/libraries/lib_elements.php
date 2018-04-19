<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class lib_elements {

	/**
	 * 用于判断是否已经调用过文件上传组件，以免重复调用js
	 */
	private $filed = false;
	private $CI;
	public function __construct()
	{
		$this->CI = & get_instance();
	}

	function get_file_element($info)
	{
		$info['filed'] = $this->filed;
		if(!isset($info['img'])) $info['img'] = '';
		$this->filed = true;
		return $this->CI->load->view('elements/file', $info, true);
	}

	/**
	 * 移动temp文件夹中的图片到指定文件夹
	 * @param  [string] $new_img [新图片路径及名称]
	 * @return [boolean]  [是否有上传或上传是否成功]
	 */
	function move_img($old_img, $new_img = '')
	{
		//echo $new_img;exit;
		/*若给的新文件名为文件夹，则将图片按原名移动到该文件夹下*/
		$len = strrpos($new_img, '/');
		$new_img = substr($new_img, 0, $len);
		
		if(!file_exists($new_img)) mkdir($new_img);
		$new_img = rtrim($new_img, '/').'/'.$old_img;

		if($new_img == '' OR !file_exists('../upload/temp/'.$old_img))
		{
			/*若无文件可移，返回*/
			return false;
		}

		return rename('../upload/temp/'.$old_img, $new_img);
	}
	
}