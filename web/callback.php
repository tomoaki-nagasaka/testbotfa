<?php
//error_log("開始します");
date_default_timezone_set('Asia/Tokyo');

//環境変数の取得
$accessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN');
$classfier = getenv('CLASSFIER');
$workspace_id = getenv('CVS_WORKSPASE_ID');
$username = getenv('CVS_USERNAME');
$password = getenv('CVS_PASS');
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');
$LTuser = getenv('LT_USER');
$LTpass = getenv('LT_PASS');
$VRkey = getenv('VR_KEY');


//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$jsonObj = json_decode($json_string);

$type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
$eventType = $jsonObj->{"events"}[0]->{"type"};
//メッセージ取得
$text = $jsonObj->{"events"}[0]->{"message"}->{"text"};
$Utext = $text;
//ReplyToken取得
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};
//ユーザーID取得
$userID = $jsonObj->{"events"}[0]->{"source"}->{"userId"};
//返信メッセージ
$resmess = "";

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

//LT問い合わせ
$bl_isNumeric = false;
$Ltext = $text;
if (is_numeric($Ltext)) {
	$bl_isNumeric = true;
	if ($link) {
		$result = pg_query("SELECT contents FROM botlog WHERE userid = '{$userID}' ORDER BY no DESC");
		while ($row = pg_fetch_row($result)) {
			if(!is_numeric($row[0])){
				$Ltext= $row[0];
				break;
			}
		}
	}
}
$jsonString = callWatsonLT1();
$json = json_decode($jsonString, true);
$language = $json["languages"][0]["language"];
error_log($language);

//日本語以外の場合は日本語に翻訳
if(!$bl_isNumeric){
	if($language != "ja"){
		$data = array('text' => $text, 'source' => $language, 'target' => 'ja');
		$text = callWatsonLT2();
		if($text == ""){
			$text = $Utext;
			$language = "ja";
		}
	}
}

if($eventType == "follow"){
	$resmess = "こんにちは。\n行政市のすいか太郎です。\n皆さんの質問にはりきってお答えしますよ～";
	$response_format_text = [
			"type" => "template",
			"altText" => "this is a buttons template",
			"template" => [
					"type" => "buttons",
					"thumbnailImageUrl" => "https://" . $_SERVER['SERVER_NAME'] . "/gyosei.jpg",
					"title" => "行政市役所",
					//"text" => "こんにちは。行政市のすいか太郎です。\n皆さんの質問にはりきってお答えしますよ～\nまずは、下のメニュータブをタップしてみてください",
					"text" => $resmess,
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
		$resmess = "それでは、質問をお願いします。";
	}

	if($bData== 'action=shomei') {
		$resmess = "証明書についてはこちらをごらんください。";
	}

	if($bData== 'action=shisetsu') {
		$resmess = "施設予約についてはこちらをごらんください。";
	}

	if($bData== 'action=riyo') {
		$resmess = "ご利用方法についてはこちらをごらんください。";
	}

	if($bData== 'action=uc_1_1') {
		$resmess = "①○○地区、△△地区、□□地区ですね。\nその場合、最寄りの税務署は「行政第一税務署」になります。「行政第一税務署」の詳細はURLをご確認ください。\n他に質問はありますか？";
	}

	if($bData== 'action=uc_1_2') {
		$resmess = "②●●地区、▲▲地区、■■地区ですね。\nその場合、最寄りの税務署は「行政第二税務署」になります。「行政第二税務署」の詳細はURLをご確認ください。\n他に質問はありますか？";
	}

	if($bData== 'action=uc_1_3') {
		$resmess = "③Ａ地区、Ｂ地区、Ｃ地区ですね。\nその場合、最寄りの税務署は「行政第三税務署」になります。「行政第三税務署」の詳細はURLをご確認ください。\n他に質問はありますか？";
	}

	if($bData== 'action=uc_1_4') {
		$resmess = "④あ地区、い地区、う地区ですね。\nその場合、最寄りの税務署は「行政第四税務署」になります。「行政第四税務署」の詳細はURLをご確認ください。\n他に質問はありますか？";
	}

	if($bData== 'action=uc_2_1') {
		$resmess = "ありがとうございます。\n個人番号カードをお持ちでコンビニエンスストアでの証明書交付の利用申請がお済の方は、下記のコンビニエンスストアでも住民票の写しが取れますよ～\n\n・セブンイレブン\n・ローソン\n・ファミリーマート\n・サークルＫサンクス\n\nまた、コンビニエンスストアの証明交付サービスは、年末年始（12月29日～翌年1月3日）を除き、毎日6:30から23:00まで、ご利用いただけます。\n他に質問はありますか？";
	}

	if($bData== 'action=uc_2_2') {
		$resmess = "個人番号カードを持っていればコンビニで住民票が発行できて便利ですよ。\n他に質問はありますか？";
	}

	if($bData== 'action=uc_2_3') {
		$resmess = "もし、個人番号カードを持っていればコンビニで住民票が発行できて便利ですよ。\n他に質問はありますか？";
	}

	$response_format_text = [
			"type" => "text",
			"text" => $resmess
	];
	goto lineSend;
}

//メッセージ以外のときは何も返さず終了
if($type != "text"){
	if($type == "image"){
		/*
		$imagedata = "https://" . $_SERVER ['SERVER_NAME'] . "/gyosei.jpg";
		$api_url = 'https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classify';
		$response = file_get_contents($api_url.'?api_key='.$VRkey.'&url='.$imagedata.'&version=2016-05-20');
		$json = json_decode ( $response, true );
		*/

		$messageId = $jsonObj->{"events"} [0]->{"message"}->{"id"};

		// 画像ファイルのバイナリ取得
		$ch = curl_init ( "https://api.line.me/v2/bot/message/" . $messageId . "/content" );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'Content-Type: application/json; charser=UTF-8',
				'Authorization: Bearer ' . $accessToken
		) );
		$result = curl_exec ( $ch );

		//$data = [ 'images_file' => '@' . file_get_contents("https://" . $_SERVER ['SERVER_NAME'] . "/gyosei.jpg") ];
		$url = "https://" . $_SERVER ['SERVER_NAME'] . "/gyosei.jpg";
		//$data= file_get_contents ( $url );
		$data= $result;
		//$data = array("images_file" => $result, "classifier_ids" => "garbage");
		//$data = array("images_file" => $result);
		//$data = imagecreatefromstring($result);
		if($data == false){
			error_log("イメージ変換エラー");
		}
		$url = 'https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classify?api_key='.$VRkey.'&version=2016-05-20';
		$jsonString = callVisual_recognition();
		$json = json_decode($jsonString, true);

		error_log($json ["images"][0]["classifiers"] [0]["classes"][0]["class"]);
		error_log($json ["images"][0]["classifiers"] [0]["classes"][0]["score"]);
		error_log("images:".count($json ["images"]));
		error_log("images_processed:".$json ["images_processed"]);

		$resmess = $json ["images"][0]["classifiers"] [0]["classes"][0]["score"] . "の確率で「".$json ["images"][0]["classifiers"] [0]["classes"][0]["class"]."」です";
		$response_format_text = [
				"type" => "text",
				"text" => $resmess
		];

		goto lineSend;
	}
	exit;
}

//山口スペシャル開始
if($text == "私が投票できる場所はどこ。"){
	$resmess = "あなたの住所を教えてください。";
	$response_format_text = [
			"type" => "text",
			"text" => $resmess
	];
	goto lineSend;
}
if($text == "立川市曙町2丁目です。"){
	$resmess = "あなたの投票所は立川小学校です。\nまた、７月１９日から２５日までは市役所本庁、立川公民館で期日前投票ができます。";
	$response_format_text = [
			"type" => "text",
			"text" => $resmess
	];
	goto lineSend;
}

//山口スペシャル終了

//$url = "https://gateway.watson-j.jp/natural-language-classifier/api/v1/classifiers/".$classfier."/classify?text=".$text;
//$url = "https://gateway.watson-j.jp/natural-language-classifier/api/v1/classifiers/".$classfier."/classify";
$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id."/message?version=2017-04-21";

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

$tdate = date("YmdHis");
if ($link) {
	$result = pg_query("SELECT * FROM cvsdata WHERE userid = '{$userID}'");
	if (pg_num_rows($result) == 0) {
		error_log("データなし");
		$jsonString = callWatson();
		$json = json_decode($jsonString, true);
		$conversation_id = $json["context"]["conversation_id"];
		$conversation_node = "root";
		$sql = "INSERT INTO cvsdata (userid, conversationid, dnode, time) VALUES ('{$userID}','{$conversation_id}','{$conversation_node}','{$tdate}')";
		$result_flag = pg_query($sql);
	}else{
		error_log("データあり");
		$row = pg_fetch_row($result);
		$conversation_id = $row[1];
		$conversation_node= $row[2];
		$conversation_time= $row[3];
		$timelag = $tdate - $conversation_time;
		if($timelag > 1000){
			$jsonString = callWatson();
			$json = json_decode($jsonString, true);
			$conversation_id = $json["context"]["conversation_id"];
			$conversation_node = "root";
		}
	}
}

$data["context"] = array("conversation_id" => $conversation_id,
		"system" => array("dialog_stack" => array(array("dialog_node" => $conversation_node)),
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

$resmess= $json["output"]["text"][0];
$conversation_node = $json["context"]["system"]["dialog_stack"][0]["dialog_node"];

if($resmess== "usrChoise_1"){
	$resmess = "お調べしますので、あなたのお住いの地区名を下記から選択してください。";
	$response_format_text = [
			"type" => "template",
			"altText" => "this is a buttons template",
			"template" => [
					"type" => "buttons",
					"text" => $resmess,
					"actions" => [
							[
									"type" => "postback",
									"label" => "①○○地区、△△地区、□□地区",
									"data" => "action=uc_1_1"
							],
							[
									"type" => "postback",
									"label" => "②●●地区、▲▲地区、■■地区",
									"data" => "action=uc_1_2"
							],
							[
									"type" => "postback",
									"label" => "③Ａ地区、Ｂ地区、Ｃ地区",
									"data" => "action=uc_1_3"
							],
							[
									"type" => "postback",
									"label" => "④あ地区、い地区、う地区",
									"data" => "action=uc_1_4"
							]
					]
			]
	];
	goto lineSend;
}

if($resmess== "usrChoise_2"){
	$resmess = "住民票の写しは行政市役所本庁舎、行政第一支所、行政第二支所の窓口で発行できます。\n受付時間は、月曜日～金曜日の午前8時30分～午後5時です。\nちなみに個人番号カードはお持ちですか？";
	$response_format_text = [
			"type" => "template",
			"altText" => "this is a buttons template",
			"template" => [
					"type" => "buttons",
					"text" => $resmess,
					"actions" => [
							[
									"type" => "postback",
									"label" => "１．はい",
									"data" => "action=uc_2_1"
							],
							[
									"type" => "postback",
									"label" => "２．いいえ",
									"data" => "action=uc_2_2"
							],
							[
									"type" => "postback",
									"label" => "３．わからない",
									"data" => "action=uc_2_3"
							]
					]
			]
	];
	goto lineSend;
}

//日本語以外の場合は翻訳
if($language != "ja"){
	$data = array('text' => $resmess, 'source' => 'ja', 'target' => $language);
	$resmess = callWatsonLT2();
}
//改行コードを置き換え
$resmess = str_replace("\\n","\n",$resmess);

$response_format_text = [
    "type" => "text",
	"text" => $resmess
];

lineSend:
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


if (!$link) {
	error_log("接続失敗です。".pg_last_error());
}else{
	if(strlen($text) > 200){
		$text = mb_substr($text,0,199,"utf-8");
	}
	if(strlen($resmess) > 200){
		$resmess= mb_substr($resmess,0,199,"utf-8");
	}
	//シングルコーテーションを除去
	$Utext= str_replace("'","",$Utext);
	$resmess = str_replace("'","",$resmess);
	$sql = "INSERT INTO botlog (time, userid, contents, return) VALUES ('{$tdate}','{$userID}','{$Utext}','{$resmess}')";
	$result_flag = pg_query($sql);
	if (!$result_flag) {
		error_log("インサートに失敗しました。".pg_last_error());
	}
	$sql = "UPDATE cvsdata SET conversationid = '{$conversation_id}', dnode = '{$conversation_node}', time = '{$tdate}' WHERE userid = '{$userID}'";
	$result_flag = pg_query($sql);
	if (!$result_flag) {
		error_log("アップデートに失敗しました。".pg_last_error());
	}
}

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

function callWatsonLT1(){
	global $curl, $LTuser, $LTpass, $Ltext, $options;
	$curl = curl_init("https://gateway.watsonplatform.net/language-translator/api/v2/identify");

	$options = array(
			CURLOPT_HTTPHEADER => array(
					'content-type: text/plain','accept: application/json'
			),
			CURLOPT_USERPWD => $LTuser. ':' . $LTpass,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $Ltext,
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	return curl_exec($curl);
}

function callWatsonLT2(){
	global $curl, $LTuser, $LTpass, $Ltext, $data, $options;
	$curl = curl_init("https://gateway.watsonplatform.net/language-translator/api/v2/translate");

	$options = array(
			CURLOPT_HTTPHEADER => array(
					'content-type: application/json','accept: application/json'
			),
			CURLOPT_USERPWD => $LTuser. ':' . $LTpass,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	$jsonString= curl_exec($curl);
	$json = json_decode($jsonString, true);
	return $json["translations"][0]["translation"];
}

function callVisual_recognition(){
	global $curl,$url,$options,$data;

	$curl = curl_init($url);
	$options = array (
			CURLOPT_POST=> TRUE ,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_RETURNTRANSFER =>TRUE
	);

	curl_setopt_array ( $curl, $options );
	return curl_exec ( $curl );
}