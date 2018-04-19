<div class="zc_main_l_t">
	<img src="<?php echo $tpl; ?>images/member/zhuce.png"/>
	<span>您好！<?php echo $this->session->userdata('name'); ?></span>
</div>
<div class="zc_main_l_b">
	<ul>
		<li class="shouye"><a href="/">返回首页</a></li>
		<li class="xinxi <?php if($sidebar_current == 'info'): ?>focus<?php endif; ?>"><a href="user/info">我的信息</a></li>
		<li class="mima <?php if($sidebar_current == 'password'): ?>focus<?php endif; ?>"><a href="user/password">修改密码</a></li>
	</ul>
</div>