<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="<?php echo $tpl; ?>css/index.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="big_box">
	<div class="box_top">
		<table class="list">
			<tbody>
				<tr style=" background:#eee;">
				<div class="top_t">
					<a href="page/speciallist">查看其他老师战法</a>
					<span style="text-align: center;margin-left: 330px;"><?php echo $tname; ?>战法列表</span>
				</div>
				</tr>
				<tr style=" background:#eee;">
					<th class="title_l"><span>标题</span></th>
					<th class="title_r"><span>发布时间</span></th>
				</tr>
				<?php foreach($tactics_list as $v){ ?>
				<tr>
					<td class="t1">
						<span>
							<i><img src="<?php echo $tpl; ?>images/point.png"/></i>
							<a href="<?php echo base_url(); ?>tactics/tactics_page/<?php echo $v['id']; ?>"><?php echo $v['title']; ?></a>
						</span>
					</td>
					<td class="t2">
						<span><?php echo $v['create_time']; ?></span>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="box_bottom">
		<?php echo $pagin; ?>
	</div>
</div>
</body>
</html>