<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>施設登録</title>
</head>
<body>
<p>施設名称</p>
<input id="meisho" maxlength="40" placeholder="施設名称を入力してください" style="width: 500px;">
<p>住所</p>
<input id="jusho" maxlength="128" placeholder="施設の住所を入力してください"  style="width: 500px;">
<p>電話番号</p>
<input id="jusho" maxlength="14" placeholder="000-0000-0000"  style="width: 500px;">
<p>ジャンル１</p>
<select id="j1">
<option value="グルメ" selected>グルメ</option>
<option value="レジャー・観光・スポーツ">レジャー・観光・スポーツ</option>
<option value="ホテル・旅館">ホテル・旅館</option>
<option value="駅・バス・車・交通">駅・バス・車・交通</option>
<option value="公共・病院・銀行・学校">公共・病院・銀行・学校</option>
<option value="ショッピング">ショッピング</option>
<option value="生活・不動産">生活・不動産</option>
<option value="ビジネス・企業間取引">ビジネス・企業間取引</option>
</select>
<p>ジャンル２</p>
<select id="j2">
<option value="0" selected>性別</option>
<option value="1">男性</option>
<option value="2">女性</option>
</select>
<p>緯度</p>
<input id="lat" maxlength="14" placeholder="999.99999" style="width: 200px;">
<p>経度</p>
<input id="lng" maxlength="14" placeholder="999.99999" style="width: 200px;">
</select>
<p>画像URL ※必ずhttpsから始まるURLを指定してください</p>
<input id="iurl" maxlength="300" placeholder="https://www.yyy.zzz.jpg" style="width: 500px;">
<p>詳細URL</p>
<input id="iurl" maxlength="300" placeholder="http://www.yyy.zzz.html" style="width: 500px;">
<br>
<input type="button" onclick="clearform()" value="クリア" />
<input type="button" onclick="update()" value="更新" />
<input type="button" onclick="back()" value="もどる" />

<?php

//引数
$user = $_GET['user'];


?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script>
var param = "";
var user = "";
var lang = "";
var age = "";
var sex = "";
var region = "";

$(function() {
	param = <?php echo json_encode($user); ?>;
	user = param.substr(0,1) + param.substr(2,1) + param.substr(6,1) + param.substr(10)
	sex = param.substr(1,1);
	age = param.substr(3,3);
	region = param.substr(7,3);
	age = Number(age);

	//alert("sex:" + sex + " age:" + age + " region:" + region);

	document.getElementById('age').value = age;
	document.getElementById('sex').value = sex;
	document.getElementById('region').value = region;
});

//言語選択
function lchange(){
	if(document.getElementById('language').value == "02"){
		location.href = "https://gyoseibot.herokuapp.com/attribute_en.php?user=" + param;
	}
}

//クリア
function clearform(){
	document.getElementById('language').selectedIndex = 0;
	document.getElementById('age').selectedIndex = 0;
	document.getElementById('sex').selectedIndex = 0;
	document.getElementById('region').selectedIndex = 0;
}

//更新
function update(){
	lang = document.getElementById('language').value;
	age = document.getElementById('age').value;
	sex = document.getElementById('sex').value;
	region = document.getElementById('region').value;
	$.ajax({
		type: "POST",
		url: "userinfoup.php",
		data: {
			"user" : user,
			"lang" : lang,
			"age" : age,
			"sex" : sex,
			"region" : region
		}
	}).then(
		function(){
			alert("登録が完了しました。画面を閉じてください。");
		},
		function(){
			alert("登録できませんでした。");
		}
	);
}

//削除
function back(){
	window.location.href = "./shisetsu.php";
}
</script>
</body>
</html>

