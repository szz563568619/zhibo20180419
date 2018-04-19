<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">老师战法管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                战法列表 
				<span class="dropdown">
				  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<?php echo isset($curteacher)?$curteacher['name']:'选择老师'; ?>
					<span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<?php foreach($teachers as $v): ?>
					<li><a href="tactics?tid=<?php echo $v['id']; ?>"><?php echo $v['name']; ?></a></li>
					<?php endforeach; ?>
				  </ul>
				</span>
				<a href="tactics/edit_tactics/" class="btn btn-primary">添加战法</a>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>战法标题</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($tactics_list as $v): ?>
                            <tr>
                                <td><?php echo $v['id']; ?></td>
                                <td><?php echo $v['title']; ?></td>
                                <td><?php echo $v['create_time']; ?></td>
                                <td>
                                    <a href="tactics/edit_tactics/<?php echo $v['id']; ?>" class="btn btn-primary">编辑</a>
                                    <button type="button" class="btn btn-danger" onclick="del_tactics(<?php echo $v['id']; ?>)">删除</button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
					<?php echo $pagin; ?>
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
    function del_tactics(id){
        if(confirm('确认删除该文章？该操作不可恢复，请谨慎操作！')){
            $.get(admin.url+'tactics/tactics_del/'+id,
                function (){
                    location.reload();
                })
        }
    }
</script>