<!DOCTYPE html style="width: 100%; height: 100%">
<html lang="zh">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<base href="<?php echo base_url(); ?>">
	<script type="text/javascript" src="js/echarts.min.js"></script>
</head>
<body>
<div id="container" style="width: 100%; height:584px"></div>
<script type="text/javascript">
	var dom = document.getElementById("container");
	var myChart = echarts.init(dom);
	var data = [];
	option = null;
	option = {
		tooltip: {
			trigger: 'axis'
		},
		xAxis: {
			type: 'category',
			interval: 3600000*12,
			data:<?php echo $data['date']; ?>,
			boundaryGap: true
		},
		yAxis: {
			type: 'value',
			min: 0,
			boundaryGap: [0.2, 0.2]
		},
		series: [{
			name: '当前数值',
			type: 'bar',
			data:<?php echo $data['count']; ?>
		}]
	};
	myChart.setOption(option, true);
</script>
</body>
</html>