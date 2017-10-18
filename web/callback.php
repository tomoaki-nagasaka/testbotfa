<?php
//error_log("開始します");
date_default_timezone_set('Asia/Tokyo');
$tdate = date("YmdHis");

//環境変数の取得
$accessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN');
$classfier = getenv('CLASSFIER');
$workspace_id = getenv('CVS_WORKSPASE_ID');
$workspace_id_ken = getenv('CVS_WORKSPASE_ID_KEN');
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
//画像判定
$imageflg = false;
//DB更新可否フラグ
$dbupdateflg = true;
//処理モード
$shorimode = "";
//年齢
$age = "999";
//性別
$sex = "0";
//地域
$region = "000";
//言語
$lang = "01";


//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

//属性情報の読み込み
if ($link) {
	$result = pg_query("SELECT * FROM userinfo WHERE userid = '{$userID}'");
	if (pg_num_rows($result) != 0) {
		$row = pg_fetch_row($result);
		$lang = $row[2];
		$sex = $row[3];
		$age = $row[4];
		$region = $row[5];
	}
}

//友達追加時の処理
if($eventType == "follow"){
	$resmess = "こんにちは。\n行政市のすいか太郎です。\nまずは画面下の問い合わせメニューから、ご希望のメニューを選択してくださいね～";
	translation();
	$response_format_text = [
			"type" => "text",
			"text" => $resmess
	];
	/*
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
	 */
	$dbupdateflg = false;
	goto lineSend;
}

//処理モード変更時
if($text == "検診相談"){
	$shorimode = "01";
	$workspace_id = $workspace_id_ken;
}
if($text == "ごみの分別"){
	$shorimode = "02";
	$resmess = "捨てたいごみの写真を撮って送ってください。まわりに何もない写真の方が正確に判定できますよ～♪";
}
if($text == "図書検索"){
	$shorimode = "03";
	$resmess = "現在準備中です。他のメニューを選択してください。";
}
if($text == "その他のお問い合わせ"){
	$shorimode = "04";
	//$resmess = "まだ、勉強中なところが多いですが、質問にお答えしますよ～。\n聞きたいことを送信してくださいね。";
	$data = array('input' => array("text" => "初回発話"));
}
if($text == "属性登録"){
	$shorimode = "00";
}
//検診相談、属性登録の場合は年齢、性別が登録されているかを確認
if($shorimode == "01" or $shorimode == "00"){
	if($sex == "1"){
		$sexN= "男";
	}
	if($sex == "2"){
		$sexN= "女";
	}
	error_log("送信データ：".$age."の".$sexN);
	$data = array('input' => array("text" => $age."の".$sexN));
	if($sex == "0" or $age == "999"){
		$resmess = "申し訳ありませんが、先に画面下の「問い合わせメニュー」より、属性登録を選択して、年齢と性別を登録してください。";
	}
	if($resmess != ""){
		$dbupdateflg = false;
		translation();
		$response_format_text = [
				"type" => "text",
				"text" => $resmess
		];
		if($shorimode == "01"){
			goto lineSend;
		}
	}
}
//属性登録のリンク生成
if($shorimode == "00"){
	if($age < 10){
		$age = "00".$age;
	}else{
		if($age < 100){
			$age = "0".$age;
		}
	}
	$link = mb_substr($userID,0,1).$sex.mb_substr($userID,1,1).$age.mb_substr($userID,2,1).$region.mb_substr($userID,3);
	if($lang == "02"){
		$resmess = "以下のリンクより属性登録をお願いします。\nhttps://gyoseibot.herokuapp.com/attribute_en.php?user=".$link;
	}else{
		$resmess = "以下のリンクより属性登録をお願いします。\nhttps://gyoseibot.herokuapp.com/attribute.php?user=".$link;
	}
}
if($shorimode == "01" or $shorimode == "04"){
	//CVSの初回呼び出し
	$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id."/message?version=2017-04-21";
	$jsonString = callWatson();
	$json = json_decode($jsonString, true);
	$conversation_id = $json["context"]["conversation_id"];
	$resmess= $json["output"]["text"][0];
	//改行コードを置き換え
	$resmess = str_replace("\\n","\n",$resmess);
	$conversation_node = $json["context"]["system"]["dialog_stack"][0]["dialog_node"];
	error_log("$conversation_node=".$conversation_node);
	if ($link) {
		$result = pg_query("SELECT * FROM cvsdata WHERE userid = '{$userID}'");
		if (pg_num_rows($result) == 0) {
			$sql = "INSERT INTO cvsdata (userid, conversationid, dnode, time) VALUES ('{$userID}','{$conversation_id}','{$conversation_node}','{$tdate}')";
			$result_flag = pg_query($sql);
		}else{
			error_log("データあり");
			$sql = "UPDATE cvsdata SET conversationid = '{$conversation_id}', dnode = '{$conversation_node}', time = '{$tdate}' WHERE userid = '{$userID}'";
			$result_flag = pg_query($sql);
		}
	}
}

if($shorimode != ""){
	if ($link) {
		$result = pg_query("SELECT userid FROM userinfo WHERE userid = '{$userID}' ");
		if (pg_num_rows($result) == 0) {
			$sql = "INSERT INTO userinfo (userid, sex, age, region, sposi, time) VALUES ('{$userID}','0','999','000','{$shorimode}','{$tdate}')";
			$result_flag = pg_query($sql);
			if (!$result_flag) {
				error_log("インサートに失敗しました。".pg_last_error());
			}
		}else{
			$sql = "UPDATE userinfo SET sposi = '{$shorimode}', time = '{$tdate}' WHERE userid = '{$userID}'";
			$result_flag = pg_query($sql);
			if (!$result_flag) {
				error_log("アップデートに失敗しました。".pg_last_error());
			}
		}
	}
	$dbupdateflg = false;
	translation();
	$response_format_text = [
			"type" => "text",
			"text" => $resmess
	];
	goto lineSend;
}

//処理モード確認
if ($link) {
	$result = pg_query("SELECT sposi,time FROM userinfo WHERE userid = '{$userID}' ");
	if (pg_num_rows($result) == 0) {
		$shorimode = "00";
	}else{
		$row = pg_fetch_row($result);
		$timelag = $tdate - $row[1];
		if($timelag > 1000){
			$shorimode = "00";
		}else{
			$shorimode = $row[0];
		}
	}
	if($shorimode == "00" ){
		$resmess = "申し訳ありませんが、先に画面下の「問い合わせメニュー」より、メニューを選択してください。";
		translation();
		$response_format_text = [
				"type" => "text",
				"text" => $resmess
		];
		$dbupdateflg = false;
		goto lineSend;
	}
}

//処理モードによる振り分け
switch ($shorimode){
	//検診相談
	case "01":
		goto PROC01;
		break;
	//ゴミ分別
	case "02":
		goto PROC02;
		break;
	//図書検索
	case "03":
		goto PROC03;
		break;
	//その他
	case "04":
		goto PROC04;
		break;
	//その他
	default:
		break;
}

//検診相談
PROC01:
/*
$resmess = "現在準備中です。他のメニューを選択してください。";
$response_format_text = [
		"type" => "text",
		"text" => $resmess
];
$dbupdateflg = false;
goto lineSend;
*/
$workspace_id = $workspace_id_ken;
goto PROC04;

//ゴミ分別
PROC02:
if($type == "image"){
	$imageflg = true;
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
	$bimage = curl_exec ( $ch );

	$arrTime = explode('.',microtime(true));
	$fdate = date("His").$arrTime[1];
	$fname = "/tmp/".$fdate.".jpg";

	//画像を保存
	$fp = fopen($fname, 'wb');
	if ($fp){
		if (flock($fp, LOCK_EX)){
			if (fwrite($fp,  $bimage) === FALSE){
				error_log('ファイル書き込みに失敗しました');
			}else{
				error_log('ファイルに書き込みました');
			}

			flock($fp, LOCK_UN);
		}else{
			error_log('ファイルロックに失敗しました');
		}
	}

	fclose($fp);

	//$data= $result;

	$cfile = new CURLFile($fname,"image/jpeg","line_image.jpg");
	$data = array("images_file" => $cfile,"classifier_ids" => "garbage_481154027", "threshold" => 0.6);
	if($data == false){
		error_log("イメージ変換エラー");
	}
	$url = 'https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classify?api_key='.$VRkey.'&version=2016-05-20';
	$jsonString = callVisual_recognition();
	$json = json_decode($jsonString, true);

	//画像ファイル削除
	unlink($fname);

	//分類
	$class = $json ["images"][0]["classifiers"] [0]["classes"][0]["class"];
	//確信度
	$scoer = $json ["images"][0]["classifiers"] [0]["classes"][0]["score"] * 100;

	/*
	 error_log($json ["images"][0]["classifiers"] [0]["classes"][0]["class"]);
	 error_log($json ["images"][0]["classifiers"] [0]["classifier_id"]);
	 error_log($json ["images"][0]["classifiers"] [1]["classifier_id"]);
	 error_log($json ["images"][0]["classifiers"] [0]["classes"][0]["score"]);
	 error_log($json ["images"][0]["image"]);
	 error_log("classifiers:".count($json ["images"][0]["classifiers"]));
	 error_log("images:".count($json ["images"]));
	 error_log("images_processed:".$json ["images_processed"]);
	 */

	//$resmess = $scoer. "％の確率で「".$class."」です";
	$setsuzoku = "";
	if($scoer < 80){
		$setsuzoku = "おそらく";
	}
	switch ($class){
		//燃えるゴミ
		case "burnable":
			$resmess = "送信された画像は、".$setsuzoku."『可燃ゴミ』です。可燃ゴミの日に出してください。\n※確信度：".$scoer."％";
			break;
			//燃えないゴミ
		case "nonburnable":
			$resmess = "送信された画像は、".$setsuzoku."『不燃ゴミ』です。不燃ゴミの日に出してください。\n※確信度：".$scoer."％";
			break;
			//資源ゴミ
		case "resource":
			$resmess = "送信された画像は、".$setsuzoku."『資源ゴミ』です。資源ゴミの日に出してください。\n※確信度：".$scoer."％";
			break;
			//粗大ゴミ
		case "bulky":
			$resmess = "送信された画像は、".$setsuzoku."『粗大ゴミ』です。粗大ゴミの日に出してください。\n※確信度：".$scoer."％";
			break;
			//その他
		default:
			$resmess = "分類できませんでした。お手数ですが、042-521-4192 までお問い合わせください。";
			break;
	}
	translation();
	$response_format_text = [
			"type" => "text",
			"text" => $resmess
	];
}else{
	$resmess = "捨てたいごみの写真を撮って送ってください。まわりに何も写真の方が正確に判定できますよ～♪";
	translation();
	$response_format_text = [
			"type" => "text",
			"text" => $resmess
	];
	$dbupdateflg = false;
}

goto lineSend;

//図書検索
PROC03:
$resmess = "現在準備中です。他のメニューを選択してください。";
translation();
$response_format_text = [
		"type" => "text",
		"text" => $resmess
];
$dbupdateflg = false;
goto lineSend;

//その他の問い合わせ
PROC04:

//LT問い合わせ
/*TODO 多言語対応は保留
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
*/

translationJa();

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

	translation();
	$response_format_text = [
			"type" => "text",
			"text" => $resmess
	];
	goto lineSend;
}

//メッセージ以外のときは何も返さず終了
if($type != "text"){
	$resmess = "まだ、勉強中なところが多いですが、質問にお答えしますよ～。\n聞きたいことを送信してくださいね。";
	$dbupdateflg = false;
	goto lineSend;
}


//$url = "https://gateway.watson-j.jp/natural-language-classifier/api/v1/classifiers/".$classfier."/classify?text=".$text;
//$url = "https://gateway.watson-j.jp/natural-language-classifier/api/v1/classifiers/".$classfier."/classify";
$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id."/message?version=2017-04-21";

//$data = array("text" => $text);
$data = array('input' => array("text" => $text));

if ($link) {
	$result = pg_query("SELECT * FROM cvsdata WHERE userid = '{$userID}'");
	$row = pg_fetch_row($result);
	$conversation_id = $row[1];
	$conversation_node= $row[2];
	$conversation_time= $row[3];
}

//error_log("CVノード:".$conversation_node);
//検診相談でrootの場合は年齢、性別をセット
if($conversation_node == "root" and $shorimode == "01"){
	if($sex == "1"){
		$sexN= "男";
	}
	if($sex == "2"){
		$sexN= "女";
	}
	$data = array('input' => array("text" => $age."の".$sexN));
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
	translation();
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
	translation();
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

//改行コードを置き換え
$resmess = str_replace("\\n","\n",$resmess);

translation();
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
	if($imageflg){
		//バイナリデータをエスケープする
		$esc = pg_escape_bytea( $bimage);
		$sql = "INSERT INTO logimage (time, userid, image, scoer, class) VALUES ('{$tdate}','{$userID}','{$esc}','{$scoer}','{$class}')";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("インサートに失敗しました。".pg_last_error());
		}
	}else{
		if($dbupdateflg){
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
			//CURLOPT_POSTFIELDS => http_build_query($data),
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_RETURNTRANSFER =>TRUE
	);

	curl_setopt_array ( $curl, $options );
	return curl_exec ( $curl );
}

function translation(){
	global $resmess,$lang,$data;
	//日本語以外の場合は翻訳
	if($lang == "02"){
		$data = array('text' => $resmess, 'source' => 'ja', 'target' => 'en');
		$resmess = callWatsonLT2();
	}
}

function translationJa(){
	global $text,$lang,$data;
	//日本語以外の場合は翻訳
	if($lang == "02"){
		$data = array('text' => $resmess, 'source' => 'en', 'target' => 'ja');
		$text = callWatsonLT2();
	}
}