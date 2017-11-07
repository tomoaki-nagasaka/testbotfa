<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Menu</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<link href="css/jquery.bootgrid.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
</head>
<body>
<div id="header"></div>
<div class="container">
	<div class="center-block">
		<input type="button" class="btn btn-default" onclick="location.href='botlog.php'" value="ログ参照" />
		<input type="button" class="btn btn-default" onclick="location.href='imagelog.php'" value="画像ログ参照" />
		<input type="button" class="btn btn-default" onclick="location.href='shisetsu.php'" value="施設情報" />
		<input type="button" class="btn btn-default" onclick="location.href='genre.php'" value="施設ジャンル" />
		<input type="button" class="btn btn-default" onclick="location.href='opinion.php'" value="市政へのご意見" />
	</div>
</div>
</body>
<script>
$(function(){
	$("#header").load("header.html");
});
</script>
</html>

