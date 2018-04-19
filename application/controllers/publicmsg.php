<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class publicmsg extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->send_captcha();
	}

	function send_captcha()
	{
		$phone = $this->input->post('phone');
		$result = array('status' => false, 'msg' => '', 'code' => -1);
		$verify_result = $this->_verify_phone($phone);
		if( ! $verify_result['status'])
		{
			$result = $verify_result;
		}
		else
		{
			$target = "http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/sendBatchMessage";
			$this->load->helper('string');
			$captcha = random_string('numeric', 4);
			$post_data = "account=sdk_yunjie&password=awfwfnyitx&destmobile={$phone}&msgText=尊敬的用户，您的验证码是：{$captcha}。请不要把验证码泄露给其他人。【瑞丰财经】";
			$result['is_send'] = true; /*仅表示程序运行到发送验证码步骤，至于是否发送成功，不知道。*/
			$gets =  $this->_post($post_data, $target);
			if($gets > 0)
			{
				$result['status'] = true;
				$this->session->set_userdata('phone', $phone);
				$this->session->set_userdata('publicmsg', $captcha);
			}
			else
			{
				$result['msg'] = '短信发送失败，请稍候重试';
			}
		}
		echo json_encode($result);
	}
	
	function verify_captcha($phone, $captcha)
	{
		$result = array('status' => false, 'msg' => '');
		if( ! ( $phone == $this->session->userdata('phone') AND $captcha == $this->session->userdata('captcha') ) )
		{
			$result['msg'] = '手机号和验证码不对应';
		}
		else
		{
			$result['status'] =  true;
		}
		return $result;
	}

	private function _verify_phone($phone)
	{
		$result = array('status' => false, 'msg' => '');
		if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){  
			$result['status'] = true;
		}else{  
			$result['msg'] = '不合法的手机号';
		}  
		return $result;
	}

	private function _post($curlPost,$url)
	{
		$header [] = "content-type: application/x-www-form-urlencoded;charset=UTF-8";
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST" );
	    curl_setopt($curl, CURLOPT_NOBODY, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $header );
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
	    $return_str = curl_exec($curl);
	    curl_close($curl);
	    return $return_str;
	}
}