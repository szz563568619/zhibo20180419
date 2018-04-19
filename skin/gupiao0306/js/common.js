resize();

var socket = io('http://'+SOCKET_URL+':'+SOCKET_PORT);
/*连接服务端*/
socket.on('connect', function (){
	var uid = MID+'_'+GID;
	socket.emit('login', {'uid' : uid,});
	if(CID != '') socket.emit('send_my_cid', CID);
	socket.emit('get_visitor_count');//获取真实在线人数
});

var open_qq = function (qq){
	var qqtc=document.createElement('iframe');
	qqtc.src="tencent://message/?Menu=yes&uin="+qq;
	qqtc.style.display="none";
	document.body.appendChild(qqtc);
}

$(function (){
	setTimeout(srcolldown, 500);
	jQuery(".banner").slide({mainCell:".bd ul",autoPlay:true,interTime:7000});
	jQuery(".chat_room_head_right").slide({mainCell:".inner_chat_room_head_right ul",autoPlay:true,effect:"leftMarquee",interTime:50});
	emotion_init();
	$('.qq_click').click(function (){
		CLICK_RESOURCE = $(this).attr('data-resource');//添加点击来源
		qq_start();
	});


	setTimeout(function (){$('.wellcome').click()}, 1000);
	if(!LOGIN) setTimeout(function (){$('.sanfenzhong').click()}, 1000*60*3);

	/*如果是第一次进页面，并且又不是公司的，显示对话框*/
	if(!$.cookie('is_first') && IS_COMPANY == 0){
		$.cookie('is_first', 1, {expires:365});
	}

	// if(LOGIN == 0) webchat_init();
	webchat_init();

	//上传图片
	$(".upload_img").change(function(){
		$(this).parent().submit();
	});
	$('#fileupload-form').on('submit',(function(e) {
		e.preventDefault();
		var serializeData = $('#fileupload-form').serialize();

      // var formData = new FormData(this);
      $('#fileupload-form').ajaxSubmit({
           type:'POST',
           url: base.url+'upload/upload_img',
           dataType: 'json',
           data: serializeData,
           contentType: false,
           cache: false,
           processData:false,

           beforeSubmit: function() {
           		//上传图片之前的处理
           },
           uploadProgress: function (event, position, total, percentComplete){
               //在这里控制进度条
           },
           success:function(result){
               if(result.status){
					var msg = '<img class="tupian" src="'+result.img+'">';
					$.post(base.url+'chat/send_msg',{content:msg, rid:RID, 'name':USERNAME, 'gid':GID},function (d){
						d = $.parseJSON(d);
						if(d.code == 403) alert('你已被禁言，请联系客服！');
					});
				}else{
					alert(result.msg);
				}
				$('input[type=file]').closest('form').get(0).reset();
           }
       });
	}));

	//站长统计
	$('a').each(function(){
		var title = $(this).attr('title');
		if(title == '站长统计'){
			$(this).hide();
		}
	})
})

/*显示客服列表*/
function show_kefu_list(service_list){
	if(service_list){
		var html = '';
		var headimg = '';
		for(var i in service_list){
			var kefu = service_list[i];
			if(kefu.headimg == '') headimg = base.tpl+"images/webchat/tou"+kefu.sex+".jpg";
			else headimg = base.url+"upload/headimg/"+kefu.headimg;
			html += '<li class="kefu_'+kefu.id+'" onclick="open_kefu_chat('+kefu.id+')"><img src="'+headimg+'"><em>'+kefu.nick+'</em><span>私聊</span></li>'
		}
		$('.kefu_list').html(html);
	}
}
/*打开客服聊天界面*/
function open_kefu_chat(aid){
	CLICK_RESOURCE = '在线客服列表';
	qq_start();
	$('#webchat iframe').contents().find(".service_list_"+aid).eq(0).click();
}

function webchat_init(){
	var screenWidth = $('body').outerWidth();
	var screenHeight = document.body.offsetHeight;

	var src = base.url+'im/';
	if(IS_COMPANY) src = base.url+'admin_ruifeng/visitor/front_im/';
	$('body').append('<div id="webchat" style="position:absolute;bottom:0;right:0;width:827px;height:534px;display:none;z-index:1;border: 2px solid #484343;border-radius: 12px;box-shadow: 2px 4px 55px #000;">'
	+'<div id="move" style="width:610px;height: 30px;position: absolute;right:50px;;top: 0;z-index:10;"></div>'
	+'<iframe id="iiframe" src="'+src+'" style="width:100%;height:100%;" frameborder="0"></iframe></div>');
}

function qq_start(){
	if(IS_COMPANY == 1){
		start_webchat();
	}else{
		$.get(base.url+"api/is_kefu_on", function(d){
			if(d == 1){
				location.href = "tencent://message/?Menu=yes&uin="+CUR_KEFU_QQ;
			}else{
				start_webchat();
			}
		});
	}
}

//随机弹qq
function tip_qq(){
	var n=Math.floor(Math.random() * Arr.length);
	var qqtc=document.createElement('iframe');
	qqtc.src="tencent://message/?Menu=yes&uin="+Arr[n];
	qqtc.style.display="none";
	document.body.appendChild(qqtc);
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

/*框架布局*/
function resize(){
	var screen_height = document.body.offsetHeight;
	var screen_width = document.body.offsetWidth; /*整体页面的宽高*/
	var sidebar_width = $('.left_area').outerWidth()+10; /*左侧sidebar宽度，包括边距*/
	var notice_height = $('.notice').outerHeight() + 5; /*聊天顶部公告的高度加边距*/
	var banner_height = $('.banner').outerHeight()+$('.bottom').outerHeight(); /*视频下方banner和版权的总高度*/

	var video_height = screen_height - notice_height - banner_height; /*视频能给的高度*/

	/*由视频高度反推计算视频宽度*/
	var video_width = video_height*1.78;

	var left_width = sidebar_width + video_width; /*左侧整体宽度*/
	$('#left').width(left_width);
	$('#right').width(screen_width - left_width - 10); /*右侧宽度还要减10px距离*/

	$('.middle_area').width(video_width);
	$('.shipin').height(video_height);

	/*实况通知*/
	$('.chat_room_head_right').width(video_width - 75);
	$('.chat_room_head_right li').css('minWidth', video_width - 75);
	$('.chat_room_head_right li').css('margin-right', 20);

	var send_area_height = $('.chat_bottom').outerHeight(); /*底部发送区域的高度*/
	var head_right_info = $('.head_right_info').outerHeight(); /*底部发送区域的高度*/

	$('.chat').height(screen_height - send_area_height - head_right_info);
	$('.chat_right').width($('#right').width() - 70);

	/*在线客服列表高度*/
	var logo_height = $('.aside_logo').outerHeight();
	var aside_zx_height = $('.aside_zx').outerHeight();
	var aside_db_height = $('.aside_db').outerHeight();
	$('.outer_news_ul').height(screen_height - logo_height - aside_zx_height - aside_db_height - 15 -42);
}

/*表情和彩条的效果初始化*/
function emotion_init(){
	$('.face01_imgs img, .face02_imgs img').bind('click', function (){
		var text = $('#msg_content').val();
		text += '[emotion_'+$(this).data('num')+']';
		$('#msg_content').val(text);
	})

	$(document).bind('click', function (e){
		e = e || window.event;
		srcObj = e.srcElement ? e.srcElement : e.target;// 获取触发事件的源对象
		if($(srcObj).hasClass('face') || $(srcObj).hasClass('clap')){
			$('.face01_imgs').css('display','block');
		}else{
			$('.face01_imgs').css('display','none');
		}

		if($(srcObj).hasClass('color_bar') || $(srcObj).hasClass('clap1')){
			$('.face02_imgs').css('display','block');
		}else{
			$('.face02_imgs').css('display','none');
		}
	})
}

//聊天信息框滚动条设定在最底部；
function srcolldown(){
	var chat_room_log = $('.chat');
	var chat_room_log_ul = $('.chat_main');
	chat_room_log.animate({scrollTop:chat_room_log_ul.outerHeight(true)}, 1000);
}

//聊天记录清屏
function clear_screen(){$('#msg_list').html('');}

/*添加到收藏夹*/
function AddFavorite(sURL, sTitle){
	try {
		window.external.addFavorite(sURL, sTitle);
	} catch (e) {
		try {
			window.sidebar.addPanel(sTitle, sURL, "");
		} catch (e) {
			alert("加入收藏失败，请使用Ctrl+D进行添加");
		}
	}
}
/*获取消息数据*/
socket.on('public_msg', function (data){
	var html = '';
	var msg = $.parseJSON(data);

	if(msg.is_handan == 1){
		barrage(msg.content);
		return;
	}

	//是否是公司内部发言
    var is_nei = '';
	// if(IS_COMPANY && msg.types == 1) is_nei = '<span style="color: red;">员工</span>';

	var shebei = '';
	if(msg.is_mobile) shebei = '<em class="mobile-icon"></em>';

	html += '<li class="';
	if(msg.gid == 0) html += ' xiaomishu ';
	else if(msg.name == USERNAME) html += ' benren ';
	html += ' level_msg_'+msg.gid+'" id="chat_'+msg.score+'"><div class="chat_head">'+shebei+'<em class="s_time">'+msg.time.substr(11, 5)+'</em><em><img src="'+base.tpl+'images/level/level'+msg.gid+'.png"/></em><em class="uname" onclick="call_username(this)">'+msg.name+'</em>';
	if(ORIGIN_GID == 0){
		html += '<em class="nei_edit"><span class="del_msg" onClick="del_msg('+msg.score+',this)">删除</span><span class="ip_ban" onClick="ip_save(\''+msg.name+'\')">屏蔽</span><span class="ip_ban" onClick="nospeaking_save(\''+msg.name+'\')">禁言</span></em>';
	}
	html += '</div><div class="chat_content"><div class="say">'+msg.content+'</div></div></li>';
	$('#msg_list').append(html);
	resize();
	srcolldown();
})

/*获取新加入用户列表*/
//巡官删除聊天信息
function del_msg(score,dom){
	//获取到时间串唯一标识
	$.ajax({
		url : base.url+'chat/del_msg',
		type : 'POST',
		dataType: "json",
		data : {score : score},
		success : function (res){
			if(res.code == 403){
				alert("无权删除，请联系技术！");
			}else if(res.code == 404){
				alert("该功能已禁用，请联系技术！");
			}else{
				$(dom).parents('li').remove();
			}
		}
	})
}

/*删除公屏聊天信息*/
socket.on('del_public_msg', function (data){
	$('#chat_'+data).remove();
})

//保存屏蔽ip
function ip_save(name){
    $.post(base.url+'chat/ip_ban',{forbidden:name},function (result){
        result = $.parseJSON(result);
        if(!result.status)
        {
            alert(result.msg);
        }
        else
        {
			alert("屏蔽成功！");
        }
    })
}

//禁言
function nospeaking_save(name){
    $.post(base.url+'chat/nospeaking_ban',{forbidden:name},function (result){
        result = $.parseJSON(result);
        if(!result.status)
        {
            alert(result.msg);
        }
        else
        {
			alert("禁言成功！");
        }
    })
}

/* 游客发言限制10秒 */
var pretime = 0;
function limit_visitor_chat(curtime){
	var restime = (curtime - pretime)/1000;
	if(restime < 10){
		alert(10-restime+"秒之后可以发言！");
		return false;
	}else{
		pretime = curtime;
		return true;
	}
}

/*发送消息*/
function send_msg(){
	var msg = $.trim($('#msg_content').val());
	if(msg == '') return;

	//发dan mu
	var is_dan = $("input[name=is_dan]:checked").val();
	if(is_dan == undefined) is_dan = 0;
	$("input[name=is_dan]").removeAttr("checked");

	msg = msg.replace(/\[emotion_([^\]]+)\]/g, '<img src="'+base.url+base.tpl+'images/biaoqing/$1.gif"/>');
	$.post(base.url+'chat/send_msg',{content:msg, rid:RID, 'name':USERNAME, 'gid':GID, 'is_dan':is_dan},function (d){
		d = $.parseJSON(d);
		if(d.code == 403) alert('你已被禁言，请联系客服！');
	})
	$('#msg_content').focus().val('');
}

function change_alias(__this){
	var dom = $(__this).find('option:selected');
	var name = dom.val();
	GID = dom.data('gid');
	USERNAME = name;
}

/*绑定发送消息的快捷键，Enter或者Ctrl+Enter*/
document.onkeydown = function (e){
	var e = e || window.event;
	var keyCode = e.keyCode || e.which || e.charCode;
	if( (e.ctrlKey && (e.keyCode == 13)) ||  e.keyCode == 13 ){
		var element = e.srcElement||e.target;
		if( $(element).attr('id') == 'msg_content'){
			send_msg();
		}
	}
}

/* 选择@对象,放到发送框 */
function call_username(dom){
	var username = $(dom).text();
	var umsg = $('#msg_content').val();
	$('#msg_content').val(umsg + ' @' + username + ' ');
	$('#msg_content').focus();
}

/* ---------------------直播室title提示--------------------- */
var toptitle=document.title;
var oldtitle=document.title;
var timerID = null;
function newtext() {
	document.title=toptitle.substring(1,toptitle.length)+toptitle.substring(0,1);
	toptitle=document.title.substring(0,toptitle.length);
}
function tips_new_msg(){
	if(timerID == null){
		toptitle = '您有新的消息，请注意查看！';
		timerID = setInterval('newtext()', 300);
	}
}
function cancel_tips(){
	clearInterval(timerID);
	timerID = null;
	document.title = oldtitle;
}
/* $(document).mouseover(function(){
	cancel_tips();
}); */
/* ---------------------直播室title提示--------------------- */

Date.prototype.Format = function(formatStr){
	var str = formatStr;
	var Week = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];

	str=str.replace(/w|W/g,Week[this.getDay()]);

	str=str.replace(/HH/,this.getHours()>9?this.getHours().toString():'0' + this.getHours());
	str=str.replace(/MM/,this.getMinutes()>9?this.getMinutes().toString():'0' + this.getMinutes());

	return str;
}
Date.prototype.Format1 = function(formatStr){
	var str = formatStr;
	var Week = ['日','一','二','三','四','五','六'];

	str=str.replace(/yyyy|YYYY/,this.getFullYear());
	str=str.replace(/yy|YY/,(this.getYear() % 100)>9?(this.getYear() % 100).toString():'0' + (this.getYear() % 100));

	str=str.replace(/MM/,this.getMonth()+1>9?(this.getMonth()+1).toString():'0' + (this.getMonth()+1));
	str=str.replace(/M/g,this.getMonth()+1);

	str=str.replace(/w|W/g,Week[this.getDay()]);

	str=str.replace(/dd|DD/,this.getDate()>9?this.getDate().toString():'0' + this.getDate());
	str=str.replace(/d|D/g,this.getDate());

	str=str.replace(/hh|HH/,this.getHours()>9?this.getHours().toString():'0' + this.getHours());
	str=str.replace(/h|H/g,this.getHours());
	str=str.replace(/mm/,this.getMinutes()>9?this.getMinutes().toString():'0' + this.getMinutes());
	str=str.replace(/m/g,this.getMinutes());

	str=str.replace(/ss|SS/,this.getSeconds()>9?this.getSeconds().toString():'0' + this.getSeconds());
	str=str.replace(/s|S/g,this.getSeconds());

	return str;
}

/*点击图片显示原图*/
$("#msg_list").click(function(ev) {
	var event = ev || window.event;
	var Tarobj = event.target || event.srcElement;
	var objSrc = Tarobj.src;
	if($(Tarobj).context.nodeName === "IMG" && $(Tarobj).attr('class') === 'tupian'){
		$("#box").show();
		$("#box img").attr("src",objSrc);
	}
	return false;
})
$("#box").click(function(){
	$("#box").hide();
})

/*弹幕*/
var timer_danmu = [];
var maxW = $(document).width();
// var maxH = $(document).height() * Math.random();
function barrage(value) {
	/*弹幕初始化*/
	/*添加弹幕消息*/
	if(value === "" || value === undefined) {
		return;
	}
	$("#marquees").append('<p class="marquee">' + value + '</p>');
	var lastW = $(".marquee").last().width();
	$(".marquee").last().css({
		"right":0,
		"bottom":($(document).height() - 300) * Math.random()
	});
	/*清除新添加弹幕的定时器，防止之前的弹幕跳动*/
	$(".marquee").each(function() {
		var index1 = $(this).index();
		clearInterval(timer_danmu[index1]);
	})

	$(".marquee").each(function() {
		var index = $(this).index();
		var x = $(document).width() - $(this).offset().left - $(this).width();
		/*给每个弹幕添加定时器*/
		timer_danmu[index] = setInterval(function() {
			$(".marquee").eq(index).css({
				"right": x
			});
			x += 2;
			if(x > $(document).width() + 50) {
				/*清除定时器*/
				clearInterval(timer_danmu[index]);
			}
		}, 50);
	});
}
