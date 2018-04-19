<?php
ini_set('memory_limit', '1024M');
use Workerman\Worker;
use Workerman\Lib\Timer;
use PHPSocketIO\SocketIO;

include __DIR__ . '/vendor/autoload.php';

$online_uid_list = array();/*保存在线用户的uid*/

// PHPSocketIO服务
$sender_io = new SocketIO(1061);
$sender_io->count = 8;

// 客户端发起连接事件时，设置连接socket的各种事件回调
$sender_io->on('connection', function($socket){

	/*当客户端发来登录事件时触发*/
	$socket->on('login', function ($data, $data2 = '') use($socket){
		if(isset($socket->uid)) return; /*已经登录过了*/

		if(isset($data['uid'])) $uid = $data['uid'];
		else $uid = $data.'_'.$data2;
		
		/*更新在线数据*/
		global $online_uid_list;
		$uid = (string)$uid;
		$online_uid_list[$uid] = array();
		
		/*将这个连接加入到uid分组，方便针对uid推送数据*/
		$socket->join($uid);
		$socket->uid = $uid;
		$socket->join('user');
	});

	/*当后台管理员账号发来登录事件时触发*/
	$socket->on('admin_login', function ($data) use($socket){
		if(isset($socket->uid)) return; /*已经登录过了*/

		$uid = $data['uid'];
		/*将这个连接加入到uid分组，方便针对uid推送数据*/
		$socket->join($uid);
		$socket->uid = $uid;
		$socket->join('admin');
	});

	/*APP用户发送所属老师ID，以加入该老师组 --- APP ---*/
	$socket->on('send_my_tid', function ($tid) use($socket){
		global $online_uid_list, $sender_io;
		$online_uid_list[$socket->uid]['tid'] = $tid;
		if($tid != '')
		{
			$socket->join('group_'.$tid);
		}
	});

	/*当前台发来设备(pc-0,app-1,wap-2)事件时触发*/
	$socket->on('where_i_from', function ($from) use($socket){
		global $online_uid_list, $sender_io;
		$online_uid_list[$socket->uid]['from'] = $from;
	});

	/*客户主动推送他的客服ID,并且告诉后台的客服我上线了*/
	$socket->on('send_my_cid', function ($cid) use($socket){
		global $online_uid_list, $sender_io;
		$online_uid_list[$socket->uid]['cid'] = $cid;
		if($cid != '')
		{
			$socket->join('group_'.$cid); /*加入该客服分组*/
			$sender_io->to('admin_'.$cid)->emit('update_visitor_list', $socket->uid);
		}
		else
		{
			$sender_io->to('admin')->emit('update_visitor_list', $socket->uid);
		}
	});
	
	/*客服主动发送id获取他的所有客户*/
	$socket->on('get_my_visitor', function ($cid) use($socket){
		global $online_uid_list, $sender_io;
		$result = array();
		foreach($online_uid_list as $k => $v)
		{
			if( (isset($v['cid']) AND ($v['cid'] == $cid)) OR !isset($v['cid']) OR $v['cid'] == '') $result[] = $k;
		}
		$sender_io->to('admin_'.$cid)->emit('update_visitor_list', join(',', $result));
	});

	/*获取所有的用户列表*/
	$socket->on('get_all_visitor', function (){
		global $online_uid_list, $sender_io;
		$sender_io->to('admin')->emit('get_all_visitor', json_encode($online_uid_list));
	});
	
	/*获取在线用户数量*/
	$socket->on('get_visitor_count', function (){
		global $online_uid_list, $sender_io;
		$sender_io->to('user')->emit('get_visitor_count', count($online_uid_list));
	});

	/*当客户端断开连接时触发（一般是关闭网页或者跳转刷新导致）*/
	$socket->on('disconnect', function () use($socket){
		if(!isset($socket->uid)) return;
		global $online_uid_list;

		/*如果是用户断开连接的话，需要告诉后台客服*/
		$info = explode('_', $socket->uid);
		if($info[0] != 'admin' AND isset($online_uid_list[$socket->uid]['cid']))
		{
			global $sender_io;
			$sender_io->to('admin_'.$online_uid_list[$socket->uid]['cid'])->emit('visitor_leave', $socket->uid);
		}

		/*删除他的在线数据*/
		unset($online_uid_list[$socket->uid]);
	});

});


$sender_io->on('workerStart', function (){

	/*当$sender_io启动后监听一个http端口，通过这个端口可以给任意uid或者所有uid推送数据，是专为CI推送消息使用的*/
	$inner_http_worker = new Worker('http://0.0.0.0:1062'); /*监听一个http端口*/
	/*当CI那边发来数据时触发*/
	$inner_http_worker->onMessage = function($http_connection, $data){
		$_POST = $_POST ? $_POST : $_GET;
		/*推送数据的url格式 type=publish&to=uid&content=xxxx*/
		global $sender_io;
		$to = isset($_POST['to']) ? $_POST['to'] : false;
		/*有指定uid则向uid所在socket组发送数据*/
		if($to)
		{
			$to = explode('|', $to);
			foreach($to as $v)
			{
				$sender_io->to($v)->emit($_POST['type'], $_POST['content']);
			}
			return $http_connection->send('ok');
		}
		else
		{
            if(isset($_POST['type'])){
                $sender_io->emit($_POST['type'], $_POST['content']);
                return $http_connection->send('ok');
            }
		}
		return $http_connection->send('fail');
	};
	$inner_http_worker->listen(); /*开始监听*/

	/*添加一个定时器，前台管理员用来自动发送聊天信息*/
	Timer::add(300, function(){
		$res = my_curl('http://local.com/ruifeng/caichuanhai/admin_send_msg');
	});

});

/*------------------其他函数功能----------------------*/
function my_curl($url)
{
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect: "));
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($ch, CURLOPT_TIMEOUT, 1 );
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

if(!defined('GLOBAL_START'))
{
	Worker::runAll();
}