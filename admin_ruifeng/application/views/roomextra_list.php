<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">房间底部信息管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                房间底部信息列表 <a href="roomextra/edit_roomextra/" class="btn btn-primary">添加房间底部信息</a>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>对应域名</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $v): ?>
                            <tr>
                                <td><?php echo my_echo($v['id']); ?></td>
                                <td><?php echo my_echo($v['domain']); ?></td>
                                <td>
                                    <a href="roomextra/edit_roomextra/<?php echo my_echo($v['id']); ?>" class="btn btn-primary">编辑</a>
                                    <button type="button" class="btn btn-danger" onclick="del_roomextra(<?php echo my_echo($v['id']); ?>)">删除</button>
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
    function del_roomextra(id){
        if(confirm('确认删除该消息？请谨慎操作！')){
            $.post(admin.url+'roomextra/del_roomextra',
                {'id': id},
                function (){
                    location.reload();
                })
        }
    }
</script>