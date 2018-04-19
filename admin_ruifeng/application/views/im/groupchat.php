<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>APP群聊</title>
<base href="<?php echo base_url(); ?>">
<link rel="stylesheet" type="text/css" href="css/chat.css" />
<script src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
<!--[if lt IE 7]>
<script src="js/IE7.js" type="text/javascript"></script>
<![endif]-->
<!--[if IE 6]>
<script src="js/iepng.js" type="text/javascript"></script>
<script type="text/javascript">
EvPNG.fix('body, div, ul, img, li, input, a, span ,label');
</script>
<![endif]-->
</head>
<body>

	<div class="content">
		<div class="chatBox">
			<div class="chatLeft" style="width: 799px;">
				<div class="chat01">

					<div class="chat01_title">APP群聊天...</div>
					<div class="chat_main">
						<ul id="msg_list" class=""></ul>
					</div>
					<div class="chat01_title" id="cur_send">
						当前发送给：<span>所有人</span>
					</div>
				</div>
				<div class="chat02">
					<div class="chat02_content">
						<textarea id="msg_content" style="width: 788px;"></textarea>
					</div>
					<div class="chat02_bar">
						<ul>
							<li style="right: 5px; top: 5px;">
								<a href="javascript:;" onclick="send_msg()"><img src="css/chat/send_btn.jpg"></a>
								<input type="hidden" id="private_chat_id">
							</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- <div class="chatRight">
				<div class="chat03">
					<div class="chat03_title">
						<label class="chat03_title_t">客户列表</label>
					</div>
					<div class="visitor_list">
						<ul></ul>
					</div>
				</div>
			</div> -->
			<div style="clear: both;"></div>
		</div>
	</div>

<script src="js/socket.io-1.4.5.js"></script>
<script>
var ID = <?php echo $this->session->userdata("id"); ?>;
var socket = io('http://<?php echo $socket_url; ?>:<?php echo $socket_port; ?>');
var DATA = []; /*保存所有的消息记录*/
var VISITOR = []; /*保存游客的信息*/
var admin = parent.admin;


socket.on('connect', function (){
	var uid = 'admin_'+ID;
	socket.emit('admin_login', {'uid' : uid});
});

get_chat_list();
/*绑定发送消息的快捷键，Enter或者Ctrl+Enter*/
document.onkeydown = function (){
	var e = e || window.event;
	var keyCode = e.keyCode || e.which || e.charCode;
	if( (e.ctrlKey && (e.keyCode == 13)) ||  e.keyCode == 13 ){
		var element = e.srcElement||e.target;
		if( $(element).attr('id') == 'msg_content'){
			send_msg();
		}
	}
}

//聊天信息框滚动条设定在最底部；
function srcolldown(){
	var chat_room_log = $('.chat_main');
	var chat_room_log_ul = $('#msg_list');
	chat_room_log.animate({scrollTop:chat_room_log_ul.outerHeight(true)}, 100);
}


/*监听接收新消息*/
socket.on('groupchat_msg', function (msg){
	/*存储到本地DATA变量中*/
	msg = $.parseJSON(msg);
	var key = msg.mid+'_'+msg.gid;
	if(DATA[key] == undefined) DATA[key] = [];
	DATA[key].push(msg);
	var html = get_msg_html(msg);
	$('#msg_list').append(html);
	srcolldown();
    //提示有新的消息过来
//    if(msg.uid)

})

/*发送消息*/
function send_msg(){
	var to = $('#private_chat_id').val();
	var content = $.trim($('#msg_content').val());
	if(content == '') return;

	$.post(admin.url+'groupchat/send_msg',{content:content, 'to':to});
	$('#msg_content').focus().val('');
	srcolldown();
}

/*返回组合出消息的html结构*/
function get_msg_html(msg){
	var html = '';
	var click_resource = '';
	html += '<li';
	if(msg.uid == ID) html += ' class="benren" ';
	else if(msg.is_member == 1) click_resource = '<i style="color:red;cursor:pointer;" data-url="'+msg.is_member+'" onclick="private_chat(\''+msg.uid+'_'+msg.gid+'\',\''+msg.send_name+'\')">&nbsp;&nbsp;&nbsp;点击回复</i>';
	html += '><div class="chat_right"><div class="chat_right_top"><span><label>'+msg.send_name+'</label>['+msg.time+']</span><div class="say">'+msg.content+click_resource+'</div></div></div></li>';
	return html;
}

function get_chat_list(){
	$.get(admin.url+'groupchat/chat_list', function(d){
		data = $.parseJSON(d);
		for(var i in data){
			var da = data[i];
			var html =get_msg_html(da);
			$('#msg_list').append(html);
		}
		srcolldown();
		
	})
}


function is_undefined(str){
	if(str == undefined) return '';
	return str;
}

function private_chat(uid,name){
	var html = '';
	$('#private_chat_id').val(uid);
	if(uid == 0){
		html = '当前发送给：<span>所有人</span>';
	}else{
		html = '当前发送给：<span>'+name+'</span><button style="color: red;margin-left: 20px;cursor:pointer;" onclick="private_chat(\'\',0)">点击切换回群聊</button>';
	}
	$('#cur_send').html(html);
}

</script>

</body>
</html>
