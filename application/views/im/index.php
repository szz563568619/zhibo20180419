<!DOCTYPE html>
<html>
<head>
<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1' />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<meta charset="UTF-8" />
<meta name="renderer" content="webkit" /><!--强制使用急速模式-->
<meta content="always" name="referrer">
<title>在线聊天</title>
<base target="_self" href="<?php echo base_url(); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo $tpl; ?>css/webchat.css" />
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
<!--[if lt IE 7]>
<script src="<?php echo $tpl; ?>js/IE7.js" type="text/javascript"></script>
<![endif]-->
<!--[if IE 6]>
<script src="<?php echo $tpl; ?>js/iepng.js" type="text/javascript"></script>
<script type="text/javascript">
EvPNG.fix('body, div, ul, img, li, input, a, span ,label'); 
</script>
<![endif]-->
</head>
<body style="border-radius: 10px;">
	<div class="content">
		<div class="chatBox">
			<div class="chatLeft">
				<div class="chat01">
					<div class="chat01_title">
						<span>请在左侧选择您要聊天的对象</span>
						<a class="close_btn" href="javascript:;" onclick="parent.close_webchat();"></a>
					</div>
					<div class="chat_area">
						<div class="chat_main">
							<ul id="msg_list"></ul>
						</div>
						<!-- <div class="slide"></div> -->
					</div>
				</div>
				<div class="chat02">
					<div class="chat02_content">
						<textarea id="msg_content" placeholder="点击此处，输入问题..."></textarea><button class="send_btn" onclick="send_msg()">发送</button>
						<input type="hidden" id="cur_service_id">
					</div>
					<!-- <div class="chat02_bar">
						<ul>
							<li style="left: 20px; top: 10px; padding-left: 30px;"></li>
							<li style="right: 5px; top: 5px;"><a href="javascript:;" onclick="send_msg()"><img src="<?php echo $tpl; ?>images/webchat/send_btn.jpg"></a></li>
						</ul>
					</div> -->
				</div>
			</div>
			<div class="chatRight" style="width:197px;">
				<div class="chat03">
					<div class="chat03_title">
						<label class="chat03_title_t">客服列表</label>
					</div>
					<div class="service_list">
						<ul>
							<?php foreach($service_list as $v): 
								$img_src = $tpl."images/webchat/tou".$v['sex'].".jpg";
								if($v['headimg'] AND file_exists('upload/headimg/'.$v['headimg'])) $img_src = base_url().'upload/headimg/'.$v['headimg'];
							?>
							<li><a href="javascript:;" class="service_list_<?php echo $v['id']; ?>" id="<?php echo $v['id']; ?>" data-name="<?php echo $v['nick']; ?>" onclick="open_chat(<?php echo $v['id']; ?>)"><img src="<?php echo $img_src; ?>" alt="" width="25" height="25" style="margin:0 10px;border-radius:50%;float:left"/><?php echo $v['nick']; ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
<div id="ie-tips"></div>
<script>
var one_aid_for_m = <?php if(count($service_list) == 1 AND $this->session->userdata('gid') != 1){ echo 1; }else{echo 0;} ?>;
var DATA = []; /*保存所有的消息记录*/
var SERVICE = <?php echo json_encode($service_list); ?>; /*保存客服的信息*/
var base = parent.base;
var socket = parent.socket;

$(function (){
	srcolldown();
	//setInterval('tips_new_msgs()', 3000*60);//如果有未读消息循环提示
	if(one_aid_for_m){
		$('.chatRight').hide();
		$('.chatLeft').css('width', '831px');
		$('.chat_main ul').css('width', '660px');
		$('.chat02_content textarea, .chat02_content textarea').css('width', '669px');
		var one_aid_for_m_id = $('.service_list ul li a').attr('id');
		open_chat(one_aid_for_m_id);	
	}
	parent.show_kefu_list(SERVICE);//父页面显示客服列表
})

/*把我在线的情况推送给我的专属客服*/
var cid = '';
<?php if($is_send_my_kefu): ?>
cid = <?php echo $cid; ?>;
parent.CID = cid;
<?php endif; ?>
socket.emit('send_my_cid', cid);

/*打开某客服的聊天框*/
function open_chat(id){
	$('.service_list li a').removeClass('active');
	$('.service_list_'+id).removeClass('new').addClass('active');
	$('#msg_list').removeClass().addClass('chat_'+id);
	$('#cur_service_id').val(id);
	var html = '与 '+$('.service_list_'+id).data('name')+' 聊天中';
	$('.chat01_title span').html(html);

	/*放聊天记录*/
	var html = '';
	if(DATA[id] != undefined){
		for(var i in DATA[id]){
			var msg = DATA[id][i];
			html += get_msg_html(msg);
		}
	}
	$('#msg_list').html(html);
	srcolldown();
}

/*发送消息*/
function send_msg(){
	var cur_service_id = $('#cur_service_id').val();
	if(cur_service_id == ''){
		alert('请在左侧选择您要联系的客服');
		return;
	}

	var msg = $.trim($('#msg_content').val());
	if(msg == '') return;

	$.post(base.url+'im/send_msg',{content:msg, service_id:cur_service_id, click_resource:parent.CLICK_RESOURCE});
	$('#msg_content').focus().val('');
}

/*绑定发送消息的快捷键，Enter或者Ctrl+Enter*/
document.onkeydown = function (e){
	var e = e || window.event;
	var keyCode = e.keyCode || e.which || e.charCode;
	if( (e.ctrlKey && (e.keyCode === 13)) ||  e.keyCode === 13 ){
		var element = e.srcElement||e.target;
		if( $(element).attr('id') == 'msg_content'){
			send_msg();
		}
	}
}

/*处理获取到的消息数据*/
socket.on('private_msg', function (msg){
	msg = $.parseJSON(msg);
	var html = '';

	/*将获取到的消息暂时保存到DATA中*/
	var key = msg.aid;
	if(DATA[key] == undefined) DATA[key] = [];
	DATA[key].push(msg);

	/* 如果客服列表中没有，就新加上去 */
	if($('.service_list_'+key).length == 0){
		$.ajax({url:base.url+'im/get_kefu/'+key,async:false,success:function(data){
			data = $.parseJSON(data);
			SERVICE[key] = data;
		}});
		var li_html = '<li><a href="javascript:;" class="service_list_'+key+'" data-name="'+msg.send_name+'" onclick="open_chat('+key+')"><img src="<?php echo base_url(); ?>upload/headimg/'+SERVICE[key].headimg+'" alt="" width="25" height="25" style="margin:0 10px;border-radius:50%;float:left"/>'+msg.send_name+'</a></li>';
		$('.service_list ul').append(li_html);
		
		parent.show_kefu_list(SERVICE);//父页面显示客服列表
	}

	/*看看该游客会话窗口是否已经打开*/
	if($('.chat_'+key).length == 0){
		move_top(key);/* 将最新发过来的消息置顶 */
		/*不仅将消息保存起来，还要显示该客服发送消息过来了*/
		if(!$('.service_list_'+key).hasClass('new') && !$('.service_list_'+key).hasClass('active')) $('.service_list_'+key).addClass('new');
	}else{
		html = get_msg_html(msg);
	}
	$('#msg_list').append(html);
	parent.qq_start();
	srcolldown();
	
	//播放提示音
	//if(msg.is_visitor == 0) play_sound();
});

/*返回组合出消息的html结构*/
function get_msg_html(msg){
	var service_id = msg.aid;
	var qq = SERVICE[service_id].qq;
	var qq_html = '';
	if(msg.send_name != parent.USERNAME && qq != '') qq_html = '<a href="tencent://message/?uin='+qq+'&Menu=yes" style="margin-left:10px;text-decoration:none;color:red;"><img src="<?php echo $tpl; ?>images/webchat/qq.png" align="absmiddle"></a>';

	var html = '';
	html += '<li';
	if(msg.is_visitor == 1 || msg.is_visitor == undefined) html += ' class="benren" ';
	html += '><div class="chat_right"><div class="chat_right_top"><span><label>'+msg.send_name+'</label>['+msg.time+']</span><div class="say">'+msg.content+qq_html+'</div></div></div></li>';
	return html;
}

//聊天信息框滚动条设定在最底部；
function srcolldown(){
	var chat_room_log = $('.chat_main');
	var chat_room_log_ul = $('#msg_list');
	chat_room_log.animate({scrollTop:chat_room_log_ul.outerHeight(true)}, 1000);
}

//将最新的消息置顶
function move_top(key){
	var obj = $('.service_list_'+key).parents('li').clone(true); 
	$('.service_list_'+key).parents('li').remove(); 
	$(".service_list ul").prepend(obj); 
}


//提示音函数
function play_sound(){
	var res = '';
	if(navigator.userAgent.indexOf("Chrome") > -1){//如果是Chrome
		res = '<audio src="<?php echo base_url().$tpl; ?>images/tips.mp3" id="tips" autoplay="autoplay" hidden="true"></audio>';
	}else if(navigator.userAgent.indexOf("Firefox")!=-1){ //如果是Firefox： 
		res = '<embed src="<?php echo base_url().$tpl; ?>images/tips.mp3" id="tips" hidden="true" loop="false" mastersound></embed> ';
	}else if(navigator.appName.indexOf("Microsoft Internet Explorer")!=-1 && document.all){ //如果是IE(6,7,8): 
		res = '<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95"><param name="AutoStart" value="1" /><param id="tips" name="Src" value="<?php echo base_url().$tpl; ?>images/tips.mp3" /></object> ';
	}else if(navigator.appName.indexOf("Opera")!=-1){ //如果是Oprea： 
		res = '<embed id="tips" src="<?php echo base_url().$tpl; ?>images/tips.mp3" loop="false"></embed> ';
	}else{
		res = '<embed id="tips" src="<?php echo base_url().$tpl; ?>images/tips.mp3" hidden="true" loop="false" mastersound></embed> ';
	}
	$('#ie-tips').html('');
	$('#ie-tips').html(res);
	
}
//如果有未读消息循环提示
function tips_new_msgs(){
	$('.service_list ul li').each(function(){
		var is_new = $(this).find('a').eq(0).hasClass('new');
		if(is_new){
			play_sound();
		}
	});
}
	

</script>

</body>
</html>