<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="Bootgrid のデモでーす。">
<title>Bootgrid - jQuery Plugin Demo</title>
<link href="css/bootstrap.css" rel="stylesheet" />
<link href="css/jquery.bootgrid.css" rel="stylesheet" />
</head>
<body>

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
	$result = pg_query("SELECT * FROM botlog");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='no' data-type='numeric' data-width='2%'>No</th>
               <th data-column-id='day' data-type='numeric' data-width='8%'>日時</th>
               <th data-column-id='user'  data-width='10%'>ユーザーID</th>
               <th data-column-id='que'  data-width='40%'>質問内容</th>
               <th data-column-id='ans'  data-width='40%'>回答内容</th>
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

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.bootgrid.js"></script>
<script src="js/jquery.resizableColumns.js"></script>
<script>
$(function() {
	$("#grid-basic").bootgrid();
	$("#grid-basic").resizableColumns();
});
</script>
</body>
</html>

