<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>ジャンル登録</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
</head>
<body>
<div id="header"></div>
<p>分類</p>
<select id="bunrui"  onChange="bchange()">
<option value="1">大分類</option>
<option value="2">小分類</option>
</select>
<br><br>
<p>大分類名称</p>
<input id="g1meisho" maxlength="50" placeholder="大分類名称" style="width: 500px;">
<select id="g1">
</select>
<br><br>
<p>小分類名称</p>
<input id="g2meisho" maxlength="50" placeholder="小分類名称" style="width: 500px;">
<br><br>
<input type="button" onclick="update()" value="更新" />
<input type="button" onclick="back()" value="もどる" />

<?php

$gid1 = "";
$gid2 = "";
$meisho = "";

//ジャンル
$j1value = array();

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

//引数
$gid1 = $_GET['gid1'];
$gid2 = $_GET['gid2'];

//error_log("★★★★★★★★★★★★★★★id:".$id);
if($gid1 > 0){
	if ($link) {
		$result = pg_query("SELECT * FROM genre WHERE gid1 = {$gid1} AND gid2 = {$gid2}");
		$row = pg_fetch_row($result);
		$meisho = $row[4];
	}
}

if ($link) {
	$result = pg_query("SELECT * FROM genre WHERE bunrui = 1");
	while ($row = pg_fetch_row($result)) {
		$j1value = $j1value + array($row[1] => $row[4]);
	}
}
?>

<script>
var bunrui = 0;
var gid1 = 0;
var gid2 = 0;
var meisho = "";
var uiKbn = 0;

$(function(){
	$("#header").load("header.html");
	gid1 = <?php echo json_encode($gid1); ?>;
	gid2 = <?php echo json_encode($gid2); ?>;
	meisho = <?php echo json_encode($meisho); ?>;

	//ジャンルの設定
	var j1value = <?php echo json_encode($j1value); ?>;
	var select = document.getElementById('g1');

	for( var key in j1value ) {
		var option = document.createElement('option');
		option.setAttribute('value', key);
		var text = document.createTextNode(j1value[key]);
		option.appendChild(text);
		select.appendChild(option);
	}


	if(gid1 > 0){
		uiKbn = 1;
		document.getElementById('bunrui').disabled = true;
		if(gid2 > 0){
			document.getElementById('bunrui').value = 2;
			document.getElementById('g1').value = gid1;
			document.getElementById('g1').disabled = true;
			document.getElementById('g1meisho').style.display = "none";
			document.getElementById('g2meisho').value = meisho;
		}else{
			document.getElementById('bunrui').value = 1;
			document.getElementById('g1').value = gid1;
			document.getElementById('g1').style.display = "none";
			document.getElementById('g1meisho').value = meisho;
			document.getElementById('g2meisho').disabled = true;
		}
	}else{
		uiKbn = 2;
		document.getElementById('g1').style.display = "none";
		document.getElementById('g2meisho').disabled = true;
	}

});

//分類選択
function bchange(){
	if(document.getElementById('bunrui').value == 1){
		document.getElementById('g1').style.display = "none";
		document.getElementById('g1meisho').style.display = "block";
		document.getElementById('g2meisho').disabled = true;
		document.getElementById('g2meisho').value = "";
	}
	if(document.getElementById('bunrui').value == 2){
		document.getElementById('g1').style.display = "block";
		document.getElementById('g1meisho').style.display = "none"
		document.getElementById('g2meisho').disabled = false;
	}
}

//更新
function update(){
	bunrui = document.getElementById('bunrui').value;
	gid1 = document.getElementById('g1').value;
	if(bunrui == 1){
		meisho = document.getElementById('g1meisho').value;
	}else{
		meisho = document.getElementById('g2meisho').value;
	}
	$.ajax({
		type: "POST",
		url: "genreup.php",
		data: {
			"uiKbn" : uiKbn,
			"bunrui" : bunrui,
			"meisho" : meisho,
			"gid1" : gid1,
			"gid2" : gid2
		}
	}).then(
		function(){
			alert("登録が完了しました。");
			window.location.href = "./genre.php";
		},
		function(){
			alert("登録できませんでした。");
		}
	);
}

//もどる
function back(){
	window.location.href = "./genre.php";
}

</script>
</body>
</html>

