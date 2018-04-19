<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?php echo base_url(); ?>">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/common.js"></script>
    <script src="js/bootstrap.min.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>

<!-- Page-Level Plugin Scripts - Tables -->
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
</head>



<body>
<a href="javascript:;" onclick="parent.close_webchat();" style="position: absolute; top: 6px; right: 2px; display: block; width: 15px; height: 15px;">X</a>
<iframe src="visitor/im_page" frameborder="0" style="width:100%;height:540px;min-height:534px;min-width:802px;overflow:hidden;"></iframe>

<div class="opacity" style="background:#000; opacity:0.5; position:fixed; top:0; left:0; width:100%; height:100%; z-index:1000;display:none;"></div>
<div id="chat_record" style="width:600px; height:600px; margin-left: 50%; position:absolute; top:100px; left:-300px; display:none;z-index:9999;">
	<iframe src="" frameborder="0" style="width:100%;height:600px;overflow:hidden;"></iframe>
</div>

<script>
$(function(){
	$('.opacity').click(function(){
		close_chat_record();
	});
})
function open_chat_record(id, gid){
	$('#chat_record iframe').attr('src', 'visitor/minichat_list/'+id+'/'+gid);
	$('#chat_record').css('display', 'block');
	$('.opacity').css('display', 'block');
}

function close_chat_record(){
	$('#chat_record').css('display', 'none');
	$('.opacity').css('display', 'none');
}


function tip_new_msg(){
	parent.qq_start();
}

function cancel_tip(){}
</script>
</body>