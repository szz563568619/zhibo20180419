<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CCH
 */

/**
 * [my_echo description]
 * @param  [type] &$variable    [description]
 * @param  string $default_echo [description]
 * @return [type]               [description]
 */
function my_echo(&$variable, $default_echo = '')
{
	if(isset($variable) and $variable != '') return $variable;
	else return $default_echo;
}


/**
 * 获取访客的真实IP
 * @return string 返回真实IP
 */
function get_ip()
{
	$ip = $_SERVER['REMOTE_ADDR'];
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '')
	{
		$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$ip = $ip[0];
	}
	return $ip;
}

/**
 * @param $str
 * @return int
 * 验证手机合法性
 */
function is_shouji($str)
{
    $pattern = "/1[34578]\d{9}/";
    preg_match_all($pattern,$str,$arr);
	foreach($arr[0] as $v){
		$str = str_replace($v,"**",$str);
	}
	return $str;
}

/**
 * @param $str
 * @return int
 * 验证qq号
 */
function is_qq($str){
    $pattern = "/[0-9]\d{5,11}/";
    preg_match_all($pattern,$str,$arr);
	foreach($arr[0] as $v){
		$str = str_replace($v,"**",$str);
	}
	return $str;
}


/**
*获取域名下对应的信息
*
*/
function get_domain_info($flag = false){
	//默认情况下显示当前域名下的信息
	$domain = $_SERVER['SERVER_NAME'];
	if($flag){
		$domain = '默认';
	}
	$CI = &get_instance();
	$CI->db->cache_on();
	$res = $CI->db->query("select * from {$CI->db->dbprefix('room_extra')} where 1=1 and domain = '{$domain}'")->row_array();
	if(!$res){
		$res = $CI->db->query("select * from {$CI->db->dbprefix('room_extra')} where 1=1 and domain = '默认'")->row_array();
	}
	if(empty($res['info'])){
		$res['info'] = $CI->db->query("select info from {$CI->db->dbprefix('room_extra')} where domain = '默认'")->row()->info;
	}
	$CI->db->cache_off();
	return $res;
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
    // curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);
}

/* 获取当前讲课老师 */
function get_cur_teacher()
{
	$CI = &get_instance();
	$curriculum = $CI->db->query("select * from {$CI->db->dbprefix('curriculum')}")->result_array();//获取课程
	//获取老师
	$teacher = $CI->db->query("select id,name from {$CI->db->dbprefix('teacher')}")->result_array();
	$tids = array_column($teacher, 'id');
	$names = array_column($teacher, 'name');
	$teacher = array_combine($tids, $names);
	//返回结果
	$result = array('curriculum' => $curriculum, 'teacher' => $teacher);
	echo json_encode($result);
	
}

/* 显示在线人数 */
function get_online()
{
	$CI = &get_instance();
	$redis = redis_conn();
	$initpeo = $redis->get('initpeo');
	if(!$initpeo){
		$initpeo = $CI->db->query("select initpeo from {$CI->db->dbprefix('room')} where id = '001' limit 1")->row()->initpeo;
		$redis->set('initpeo', $initpeo);
	}
	
	echo $initpeo;
}

function get0peo()
{
	$redis = redis_conn();
	$data = $redis->zRevRange('innerpeo_001', 0, 9);
	return $data;
}

/* 连接redis */
function redis_conn()
{
	$CI = &get_instance();
	$redis = new Redis();
	$redis_conf = $CI->config->item('redis');
	$redis->connect($redis_conf['host'], $redis_conf['port']);
	if($redis_conf['auth'] != '') $redis->auth($redis_conf['auth']);
	$redis->select($redis_conf['db']);
	return $redis;

}

//去除恶意链接
function content_nofollow($content){
	$oldcontent = $content;
	//注意，这里把上面的正则表达式中的单引号用反斜杠转义了，不然没法放在字符串里
	$regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|[a-z0-9\-]{0,10}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

	preg_match_all($regex, $content, $matches, PREG_PATTERN_ORDER);  //true
	foreach($matches[0] as $v){
		if(!preg_match('/.*\.(gif|jpg|png)/', $v) AND !preg_match('/tencent:\/\/message/', $v)){
			$content = str_replace($v, '**', $content);
		}
	}
	return $content;
}