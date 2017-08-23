<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="回答ランキング">
<title>回答のランキングを表示します。</title>
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
<select name="ym">

<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

$ym = "99999999999999";
//$ym = "20170800000000";
$endFlg = false;
$count = 0;

if ($link) {

	while ($endFlg == false){
		$result = pg_query("SELECT time FROM botlog WHERE TIME < '{$ym}' ORDER BY time DESC");
		$row = pg_fetch_row($result);
		error_log($row);
		if($row){
			$yyyymm = substr($row[0], 0,4)."/".substr($row[0], 4,2);
			echo('<option value="' . substr($row[0], 0,6). '">' . $yyyymm. '</option>');
			$ym = substr($row[0], 0,6)."00000000";
		}else{
			$endFlg = true;
		}
		$count++;
		if($count > 5){
			$endFlg = true;
		}
	}
	/*
	$result = pg_query("SELECT time FROM botlog WHERE TIME < '{$ym}' ORDER BY time DESC");
	$row = pg_fetch_row($result);
	$yyyymm = substr($row[0], 0,4)."/".substr($row[0], 4,2);
	echo('<option value="' . substr($row[0], 0,6). '">' . $yyyymm. '</option>');
	$ym = substr($row[0], 0,6)."99999999";
	*/
}


?>
</select>
<input id="btn_hyoji" type="button" value="表示" onclick="draw()"  >
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.bootgrid.js"></script>
<script>
var rowIds = [];
$(function() {
	var h = $(window).height();
	$('#wrap').css('display','none');
	$('#loader-bg ,#loader').height(h).css('display','block');

	/*
	$("#grid-basic").bootgrid({
		selection: true,
		multiSelect: true,
		rowSelect: true,
	    keepSelection: true,
	}).on("selected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	        rowIds.push(rows[i].no);
	    }
	    //alert("Select: " + rowIds.join(","));
	}).on("deselected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	    	rowIds.some(function(v, ii){
	    	    if (v==rows[i].no) rowIds.splice(ii,1);
	    	});
	        //rowIds.push(rows[i].no);
	    }
	    //alert("Deselect: " + rowIds.join(","));
	});
	*/
});

$(window).load(function () { //全ての読み込みが完了したら実行
	  $('#loader-bg').delay(900).fadeOut(800);
	  $('#loader').delay(600).fadeOut(300);
	  $('#wrap').css('display', 'block');
	  $('#btn_del').css('display', 'block');
});

function drow() {
	var successFlg = true;
	var myRet = confirm("選択行を削除しますか？");
	if ( myRet == true ){
		for (var i = 0; i < rowIds.length; i++){
			$.ajax({
				type: "POST",
				url: "botlogdel.php",
				data: "no=" + rowIds[i],
			}).then(
				function(){
				},
				function(){
					successFlg = false;
				}
			);
		}
	}
	if( successFlg == true){
		alert("削除しました");
		location.reload();
	}else{
		alert("削除できませんでした");
	}
}


</script>
</body>
</html>

