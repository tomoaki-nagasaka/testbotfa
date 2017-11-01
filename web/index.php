<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Menu</title>
</head>
<body>
<div id="haader"></div>
<input type="button" onclick="location.href='botlog.php'" value="ログ参照" />
<input type="button" onclick="location.href='imagelog.php'" value="画像ログ参照" />
<input type="button" onclick="location.href='rank.php'" value="ランキング" />
<input type="button" onclick="location.href='attribute.php'" value="属性登録" />
<input type="button" onclick="location.href='shisetsu.php'" value="施設情報" />
<input type="button" onclick="location.href='genre.php'" value="施設ジャンル" />
</body>
<script>
$(function(){
	$("#header").load("header.html");
});
</script>
</html>

