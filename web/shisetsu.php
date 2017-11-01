<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="施設情報">
<title>施設情報</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<link href="css/jquery.bootgrid.css" rel="stylesheet" />
</head>
<body>
<div id="loader-bg">
  <div id="loader">
    <img src="img/loading.gif" width="80" height="80" alt="Now Loading..." />
    <p>Now Loading...</p>
  </div>
</div>
<div id="wrap" style="display:none">

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
	$result = pg_query("SELECT * FROM shisetsu");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='id' data-type='numeric' data-identifier='true' data-width='3%'>ID</th>
               <th data-column-id='meisho' data-width='10%'>名称</th>
               <th data-column-id='jusho'  data-width='10%'>住所</th>
               <th data-column-id='tel'  data-width='7%'>電話番号</th>
               <th data-column-id='genre1'  data-width='10%'>ジャンル１</th>
               <th data-column-id='genre2'  data-width='10%'>ジャンル２</th>
               <th data-column-id='lat'  data-width='5%'>緯度</th>
               <th data-column-id='lng'  data-width='5%'>経度</th>
               <th data-column-id='iurl'  data-width='20%'>画像URL</th>
               <th data-column-id='url'  data-width='20%'>詳細URL</th>
           </tr>";
	echo "</thead>";
	echo "<tbody>";
	while ($row = pg_fetch_row($result)) {
		echo "<tr>";
		echo "<td>";
		echo $row[0];
		echo "</td>";
		echo "<td>";
		echo $row[1];
		echo "</td>";
		echo "<td>";
		echo $row[2];
		echo "</td>";
		echo "<td>";
		echo $row[3];
		echo "</td>";
		$result2 = pg_query("SELECT meisho FROM genre WHERE gid1 = {$row[4]} AND bunrui = 1");
		$row2 = pg_fetch_row($result2);
		echo "<td>";
		echo $row2[0];
		echo "</td>";
		$result2 = pg_query("SELECT meisho FROM genre WHERE gid1 = {$row[4]} AND gid2 = {$row[5]} AND bunrui = 2");
		$row2 = pg_fetch_row($result2);
		echo "<td>";
		echo $row2[0];
		echo "</td>";
		echo "<td>";
		echo $row[7];
		echo "</td>";
		echo "<td>";
		echo $row[8];
		echo "</td>";
		echo "<td>";
		echo $row[9];
		echo "</td>";
		echo "<td>";
		echo $row[10];
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<br>";
}

?>
</div>
<input id="btn_del" type="button" value="選択行の削除" onclick="drow()"  style="display:none">
<input id="btn_ins" type="button" value="施設の追加" onclick="irow()"  style="display:none">
<input id="btn_mod" type="button" value="施設の修正" onclick="mrow()"  style="display:none">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.bootgrid.js"></script>
<script>
var rowIds = [];
$(function() {
	var h = $(window).height();
	$('#wrap').css('display','none');
	$('#loader-bg ,#loader').height(h).css('display','block');

	$("#grid-basic").bootgrid({
		selection: true,
		multiSelect: true,
		rowSelect: true,
	    keepSelection: true,
	}).on("selected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	        rowIds.push(rows[i].id);
	    }
	    //alert("Select: " + rowIds.join(","));
	}).on("deselected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	    	rowIds.some(function(v, ii){
	    	    if (v==rows[i].id) rowIds.splice(ii,1);
	    	});
	        //rowIds.push(rows[i].no);
	    }
	    //alert("Deselect: " + rowIds.join(","));
	});
});

$(window).load(function () { //全ての読み込みが完了したら実行
	  $('#loader-bg').delay(900).fadeOut(800);
	  $('#loader').delay(600).fadeOut(300);
	  $('#wrap').css('display', 'block');
	  $('#btn_del').css('display', 'block');
	  $('#btn_ins').css('display', 'block');
	  $('#btn_mod').css('display', 'block');
});

function drow() {
	var successFlg = true;
	var myRet = confirm("選択行を削除しますか？");
	if ( myRet == true ){
		for (var i = 0; i < rowIds.length; i++){
			$.ajax({
				type: "POST",
				url: "shisetsudel.php",
				data: "id=" + rowIds[i],
			}).then(
				function(){
				},
				function(){
					successFlg = false;
				}
			);
		}
		if( successFlg == true){
			alert("削除しました");
			location.reload();
		}else{
			alert("削除できませんでした");
		}
	}
}

function irow(){
	window.location.href = "./shisetsuadd.php";
}

function mrow(){
	if(rowIds.length == 0){
		alert("修正する行を選択してください");
		return;
	}

	if(rowIds.length > 1){
		alert("修正対象の行のみ選択してください");
		return;
	}

	window.location.href = "./shisetsuadd.php?id=" + rowIds[0];
}
</script>
</body>
</html>

