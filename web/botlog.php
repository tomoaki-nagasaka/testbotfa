<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="チャットボットのログを表示します。">
<title>チャットボットログ</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<link href="css/jquery.bootgrid.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="js/jquery.bootgrid.js"></script>
</head>
<body>
<div id="loader-bg">
  <div id="loader">
    <img src="img/loading.gif" width="80" height="80" alt="Now Loading..." />
    <p>Now Loading...</p>
  </div>
</div>
<div id="wrap" style="display:none">
<div id="header"></div>
<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

$dbvalue = array();

if ($link) {
	$result = pg_query("SELECT * FROM botlog");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='no' data-type='numeric' data-identifier='true' data-width='3%'>No</th>
               <th data-column-id='day' data-width='7%'>日時</th>
               <th data-column-id='user'  data-width='20%'>ユーザーID</th>
               <th data-column-id='que'  data-width='32%'>質問内容</th>
               <th data-column-id='ans'  data-width='32%'>回答内容</th>
               <th data-column-id='detail'  data-width='6%' data-formatter='details' data-sortable='false'></th>
           </tr>";
	echo "</thead>";
	echo "<tbody>";
	while ($row = pg_fetch_row($result)) {
		array_push($dbvalue,$row);
		echo "<tr>";
		echo "<td>";
		echo $row[0];
		echo "</td>";
		echo "<td>";
		echo substr($row[1], 0,4)."/".substr($row[1], 4,2)."/".substr($row[1], 6,2)." ".substr($row[1], 8,2).":".substr($row[1], 10,2);
		echo "</td>";
		echo "<td>";
		echo $row[2];
		echo "</td>";
		echo "<td>";
		echo $row[3];
		echo "</td>";
		echo "<td>";
		echo $row[4];
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<br>";
}

?>
<div class="container" align="center">
	<input id="btn_del" type="button" class="btn btn-default" value="選択行の削除" onclick="drow()">
</div>
</div>
<script>
var rowIds = [];
var dbvalue = [];
$(function() {
	dbvalue = <?php echo json_encode($dbvalue); ?>;
	var h = $(window).height();
	$('#wrap').css('display','none');
	$('#loader-bg ,#loader').height(h).css('display','block');

	$("#header").load("header.html");

	$("#grid-basic").bootgrid({
		selection: true,
		multiSelect: true,
		rowSelect: true,
	    keepSelection: true,
	    formatters: {
	        "details": function($column, $row) {
	        	return "<input type='button' value='詳細' onclick='detailwin("  + $row.no + ")'> ";
             }
	    }
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
});

$(window).load(function () { //全ての読み込みが完了したら実行
	  $('#loader-bg').delay(900).fadeOut(800);
	  $('#loader').delay(600).fadeOut(300);
	  $('#wrap').css('display', 'block');
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
		if( successFlg == true){
			alert("削除しました");
			location.reload();
		}else{
			alert("削除できませんでした");
		}
	}
}

function detailwin(value){
	for (var i = 0; i < dbvalue.length; i++){
		if(dbvalue[i][0] == value){
			// 表示するウィンドウのサイズ
			var w_size=900;
			var h_size=400;
			// 表示するウィンドウの位置
			var l_position=Number((window.screen.width-w_size)/2);
			var t_position=Number((window.screen.height-h_size)/2);

		    myWin = window.open("" , "detailwindow" , 'width='+w_size+', height='+h_size+', left='+l_position+', top='+t_position); // ウィンドウを開く

		    myWin.document.open();
		    myWin.document.write( "<html>" );
		    myWin.document.write( "<head>" );
		    myWin.document.write( "<title>", "詳細" , "</title>" );
		    myWin.document.write( "</head>" );
		    myWin.document.write( "<body style='margin:10px;padding:10px'>" );
		    var idate = dbvalue[i][1].substr(0,4) + "/" + dbvalue[i][1].substr(4,2) + "/" + dbvalue[i][1].substr(6,2) + " " + dbvalue[i][1].substr(8,2) + ":" + dbvalue[i][1].substr(10,2);
		    myWin.document.write( "<p style='display:inline;'>　　　　日時　</p>" );
		    myWin.document.write( "<input type='text' readonly style='width: 600px;' value='" + idate + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<p style='display:inline;'>ユーザーＩＤ　</p>" );
		    myWin.document.write( "<input type='text' readonly style='width: 600px;' value='" + dbvalue[i][2] + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<label>　　質問内容　</label>" );
		    myWin.document.write( "<textarea  readonly rows='10' cols='100' style='vertical-align:middle;'>" + dbvalue[i][3] + "</textarea>");
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<label>　　回答内容　</label>" );
		    myWin.document.write( "<textarea  readonly rows='10' cols='100' style='vertical-align:middle;'>" + dbvalue[i][4] + "</textarea>");
		    myWin.document.write( "</body>" );
		    myWin.document.write( "</html>" );
		    myWin.document.close();

		    myWin.onpageshow = function(){

		    	var width=screen.availWidth - 600;
		        var height=screen.availHeight - 300;
		        myWin.moveTo(width/2, height/2);
		    };
		    break;
		}
	}
}
</script>
</body>
</html>

