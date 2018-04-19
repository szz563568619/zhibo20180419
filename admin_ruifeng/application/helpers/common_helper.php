<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CCH
 */

/**
* Returns variable or default value
* @access	public
* @param	mixed
* @return	mixed
*/
if(!function_exists('my_echo'))
{
	function my_echo(&$variable, $default_echo = '')
	{
		if(isset($variable) and $variable != '') return $variable;
		else return $default_echo;
	}
}

/**
 * 向websocket服务器发送数据
 * @param  [Array] $data [要发送的数据，包括type，to，content]
 */
function send_websocket($data)
{
    // 推送的url地址，上线时改成自己的服务器地址
    $CI = &get_instance();
    $socket_conf = $CI->config->item('socket');
    $push_api_url = "http://127.0.0.1:".$socket_conf['send_port'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $push_api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect: "));
    //curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);
}