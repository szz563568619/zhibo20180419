var CLICK_RESOURCE = '移动端页面';
$(window).resize(function(){win_resize()});

var socket = io('http://'+SOCKET_URL+':'+SOCKET_PORT);
/*连接服务端*/
socket.on('connect', function (){
	var uid = MID+'_'+GID;
	socket.emit('login', {'uid' : uid,});
	socket.emit('where_i_from', 2);
});

$(function(){

	win_resize();
	//poppop_login_inner_close 关闭弹窗
	$('.pop_login_inner_close').click(function(){
		$('.pop_login').hide();
		$('#register').hide();
	})
	$('.enter_btns_login').click(function(){
		$('.pop_login').show();
	})
	$('.register').click(function(){
		$('#register').show();
	})
	var chatroom_ul_height = $('.chatroom_ul').height();
	$('.chatroom').scrollTop(chatroom_ul_height);

    setTimeout(srcolldown,500);

    $('.qq_click').click(function (){im_start();})
    $('.im_click').click(function (){qq_start();})
    webchat_init();
    
})

function im_iframe_load(){
	var im_iframe_dom = $('#im_iframe')[0].contentDocument;
    $('.say a', im_iframe_dom).click(function (){
    	var url = $(this).attr('href');
    	var parse_url = parseURL(url);
    	var qq = parse_url.params.uin;
    	if(qq != '') location.href = "mqq://im/chat?chat_type=wpa&uin="+qq+"&version=1&src_type=web";
    	return false;
    })
    setTimeout(im_iframe_load, 3000);
}

function qq_start(){
	start_webchat();
}

function im_start(){
	var qq_arr = $('#tencent').html().split(',');
	var n = Math.floor(Math.random() * qq_arr.length);
	location.href = "mqq://im/chat?chat_type=wpa&uin="+qq_arr[n]+"&version=1&src_type=web";
}

function webchat_init(){
	var screenWidth = $('body').outerWidth();
	var screenHeight = document.body.offsetHeight;
	// var left = (screenWidth-800)/2;
	// var top = (screenHeight-534)/2;
	var src = base.url+'im/';
	// $('body').append('<div id="webchat" style="position:absolute;top:'+top+'px;left:'+left+'px;width:827px;height:534px;display:none;z-index:1;border: 2px solid #484343;border-radius: 12px;box-shadow: 2px 4px 55px #000;">'
	$('body').append('<div id="webchat" style="position:absolute;top:15%;right:0;width:100%;height:70%;display:none;z-index:999999;border: 2px solid #484343;border-radius: 12px;box-shadow: 2px 4px 55px #000;">'+'<iframe id="im_iframe" src="'+src+'" style="width:100%;height:100%;" frameborder="0" onload="im_iframe_load()"></iframe></div>');
}

function start_webchat(){
	if($('#webchat').length == 0){
		webchat_init();
	}
	//close_dialog();
	if($('#webchat').css('display') == 'none'){
		//如果之前是未打开的
		$("#webchat iframe").contents().find(".new").eq(0).click();
	}
	$('#webchat').css('display', 'block');

	var dom = $('#move');

	dom.on("mousedown",function(evt){
		this.style.cursor = "move";
		var event = evt || window.event;

		var pointx = event.pageX - $("#webchat").offset().left;
		var pointy =event.pageY - $("#webchat").offset().top;

		dom.on("mousemove",function(evt1){//移动事件

			var event1 = evt1 || window.event;

			if(event1.which != 1) return;

			var movex = event1.pageX - pointx;//获取两次的移动长度也可用前后两次的坐标相减
			var movey = event1.pageY - pointy;//movex 和movey 为鼠标移动之后的位置

			$("#webchat").css({
				"left" : movex + "px",
				"top" : movey + "px"
			});
		});
	})
	dom.on("mouseup", function () {

			dom.off("mousemove");
			dom.off("mouseup");
		this.style.cursor = "default";
			})
}

function close_webchat(){
	$('#webchat').css('display', 'none');
}

var win_resize = function (){
	// chatroom_height
	var screenHeight = document.documentElement.clientHeight;
	var video_height = screenHeight*0.5;
	$('.video').outerHeight(video_height);
	var enter_btns_height = $('.enter_btns').outerHeight();
	var input_tool_height =$('.input_tool').outerHeight();
	var chatroom_height = screenHeight - video_height - enter_btns_height -input_tool_height;
	$('.chatroom').css('min-height',95);
	$('.chatroom').outerHeight(chatroom_height);
}

/*获取消息数据*/
socket.on('public_msg', function (data){
	var html = '';
	var msg = $.parseJSON(data);
	html += '<li class="chatroom_li" id="chat_'+msg.score+'"><div class="chatroom_user"><span class="chatroom_user_icon"><img src="'+base.tpl+'images/level/level'+msg['gid']+'.gif" class="chatroom_user_icon_img"></span><span class="chatroom_user_name">'+msg['name']+'</span><span class="chatroom_time">'+msg['time']+'</span></div><div class="chatroom_message"><p class="chatroom_message_words chat_data_'+msg['gid']+'">'+msg['content']+'</p></div></li>';
	$('#msg_list').append(html);
	srcolldown();
})

/*删除公屏聊天信息*/
socket.on('del_public_msg', function (data){
	$('#chat_'+data).remove();
})

/*使用哪种直播代码*/
socket.on('is_obs_video', function (data){
	//location.reload();
	var cur_code = $('#zhibo').attr('src');
	if(cur_code != 'zhibo?code='+data) $('#zhibo').attr('src', 'zhibo?code='+data);
})

function send_msg(){
	var msg = $.trim($('#msg_content').text());
	if(msg == '') return;

	$.post(base.url+'chat/send_msg',{content:msg, rid:RID, is_mobile:2},function (){})
	$('#msg_content').focus().html('');
}

//滚动条设定在最底部；
function srcolldown(){
	var chat_room_log = $('.chatroom');
	var chat_room_log_ul = $('.chatroom ul');
	chat_room_log.animate({scrollTop:chat_room_log_ul.outerHeight(true)}, 1000);
}

/* 登陆 */
$('#login_form').submit(function (){
	$.post(base.url+'user/login',
	$(this).serialize(),
	function (result){
		result = $.parseJSON(result);
		if(result.status){
			location.reload();
		}else{
			alert(result.msg);
		}
	})
	return false;
})

/* 注册 */
var querystring = UrlSearch(window.location.href);
$('input[name=querystring]').val(querystring);
$('input[name=allurl]').val(window.location.href);

function UrlSearch(url) 
{
   var num=url.indexOf("?") 
   if(num == -1) return '';
   url=url.substr(num+1); //取得所有参数   stringvar.substr(start [, length ]
	return url;
} 
//获取短信验证
function get_publicmsg(){
	var phone = $('#register_form input[name=phone]').val();
	var is_phone = isphone(phone);
	if(is_phone){
		set_time();
		$.post(base.url+'publicmsg',
		{phone:phone},
		function (result){
			result = $.parseJSON(result);
			if(!result.status){
				alert(result.msg);
			}
		});
	}else{
		alert('不合法的手机号！');
	}
	
}

//设置倒计时
function set_time(){
	var i = 60;
	var timer = null;
	if(btn) {
		btn = false;
		timer = setInterval(function() {
			$("#register_form .msgp").html(i + "s后重新获取").css({background:"darkred",color:"white",cursor:"auto"});
			i--;
			if(i < 0) {
				clearInterval(timer);
				$("#register_form .msgp").html("重新获取验证码").css({background:"pink",color:"black",cursor:"pointer"});
				btn = true;
			}
		}, 1000);
	}
}

/*判断输入是否为合法的手机号码*/
function isphone(inputString){
	var partten = /^1[3,4,5,7,8]\d{9}$/;
	var fl=false;
	if(partten.test(inputString)){
		//alert('是手机号码');
		return true;
	} else{
		return false;
		//alert('不是手机号码');
	}
}
$('#register_form').submit(function (){
	$.post(base.url+'user/register',
		$('#register_form').serialize(),
		function (result){
			result = $.parseJSON(result);
			if(result.status){
				alert('注册成功！');
				$('.pop_login_inner_close').click();
				$('.enter_btns_login').click();
			}else{
				alert(result.msg);
			}
		});
	return false;
})

var time = $("#register_form .msgp b");
var msgp = $("#register_form .msgp");
var btn = true;
$("#register_form .msgp").click(function() {
	if(btn) get_publicmsg();
	
})

var open_qq = function (qq){
	var src="mqq://im/chat?chat_type=wpa&uin="+qq+"&version=1&src_type=web";
	location.href = src;
}


/** 
*@param {string} url 完整的URL地址 
*@returns {object} 自定义的对象 
*@description 用法示例：var myURL = parseURL('http://abc.com:8080/dir/index.html?id=255&m=hello#top');
myURL.file='index.html' 

myURL.hash= 'top' 

myURL.host= 'abc.com' 

myURL.query= '?id=255&m=hello' 

myURL.params= Object = { id: 255, m: hello } 

myURL.path= '/dir/index.html' 

myURL.segments= Array = ['dir', 'index.html'] 

myURL.port= '8080' 

myURL.protocol= 'http' 

myURL.source= 'http://abc.com:8080/dir/index.html?id=255&m=hello#top' 

*/
function parseURL(url) {  
	var a =  document.createElement('a');  
	a.href = url;  
	return {
		source: url,  
		protocol: a.protocol.replace(':',''),  
		host: a.hostname,  
		port: a.port,  
		query: a.search,  
		params: (function(){  
			var ret = {},  
			seg = a.search.replace(/^\?/,'').split('&'),  
			len = seg.length, i = 0, s;  
			for (;i<len;i++) {  
				if (!seg[i]) { continue; }  
				s = seg[i].split('=');  
				ret[s[0]] = s[1];  
			}  
			return ret;  
		})(),  
		file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],  
		hash: a.hash.replace('#',''),  
		path: a.pathname.replace(/^([^\/])/,'/$1'),  
		relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],  
		segments: a.pathname.replace(/^\//,'').split('/')  
	};
}