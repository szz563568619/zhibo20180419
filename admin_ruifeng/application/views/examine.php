<style>.data_list{word-break: break-all;}</style>
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">聊天审核</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <form class="form-inline">
                    <div class="form-group">
                        <label>消息自动审核</label>
                            <?php $auto_examine = my_echo($room_info['auto_examine'], 1); ?>
                            <label class="radio-inline">
                                <input type="radio" name="auto_examine" value="1" <?php if($auto_examine == 1): ?>checked<?php endif; ?>> 是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="auto_examine" value="0" <?php if($auto_examine == 0): ?>checked<?php endif; ?>> 否
                            </label>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="update_room_examine()">保存</button>
                </form>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th style="width:30px;"><input class="selectall" type="checkbox" onchange="selectall();"></th>
                            <!-- <th style="width:65px;">房间ID</th> -->
                            <th>内容</th>
                            <th style="width:200px;">发布人</th>
                            <th style="width:160px;">发布时间</th>
                            <th style="width: 218px;">操作</th>
                        </tr>
                        </thead>
                        <tbody class="data_list">
                        <?php
                        foreach($chat_list as $k => $de_v):
                            ?>
                            <tr class="list_<?php echo $de_v['score']; ?>">
                                <td><input type="checkbox" name="examine[]" value="<?php echo $de_v['score']; ?>" data-rid="<?php echo $de_v['rid']; ?>"></td>
                                <!-- <td><?php echo $de_v['rid']; ?></td> -->
                                <td><?php echo $de_v['content']; ?></td>
                                <td><img src="../skin/gupiao0306/images/level/level<?php echo $de_v['gid']; ?>.png" />&nbsp;&nbsp;<?php echo $de_v['name']; ?></td>
                                <td><?php echo $de_v['time']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary" onclick="release(<?php echo $de_v['score']; ?>,'<?php echo $de_v['rid']; ?>')">发布</button>
                                    <button type="button" class="btn btn-danger" onclick="del('<?php echo $de_v['score']; ?>', '<?php echo $de_v['rid']; ?>')">删除</button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" onclick="select_toggle()">全选</button>
                    <button type="button" class="btn btn-primary" onclick="batch_release()">批量发布</button>
                    <button type="button" class="btn btn-primary" onclick="bath_del()">批量删除</button>
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

    var ID = <?php echo $this->session->userdata("id"); ?>;
    var socket = io('http://<?php echo $socket_url; ?>:<?php echo $socket_port; ?>');

    socket.on('connect', function (){
        var uid = 'admin_'+ID;
        socket.emit('admin_login', {'uid' : uid,});
    });

    socket.on('examine_public_msg', function (data){
        deal_data($.parseJSON(data));
    })

    function deal_data(data){
        var html = '';

        html += '<tr class="list_'+data.score+'" ';
        if(data.types == 1) html += ' style="color:red;" '; /*内部发言以红色表示*/
        html += '><td><input type="checkbox" name="examine[]" value="'+data.score+'" data-rid="\''+data.rid+'\'"></td><td>'+data.content+'</td><td><img src="../skin/gupiao0306/images/level/level'+ data.gid + '.png" />&nbsp;&nbsp;'+data.name+'</td><td>'+data.time+'</td><td><button type="button" class="btn btn-primary" onclick="release('+data.score+',\''+data.rid+'\')">发布</button><button type="button" class="btn btn-danger" onclick="del('+data.score+', \''+data.rid+'\')">删除</button></td></tr>';
        $('.data_list').append(html);
    }

    function selectall(){
        $('input[name^=examine]').prop('checked', $('.selectall').is(':checked'));
    }

    function select_toggle(){
        var status = $('.selectall').is(':checked');
        if(status) status = false;
        else status = true;
        $('input[name^=examine], .selectall').prop('checked', status);
    }

    function del(id,rid){
        $.post(admin.url+'examine/del',
            {id:id,rid:rid},
            function (){
                $('.list_'+id).remove();
            })
    }

    function bath_del(){
        var examine = $('input[name^=examine]:checked');
        var ids = new Array();;
        var rids = new Array();;
        var len = examine.length;
        for(var i = 0; i < len; i++){
            ids.push($(examine[i]).val());
            rids.push($(examine[i]).data('rid'));
            $('.list_'+$(examine[i]).val()).remove();
        }
        $.post(admin.url+'examine/del',
            {id:ids, rid:rids},
            function (){})
    }

    function release(id,rid){
        $.post(admin.url+'examine/release',
            {id:id,rid:rid},
            function (){
                $('.list_'+id).remove();
            })
    }

    function batch_release(){
        var examine = $('input[name^=examine]:checked');
        var ids = new Array();;
        var rids = new Array();;
        var len = examine.length;
        for(var i = 0; i < len; i++){
            ids.push($(examine[i]).val());
            rids.push($(examine[i]).data('rid'));
            $('.list_'+$(examine[i]).val()).remove();
        }
        $.post(admin.url+'examine/release',
            {id:ids, rid:rids},
            function (){})
    }

    function update_room_examine(){
        var auto_examine = $('input[name=auto_examine]:checked').val();
        $.post(admin.url+'examine/update_room_examine',
            {auto_examine:auto_examine},
            function (){
                alert('保存成功！');
                location.reload();
            })
    }

</script>