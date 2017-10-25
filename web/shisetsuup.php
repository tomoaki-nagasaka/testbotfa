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
$meisho= $_POST['meisho'];
$jusho= $_POST['jusho'];
$tel= $_POST['tel'];
$j1= $_POST['j1'];
$j2= $_POST['j2'];
$lat= $_POST['lat'];
$lng= $_POST['lng'];
$iurl= $_POST['iurl'];
$url= $_POST['url'];


//error_log("user:".$user." age:".$age." sex:".$sex." region:".$region);

if ($link) {
	$sql = "INSERT INTO shisetsu (meisho, jusho, tel, genre1, genre2, genre3, lat, lng, imageurl, url, geom) VALUES
                                 ('{$meisho}','{$jusho}','{$tel}','{$j1}','{$j2}','','{$lat}','{$lng}','{$iurl}','{$url}',GeomFromText('POINT(35.69641 139.40641)',4326))";
	$result_flag = pg_query($sql);
	if (!$result_flag) {
		error_log("インサートに失敗しました。".pg_last_error());
	}
}


?>

