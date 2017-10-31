<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Menu</title>
</head>
<body>
<input type="button" onclick="location.href='botlog.php'" value="ログ参照" />
<input type="button" onclick="location.href='imagelog.php'" value="画像ログ参照" />
<input type="button" onclick="location.href='rank.php'" value="ランキング" />
<input type="button" onclick="location.href='attribute.php'" value="属性登録" />
<input type="button" onclick="location.href='shisetsu.php'" value="施設情報" />
<input type="button" onclick="location.href='genre.php'" value="施設ジャンル" />
<img src="getimage.php?id=2" />

<?php

/*
//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続


$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

if ($link) {
	$result = pg_query("SELECT image FROM logimage ORDER BY no DESC");
	while ($row = pg_fetch_row($result)) {
		echo "<br>";
		//header('Content-type: image/jpeg');
		$img_data=pg_unescape_bytea($row[0]);
		print ($img_data);
		//echo "<img src='".$img_data."' alt='' />";
		echo $img_data;
	}
}
*/

?>
</body>
</html>

