<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>在线聊天</title>
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
			<div class="chatLeft">
				<div class="chat01">

					<div class="chat01_title">请在右侧选择您要聊天的对象<input type="text" class="search-user" value="" onkeyup="search()" placeholder="输入你要查找的内容！" style="float: right;margin-top: 5px;"/></div>
					<div class="chat_main">
						<ul id="msg_list" class=""></ul>
					</div>
				</div>
				<div class="chat02">
					<div class="chat02_content">
						<textarea id="msg_content"></textarea>
					</div>
					<div class="chat02_bar">
						<ul>
							<li style="left:10px; top: 12px;" class="set_available"></li>
							<li style="right:111px; top: 12px;"><a href="javascript:" onclick="open_chat_record()">查看聊天记录</a></li>
							<li style="right: 5px; top: 5px;">
								<a href="javascript:;" onclick="send_msg()"><img src="css/chat/send_btn.jpg"></a>
								<input type="hidden" id="cur_visitor_id" data-remark="">
								<input type="hidden" id="cur_visitor_gid">
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="chatRight">
				<div class="chat03">
					<div class="chat03_title">
						<label class="chat03_title_t">游客列表</label>
					</div>
					<div class="visitor_list">
						<ul></ul>
					</div>
				</div>
			</div>
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
	socket.emit('admin_login', {'uid' : uid,});
	socket.emit('get_my_visitor', ID);
});

/*更新在线用户列表*/
socket.on('update_visitor_list', function (data){
	$.post(admin.url+'visitor/get_online_visitor?salt='+Date.parse(new Date()), {user_list:data}, function (data){
		data = $.parseJSON(data);
		var html = '';
		for(var i in data){
			var key = data[i]['id']+'_'+data[i]['gid'];
			VISITOR[key] = data[i];
			if($('.visitor_list_'+key).length == 0){
				html += '<li><a href="javascript:;" class="visitor_list_'+key+'" onclick="open_chat('+data[i]['id']+', '+data[i]['gid']+')">'+data[i]['name']+'</a></li>';
			}
		}
		$('.visitor_list ul').append(html);
	})
})

/*用户离开页面*/
//socket.on('visitor_leave', function (uid){
//	$('.visitor_list_'+uid).parent().remove();
//})

function search(){
    var txt=$(".search-user").val();
    if($.trim(txt)!=""){
		var res = $(".visitor_list ul li").hide().filter(":contains('"+txt+"')").show().css('background','#d26e6e');
    }else{
		$(".visitor_list ul li").show().css('background','#ebebeb');
    }
}


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

/*打开某游客的聊天框*/
function open_chat(id, gid){
	var key = id+'_'+gid;
	$('.visitor_list li a').removeClass('active');
	$('.visitor_list_'+key).removeClass('new').addClass('active');
	$('#msg_list').removeClass().addClass('chat_'+key);
	$('#cur_visitor_id').val(id);
	$('#cur_visitor_gid').val(gid);
	$('#cur_visitor_id').attr('data-remark',VISITOR[key]['remark']);
	var html = '与 '+VISITOR[key]['name']+' 聊天中';
	if(VISITOR[key]['keyword'] == null) VISITOR[key]['keyword'] = '';
	html += '(来源：'+VISITOR[key]['source']+'，关键词：'+VISITOR[key]['keyword'].substring(0,10)+')';
	//搜索游客
	var invalue = $('.search-user').val();
    html += '<input type="text" class="search-user" value="'+invalue+'" onkeyup="search()" placeholder="输入你要查找的内容！" style="float: right;margin-top: 5px;">';

	$('.chat01_title').html(html);

	/*放聊天记录*/
	/* var html = '';
	if(DATA[key] != undefined){
		for(var i in DATA[key]){
			var msg = DATA[key][i];
			html += get_msg_html(msg);
		}
	}
	$('#msg_list').html(html); */
	/*放聊天记录*/
	var html = '';
	if(DATA[key] != undefined){
		for(var i in DATA[key]){
			var msg = DATA[key][i];
			html += get_msg_html(msg);
		}
		$('#msg_list').html(html);
	}else{
		if(DATA[key] == undefined) DATA[key] = [];
		$.get('visitor/minichat_msg/'+id+'/'+gid, function(d){
			d = $.parseJSON(d);
			for(var i in d){
				var msg = d[i];
				html += get_msg_html(msg);
				DATA[key].push(msg);
			}
			$('#msg_list').html(html);
			srcolldown();
		});
	}
	srcolldown();

	get_is_available(VISITOR[key]['name']);//获取游客是否有效
}

function get_is_available(name){
	$.get('http://statistics.wanhuiit.com/statistics/get_is_available?name='+name+'&zhibo_flag=hjjr',function(d){
		//<button class="btn btn-success" id="set_available"></button>
		if(d != ''){
			var str = '当前无效，点击设为有效';
			if(d == 1) str = '当前有效，点击设为无效';
			$('.set_available').html('<a href="javascript:;" id="set_available" onclick="set_is_available('+d+',\''+name+'\')">'+str+'</a>');
		}
	})
}
function set_is_available(is_available,name){
	if(confirm('确认重新设置？')){
		$.get('http://statistics.wanhuiit.com/statistics/set_is_available?name='+name+'&zhibo_flag=hjjr&is_available='+is_available,
		function (){
			//location.href = admin.url+'visitor';
			var valuablehtml = '';
			if(is_available == 0){
				$('.set_available').html('<a href="javascript:;" id="set_available" onclick="set_is_available(1,\''+name+'\')">当前有效，点击设为无效</a>');
			}else{
				$('.set_available').html('<a href="javascript:;" id="set_available" onclick="set_is_available(0,\''+name+'\')">当前无效，点击设为有效</a>');
			}
		})
	}
}

//设置游客备注
function set_remark(){
	var id = $('#cur_visitor_id').val();
	var gid = $('#cur_visitor_gid').val();
	var key = id+'_'+gid;
	if(id == ''){
		alert('请选择要备注的游客');
		return;
	}
	if(gid != 1){
		alert('马甲用户不需要备注');
		return;
	}
	var remark = $('#cur_visitor_id').attr('data-remark');
	var new_remark = prompt('请输入该游客的备注', remark);
	if(new_remark == null) return;
	$.post(admin.url+'visitor/set_remark',
	{id:id, remark:new_remark},
	function (){
		VISITOR[key]['remark'] = new_remark;
		//搜索游客
		var invalue = $('.search-user').val();
		var htmlinput = '<input type="text" class="search-user" value="'+invalue+'" onkeyup="search()" placeholder="输入你要查找的内容！" style="float: right;margin-top: 5px;">';
		$('.chat01_title').html('与 '+VISITOR[key]['name']+' 聊天中(来源：'+VISITOR[key]['source']+'，关键词：'+VISITOR[key]['keyword'].substring(0,10) + htmlinput);
		})
}

/*监听接收新消息*/
socket.on('private_msg', function (msg){
	/*存储到本地DATA变量中*/
	msg = $.parseJSON(msg);
	var key = msg.mid+'_'+msg.gid;
	if(DATA[key] == undefined) DATA[key] = [];
	DATA[key].push(msg);
	/*看看该游客会话窗口是否已经打开*/
	if($('.chat_'+key).length == 0){
		move_top(key);/* 将最新发过来的消息置顶 */
		/*不仅将消息保存起来，还要显示该游客发送消息过来了*/
		if(!$('.visitor_list_'+key).hasClass('new') && !$('.visitor_list_'+key).hasClass('active') && msg.is_visitor != 0) $('.visitor_list_'+key).addClass('new');
	}else{
		var html = get_msg_html(msg);
		$('#msg_list').append(html);
		srcolldown();
	}
	check_new();

})

/**
 * 获取注册成功的发送信息
 */
 socket.on('register_success', function(msg){
	 alert('您有新注册的用户,用户名为：'+msg+"\r\n"+'请及时到会员列表刷新查看');
 })

/*发送消息*/
function send_msg(){
	var visitor_id = $('#cur_visitor_id').val();
	var visitor_gid = $('#cur_visitor_gid').val();
	var key = visitor_id+'_'+visitor_gid;
	if(visitor_id == ''){
		alert('请选择聊天对象');
		return;
	}
	var content = $.trim($('#msg_content').val());
	if(content == '') return;

	$.post(admin.url+'visitor/send_msg',{content:content, 'visitor_id':visitor_id, 'gid' : visitor_gid});
	$('#msg_content').focus().val('');
	srcolldown();
}

/*返回组合出消息的html结构*/
function get_msg_html(msg){
	var html = '';
	var click_resource = '';
	html += '<li';
	if(msg.is_visitor == 0) html += ' class="benren" ';
	else click_resource = '<i style="color:red;">&nbsp;&nbsp;&nbsp;来源：'+msg.click_resource+'</i>';
	html += '><div class="chat_right"><div class="chat_right_top"><span><label>'+msg.send_name+'</label>['+msg.time+']</span><div class="say">'+msg.content+click_resource+'</div></div></div></li>';
	return html;
}

/*查看游客聊天记录*/
function open_chat_record(){
	var visitor_id = $('#cur_visitor_id').val();
	var visitor_gid = $('#cur_visitor_gid').val();
	if(visitor_id == ''){
		alert('请选择要查看的游客');
		return;
	}

	parent.open_chat_record(visitor_id, visitor_gid);
}

//将最新的消息置顶
function move_top(key){
	var obj = $('.visitor_list_'+key).parents('li').clone(true);
	$('.visitor_list_'+key).parents('li').remove();
	$(".visitor_list ul").prepend(obj);
}

$('#msg_content').focus(function (){
	check_new();
});
$('.visitor_list').click(function(){
	check_new();
});

/* 检查是否有未读消息，有就提示 */
function check_new(){
	var is_new = $('.visitor_list ul li a').hasClass('new');
	if(is_new == false){//如果没有系消息就取消提示
		parent.cancel_tip();
		if(typeof(parent.parent.cancel_tips) == "function") parent.parent.cancel_tips();
	}else{
		parent.tip_new_msg();
		if(typeof(parent.parent.cancel_tips) == "function") parent.parent.tips_new_msg();
	}
}

function is_undefined(str){
	if(str == undefined) return '';
	return str;
}


</script>

</body>
</html>
