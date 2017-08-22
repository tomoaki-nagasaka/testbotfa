<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Menu</title>
</head>
<body>
<input type="button" onclick="location.href='botlog.php'" value="ログ参照" />
<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
/*
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

if ($link) {
	$result = pg_query("SELECT contents FROM botlog ORDER BY no DESC");
	while ($row = pg_fetch_row($result)) {
		echo "<br>";
		echo $row[0];
	}
}
*/
?>
</body>
</html>

