<?php
$hub_verify_token = "testbotfa"; // 適当なトークンを自分で作成
if($_GET['hub_verify_token'] == $hub_verify_token) {
	echo $_GET["hub_challenge"];
} else {
	echo 'error';
}