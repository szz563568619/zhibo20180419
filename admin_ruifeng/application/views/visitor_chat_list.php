<style>.panel{margin-bottom: 10px;}</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">聊天记录 <a class="btn btn-default" href="javascript:location.href = document.referrer;" role="button">返回游客列表</a>
			<span class="set_available"></span>
		</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-10">
		<?php foreach($chat_list as $v): ?>
		<div class="panel panel-<?php echo $v['is_visitor'] ? 'primary' : 'success'; ?>">
			<div class="panel-heading"><?php echo $v['send_name']; ?> [<?php echo $v['time']; ?>]</div>
			<div class="panel-body">
				<div class="table-responsive"><?php echo $v['content']; ?></div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<script>
function del_visitor(id){
	if(confirm('确认删除该游客？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'visitor/del_visitor_istalk',
		{'id': id},
		function (){
			//location.href = admin.url+'visitor';
			location.href = document.referrer;
		})
	}
}
function set_is_available(is_available){
	if(confirm('确认重新设置？')){
		$.get('http://statistics.wanhuiit.com/statistics/set_is_available?name=<?php echo $visitor['name']; ?>&zhibo_flag=hjjr&is_available='+is_available,
		function (){
			//location.href = admin.url+'visitor';
			var valuablehtml = '';
			if(is_available == 0){
				$('.set_available').html('<button class="btn btn-success" id="set_available" onclick="set_is_available(1)">当前有效，点击设为无效</button>');
			}else{
				$('.set_available').html('<button class="btn btn-success" id="set_available" onclick="set_is_available(0)">当前无效，点击设为有效</button>');
			}
		})
	}
}

get_is_available();
function get_is_available() {
	$.get('http://statistics.wanhuiit.com/statistics/get_is_available?name=<?php echo $visitor['name']; ?>&zhibo_flag=hjjr',function(d){
		//<button class="btn btn-success" id="set_available"></button>
		if(d != ''){
			var str = '当前无效，点击设为有效';
			if(d == 1) str = '当前有效，点击设为无效';
			$('.set_available').html('<button class="btn btn-success" id="set_available" onclick="set_is_available('+d+')">'+str+'</button>');
		}
	})
}
</script>