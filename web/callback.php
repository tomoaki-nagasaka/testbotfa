<?php
//error_log("開始します");
$accessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN');


//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$jsonObj = json_decode($json_string);

$type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
$eventType = $jsonObj->{"events"}[0]->{"type"};
//メッセージ取得
$text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
//ReplyToken取得
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};
//ユーザーID取得
$userID = $jsonObj->{"events"}[0]->{"source"}->{"userId"};

error_log($eventType);
if($eventType == "follow"){
	$response_format_text = [
			"type" => "template",
			"altText" => "this is a buttons template",
			"template" => [
					"type" => "buttons",
					"thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/gyosei.jpg",
					"title" => "行政市役所",
					//"text" => "こんにちは。行政市のすいか太郎です。\\n皆さんの質問にはりきってお答えしますよ～\\nまずは、下のメニュータブをタップしてみてください",
					"text" => "こんにちは。行政市のすいか太郎です。\\n皆さんの質問にはりきってお答えしますよ～",
					"actions" => [
							[
									"type" => "postback",
									"label" => "LINEで質問",
									"data" => "action=qaline"
							],
							[
									"type" => "postback",
									"label" => "証明書",
									"data" => "action=shomei"
							],
							[
									"type" => "postback",
									"label" => "施設予約",
									"data" => "action=shisetsu"
							],
							[
									"type" => "postback",
									"label" => "ご利用方法",
									"data" => "action=riyo"
							]
					]
			]
	];
	goto lineSend;
}

if($eventType == "postback"){
	$bData = $jsonObj->{"events"}[0]->{"postback"}->{"data"};
	if($bData== 'action=qaline') {
		$response_format_text = [
				"type" => "text",
				"text" => "それでは、質問をお願いします。"
		];
		goto lineSend;
	}

	if($bData== 'action=shomei') {
		$response_format_text = [
				"type" => "text",
				"text" => "証明書についてはこちらをごらんください。"
		];
		goto lineSend;
	}

	if($bData== 'action=shisetsu') {
		$response_format_text = [
				"type" => "text",
				"text" => "施設予約についてはこちらをごらんください。"
		];
		goto lineSend;
	}

	if($bData== 'action=riyo') {
		$response_format_text = [
				"type" => "text",
				"text" => "ご利用方法についてはこちらをごらんください。"
		];
		goto lineSend;
	}
}

//メッセージ以外のときは何も返さず終了
if($type != "text"){
	exit;
}



$classfier = "12d0fcx34-nlc-410";
$workspace_id = "fa6f1b64-533d-4aaa-b181-b534fc0b3d1e";

//$url = "https://gateway.watson-j.jp/natural-language-classifier/api/v1/classifiers/".$classfier."/classify?text=".$text;
//$url = "https://gateway.watson-j.jp/natural-language-classifier/api/v1/classifiers/".$classfier."/classify";
$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id."/message?version=2017-04-21";

$username = "bfeeeb55-a8a0-459b-9410-0eb1fa44a285";
$password = "kR2NobNe1lkJ";

//$data = array("text" => $text);
$data = array('input' => array("text" => $text));
/*
$data["context"] = array("conversation_id" => "",
      "system" => array("dialog_stack" => array(array("dialog_node" => "")),
      "dialog_turn_counter" => 1,
      "dialog_request_counter" => 1));

$curl = curl_init($url);

$options = array(
    CURLOPT_HTTPHEADER => array(
     'Content-Type: application/json',
    ),
    CURLOPT_USERPWD => $username . ':' . $password,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_RETURNTRANSFER => true,
);

curl_setopt_array($curl, $options);
$jsonString = curl_exec($curl);
*/
$jsonString = callWatson();
$json = json_decode($jsonString, true);

$conversation_id = $json["context"]["conversation_id"];
$userArray[$userID]["cid"] = $conversation_id;
$userArray[$userID]["time"] = date('Y/m/d H:i:s');

$data["context"] = array("conversation_id" => $conversation_id,
      "system" => array("dialog_stack" => array(array("dialog_node" => "root")),
      "dialog_turn_counter" => 1,
      "dialog_request_counter" => 1));

/*
$curl = curl_init($url);
$options = array(
    CURLOPT_HTTPHEADER => array(
     'Content-Type: application/json',
    ),
    CURLOPT_USERPWD => $username . ':' . $password,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_RETURNTRANSFER => true,
);

curl_setopt_array($curl, $options);
$jsonString = curl_exec($curl);
*/
$jsonString = callWatson();
//error_log($jsonString);
$json = json_decode($jsonString, true);

$mes = $json["output"]["text"][0];
//$mes = $json["output"];

$response_format_text = [
    "type" => "text",
    "text" => $mes
];

lineSend:
error_log($response_format_text);
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

function makeOptions(){
	global $username, $password, $data;
	return array(
			CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
			),
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_RETURNTRANSFER => true,
	);
}

function callWatson(){
	global $curl, $url, $username, $password, $data, $options;
	$curl = curl_init($url);

	$options = array(
			CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
			),
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	return curl_exec($curl);
}
