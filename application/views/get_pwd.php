<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>该房间需要密码</title>
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

.input_control{
  width:360px;
  margin:20px auto;
}
input[type="text"],#btn1,#btn2{
  box-sizing: border-box;
  text-align:center;
  font-size:1.4em;
  height:2.7em;
  border-radius:4px;
  border:1px solid #2089d8;
  color:#6a6f77;
  -web-kit-appearance:none;
  -moz-appearance: none;
  display:block;
  outline:0;
  padding:0 1em;
  text-decoration:none;
  width:100%;
}
input[type="text"]:focus{
  border:1px solid #ff7496;
}
label{color: red;}
</style>
</head>
<body>
	<div id="container">
		<form class="" action="<?php echo base_url(); ?>room/<?php echo $rid; ?>/" method="get">
				<div class="input_control">
					<input type="text" name="pwd" value="" placeholder="请输入房间密码"><label for="res"><?php echo $res; ?></label>
				</div>
				<div class="input_control">
					<button type="submit" id="btn1">点击进入房间</button>
			</div>
		</form>
		<h1>如果忘记密码，请点击下方QQ客服咨询</h1>
		<?php foreach($qq as $v): ?>
		<!--<a href="tencent://message/?uin=<?php echo $v; ?>&Menu=yes" target="_blank"><img src="http://combo.b.qq.com/bqq/v5/images/btn_wpa.png"></a>-->
		<a href="javascript:;" target="_blank" onclick="qq_start(<?php echo $v; ?>)"><img src="http://combo.b.qq.com/bqq/v5/images/btn_wpa.png"></a>
		
		<?php endforeach; ?>
	</div>
	<script>
		function qq_start(qq){
			var qqurl = "mqq://im/chat?chat_type=wpa&uin="+qq+"&version=1&src_type=web";
			var is_pc = IsPC();
			if(is_pc) qqurl = 'tencent://message/?uin=<?php echo $v; ?>&Menu=yes';

			location.href = qqurl;
		}

		function IsPC() {
		    var userAgentInfo = navigator.userAgent;
		    var Agents = ["Android", "iPhone",
		                "SymbianOS", "Windows Phone",
		                "iPad", "iPod"];
		    var flag = true;
		    for (var v = 0; v < Agents.length; v++) {
		        if (userAgentInfo.indexOf(Agents[v]) > 0) {
		            flag = false;
		            break;
		        }
		    }
		    return flag;
		}
	</script>
</body>
</html>
