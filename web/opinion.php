<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="市政へのご意見">
<title>市政へのご意見</title>
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
	$result = pg_query("SELECT * FROM opinion");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='no' data-type='numeric' data-identifier='true' data-width='3%'>No</th>
               <th data-column-id='date' data-width='7%'>日時</th>
               <th data-column-id='sex'  data-width='5%'>性別</th>
               <th data-column-id='age'  data-width='5%'>年齢</th>
               <th data-column-id='sadness'  data-width='9%'>悲しみ</th>
               <th data-column-id='joy'  data-width='9%'>喜び</th>
               <th data-column-id='fear'  data-width='9%'>恐れ</th>
               <th data-column-id='disgust'  data-width='9%'>嫌悪</th>
               <th data-column-id='anger'  data-width='9%'>怒り</th>
               <th data-column-id='opinion'  data-width='30%'>ご意見</th>
               <th data-column-id='detail'  data-width='5%' data-formatter='details' data-sortable='false'></th>
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
		if($row[2] == "1"){
			echo "男性";
		}else if($row[2] == "2"){
			echo "女性";
		}else{
			echo "登録なし";
		}
		echo "</td>";
		echo "<td>";
		echo $row[3];
		echo "</td>";
		echo "<td>";
		echo $row[5];
		echo "</td>";
		echo "<td>";
		echo $row[6];
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
		echo $row[4];
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<br>";
}

?>
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
                  //return "<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-row-id=\"" + $row.no + "\">画像拡大</button> ";
	        	//return "<Form><input type='button' value='画像拡大' onClick='window.open('" + getimage.php?id=$row.no + "','test','width=250,height=100,');'></Form> ";
	        	//return "<Form><input type='button' value='画像拡大' onclick='imgwin()'></Form> ";
	        	var ivalue = $row.date + "|" + $row.sex + "|" + $row.age + "|" + $row.sadness + "|" + $row.joy + "|" + $row.fear + "|" + $row.disgust + "|" + $row.anger + "|" + $row.opinion;
	        	return "<input type='button' value='詳細' onclick='detailwin("  + $row.no + ")'> ";
             }
	    }
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
});

/*
function detailwin(date,sex,age,sadness,joy,fear,disgust,anger,opinion){
	alert(date + "/" + sex + "/" + age);
}
*/
function detailwin(value){
	for (var i = 0; i < dbvalue.length; i++){
		if(dbvalue[i][0] == value){
			// 表示するウィンドウのサイズ
			var w_size=800;
			var h_size=600;
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
		    myWin.document.write( "<p style='display:inline;'>　日時　</p>" );
		    myWin.document.write( "<input type='text' readonly value='" + idate + "'>" );
		    myWin.document.write( "<br>" );
		    var sex = "";
		    if(dbvalue[i][2] = 1){
			    sex = "男性";
		    }
		    if(dbvalue[i][2] = 2){
			    sex = "女性";
		    }
		    myWin.document.write( "<p style='display:inline;'>　性別　</p>" );
		    myWin.document.write( "<input type='text' readonly value='" + sex + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<p style='display:inline;'>　年齢　</p>" );
		    myWin.document.write( "<input type='text' readonly value='" + dbvalue[i][3] + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<p style='display:inline;'>　悲しみ　</p>" );
		    myWin.document.write( "<input type='text' readonly value='" + dbvalue[i][5] + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<p style='display:inline;'>　喜び　</p>" );
		    myWin.document.write( "<input type='text' readonly value='" + dbvalue[i][6] + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<p style='display:inline;'>　恐れ　</p>" );
		    myWin.document.write( "<input type='text' readonly value='" + dbvalue[i][7] + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<p style='display:inline;'>　嫌悪　</p>" );
		    myWin.document.write( "<input type='text' readonly value='" + dbvalue[i][8] + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<p style='display:inline;'>　怒り　</p>" );
		    myWin.document.write( "<input type='text' readonly value='" + dbvalue[i][9] + "'>" );
		    myWin.document.write( "<br>" );
		    myWin.document.write( "<p style='display:inline;'>　ご意見　</p>" );
		    myWin.document.write( "<textarea  style='display:inline;' readonly rows='5' cols='100' >" + dbvalue[i][4] + "</textarea>");
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

