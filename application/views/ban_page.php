<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>您已被屏蔽</title>
<style type="text/css">

::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	text-align: center;
	margin: 10px;
	border: 1px solid #D0D0D0;
	-webkit-box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}
</style>
</head>
<body>
	<div id="container">
		<h1>您的IP：<?php echo get_ip(); ?>&nbsp;&nbsp;您的用户名：<?php echo $this->session->userdata('name'); ?></h1>
		<h1>您已经被屏蔽，如有疑问，请点击下方QQ客服咨询</h1>
		<?php foreach($qq as $v): ?>
		<a href="tencent://message/?uin=<?php echo $v; ?>&Menu=yes" target="_blank"><img src="http://combo.b.qq.com/bqq/v5/images/btn_wpa.png"></a>
		<?php endforeach; ?>
	</div>
</body>
</html>