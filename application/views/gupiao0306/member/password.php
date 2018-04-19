<form class="zc_main_r mm_main_r password_form">
	<h3><span>您当前的位置：</span><a href="">首页</a>&nbsp;>&nbsp;<span class="last">修改密码</span></h3>
	<ul>
		<li>
			<span>原密码：</span>
			<input type="password" name="old_password"/>
		</li>
		<li>
			<span>新密码：</span>
			<input type="password" name="password" id="password" />
		</li>
		<li>
			<span>确认密码：</span>
			<input type="password" id="re_password"/>
		</li>
		<li>
			<button type="submit">提交</button>
		</li>
	</ul>
</form>

<script>
	
$('.password_form').submit(function (){
	var password = $('#password').val();
	var re_password = $('#re_password').val();
	if(password == ''){
		alert("请输入新密码");
	}else if(password != re_password){
		alert("两次密码输入不一致");
	}else{
		$.post($('base').attr('href')+'user/update_password',
		$(this).serialize(),
		function (result){
			result = $.parseJSON(result);
			if(result.status){
				alert("修改密码成功，下次请使用新密码登录");
				location.reload();
			}else{
				alert(result.msg);
			}
		})
	}
	return false;
})

</script>