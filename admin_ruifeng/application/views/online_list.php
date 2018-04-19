<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">在线用户列表</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				当前在线人数： <a href="javascript:" class="btn btn-primary visitor_count"></a>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>用户名</th>
								<th>所属客服</th>
								<th>来源</th>
							</tr>
						</thead>
						<tbody class="visitor_list"></tbody>
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

<script src="js/socket.io-1.4.5.js"></script>
<script>
	
FROM = ['电脑PC端','手机APP','手机wap端'];	
var ID = <?php echo $this->session->userdata("id"); ?>;
var socket = io('http://<?php echo $socket["url"]; ?>:<?php echo $socket["receive_port"]; ?>');
socket.on('connect', function (){
	var uid = 'admin_'+ID;
	socket.emit('admin_login', {'uid' : uid,});
	socket.emit('get_all_visitor');
});

socket.on('get_all_visitor', function (data){
	$.post(admin.url+'online_peo/get_all_visitor', {data:data}, function (data){
		data = $.parseJSON(data);
		$('.visitor_count').html(data.count);
		var html = '';
		for(var i in data.data){
			html += '<tr><td>'+data['data'][i]['name']+'</td><td>'+data['data'][i]['nick']+'</td><td>'+FROM[data['data'][i]['from']]+'</td></tr>';
		}
		$('.visitor_list').html(html);
	})
})

</script>