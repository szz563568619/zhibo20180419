<?php if(!$filed): ?>
<link rel="stylesheet" type="text/css" href="plugins/webuploader/webuploader.css">
<script src="plugins/webuploader/webuploader.min.js"></script>
<?php endif; ?>
<div class="form-group">
	<label for="img_name" class="col-sm-2 control-label"><?php echo $title; ?></label>
	<div class="col-sm-10">
		<input type="hidden" name="<?php echo $name; ?>" id="<?php echo $name; ?>" >
		<div id="<?php echo $name; ?>List" class="uploader-list"></div>
		<div id="<?php echo $name; ?>Picker">选择图片</div>
		<?php
		if(!is_dir($img) and file_exists($img)):
		?>
		<div>
			<a href="<?php echo $img; ?>" target="_blank"><img src="<?php echo $img; ?>" style="max-height:100px; border:1px solid #ccc;"></a>
		</div>
		<?php endif ?>
	</div>
	
</div>
<script>
jQuery(function() {

	var $ = jQuery,
		$list = $('#<?php echo $name; ?>List'),
		// 优化retina, 在retina下这个值是2
		ratio = window.devicePixelRatio || 1,

		// 缩略图大小
		thumbnailWidth = 100 * ratio,
		thumbnailHeight = 100 * ratio,

		// Web Uploader实例
		uploader;

	// 初始化Web Uploader
	uploader = WebUploader.create({

		// 自动上传。
		auto: true,

		// swf文件路径
		swf: admin.url+'plugins/webuploader/Uploader.swf',

		// 文件接收服务端。
		server: admin.url+'elements/upload_img',

		// 选择文件的按钮。可选。
		// 内部根据当前运行是创建，可能是input元素，也可能是flash.
		pick: {
			id : '#<?php echo $name; ?>Picker',
			//是否同时选择多个图片
			multiple: false
		},

		//限制上传文件数量
		fileNumLimit: 1,

		// 只允许选择文件，可选。
		accept: {
			title: 'Images',
			extensions: 'gif,jpg,jpeg,bmp,png',
			mimeTypes: 'image/*'
		}
	});

	// 当有文件添加进来的时候
	uploader.on( 'fileQueued', function( file ) {
		var $li = $(
				'<div id="' + file.id + '" class="file-item thumbnail">' +
					'<img>' +
					'<div class="info">' + file.name + '</div>' +
				'</div>'
				);
			$img = $li.find('img');

		$list.append( $li );

		// 创建缩略图
		uploader.makeThumb( file, function( error, src ) {
			if ( error ) {
				$img.replaceWith('<span>不能预览</span>');
				return;
			}

			$img.attr( 'src', src );
		}, thumbnailWidth, thumbnailHeight );
		// alert(1);
		// return;
	});

	// 文件上传过程中创建进度条实时显示。
	uploader.on( 'uploadProgress', function( file, percentage ) {
		var $li = $( '#'+file.id ),
			$percent = $li.find('.progress span');

		// 避免重复创建
		if ( !$percent.length ) {
			$percent = $('<p class="progress"><span></span></p>')
					.appendTo( $li )
					.find('span');
		}

		$percent.css( 'width', percentage * 100 + '%' );
	});

	// 文件上传成功，给item添加成功class, 用样式标记上传成功。
	uploader.on( 'uploadSuccess', function( file ) {
		$( '#'+file.id ).addClass('upload-state-done');
	});

	// 文件上传失败，现实上传出错。
	uploader.on( 'uploadError', function( file ) {
		var $li = $( '#'+file.id ),
			$error = $li.find('div.error');

		// 避免重复创建
		if ( !$error.length ) {
			$error = $('<div class="error"></div>').appendTo( $li );
		}

		$error.text('上传失败');
	});

	// 完成上传完了，成功或者失败，先删除进度条。
	uploader.on( 'uploadComplete', function( file ) {
		$( '#'+file.id ).find('.progress').remove();

		//若要删除该图片重新上传
		$( '#'+file.id ).on('click', function (){
			uploader.removeFile( file );
			$( '#'+file.id ).remove();
			$('#img').attr('value', '');
		})
	});

	//
	uploader.on('uploadAccept', function(obj, data){
		if(data.status){
			$('#<?php echo $name; ?>').attr('value', data.img);
		}
		return data.status;
	});

	
});
</script>