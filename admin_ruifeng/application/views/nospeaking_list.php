<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">用户禁言</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="height: 55px;">
				<div class="col-lg-4 col-md-8">
					<div class="input-group">
						<input type="text" name="forbidden" class="form-control">
						<span class="input-group-btn">
							<a class="btn btn-danger" onclick="ip_save()"><i class="fa fa-fw fa-ban"></i>屏蔽该IP（用户）</a>
						</span>
					</div>
				</div>
				<form>
                    <div class="col-lg-4 col-md-8">
                        <div class="input-group">
                            <input type="text" name="name" class="form-control">
							<span class="input-group-btn">
								<a class="btn btn-info" onclick="ip_search()"><i class="fa fa-fw fa-search"></i>查找IP</a>
							</span>
                        </div>
                    </div>
                </form>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>IP(用户名)</th>
								<th>屏蔽人</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($nospeaking_list as $k => $v):
								$k = str_replace('nospeaking_', '', $k);
							?>
							<tr>
								<td><?php echo $k; ?></td>
								<td><?php echo $v; ?></td>
								<td>
									<button type="button" class="btn btn-danger" onclick="ip_del(this, '<?php echo $k;?>')">删除</button>
								</td>
							</tr>
							<?php endforeach ?>
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
<script src="js/modal.js"></script>
<!-- Modal -->
    <table class="table table-striped table-bordered table-hover mytan modal fade col-lg-4" id="myModal" tabindex="-1" role="dialog" style="overflow-y: auto;top: 40%;left: 30%;width:600px;">
        <thead>
        <tr class="success">
            <td>Name</td>
            <td>IP</td>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
<script type="text/javascript">
function ip_del(obj, forbidden){
	$.post(admin.url+'nospeaking/nospeaking_del/',{forbidden:forbidden},function (){
		$($(obj).parent()).parent().remove();
	})
}

//保存屏蔽ip
function ip_save(){
    var forbidden = $('input[name=forbidden]').val();
    $.post(admin.url+'nospeaking/nospeaking_ban',{forbidden:forbidden},function (result){
        result = $.parseJSON(result);
        if(!result.status)
        {
            alert(result.msg);
        }
        else
        {
			location.reload();
        }
    })
}
//查询ip
var jso;
function ip_search(){
    var name = $('input[name=name]').val();
    $.post(admin.url+'nospeaking/nospeaking_search',{name:name},function (res){
        if(res == '[]'){
            alert("没有相关用户信息！");
        }else{
			var j = "(" + res + ")"; // 用括号将json字符串括起来
			jso = eval(j); // 返回json对象
			for(var i in jso)
			{
				if(jso[i].ip == null)
				{
					jso[i].ip = '';
				}
				$('.mytan tbody').html("<tr class='success'><td>"+name+"</td> <td>"+jso[i].ip+"</td> </tr>");
			}
			$('.mytan').modal('show');
		}
    })
}
</script>