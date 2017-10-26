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
$id= $_POST['id'];
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
	if(id == ""){
		$sql = "INSERT INTO shisetsu (meisho, jusho, tel, genre1, genre2, genre3, lat, lng, imageurl, url, geom) VALUES
       	                          ('{$meisho}','{$jusho}','{$tel}','{$j1}','{$j2}','','{$lat}','{$lng}','{$iurl}','{$url}',ST_GeomFromText('POINT({$lat} {$lng})',4326))";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("インサートに失敗しました。".pg_last_error());
		}
	}else{
		$sql = "UPDATE shisetsu SET meisho = '{$meisho}', jusho = '{$jusho}', tel = '{$tel}' , genre1 = '{$j1}' , genre2 = '{$j2}' , lat = '{$lat}' , lng = '{$lng}' , imageurl = '{$iurl}'
                , url = '{$url}' , geom = ST_GeomFromText('POINT({$lat} {$lng})',4326)) WHERE id = '{$id}'";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("アップデートに失敗しました。".pg_last_error());
		}
	}
}


?>

