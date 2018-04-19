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
	<div class="xq_main" style="padding:0;">
		<object classid="clsid:CA8A9780-280D-11CF-A24D-444553540000" width="990" height="560" border="0"> 
			<param name="src" value="<?php echo base_url().'upload/zhanfa/'.$art['fname'].'.swf'; ?>">
			<!--<object data="<?php echo base_url().'upload/zhanfa/'.$art['fname'].'.swf'; ?>"  width="900" height="500"></object>-->
			 <embed src="<?php echo base_url().'upload/zhanfa/'.$art['fname'].'.swf'; ?>" width="990" height="560"></embed> 
		</object>
	</div>
</div>
</body>
</html>