<?php
/**
 * メール送信モジュール用のコントローラー
 * charaset	utf-8
 * created	2017-08-29 デザインデータのアップロードを通知する
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/config.php';
require_once dirname(__FILE__).'/mailer.php';

if(isset($_REQUEST['act'])){
	$mailer = new Mailer($_REQUEST);
	switch($_REQUEST['act']){
		case 'fileupload':
			$dat = $mailer->send_upload_info();
			$res = array('response'=>$dat);
			break;
		default: $res = array('response'=>false);
	}
} else {
	$res = array('response'=>false);
}
$res = json_encode($res);
header("Content-Type: text/javascript; charset=utf-8");
echo $res;
?>
