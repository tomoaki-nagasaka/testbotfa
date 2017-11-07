<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>施設登録</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
</head>
<div id="header"></div>
<body>
<div class="container">
	<p  style="display:inline;">施設名称</p>
	<input id="meisho" class="form-control" maxlength="40" placeholder="行政公園" style="width: 600px;">
	<br>
	<p style="display:inline;">住所</p>
	<input id="jusho" class="form-control" maxlength="128" placeholder="行政市行政1-1-1"  style="width: 600px;">
	<br>
	<p style="display:inline;">電話番号</p>
	<input id="tel" class="form-control" type="tel" maxlength="14" placeholder="000-000-0000"  style="width: 600px;">
	<br>
	<p style="display:inline;">ジャンル１</p>
	<select class="form-control" id="j1"  onChange="j1change()" style="width: 600px;">
	</select>
	<br>
	<p style="display:inline;">ジャンル２</p>
	<select class="form-control" id="j2" style="width: 600px;">
	</select>
	<br>
	<p style="display:inline;">緯度・経度</p>
	<input id="latlng" class="form-control" maxlength="33" placeholder="999.99999,999.99999" style="width: 600px;">
	<input type="button" class="btn btn-default" onclick="map()" value="地図の確認" style="width: 100px;"/>
	<br><br>
	<p style="display:inline;">画像ＵＲＬ</p>
	<input id="iurl" class="form-control" maxlength="300" placeholder="https://www.yyy.zzz.jpg" style="width: 600px;">
	※必ずhttpsから始まるURLを指定してください
	<br><br>
	<p style="display:inline;">詳細ＵＲＬ</p>
	<input id="url" class="form-control" maxlength="300" placeholder="http://www.yyy.zzz.html" style="width: 600px;">
	<br>
	<input type="button" class="btn btn-default" onclick="clearform()" value="クリア" />
	<input type="button" class="btn btn-default" onclick="update()" value="更新" />
	<input type="button" class="btn btn-default" onclick="back()" value="もどる" />
</div>

<?php

$id = "";
$meisho = "";
$jusho= "";
$tel= "";
$genre1= "";
$genre2= "";
$lat= "";
$lng= "";
$imageurl= "";
$url = "";

//ジャンル
$j1value = array();
$j2value = array();

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

if( array_key_exists( 'id',$_GET ) ) {

	//引数
	$id = $_GET['id'];

	//error_log("★★★★★★★★★★★★★★★id:".$id);

	if ($link) {
		$result = pg_query("SELECT meisho, jusho, tel, genre1, genre2, lat, lng, imageurl, url FROM shisetsu WHERE id = '{$id}'");
		$row = pg_fetch_row($result);
		$meisho = $row[0];
		$jusho= $row[1];
		$tel= $row[2];
		$genre1= $row[3];
		$genre2= $row[4];
		$lat= $row[5];
		$lng= $row[6];
		$imageurl= $row[7];
		$url = $row[8];
	}
}

if ($link) {
	$result = pg_query("SELECT * FROM genre WHERE bunrui = 1");
	while ($row = pg_fetch_row($result)) {
		$j1value = $j1value + array($row[1] => $row[4]);
	}

	foreach($j1value as $key => $value){
		$result = pg_query("SELECT * FROM genre WHERE bunrui = 2 AND gid1 = {$key}");
		$arr = array();
		while ($row = pg_fetch_row($result)) {
			$arr = $arr + array($row[2] => $row[4]);
		}
		$j2value = $j2value + array($key => $arr);
	}
}
?>
<script>
var id = "";
var meisho = "";
var jusho = "";
var tel = "";
var j1 = "";
var j2 = "";
var latlng = "";
var lat = "";
var lng = "";
var iurl = "";
var url = "";

$(function(){
	$("#header").load("header.html");
	meisho = <?php echo json_encode($meisho); ?>;
	jusho = <?php echo json_encode($jusho); ?>;
	tel = <?php echo json_encode($tel); ?>;
	j1 = <?php echo json_encode($genre1); ?>;
	j2 = <?php echo json_encode($genre2); ?>;
	lat = <?php echo json_encode($lat); ?>;
	lng = <?php echo json_encode($lng); ?>;
	iurl = <?php echo json_encode($imageurl); ?>;
	url = <?php echo json_encode($url); ?>;

	//ジャンルの設定
	var j1value = <?php echo json_encode($j1value); ?>;
	var select = document.getElementById('j1');

	for( var key in j1value ) {
		var option = document.createElement('option');
		option.setAttribute('value', key);
		var text = document.createTextNode(j1value[key]);
		option.appendChild(text);
		select.appendChild(option);
	}
	j1change();

	if(meisho != ""){
		document.getElementById('meisho').value = meisho;
		document.getElementById('jusho').value = jusho;
		document.getElementById('tel').value = tel;
		document.getElementById('j1').value = j1;
		j1change();
		document.getElementById('j2').value = j2;
		document.getElementById('latlng').value = lat + "," + lng;
		document.getElementById('iurl').value = iurl;
		document.getElementById('url').value = url;
	}

});

//ジャンル選択
function j1change(){
	var select = document.getElementById('j2');
	while (0 < select.childNodes.length) {
		select.removeChild(select.childNodes[0]);
	}

	var j2value = <?php echo json_encode($j2value); ?>;
	var janru = j2value[document.getElementById('j1').value];

	for( var key in janru ) {
		var option = document.createElement('option');
		option.setAttribute('value', key);
		var text = document.createTextNode(janru[key]);
		option.appendChild(text);
		select.appendChild(option);
	}

}

//クリア
function clearform(){
	document.getElementById('meisho').value = "";
	document.getElementById('jusho').value = "";
	document.getElementById('tel').value = "";
	document.getElementById('j1').selectedIndex = 0;
	document.getElementById('j2').selectedIndex = 0;
	document.getElementById('latlng').value = "";
	document.getElementById('iurl').value = "";
	document.getElementById('url').value = "";
}

//更新
function update(){
	id = <?php echo json_encode($id); ?>;
	meisho = document.getElementById('meisho').value;
	jusho = document.getElementById('jusho').value;
	tel = document.getElementById('tel').value;
	j1 = document.getElementById('j1').value;
	j2 = document.getElementById('j2').value;
	latlng = document.getElementById('latlng').value;
	var arrayOfStrings = latlng.split(",");
	lat = arrayOfStrings[0];
	lng = arrayOfStrings[1];
	iurl = document.getElementById('iurl').value;
	url = document.getElementById('url').value;
	$.ajax({
		type: "POST",
		url: "shisetsuup.php",
		data: {
			"id" : id,
			"meisho" : meisho,
			"jusho" : jusho,
			"tel" : tel,
			"j1" : j1,
			"j2" : j2,
			"lat" : lat,
			"lng" : lng,
			"iurl" : iurl,
			"url" : url
		}
	}).then(
		function(){
			alert("登録が完了しました。");
			window.location.href = "./shisetsu.php";
		},
		function(){
			alert("登録できませんでした。");
		}
	);
}

//もどる
function back(){
	window.location.href = "./shisetsu.php";
}

//地図の確認
function map(){
	latlng = document.getElementById('latlng').value;
	window.open( "http://maps.google.com/maps?q=" + latlng + "+(ココ)", '_blank');
}
</script>
</body>
</html>

