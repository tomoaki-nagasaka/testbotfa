<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="施設ジャンル">
<title>施設ジャンル</title>
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
	$no = 1;
	$result = pg_query("SELECT * FROM genre ORDER BY gid1,gid2");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='no' data-type='numeric' data-identifier='true' data-width='3%'>No</th>
               <th data-column-id='bunrui' >分類</th>
               <th data-column-id='g1'  >大分類名称</th>
               <th data-column-id='g2'  >小分類名称</th>
               <th data-column-id='gid1'>分類ID1</th>
               <th data-column-id='gid2'>分類ID2</th>
           </tr>";
	echo "</thead>";
	echo "<tbody>";
	while ($row = pg_fetch_row($result)) {
		echo "<tr>";
		echo "<td>";
		echo $no++;
		echo "</td>";
		echo "<td>";
		if($row[0] == 1){
			echo "大分類";
		}else{
			echo "小分類";
		}
		echo "</td>";
		echo "<td>";
		if($row[0] == 1){
			echo $row[4];
		}else{
			$result2 = pg_query("SELECT meisho FROM genre WHERE bunrui = 1 AND gid1 = {$row[1]}");
			$row2 = pg_fetch_row($result2);
			echo $row2[0];
		}
		echo "</td>";
		echo "<td>";
		if($row[0] == 1){
			echo "－";
		}else{
			echo $row[4];
		}
		echo "</td>";
		echo "<td>";
		echo $row[1];
		echo "</td>";
		echo "<td>";
		echo $row[2];
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
<input id="btn_ins" type="button" value="ジャンルの追加" onclick="irow()"  style="display:none">
<input id="btn_mod" type="button" value="ジャンルの修正" onclick="mrow()"  style="display:none">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.bootgrid.js"></script>
<script>
var rowIds = [];
var rowgid1 = [];
var rowgid2 = [];
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
	        rowIds.push(rows[i].no);
	        rowgid1.push(rows[i].gid1);
	        rowgid2.push(rows[i].gid2);
	        //alert("rowgid1:" + rows[i].gid1 + " rowgid2:" + rows[i].gid2);
	    }
	    //alert("Select: " + rowIds.join(","));
	}).on("deselected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	    	for (var ii = 0; ii < rowIds.length; ii++){
		    	if(rowIds[ii] == rows[i].no){
		    		rowIds.splice(ii,1);
		    		rowgid1.splice(ii,1);
		    		rowgid2.splice(ii,1);
		    		break;
		    	}
	    	}
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
	if(rowIds.length == 0){
		alert("削除する行を選択してください");
		return;
	}
	var successFlg = true;
	var myRet = confirm("選択行を削除しますか？");
	if ( myRet == true ){
		for (var i = 0; i < rowIds.length; i++){
			$.ajax({
				type: "POST",
				url: "genredel.php",
				data: "gid1=" + rowgid1[i],
				data: "gid2=" + rowgid2[i]
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
	window.location.href = "./genreadd.php?gid1=0&gid2=0";
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

	window.location.href = "./genreadd.php?gid1=" + rowgid1[0] + "&gid2=" + rowgid2[0];
}
</script>
</body>
</html>

