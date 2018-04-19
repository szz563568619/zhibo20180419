<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<base href="<?php echo base_url(); ?>">

	<title>瑞丰财经直播间直播平台</title>

	<!-- Bootstrap core CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/common.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>



<body>

<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="login-panel panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">瑞丰财经直播间直播平台</h3>
				</div>
				<div class="panel-body">
					<form role="form">
						<fieldset>
							<div class="form-group">
								<input class="form-control" placeholder="请输入用户名" name="name" type="input" required autofocus>
							</div>
							<div class="form-group">
								<input class="form-control" placeholder="请输入密码" name="password" type="password" required>
							</div>
							<button type="button" onclick="login()" class="btn btn-lg btn-success btn-block">登录</button>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


</body>
</html>

<script src="js/jquery.min.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/common.js"></script>
<script>
	
function login(){
	var name = $.trim($('input[name=name]').val());
	var password = $.trim($('input[name=password]').val());
	if(name === '' || password === ''){
		alert('请输入用户名和密码');
	}else{
		$.post(admin.url+'login/check_login',
		{'name':name, 'password':password},
		function (result){
			result = $.parseJSON(result);
			if(result.status){
				location.href = '<?php echo $refer; ?>'
			}else{
				alert(result.msg);
			}
		})
	}
}

</script>