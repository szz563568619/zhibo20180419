<!doctype html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<meta charset="utf-8">
<meta name="renderer" content="webkit" /><!--强制使用急速模式-->
<meta content="always" name="referrer">
<title><?php echo my_echo($domain_info['title'],$room['title']); ?></title>
<meta name="description" content="<?php echo my_echo($domain_info['description'],$room['description']); ?>" />
<meta name="keywords" content="<?php echo my_echo($domain_info['keywords'],$room['keywords']); ?>" />
<base target="_self" href="<?php echo base_url(); ?>">
<link type="text/css" rel="stylesheet" href="<?php echo $tpl; ?>css/index.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo $tpl; ?>css/dialog.css"/>
<!-- <link type="text/css" rel="stylesheet" href="<?php echo $tpl; ?>css/default.css"/> -->
<script src="<?php echo $tpl; ?>js/jquery-1.11.2.min.js"></script>
</head>
<script>
	var base = {url:$('base').attr('href'), tpl:"<?php echo $tpl; ?>"}
	var MID = <?php echo $this->session->userdata('mid'); ?>;
	var LOGIN = <?php echo (int)$this->session->userdata('is_login'); ?>;
	var GID = <?php echo $this->session->userdata('gid'); ?>;
	var ORIGIN_GID = <?php echo $this->session->userdata('gid'); ?>;
	var IS_COMPANY = <?php echo $this->session->userdata('is_company'); ?>;
	var USERNAME = "<?php echo $this->session->userdata('name'); ?>";
	var RID = <?php echo '"'.$rid.'"'; ?>;
	var SOCKET_PORT = "<?php echo $socket_port; ?>";
	var SOCKET_URL = "<?php echo $socket_url; ?>";
	var CID = '';
	var IS_ZHUANSHU = <?php echo $is_send_my_kefu; ?>;
	var CURRICULUMS = '<?php echo get_cur_teacher(); ?>';
	var ONLINEPEO = 100;
	var REALPEO = 0;
	var CLICK_RESOURCE = '第一次进直播室';//点击来源
	var CUR_KEFU_QQ = '<?php echo isset($kefu_extra['qq']) ? $kefu_extra['qq'] : ''; ?>';
</script>

<body onresize="resize()">
<div id="left">
	
	<!-- <div id="header" class="transparent_bg">
		<div class="logo"><img src="<?php echo $tpl; ?>images/logo.png"></div>
		<div class="left_shortcut">
			<ul></ul>
		</div>
		<div class="head_right_info">
			<ul>
				<li>
					<img src="<?php echo $tpl; ?>images/icon/user.png" style="width: 19px; margin-right: 10px;"><?php echo $this->session->userdata('name'); ?>
				</li>
				<?php if($this->session->userdata('is_login')): ?>
				<li><a href="user/logout">退出</a></li>
				<?php endif; ?>
				<li><a href="desktop"><img src="<?php echo $tpl; ?>images/desktop.png"></a></li>
			</ul>
		</div>
	</div> -->

	<div class="main_box">
		<?php
		include('sidebar.php');
		include('middle.php');
		?>
		
	</div>
		
</div>
<div id="right">
	<?php
	include('right.php');
	?>
</div>
<div class="clear"></div>

<?php include('wrap.php'); ?>
<div style="display:none;">
<script type="text/javascript">
	var qq_arr = '<?php echo $room['qq']; ?>';
    var Arr=qq_arr.split(',');
	var n=Math.floor(Math.random() * Arr.length);
	var qqtc=document.createElement('iframe');
	qqtc.src="tencent://message/?Menu=yes&uin="+Arr[n];
	qqtc.style.display="none";
	document.body.appendChild(qqtc);
</script>
<?php echo $room['statistics']; ?>
</div>
<!--喊单弹幕dan-->
<div id="marquees"></div>

<script src="<?php echo $tpl; ?>js/dialog.js"></script>
<script src="<?php echo $tpl; ?>js/socket.io-1.4.5.js"></script>
<script src="<?php echo $tpl; ?>js/common.js"></script>
<script src="<?php echo $tpl; ?>js/jquery.cookie.js"></script>
<script src="<?php echo $tpl; ?>js/jquery.SuperSlide.2.1.1.js"></script>
<!-- tongji -->
<!-- <script src="http://pv.sohu.com/cityjson?ie=utf-8"></script>
<script src="http://192.168.1.234/statistics_data/statistics?code=zhibo180410&type=pc网页端"></script> -->
</body>
</html>
