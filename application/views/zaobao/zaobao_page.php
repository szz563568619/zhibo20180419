<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="<?php echo $tpl; ?>css/index.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="xq_box">
	<div class="top_t"><a href="javascript:history.go(-1);">返回</a></div>
	<div class="xq_main">
		<div class="xq_title">
			<h2><?php echo $art['title']; ?></h2>
			<span>发布时间：<?php echo $art['time']; ?></span>
		</div>
		<?php if($this->session->userdata('is_login')){ ?>
			<p><strong><?php echo $art['content']; ?></strong></p>
		<?php }else{ ?>
			<div style="background-color: #000;opacity: 0.8;z-index: 999;"><p style="text-align: center;z-index: 9999;"><strong>登录之后才能查看，<a style="color: red;" href="javascript:;" onclick="window.parent.document.getElementById('login').click();">点击登录</a></strong></p></div>
		<?php } ?>
	</div>
	<!--<div class="shengming">免责声明：本报告的信息均来源于公开资料，我公司对这些信息的准确性和完整性不作任何保证，也不保证所包含的信息和建议不会发生任何变更。我们已力求报告内容的客观、公正，但文中的观点、结论和建议仅供参考，报告中的信息或意见并不构成所述品种的买卖出价，投资者据此做出的任何投资决策与本公司和作者无关.</div>-->
</div>
<script>
	var login = <?php echo $this->session->userdata('is_login') ?>;
	if(!login){
		alert('请登录之后查看！');
		window.parent.document.getElementById('login').click();
	}
</script>
</body>
</html>