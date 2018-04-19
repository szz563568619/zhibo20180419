<div class="zc_main_r">
	<h3><span>您当前的位置：</span><a href="/">首页</a>&nbsp;>&nbsp;<span class="last">我的信息</span></h3>
	<ul>
		<h4>基本信息</h4>
		<li>
			<span>用户名：</span>
			<txt><?php echo $info['name']; ?></txt>
		</li>
		<li>
			<span>用户等级：</span>
			<txt><?php echo $info['gname']; ?></txt>
		</li>
		<li>
			<span>注册时间：</span>
			<em><?php echo $info['re_time']; ?></em>
		</li>
		<li>
			<span>最后登录：</span>
			<em><?php echo $info['login_time']; ?></em>
		</li>
		<li>
			<span>当前IP：</span>
			<em><?php echo $info['ip']; ?></em>
		</li>
	</ul>
</div>