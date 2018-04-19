<style>
.table>tbody>tr>td{vertical-align: middle;}
.data_list{word-break: break-all;}
</style>

<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">聊天记录 <button class="btn btn-danger" onclick="clear_screen();">清屏</button></h3>
	</div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th style="width:65px;">房间ID</th>
								<th>内容</th>
								<th style="width:150px;">发布人</th>
								<th style="width:160px;">发布时间</th>
							</tr>
						</thead>
						<tbody class="data_list">
						
						</tbody>
					</table>
					
				</div>
				<!-- /.table-responsive -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-6 -->
</div>
<!-- /.row -->
<script>


$(function (){
	get_check_data();
})

function get_check_data(){
	var score = arguments[0] ? arguments[0] : 0;
	$.ajax({
		url : admin.url+'speaker/get_check_data',
		type : 'POST',
		dataType: "json",
		cache : false,
		timeout : 30000,
		data : {score : score},
		success : function (result){
			get_check_data(result.score);
			deal_data(result.data_list);
		},
		error : function (XMLHttpRequest, textStatus){
			if(textStatus == 'timeout') get_check_data(score);
			else setTimeout(function (){get_check_data(score)}, 6000);
		}
	})
}
function deal_data(data_list){
	var html = '';
	for(var i in data_list){
		var data = $.parseJSON(data_list[i]);
		console.log(data);
		html += '<tr class="list_'+i+'"';
		if(data.types == 1) html += ' style="color:red;"';
		html += '><td>'+data.rid+'</td><td>'+data.content+'</td><td><img src="../skin/gupiao0306/images/level/level'+ data.gid + '.png" />&nbsp;&nbsp;'+data.name+'</td><td>'+data.time+'</td></tr>';
	}
	$('.data_list').append(html);
}

function clear_screen(){$('.data_list').html('');}

</script>