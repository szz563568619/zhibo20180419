<div class="middle_area">
	<div class="main">
		<div class="chat_room_head transparent_bg notice">
			<span class="chat_room_head_left"></span>
			<span class="chat_room_head_right">
				<span class="inner_chat_room_head_right">
					<ul><li><?php echo $room['shikuang']; ?></li></ul>
				</span>
			</span>
			<br style="clear: both;">
		</div>
		<div class="shipin">
			<?php
				$is_vip360 = '';
				if(stripos($_SERVER['QUERY_STRING'], 'VIP360') !== false) $is_vip360 = '_VIP360';
				echo str_replace('{name}', $this->session->userdata('name').$source.$is_vip360, $room['video']);
			?>
		</div>
		<div class="banner">
			<div class="hd">
				<ul><li class="on"></li><li></li></ul>
			</div>
			<div class="bd">
				<ul>
					<!-- <li><div class="home-banner"><a href="javascript:" class="qq_click" data-resource="轮播图" style="background: url(<?php echo $tpl; ?>images/banner/20180413.gif) center center no-repeat;"></a></div></li> -->
					<li><div class="home-banner"><a href="javascript:" class="qq_click" data-resource="轮播图" style="background: url(<?php echo $tpl; ?>images/banner/201804131.jpg) center center no-repeat;"></a></div></li>
					<li><div class="home-banner"><a href="javascript:" class="qq_click" data-resource="轮播图" style="background: url(<?php echo $tpl; ?>images/banner/201804161.jpg) center center no-repeat;"></a></div></li>
					<!-- <li><div class="home-banner"><a href="javascript:" class="qq_click" data-resource="轮播图" style="background: url(<?php echo $tpl; ?>images/banner/201804132.jpg) center center no-repeat;"></a></div></li> -->
				</ul>
			</div>
			<a class="prev" href="javascript:void(0)" target="_self"></a>
			<a class="next" href="javascript:void(0)" target="_self"></a>
		</div>
		<div class="bottom translucent01"><?php echo my_echo($domain_info['info']); ?></div>
	</div>
</div>
