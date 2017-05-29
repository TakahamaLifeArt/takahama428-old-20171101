<?php
ini_set('memory_limit', '128M');
require_once dirname(__FILE__).'/php_libs/mailer.php';
require_once dirname(__FILE__).'/php_libs/conndbpost.php';

if( isset($_REQUEST['ticket'], $_REQUEST['u']) ) {
	$conndb = new Conndbpost();
	
	$newpass = substr(sha1(time().mt_rand()),0,10);
	$args = array('userid'=>$_REQUEST['u'], 'pass'=>$newpass);
	$res = $conndb->updatePass($args);
	if($res){
		$dat = $conndb->getUserList($_REQUEST['u']);
		$args = array('email'=>$dat[0]['email'], 'newpass'=>$newpass, 'username'=>$dat[0]['customername']);
		$mailer = new Mailer($args);
		$isSend = $mailer->send();
	}
}
/*
else{
	unset($_SESSION['ticket']);
	header("Location: "._DOMAIN);
}
*/
/* セッションの使用を廃止
if($isSend){
	unset($_SESSION['ticket']);
}
*/
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="keywords" content="<?php echo $categoryname; ?>,オリジナル<?php echo $categoryname; ?>,作成,プリント,東京,即日,最短" />
	<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
	<title>メール送信 | タカハマライフアート</title>
	<link rel="shortcut icon" href="/icon/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="/common/css/common_responsive.css" media="all" />
	<link rel="stylesheet" type="text/css" href="/common/css/base_responsive.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="./css/finish_responsive.css" media="screen" />
<!-- m3 begin -->
	<link rel="stylesheet" type="text/css" href="/m3/common/css/common_responsive.css" media="all">
	<link rel="stylesheet" type="text/css" href="/m3/common/css/slidebars_responsive.css" media="all">
	<link rel="stylesheet" href="/m3/common/css/import_responsive.css">
	<link rel="stylesheet" href="/m3/items/css/detail_responsive.css">
<!-- m3 end -->
	<script type="text/javascript" src="/common/js/jquery.js"></script>
	<script type="text/javascript" src="/common/js/tlalib.js"></script>
	<!--m3 begin-->
	<script src="/m3/common/js/common1.js"></script>
	<!--m3 end-->
</head>

<body>

	<?php
		$php = file_get_contents($_SERVER['DOCUMENT_ROOT']."/common/inc/header.php");
		eval('?>'. mb_convert_encoding($php, 'UTF-8', 'euc-jp'). '<?');
		$php = file_get_contents($_SERVER['DOCUMENT_ROOT']."/common/inc/globalmenu.php");
		eval('?>'. mb_convert_encoding($php, 'UTF-8', 'euc-jp'). '<?');
	?>

	<!-- m3 begin -->
	<header id="header" class="head2">
		<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc_utf8/header.html"); ?>
	</header>
	<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc_utf8/gnavi.html"); ?>
	<!-- m3 end -->
	
	<div id="container">
		<?php
			$php = file_get_contents($_SERVER['DOCUMENT_ROOT']."/common/inc/sidenavi.php");
			eval('?>'. mb_convert_encoding($php, 'UTF-8', 'euc-jp'). '<?');
		?>
		
		<div class="contents">
			
			<?php
				$cst = 'cst';
				function cst($constant){
					return $constant;
				}
				if($isSend){
					$heading = '仮パスワードを送信しています。<br>ご確認ください！';
					$sub = 'Sending';
					$html = <<<DOC
				<div class="inner">
					<p>この度はタカハマライフアートをご利用いただき、誠にありがとうございます。</p>
					<p>仮パスワードは、ログイン後にマイページで変更できます。</p>
				</div>
				<div class="inner">
					<h3>【 <span class="highlights">メールが届かない場合</span> 】</h3>
					<p>
						お客様が入力されました {$args['email']} 宛てに確認メールを返信しておりますが。届かない場合には、<br>
						お手数ですが下記の連絡先までお問い合わせください。<br>
						お急ぎのお客様は、フリーダイヤル {$cst(_TOLL_FREE)} までお気軽にご連絡ください。
					</p>
					<p><a href="/contact/">メールでのお問い合わせはこちらから</a></p>
					<hr />
					<p class="gohome"><a href="/">ホームページに戻る</a></p>
				</div>
DOC;

				}else{
					$heading = '送信エラー！';
					$sub = 'Error';
					$html = <<<DOC
				<div class="inner">
					<div class="remarks">
						<p><strong>メールの送信が出来ませんでした。</strong></p>
						<p>メールの送信中にエラーが発生いたしました。</p>
					</div>
					<p>恐れ入りますが、再度 [ 送信 ] ボタンをクリックして下さい。</p>
				</div>
				<div class="inner">
					<h3>【 親切対応でしっかりサポート 】</h3>
					<p class="note">お急ぎのお客様は、フリーダイヤル {$cst(_TOLL_FREE)} までお気軽にご連絡ください。</p>
					<p><a href="/contact/">メールでのお問い合わせはこちらから</a></p>
					<hr />
					<p class="gohome"><a href="/">ホームページに戻る</a></p>
				</div>
DOC;
				}
			?>
			
			<div class="heading1_wrapper">
				<h1><?php echo $heading;?></h1>
				<p class="comment"></p>
				<p class="sub"><?php echo $sub;?></p>
			</div>
			<?php echo $html;?>
		</div>
		
	</div>
	
	<?php
		$php = file_get_contents($_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php");
		eval('?>'. mb_convert_encoding($php, 'UTF-8', 'euc-jp'). '<?');
	?>

<!--Yahoo!タグマネージャー導入 2014.04 -->
<script type="text/javascript">
  (function () {
    var tagjs = document.createElement("script");
    var s = document.getElementsByTagName("script")[0];
    tagjs.async = true;
    tagjs.src = "//s.yjtag.jp/tag.js#site=bTZi1c8";
    s.parentNode.insertBefore(tagjs, s);
  }());
</script>
<noscript>
  <iframe src="//b.yjtag.jp/iframe?c=bTZi1c8" width="1" height="1" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
</noscript>
<!-- m3 begin -->
<div id="phonepage">
<div id="fb-root"></div>
<div id="container">
	<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc_utf8/footer.html"); ?>
	<div class="sb-slidebar sb-right">
	<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/sidemenu.html"); ?>
	</div>
<!-- /container --></div>
</div>
<!-- m3 end -->
	
</body>
</html>
