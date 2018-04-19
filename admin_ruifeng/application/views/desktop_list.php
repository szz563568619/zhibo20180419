<script src="js/plugins/datepicker/bootstrap-datetimepicker.min.js"></script>
<script src="js/plugins/datepicker/bootstrap-datetimepicker.zh-CN.js"></script>
<link rel="stylesheet" href="css/plugins/datepicker/bootstrap-datetimepicker.min.css">
<style>
.table>tbody>tr>td{vertical-align: middle;}
.w200{display: inline; width: 200px;}
.w400{display: inline; width: 400px;}
</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">保存桌面/从桌面进入网站统计导出</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<form method="get" action="desktop/desktop_list">
					<p>选择导出/查询类型：
						<select class="form-control date w200" name="desk" id="desk">
							<option value="1" <?php echo my_echo($desk) == 1 ? 'selected' : ''; ?> >保存桌面统计查询</option>
							<option value="2" <?php echo my_echo($desk) == 2 ? 'selected' : ''; ?> >从桌面进入网站统计查询</option>
						</select>
					</p>
					
					<p>选择时间范围：
						<input type="text" name="start" class="form-control date w200" id="start" value="<?php echo my_echo($start); ?>" readonly > 至 <input type="text" class="form-control date w200" id="end" name="end" value="<?php echo my_echo($end); ?>" readonly >
					</p>
					<p>
						<a  class="btn btn-success" onclick="select()">查询</a>
						<a  class="btn btn-success" onclick="for_excel()">导出成Excel表</a>
						<button class="btn btn-danger" onclick="desktop_del()" type="button">清空一周之前数据</button>
					</p>
				</form>
			</div>
			<div class="panel-body">
				<iframe id="concurrent" src="" style="width: 100%; height: 600px; border: none; overflow: hidden;"></iframe>
			</div>
		</div>
	<!-- /.col-lg-6 -->
	</div>
<!-- /.row -->
</div>
<script>
	
$('#start, #end').datetimepicker({ format:'yyyy-mm-dd', language:'zh-CN', autoclose:true, minView:2, todayBtn:true, todayHighlight:true, minuteStep:1 });

function select(){
	var start_time = $('#start').val();
	var end_time = $('#end').val();
	if(start_time == '' || end_time == ''){
		alert('请选择起止时间！');
		return;
	}
	var desk = $('#desk').val();
	$('#concurrent').attr('src', admin.url+'desktop/desktop_data/'+desk+'?start_time='+start_time+'&end_time='+end_time);
}

function for_excel(){
	var start_time = $('#start').val();
	var end_time = $('#end').val();
	var desk = $('#desk').val();
	$.post(admin.url + 'desktop/for_excel',
	{start_time:start_time,end_time:end_time,desk:desk},
	function(result){
		result = $.parseJSON(result);
		if(result.status)
		{
			window.location.href = result.msg;
		}
		else
		{
			alert(result.msg);
		}
	})

}

function desktop_del(){
	if(confirm('确认清空数据,请谨慎操作?')){
		$.post(admin.url+'desktop/desktop_del','',function (){
			location.reload();
		})
	}
}


</script>