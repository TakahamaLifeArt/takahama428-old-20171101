<?php
error_reporting(E_ALL | E_STRICT);
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/MyUploadHandler.php';
if(isset($_REQUEST['path_name'])){
	$pathName = $_REQUEST['path_name'];
}else{
	$pathName = '/user/support/data/files/';
}
$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
	!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
	strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
$full_url = ($https ? 'https://' : 'http://').
	(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
	(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
															 ($https && $_SERVER['SERVER_PORT'] === 443 ||
															  $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT'])));
$upload_handler = new MyUploadHandler(array(
	'upload_dir' => $_SERVER['DOCUMENT_ROOT'].$pathName,
	'upload_url' => $full_url.$pathName,
	'user_dirs' => true,
	'param_name' => 'files',
	'accept_file_types' => '/\.(ai|jpe?g|png|psd|pdf|zip)$/i',
	'over_write' => TRUE
));
