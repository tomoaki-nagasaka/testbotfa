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
$uiKbn= $_POST['uiKbn'];
$bunrui= $_POST['bunrui'];
$meisho= $_POST['meisho'];
$gid1= $_POST['gid1'];
$gid2= $_POST['gid2'];

//error_log("user:".$user." age:".$age." sex:".$sex." region:".$region);

if ($link) {
	if($uiKbn == 1){
		$sql = "UPDATE genre SET meisho = '{$meisho}' WHERE gid1 = {$gid1} AND gid2 = {$gid2}";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("アップデートに失敗しました。".pg_last_error());
		}
	}else{
		if($bunrui == 1){
			$result= pg_query("SELECT gid1 FROM genre ORDER BY gid1 DESC");
			$row = pg_fetch_row($result);
			$gid1 = $row[0] + 1;
			$sql = "INSERT INTO genre (bunrui, gid1, gid2, gid3, meisho) VALUES ({$bunrui}, {$gid1}, 0, 0, '{$meisho}')";
			$result_flag = pg_query($sql);
		}else{
			$result= pg_query("SELECT gid2 FROM genre WHERE gid1 = {$gid1} ORDER BY gid2 DESC");
			$row = pg_fetch_row($result);
			$gid2 = $row[0] + 1;
			$sql = "INSERT INTO genre (bunrui, gid1, gid2, gid3, meisho) VALUES ({$bunrui}, {$gid1}, {$gid2}, 0, '{$meisho}')";
			$result_flag = pg_query($sql);
		}
		if (!$result_flag) {
			error_log("インサートに失敗しました。".pg_last_error());
		}
	}
}


?>

