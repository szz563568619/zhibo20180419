<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">问题列表</h3>
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
                            <th>用户名</th>
                            <th>问题</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($wenda as $v): ?>
                            <tr>
                                <td><?php echo $v['uname']; ?></td>
                                <td><?php echo $v['content']; ?></td>
                                <td><?php echo $v['time']; ?></td>
                                <td>
                                    <a href="wenda/edit_wenda/<?php echo $v['id']; ?>" class="btn btn-primary"><?php echo $v['is_huida']?'继续回答':'回答'; ?></a>
                                    <button type="button" class="btn btn-primary" onclick="set_show(<?php echo $v['id']; ?>,<?php echo $v['is_show']; ?>)"><?php echo $v['is_show'] == 1 ? '取消显示' : '设为显示'; ?></button>
                                    <button type="button" class="btn btn-danger" onclick="del_wen(<?php echo $v['id']; ?>)">删除</button>
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
<script>
    function del_wen(id){
        if(confirm('确认删除该问题？该问题下的所有回答也会删除！')){
            $.get(admin.url+'wenda/del_wen/'+id,
                function (){
                    location.reload();
                })
        }
    }
	function set_show(id,is_show){
		if(is_show == 1) is_show = 0;
		else is_show = 1;
		$.get(admin.url+'wenda/set_show/'+id+'/'+is_show,
                function (){
                    location.reload();
                })
	}
</script>