<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"D:\phpStudy\WWW\tp5shop\public/../application/home\view\login\register.html";i:1523501845;}*/ ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<title>个人注册</title>


    <link rel="stylesheet" type="text/css" href="/static/home/css/all.css" />
    <link rel="stylesheet" type="text/css" href="/static/home/css/pages-register.css" />
</head>

<body>
	<div class="register py-container ">
		<!--head-->
		<div class="logoArea">
			<a href="" class="logo"></a>
		</div>
		<!--register-->
		<div class="registerArea">
			<h3>注册新用户<span class="go">我有账号，去<a href="<?php echo url('login'); ?>">登陆</a></span></h3>
			<div class="info">
				<form class="sui-form form-horizontal" action="<?php echo url('phone'); ?>" method="post">
					<div class="control-group">
						<label class="control-label">手机号：</label>
						<div class="controls">
							<input type="text" id="phone" name="phone" placeholder="请输入你的手机号" class="input-xfat input-xlarge">
						</div>
					</div>
					<div class="control-group">
						<label for="inputPassword" class="control-label">验证码：</label>
						<div class="controls">
							<input type="text" name="code" placeholder="验证码" class="input-xfat input-xlarge" style="width:120px">
							<button type="button" class="btn-xlarge" id="dyMobileButton">发送验证码</button>
						</div>
					</div>
					<div class="control-group">
						<label for="inputPassword" class="control-label">登录密码：</label>
						<div class="controls">
							<input type="password" name="password" placeholder="设置登录密码" class="input-xfat input-xlarge">
						</div>
					</div>
					<div class="control-group">
						<label for="inputPassword" class="control-label">确认密码：</label>
						<div class="controls">
							<input type="password" name="repassword" placeholder="再次确认密码" class="input-xfat input-xlarge">
						</div>
					</div>
					<div class="control-group">
						<label for="inputPassword" class="control-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						<div class="controls">
							<input name="m1" type="checkbox" value="2" checked=""><span>同意协议并注册《品优购用户协议》</span>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"></label>
						<div class="controls btn-reg">
							<a class="sui-btn btn-block btn-xlarge btn-danger reg-btn" href="javascript:;" target="_blank">完成注册</a>
						</div>
					</div>
				</form>
				<div class="clearfix"></div>
			</div>
		</div>
		<!--foot-->
		<div class="py-container copyright">
			<ul>
				<li>关于我们</li>
				<li>联系我们</li>
				<li>联系客服</li>
				<li>商家入驻</li>
				<li>营销中心</li>
				<li>手机品优购</li>
				<li>销售联盟</li>
				<li>品优购社区</li>
			</ul>
			<div class="address">地址：北京市昌平区建材城西路金燕龙办公楼一层 邮编：100096 电话：400-618-4000 传真：010-82935100</div>
			<div class="beian">京ICP备08001421号京公网安备110108007702
			</div>
		</div>
	</div>


<script type="text/javascript" src="/static/home/js/all.js"></script>
<script type="text/javascript" src="/static/home/js/pages/register.js"></script>
	<script>
		$(function(){
			//给“发送验证码”绑定点击事件
			$('#dyMobileButton').click(function(){
				var phone = $('#phone').val();
				//判断
				if(phone == ''){
					return;
				}
				var _this = this;
				//禁用button的点击效果
				_this.disabled = true;
				//发送ajax
				$.ajax({
					'url':'<?php echo url("sendcode"); ?>',
					'type':'post',
					'data':{'phone': phone},
					'dataType':'json',
					'success':function(response){
						alert(response.msg);
						return;
					}
				});
				//设置定时器
				var time = 6;
				var interval = setInterval(function(){
					if(time > 0){
						//每秒执行一次
						time--;
						_this.innerHTML = '重新发送：' + time + 's';
					}
					if(time == 0){
						//清除定时器，button恢复正常
						time = 6;
						_this.innerHTML = '发送验证码';
						_this.disabled = false;
						clearInterval(interval);
					}
				}, 1000, time, _this);
			});

			//注册
			$('.reg-btn').click(function(){
				$('form').submit();
			});
		});
	</script>
</body>

</html>