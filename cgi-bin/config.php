<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/php_libs/conndb_holiday.php';
require_once dirname(__FILE__).'/JSON.php';

define('_DOMAIN', 'http://'.$_SERVER['HTTP_HOST']);

define('_DOC_ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
define('_SESS_SAVE_PATH', $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/sesstmp/');

define('_GUEST_IMAGE_PATH', 'user/guest/data/');
define('_MEMBER_IMAGE_PATH', 'user/member/data/');

define('_MAXIMUM_SIZE', 314572800);		// max upload file size is 100MB(1024*1024*300).

define('_ALL_EMAIL', 'all@takahama428.com');
define('_INFO_EMAIL', 'info@takahama428.com');
define('_ORDER_EMAIL', 'order@takahama428.com');
define('_REQUEST_EMAIL', 'request@takahama428.com');
define('_ESTIMATE_EMAIL', 'estimate@takahama428.com');

define('_TEST_EMAIL', 'test@takahama428.com');

define('_OFFICE_TEL', '03-5670-0787');
define('_OFFICE_FAX', '03-5670-0730');
define('_TOLL_FREE', '0120-130-428');

define('_PACK_FEE', 50);
define('_NO_PACK_FEE', 10);
define('_CREDIT_RATE', 0.05);	// カード手数料率

define('_API', 'http://takahamalifeart.com/v1/api');
define('_API_U', 'http://takahamalifeart.com/v1/apiu');
define('_API_PSS', 'http://takahama428.co-site.jp/v1/api');		// Photo Sharing Service Member
define('_IMG_PSS', 'http://takahamalifeart.com/weblib/img/');

// 注文情報の登録
define('_ORDER_INFO', 'http://original-sweat.com/system/php_libs/ordersinfo.php');

// マイページのイメージ画像で使用
define('_ORDER_DOMAIN', 'http://original-sweat.com');

// PASSWORD KEY
define('_PASSWORD_KEY', 'Rxjo:akLK(SEs!8E');

// Sweat Campaign 2011
define('_REDIRECT', 'http://www.takahama428.com/sweat_campaign/');

// Facebook App
define('_FB_APP_ID', '333981563415198');
define('_FB_APP_SECRET', 'd9d6f330b795e81af0d875c0e5b0d9a3');

// アイテム一覧ページで当日特急用サムネイルを使用する場合は 1、使用しない場合は 0 を設定
define('_IS_THUMB_FOR_EXPRESS', '1');

//本サイトの識別子
define('_SITE', '1');

//休業終始日付、お知らせの取得
$hol = new Conndb_holiday();
$holiday_data = $hol->getHolidayinfo();
if($holiday_data['notice']=="" && $holiday_data['notice-ext']==""){
	$notice = "";
	$extra_noitce = "";
}else{
	$notice = $holiday_data['notice'];
	$extra_noitce = $holiday_data['notice-ext'];
}
$time_start = str_replace("-","/",$holiday_data['start']);
$time_end = str_replace("-","/",$holiday_data['end']);

//休業終始日付、お知らせ
define('_FROM_HOLIDAY', $time_start);
define('_TO_HOLIDAY', $time_end);
/*
$_NOTICE_HOLIDAY = "\n<==========  年末年始のお知らせ  ==========>\n";
$_NOTICE_HOLIDAY .= "12月25日(金)から1月5日(火)の間、休業とさせて頂きます。\n";
$_NOTICE_HOLIDAY .= "休業期間中に頂きましたお問合せにつきましては、1月6日(水)からの対応とさせて頂きます。\n";
$_NOTICE_HOLIDAY .= "お急ぎの方はご注意下さい。何卒よろしくお願い致します。\n\n";
*/
//$_NOTICE_HOLIDAY = '';
//$_NOTICE_HOLIDAY = $_TEMPORARY_NOTICE;
define('_NOTICE_HOLIDAY', $notice);

/*
$_EXTRA_NOTICE = "\n\n<==========  アイテム価格改定のお知らせ  ==========>\n";
$_EXTRA_NOTICE .= "タカハマライフアートをご利用頂きありがとうございます。\n";
$_EXTRA_NOTICE .= "為替の影響と原産国の人件費の上昇による各社メーカーの値上げに伴い\n";
$_EXTRA_NOTICE .= "平成27年4月より当社もアイテムの値上げさせていただくことになりました。\n";
$_EXTRA_NOTICE .= "アイテムにより10%?30%値上げの予定です。\n";
$_EXTRA_NOTICE .= "そのため、3月中のお見積もり内容の有効期限を3月31日（火）ご注文確定分（13時まで）とさせていただきます。\n";
$_EXTRA_NOTICE .= "\n\n";
*/
//$_EXTRA_NOTICE = '';

define('_EXTRA_NOTICE', $extra_noitce);

//$_TEMPORARY_NOTICE_TITLE = "【夏季休業のお知らせ】";

//$_TEMPORARY_NOTICE_TITLE = "";

//	define('_TEMPORARY_NOTICE_TITLE', $_TEMPORARY_NOTICE_TITLE);

//$_TEMPORARY_NOTICE = "8月11日(木)から8月14日(日)の間、休業とさせて頂きます。<br>";
//$_TEMPORARY_NOTICE .="休業期間中に頂きましたお問合せにつきましては、8月15日(月)以降対応させて頂きます。<br>";
//$_TEMPORARY_NOTICE .="お急ぎの方はご注意下さい。何卒よろしくお願い致します。";

//$_TEMPORARY_NOTICE = "";

//	define('_TEMPORARY_NOTICE', $_TEMPORARY_NOTICE);
?>
