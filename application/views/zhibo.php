
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
<?php
if($is_obs_video == 0){
	?>
	<style type="text/css">   
  
body{margin:0;}
</style>
	<?php echo str_replace('{name}', $this->session->userdata('name').$source, $video_code); ?>
<?php }else{

?>
<script src="//imgcache.qq.com/open/qcloud/video/vcplayer/TcPlayer-2.2.0.js" charset="utf-8"></script>
<style>
.vcp-playtoggle{display: none !important;}
.vcp-error-tips{height: 1em; top:100%; margin-top: -34px; width: 250px; left: 20px; text-align: left;}
</style>
<div id="id_test_video" style="width:100%; height:100%;"></div>

<script>
<?php echo $video_code;  ?>
</script>

<?php } ?>
</body>
</html>