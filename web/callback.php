<?php
$accessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN');


//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$jsonObj = json_decode($json_string);

$type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
//メッセージ取得
$text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
//ReplyToken取得
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};

//メッセージ以外のときは何も返さず終了
if($type != "text"){
	exit;
}

$classfier = "12d0fcx34-nlc-410"

$url = "https://gateway.watson-j.jp/natural-language-classifier/api/v1/classifiers/".$classfier."/classify?text=".$text;

$username = "8a9fc757-fc79-43c2-ac3c-16cd7ed91f0b";
$password = "Uj31NjHaEspV";

$headers = array(
    'Authorization: Basic '.base64_encode($username.':'.$password),
    'Content-Type: text/plain'
);

$options = array('http' => array(
    'method' => 'POST',
    'header' => implode("\r\n", $headers)
));
$result = file_get_contents($url, false, stream_context_create($options));

$response_format_text = [
    "type" => "text",
    "text" => "こんにちは"
];

$post_data = [
	"replyToken" => $replyToken,
	"messages" => [$response_format_text]
	];

$ch = curl_init("https://api.line.me/v2/bot/message/reply");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charser=UTF-8',
    'Authorization: Bearer ' . $accessToken
    ));
$result = curl_exec($ch);
curl_close($ch);
