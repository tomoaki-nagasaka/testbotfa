<?php

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

		header('Content-type: image/jpeg');
		header("Content-Disposition: inline; filename=image.jpg");
		$img_data=pg_unescape_bytea($row[0]);
		echo $img_data;
	}
}

?>
