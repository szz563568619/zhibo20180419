<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $room['title']; ?></title>
	<meta name="description" content="<?php echo $room['description']; ?>" />
	<meta name="keywords" content="<?php echo $room['keywords']; ?>" />
	<base target="_self" href="<?php echo base_url(); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">  
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<meta name="HandheldFriendly" content="true">
	<meta name="format-detection" content="telephone=no" />
	<meta name="format-detection" content="email=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="screen-orientation" content="portrait">
	<meta name="x5-orientation" content="portrait">
	<meta name="full-screen" content="yes">
	<meta name="x5-fullscreen" content="true">
	<meta name="browsermode" content="application">
	<meta name="x5-page-mode" content="app">
	<link rel="stylesheet" href="<?php echo $tpl; ?>css/reset.css">
	<link rel="stylesheet" type="text/css"  href="<?php echo $tpl; ?>css/index.css"  media="screen and (min-device-width:320px) "/>
	<script src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
</head>
<body>

<script>
var base = {url:$('base').attr('href'), tpl:"<?php echo $tpl; ?>"}
var MID = <?php echo $this->session->userdata('mid'); ?>;
var LOGIN = <?php echo (int)$this->session->userdata('is_login'); ?>;
var GID = <?php echo $this->session->userdata('gid'); ?>;
var USERNAME = "<?php echo $this->session->userdata('name'); ?>";
var RID = <?php echo '"'.$rid.'"'; ?>;
var SOCKET_PORT = "<?php echo $socket_port; ?>";
var SOCKET_URL = "<?php echo $socket_url; ?>";
</script>

<section class="video">
	<header class="video_head">
		<div class="logo"></div>
		<div class="head_tel"><a href="tel:<?php echo $room['phone']; ?>">咨询电话:<?php echo $room['phone']; ?></a></div>
	</header>
	<div class="video_inner">
		<iframe id="zhibo"  width="100%" height="100%" frameborder=0 scrolling='no' src="zhibo?code=<?php echo $room['is_obs_video']; ?>"></iframe>
	</div>
</section>
<div class="outer_chat">
	<section class="chat_tool">
		<div class="enter_btns">
			<?php if($this->session->userdata('is_login')): ?>
			<button>
				<span class="enter_btns_login_span03 "><span class="enter_btns_login_span02"><?php echo $this->session->userdata('name'); ?></span><a class="enter_btns_login_span01 cYellow" href="user/logout">退出</a></span>
			</button>
			<?php else: ?>
			<button class="enter_btns_login">
				<span class="enter_btns_login_img enter_btns_login_span01 line1_cLight"><img src="<?php echo $tpl; ?>images/btns_name.png"></span>
				<span class="enter_btns_login_span02 line1_cDark">登 陆</span>
			</button>
			<a href="javascript:" class="bRed register">
				<span class="enter_btns_tel_img enter_btns_login_span01 line3_cLight"><img src="<?php echo $tpl; ?>images/btns_name.png"></span>
				<span class="enter_btns_login_span02 line3_cDark">注 册</span>
			</a>
			<?php endif; ?>
			
			<a href="javascript:" class="bYellow qq_click">
				<span class="enter_btns_ask_img enter_btns_login_span01 line2_cLight"><img src="<?php echo $tpl; ?>images/btns_pass.png"></span>
				<span class="enter_btns_login_span02 line2_cDark">QQ咨询</span>
			</a>
			<button class="im_click">
				<span class="enter_btns_login_img enter_btns_login_span01 line1_cLight"><img src="<?php echo $tpl; ?>images/im.png"></span>
				<span class="enter_btns_login_span02 line1_cDark">助 理</span>
			</button>
		</div>
		<div class="chatroom">
			<ul id="msg_list" class="chatroom_ul">
				<?php foreach($chat_list as $v): ?>
				<li class="chatroom_li">
					<div class="chatroom_user">
						<span class="chatroom_user_icon"><img src="<?php echo $tpl; ?>images/level/level<?php echo $v['gid']; ?>.gif" class="chatroom_user_icon_img"></span>
						<span class="chatroom_user_name"><?php echo $v['name']; ?></span>
						<span class="chatroom_time"><?php echo $v['time']; ?></span>
					</div>
					<div class="chatroom_message">
						<p class="chatroom_message_words chat_data_<?php echo $v['gid']; ?>"><?php echo $v['content']; ?></p>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="input_tool">
			<div id="msg_content" class="input_box" contentEditable="true"></div>
			<div class="input_btn"><button class="input_inner_btn" onclick="send_msg()">发 送</button></div>
		</div>
	</section>
</div>
<!-- pop_login -->
<div class="pop_login">
	<div class="pop_mask"></div>
	<div class="pop_login_inner">
		<div class="pop_login_head">欢迎登陆</div>
		<form id="login_form">
			<div class="pop_login_uesr_text"><span class="pop_login_uesr_img"><img src="<?php echo $tpl; ?>images/btns_name.png"></span><input type="text" name="user" placeholder="账 号"></div>
			<div class="pop_login_uesr_text"><span class="pop_login_uesr_img"><img src="<?php echo $tpl; ?>images/btns_pass.png"></span><input type="password" name="pwd" placeholder="密 码"></div>
			<div class="pop_login_inner_code"><input type="text" name="captcha" placeholder="输入验证码" class="pop_login_inner_code_input"><div class="code_img"><img src="captcha/get_captcha" onclick="this.src='captcha/get_captcha'+'?time='+Date.parse(new Date())"></div></div>
			<div class="pop_login_inner_btn"><button>立即登录</button></div>
		</form>
		<div class="pop_login_inner_close"></div>
	</div>
</div>
<!-- pop_register -->
<div class="" id="register">
	<div class="pop_mask"></div>
	<div class="pop_register_inner">
		<div class="pop_login_head">欢迎注册</div>
		<form id="register_form">
			<div class="pop_login_uesr_text"><span class="pop_login_uesr_img"><img src="<?php echo $tpl; ?>images/btns_name.png"></span><input type="text" name="user" placeholder="账号，只限汉字和数字"></div>
			<div class="pop_login_uesr_text"><span class="pop_login_uesr_img"><img src="<?php echo $tpl; ?>images/btns_pass.png"></span><input type="password" name="pwd" placeholder="密码"></div>
			<div class="pop_login_uesr_text"><span class="pop_login_uesr_img"><img src="<?php echo $tpl; ?>images/btns_pass.png"></span><input type="password" name="repwd" placeholder="确认密码"></div>
			<div class="pop_login_uesr_text"><span class="pop_login_uesr_img"><img src="<?php echo $tpl; ?>images/btns_tel.png"></span><input type="text" name="phone" placeholder="请输入11位手机号"></div>
			<div class="pop_login_inner_code"><input type="text" name="publicmsg" placeholder="输入验证码" class="pop_login_inner_code_input"><span class="msgp" style="display:inline-block;line-height: 47px;float:left;background: pink;width:95px;text-align:center;height: 44px; cursor: pointer;outline: none;margin-left: 10px;">获取验证码</span></div>
			<input type="hidden" name="querystring" value="">
				<input type="hidden" name="allurl" value="">
			<div class="pop_login_inner_btn"><button>立即注册</button></div>
		</form>
		<div class="pop_login_inner_close"></div>
	</div>
</div>
<div id="tencent" style="display:none;"><?php echo $room['qq']; ?></div>
<script src="<?php echo $tpl; ?>js/socket.io-1.4.5.js"></script>
<script src="<?php echo $tpl; ?>js/jquery.cookie.js"></script>
<script src="<?php echo $tpl; ?>js/common.js"></script>
<script src="http://statistics.wanhuiit.com/statistics?code=hjjr&type=wap网页端"></script>
<div style="display:none;"><?php echo $room['statistics']; ?></div>
</body>
</html>