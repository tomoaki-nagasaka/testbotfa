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
$user = $_POST['user'];
$age= $_POST['age'];
$sex= $_POST['sex'];
$region= $_POST['region'];


//error_log("user:".$user." age:".$age." sex:".$sex." region:".$region);

if ($link) {
	$result = pg_query("SELECT * FROM userinfo WHERE userid = '{$user}'");
	if (pg_num_rows($result) == 0) {
		$sql = "INSERT INTO userinfo (userid, sex, age, region) VALUES ('{$user}','{$sex}','{$age}','{$region}')";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("インサートに失敗しました。".pg_last_error());
		}
	}else{
		$sql = "UPDATE userinfo SET sex = '{$sex}', age = '{$age}' , region = '{$region}' WHERE userid = '{$user}'";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("アップデートに失敗しました。".pg_last_error());
		}
	}
}


?>

