/*
By Ivan Hanloth
本文件为易传客户端js文件
2022/4/16
*/
layui.use(function () {
	var form = layui.form,
		$ = layui.jquery,
		element = layui.element,
		upload = layui.upload,
		layer = layui.layer;

	//自定义验证规则
	form.verify({
		get: function (value) {
			if (value.length != 4) {
				return '提取码为4位';
			}
		}
	});
	//监听提交
	form.on('submit(getbtn)', function (data) {
		$.ajax({
			//定义提交的方式
			type: "POST",
			//定义要提交的URL
			url: '/function/get_data.php',
			//定义提交的数据类型
			dataType: 'json',
			async: false,
			//要传递的数据
			data: {
				'key': JSON.stringify(data.field)
			},
			//服务器处理成功后传送回来的json格式的数据
			success: function (res) {
				if (res.code == 200) { //返回存在该提取码
					$("#input")
						.addClass("layui-hide");
					layer.msg("获取成功", {
						icon: 1
					});
					$("#result")
						.removeClass("layui-hide");
					$("#result-info")
						.html('<span>剩余查看次数:</span><span style="color: #FF5722;">' + res.times + '</span><br><span>到期时间:</span><span style="color: #FF5722;">' + res.tillday + '</span>')
					if (res.type == 1) { //为文件型s
						$("#result-download-btn")
							.removeClass("layui-hide");
						$("#result-file")
							.removeClass("layui-hide");
						$("#result-url")
							.attr("value", res.data);
						$("#result-download")
							.attr("href", res.data);
					} else {
						if (res.type == 2) { //为文本型
							$("#result-text")
								.removeClass("layui-hide");
							$("#result-value")
								.val(res.data);
						}
					}
				} else { //返回不存在该提取码
					layer.msg(res.tip, {
						icon: 2
					});
				}
			},

			error: function () {
				layer.msg('出现异常，请重试', {
					icon: 2
				});
			}
		});
		return false;
	});

	//自定义验证规则
	form.verify({
		text: function (value) {
			if (value.length > 5000) {
				return '文本不能超过5000字符';
			}
		}
	});
	//监听提交
	form.on('submit(save)', function (data) {
		$.ajax({
			//定义提交的方式
			type: "POST",
			//定义要提交的URL
			url: '/function/save_text.php',
			//定义提交的数据类型
			dataType: 'json',
			async: false,
			//要传递的数据
			data: {
				'data': JSON.stringify(data.field)
			},
			//服务器处理成功后传送回来的json格式的数据
			success: function (res) {
				if (res.code == 200) {
					$("#textinfo")
						.html('<span>提取码:</span><span style="color: #FF5722;">' + res.key + '</span><br><span>剩余查看次数:</span><span style="color: #FF5722;">' + res.times + '</span><br><span>到期时间:</span><span style="color: #FF5722;">' + res.tillday + '</span>')
					$('#text')
						.addClass('layui-hide');
					$('#textbtn')
						.addClass('layui-hide');
					layer.msg('上传完毕', {
						icon: 1
					});
				}
			},

			error: function () {
				layer.msg('出现异常，请重试', {
					icon: 2
				});
			}
		});
		return false;
	});

	var uploader = upload.render({
		elem: '#upload',
		auto: false,
		accept: 'file',
		bindAction: '#uploadAction',
		size: 102400000,
		url: '/function/upload_file.php',
		choose: function (obj) {

			element.progress('progress', '0%'); //进度条复位
			var files = this.files = obj.pushFile();

			obj.preview(function (index, file, result) {
				if (file.name.length > 10) {
					var filename = file.name.substring(0, 10) + " ...";
				} else {
					var filename = file.name;
				}
				$("#localinfo")
					.html('文件名称：' + filename + '&nbsp;&nbsp;文件大小：' + (file.size / 1048517)
						.toFixed(1) + 'Mb');
			});
			layui.$('#uploadinfo')
				.removeClass('layui-hide');
			layui.$('#uploadprogress')
				.removeClass('layui-hide');
			$('#key')
				.html('');
		},
		before: function (obj) {
			element.progress('progress', '0%'); //进度条复位
			layer.msg('上传中', {
				icon: 16,
				time: 0
			});
			layui.$('#uploadprogress')
				.removeClass('layui-hide');
			$('#uploadAction')
				.addClass('layui-hide');
			$('#key')
				.html('');
		},
		done: function (res, index, upload) {
			//假设code=0代表上传成功
			if (res.code == 200) {
				$('#tip')
					.html('');
				$('#upload')
					.addClass('layui-hide');
				$('#uploadAction')
					.addClass('layui-hide');
				$('#reload-tip')
					.addClass('layui-hide');
				$("#fileinfo")
					.html('<span>提取码:</span><span style="color: #FF5722;">' + res.key + '</span><br><span>剩余查看次数:</span><span style="color: #FF5722;">' + res.times + '</span><br><span>到期时间:</span><span style="color: #FF5722;">' + res.tillday + '</span>')
				layer.msg('上传完毕', {
					icon: 1
				});
			}
			//上传成功的一些操作
			//…… //置空上传失败的状态
		},

		error: function () {
			layer.msg('出现异常，请重试', {
				icon: 2
			});
			$('#reload-tip')
				.html('<button type="button" class="layui-btn btn" id="reload">重新上传</button>');
			$('#reload-tip')
				.find('#reload')
				.on('click', function () {
					uploader.upload();
				});
		},
		progress: function (n, elem, e) {
			element.progress('progress', n + '%'); //可配合 layui 进度条元素使用
		}
	});
});