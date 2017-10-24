<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>施設登録</title>
</head>
<body>
<p  style="display:inline;">　施設名称</p>
<input id="meisho" maxlength="40" placeholder="施設名称を入力してください" style="width: 500px;">
<br><br>
<p style="display:inline;">　　　住所</p>
<input id="jusho" maxlength="128" placeholder="施設の住所を入力してください"  style="width: 500px;">
<br><br>
<p style="display:inline;">　電話番号</p>
<input id="jusho" maxlength="14" placeholder="000-0000-0000"  style="width: 100px;">
<br><br>
<p style="display:inline;">ジャンル１</p>
<select id="j1"  onChange="j1change()">
<option value="" selected></option>
<option value="グルメ" selected>グルメ</option>
<option value="レジャー・観光・スポーツ">レジャー・観光・スポーツ</option>
<option value="ホテル・旅館">ホテル・旅館</option>
<option value="駅・バス・車・交通">駅・バス・車・交通</option>
<option value="公共・病院・銀行・学校">公共・病院・銀行・学校</option>
<option value="ショッピング">ショッピング</option>
<option value="生活・不動産">生活・不動産</option>
<option value="ビジネス・企業間取引">ビジネス・企業間取引</option>
</select>
<br><br>
<p style="display:inline;">ジャンル２</p>
<select id="j2">
</select>
<br><br>
<p style="display:inline;">　　　緯度</p>
<input id="lat" maxlength="14" placeholder="999.99999" style="width: 100px;">
<br><br>
<p style="display:inline;">　　　経度</p>
<input id="lng" maxlength="14" placeholder="999.99999" style="width: 100px;">
<br><br>
<p style="display:inline;">画像ＵＲＬ</p>
<input id="iurl" maxlength="300" placeholder="https://www.yyy.zzz.jpg" style="width: 500px;">
<br>
※必ずhttpsから始まるURLを指定してください
<br><br>
<p style="display:inline;">詳細ＵＲＬ</p>
<input id="iurl" maxlength="300" placeholder="http://www.yyy.zzz.html" style="width: 500px;">
<br><br>
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

//ジャンル選択
function j1change(){
	var select = document.getElementById('j2');
	while (0 < select.childNodes.length) {
		select.removeChild(select.childNodes[0]);
	}

	switch (document.getElementById('j1').value){
	  case "グルメ":
		  j1_s1();
	    break;
	  case "レジャー・観光・スポーツ":
		  j1_s2();
	    break;
	  case "ホテル・旅館":
		  j1_s3();
	    break;
	  case "駅・バス・車・交通":
		  j1_s4();
	    break;
	  case "公共・病院・銀行・学校":
		  j1_s5();
	    break;
	  case "ショッピング":
		  j1_s6();
	    break;
	  case "生活・不動産":
		  j1_s7();
	    break;
	  case "ビジネス・企業間取引":
		  j1_s8();
	    break;
	  default:
	    break;
	}
}

function j1_s1(){
	var select = document.getElementById('j2');

	var option = document.createElement('option');
	option.setAttribute('value', '和食');
	var text = document.createTextNode('和食');
	option.appendChild(text);
	select.appendChild(option);

	option = document.createElement('option');
	option.setAttribute('value', '寿司');
	text = document.createTextNode('寿司');
	option.appendChild(text);
	select.appendChild(option);
}

function j1_s2(){
	var select = document.getElementById('j2');

	var option = document.createElement('option');
	option.setAttribute('value', 'レジャー施設');
	var text = document.createTextNode('レジャー施設');
	option.appendChild(text);
	select.appendChild(option);

	option = document.createElement('option');
	option.setAttribute('value', '美術館');
	text = document.createTextNode('美術館');
	option.appendChild(text);
	select.appendChild(option);
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

