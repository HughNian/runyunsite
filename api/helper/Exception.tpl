<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统发生错误</title>
<style type="text/css">
::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }
*{ padding: 0; margin: 0; }
html{ overflow-y: scroll; }
body{ margin: 40px; background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
img{ border: 0; }
.error{ padding: 24px 48px; margin: 10px;border: 1px solid #D0D0D0;-webkit-box-shadow: 0 0 8px #D0D0D0;}
.face{ font-size: 60px; font-weight: normal; line-height: 120px; margin-bottom: 12px; color:#d23939; }
h1{color: #444;background-color: transparent;border-bottom: 1px solid #D0D0D0;font-size:40px;font-weight: normal;margin: 0 0 14px 0;padding: 14px 15px 10px 15px; }
.error .content{ padding-top: 10px}
.error .info{ margin-bottom: 12px; }
.error .info .title{ margin-bottom: 3px; }
.error .info .title h3{ color: #5CB7FD; font-weight: 700; font-size: 16px; }
.error .info .text{ line-height: 24px; }
.error .info .text p {background-color:#CFE7F6;}
.copyright{ padding: 12px 48px; color: #999; }
.copyright a{ color: #000; text-decoration: none; }
</style>
</head>
<body>
<div class="error">
<p class="face">Exception page!</p>
<h1><?php echo strip_tags($e['message']);?></h1>
<div class="content">
<?php if(isset($e['file'])) {?>
	<div class="info">
		<div class="title">
			<h3>错误位置</h3>
		</div>
		<div class="text">
			<p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
		</div>
	</div>
<?php }?>
<?php if(isset($e['trace'])) {?>
	<div class="info">
		<div class="title">
	    <h3>TRACE</h3>
		</div>
		<div class="text">
			<p><?php echo nl2br($e['trace']);?></p>
		</div>
	</div>
<?php }?>
</div>
</div>
<div class="copyright">
<p>niansong's simple php framework</p>
</div>
</body>
</html>
