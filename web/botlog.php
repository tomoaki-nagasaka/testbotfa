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
	echo "<tr><th>No</th><th>日時</th><th>ユーザーID</th><th>質問内容</th><th>回答内容</th></tr>";
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
	echo "</table>";
	echo "<br>";
}
?>

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

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.bootgrid.js"></script>
<script>
$(function() {
	$("#grid-basic").bootgrid();
});
</script>
</body>
</html>

