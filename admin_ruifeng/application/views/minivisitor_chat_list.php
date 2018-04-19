<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?php echo base_url(); ?>">

    <title>云杰直播平台</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/common.js"></script>
    <script src="js/bootstrap.min.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>

<!-- Page-Level Plugin Scripts - Tables -->
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
</head>



<body>

<div id="wrapper">
    <div id="page-wrapper">
		<style>.panel{margin-bottom: 10px;}</style>
<div class="row">
	<div class="col-lg-6">
		<?php foreach($chat_list as $v): ?>
		<div class="panel panel-<?php echo $v['is_visitor'] ? 'primary' : 'success'; ?>">
			<div class="panel-heading"><?php echo $v['send_name']; ?> [<?php echo $v['time']; ?>]</div>
			<div class="panel-body">
				<div class=""><?php echo $v['content']; ?></div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>
    </div>
</div>


</body>
</html>


