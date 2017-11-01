<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

//引数
$gid1 = $_POST['gid1'];
$gid2 = $_POST['gid2'];

error_log("★★★★★★★★★★★ gid1:".$gid1." gid2:".$gid2);

if ($link) {
	$result = pg_query("DELETE FROM genre WHERE gid1 = {$gid1} AND gid2 = {$gid2}");

}

?>

