<?php
/*------------------------------------------------------------

	File_name    : multisend.php
	Description  : the text for mass E-mail
	Charset      : utf-8
	The date     : 2011.03.26
	Last up date : 2012.03.14
	
-------------------------------------------------------------- */
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/JSON.php';
require_once dirname(__FILE__)."/mailer.php";
$mailer = new Mailer();

$subject = $_REQUEST['subject'];
$mail_msg = "☆━━━━━━━━【　".$subject."　】━━━━━━━━☆\n\n";
$mail_msg .= "このたびは、タカハマライフアートをご利用いただき誠にありがとうございます。\n";
$mail_msg .= $_REQUEST['myname']." 様からの御見積りのお知らせです。\n\n";

/*
$mail_msg .= "<==========  年末年始休業のお知らせ  ==========>\n";
$mail_msg .= "12月29日から1月9日までは、休業となります。\n";
$mail_msg .= "年始に納品を希望されるお客様は、12月22日（木）までにお早めのご注文をお願い致します。\n";
$mail_msg .= "休業期間中にご連絡を頂いた場合、1月10日以降の対応となりますのでご了承下さいませ。\n\n";
*/				

$mail_msg .= "┏━━━━━━━━━┓\n";
$mail_msg .= "◆　　お見積り内容\n";
$mail_msg .= "┗━━━━━━━━━┛\n\n";

/*
$mail_msg .= "◇発送日：　今！お電話で注文確定すると".$fin['Year']."年".$fin['Month']."月".$fin['Day']."日に発送です！\n";
*/


$mail_msg .= "◇商品名：　".$_REQUEST['item_name']."\n";
$mail_msg .= "◇枚　数：　".$_REQUEST['amount']."\n";
$mail_msg .= "------------------------------------------\n\n";
$mail_msg .= "◇制作費TOTAL：　".$_REQUEST['total']."\n";
$mail_msg .= "◇１枚あたり：　".$_REQUEST['per']."\n";
$mail_msg .= "━━━━━━━━━━━━━━━━━━━━━\n\n";

/*
$prm = array('total'=>"制作費TOTAL",'per'=>"１枚あたり",'amount'=>"枚数");
foreach($prm as $key=>$val){
	$mail_msg .= "◇".$val."：　".$_REQUEST[$key]."\n";
}
for($i=0; $i<count($_REQUEST['item_name']); $i++){
	$mail_msg .= "------------------------------------------\n\n";
	$mail_msg .= "◇品番・商品名：　".$_REQUEST['item_code'][$i]." ".$_REQUEST['item_name'][$i]."\n";
	$mail_msg .= "◇カラー：　".$_REQUEST['color_name'][$i]."\n";
	$mail_msg .= "◇サイズ：　".$_REQUEST['size_name'][$i]."\n";
	$mail_msg .= "◇枚数：　".$_REQUEST['volume'][$i]."\n";
	$mail_msg .= "◇単価：　".$_REQUEST['price'][$i]."\n";
}
 */


$mail_msg .= "◇プリント位置：\n";
for($i=0; $i<count($_REQUEST['pos']); $i++){
	$mail_msg .= "　・".$_REQUEST['pos'][$i]."\n";
}
$mail_msg .= "━━━━━━━━━━━━━━━━━━━━━\n\n";
$mail_msg .= "┏━━━━━━━━┓\n";
$mail_msg .= "◆　　メッセージ\n";
$mail_msg .= "┗━━━━━━━━┛\n";
$mail_msg .= $_REQUEST['message']."\n";
$mail_msg .= "━━━━━━━━━━━━━━━━━━━━━\n\n";
$mail_msg .= "このアイテムの写真は　"._DOMAIN.$_REQUEST['pageurl']."\n";
$mail_msg .= "━━━━━━━━━━━━━━━━━━━━━\n\n";

for($i=0; $i<count($_REQUEST['email']); $i++){
	if(!empty($_REQUEST['email'][$i])) $emails[] = $_REQUEST['email'][$i];
}
$fromname = $_REQUEST['myname'];
$fromaddr = $_REQUEST['myemail'];

$result = $mailer->send_multi($mail_msg,$subject,$emails,$fromname,$fromaddr);

$json = new Services_JSON();
$res = $json->encode($result);
header("Content-Type: text/javascript; charset=utf-8");
echo $res;
?>
