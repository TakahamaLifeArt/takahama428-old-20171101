<?php
session_start();

//各種設定
$ipAddress = NULL;
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $ipAddress = $_SERVER['REMOTE_ADDR'];
}

$scriptFlag = true;
define('DIR_DOCUMENT_ROOT', '/virtual/119.245.187.244/home');
if (!empty($_SERVER['DOCUMENT_ROOT'])) {
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
} else {
    $documentRoot = DIR_DOCUMENT_ROOT;
}
define('DIR_APPLOG_ROOT', $documentRoot . '/../data/applog/');
define('DIR_THEME_ROOT', $documentRoot . '/app/WP2/wp-content/themes/careta/');
define('EMAIL_ADMIN', 'takahamainfo@gmail.com');
//define('EMAIL_ADMIN', 'takahamainfo@gmail.com');
$emailAdminList = array(EMAIL_ADMIN,);
define('MAIL_SUBJECT_ADMIN', '[管理者控え]お問い合わせを受け付けました');
define('MAIL_SUBJECT_USER', 'お問い合わせを受け付けました');
define('PATH_TO_DEBUG_LOG_FILE', DIR_APPLOG_ROOT . 'debug.log');
define('PATH_TO_CONTACT_LOG_FILE', DIR_APPLOG_ROOT . 'contact.csv');

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('log_errors', true);
ini_set('error_log', DIR_APPLOG_ROOT . 'error_log.log');



//デバッグログ出力設定
$flpath = PATH_TO_DEBUG_LOG_FILE;
$fp = fopen($flpath, 'a');
fwrite($fp, date('Y/m/d H:i:s') . ":[" . $ipAddress . "]start\n");


//CSRF対策
$postCfMytoken = NULL;
if (!empty($_POST['cf_mytoken'])) {
    $postCfMytoken = $_POST['cf_mytoken'];
}
$sessionCfMytoken = NULL;
if (!empty($_SESSION['contactform']['mytoken'])) {
    $sessionCfMytoken = $_SESSION['contactform']['mytoken'];
}


//if (empty($postCfMytoken) || empty($sessionCfMytoken) || ($sessionCfMytoken != $postCfMytoken)) {
//    $errMsg[] = '[01]システムエラーが発生してください。 postCfMytoken[' . $postCfMytoken . '] sessionCfMytoken[' . $sessionCfMytoken . ']';
//    fwrite($fp, date('Y/m/d H:i:s') . ":" . implode(',', $errMsg) . "\n");
//    $result = -1;
//    header('Content-type: application/json');
//    print json_encode($result);
//    exit;
//}





//入力チェック
$errMsg = array();
$cf_id_title = NULL;
if (!empty($_POST['cf_id_title'])) {
    $cf_id_title = $_POST['cf_id_title'];
}
$cf_name = NULL;
if (empty($_POST['cf_name'])) {
    $errMsg[] = 'お名前を入力してください。';
} elseif (!empty($_POST['cf_name'])) {
    $cf_name = $_POST['cf_name'];
}
$cf_email = NULL;
if (empty($_POST['cf_email'])) {
    $errMsg[] = 'メールアドレスをを入力してください。';
} elseif (!empty($_POST['cf_email'])) {
    $cf_email = $_POST['cf_email'];
}
$cf_tel = NULL;
if (empty($_POST['cf_tel'])) {
    $errMsg[] = 'お電話を入力してください。';
} elseif (!empty($_POST['cf_tel'])) {
    $cf_tel = $_POST['cf_tel'];
}
$cf_category = NULL;
if (!empty($_POST['cf_category'])) {
    $cf_category = $_POST['cf_category'];
}
$cf_destination = NULL;
if (!empty($_POST['cf_destination'])) {
    $cf_destination = $_POST['cf_destination'];
}
$cf_delivery_date = NULL;
if (!empty($_POST['cf_delivery_date'])) {
    $cf_delivery_date = $_POST['cf_delivery_date'];
}
$cf_message = NULL;
if (!empty($_POST['cf_message'])) {
    $cf_message = $_POST['cf_message'];
}



//エラーチェック
if (is_array($errMsg) && count($errMsg) >= 1) {
    $result = 0;
    header('Content-type: application/json');
    print json_encode($result);
    exit;
}



$message = file_get_contents(DIR_THEME_ROOT . 'cf_mail.php');
$message = str_replace('{{cf_id_title}}', $cf_id_title, $message);
$message = str_replace('{{cf_name}}', $cf_name, $message);
$message = str_replace('{{cf_email}}', $cf_email, $message);
$message = str_replace('{{cf_tel}}', $cf_tel, $message);
$message = str_replace('{{cf_category}}', $cf_category, $message);
$message = str_replace('{{cf_destination}}', $cf_destination, $message);
$message = str_replace('{{cf_delivery_date}}', $cf_delivery_date, $message);
$message = str_replace('{{cf_message}}', $cf_message, $message);



//メール送信処理
mb_language('Japanese');
mb_internal_encoding('UTF-8');

//メール送信(管理者へ)
$to = implode(',', $emailAdminList);
$subject = MAIL_SUBJECT_ADMIN;
$headers = 'From: ' . $cf_email . "\r\n";
mb_send_mail($to, $subject, $message, $headers);

//メール送信(ユーザーへ)
$to = $cf_email;
$subject = MAIL_SUBJECT_USER;
$headers = 'From: ' . EMAIL_ADMIN . "\r\n";
mb_send_mail($to, $subject, $message, $headers);



//お問い合わせログ出力
$contactLogData = array(
    date('Y/m/d H:i:s'),
    $ipAddress,
    $cf_id_title,
    $cf_name,
    $cf_email,
    $cf_tel,
    $cf_category,
    $cf_destination,
    $cf_delivery_date,
    $cf_message,
);
foreach ($contactLogData as $key => $value) {
    $value = preg_replace("/,/", "、", $value);
    $value = preg_replace("/(\r\n|\r|\n)/", "<br>", $value);
    $contactLogData[$key] = mb_convert_encoding($value, 'SJIS-WIN', 'UTF8');
}


//CSV出力
$flpath2 = PATH_TO_CONTACT_LOG_FILE;
$fp2 = fopen($flpath2, 'a');
$str = implode(',', $contactLogData) . "\r\n";
fwrite($fp2, $str);
fclose($fp2);


fwrite($fp, date('Y/m/d H:i:s') . ":[" . $ipAddress . "]finish\n");


//デバッグログ終了
fclose($fp);


$result = 1;

header('Content-type: application/json');
print json_encode($result);
exit;
