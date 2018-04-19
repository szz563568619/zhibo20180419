<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">与游客会话</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">
					<iframe src="visitor/im_page" frameborder="0" style="width:100%;height:540px;min-height:534px;min-width:802px;overflow:hidden;"></iframe>
				</div>
			</div>
		</div>
	</div>
</div>
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


var title=document.title
var timerID = null;
function newtext() {
	document.title=title.substring(1,title.length)+title.substring(0,1)
	title=document.title.substring(0,title.length)
}

function tip_new_msg(){
	if(timerID == null){
		title = '您有新的消息，请注意查看！';
		timerID = setInterval("newtext()", 100);
	}
}

function cancel_tip(){
	clearInterval(timerID);
	timerID = null;
	document.title = '云杰直播平台';
}
</script>