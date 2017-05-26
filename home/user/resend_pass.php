<?php
require_once dirname(__FILE__).'/php_libs/funcs.php';

if($_SERVER['REQUEST_METHOD']!='POST'){
	setToken();
}else{
	chkToken();
	
	if(isset($_POST['resend'])){
		$conndb = new Conndb(_API_U);

		$param['email'] = trim(mb_convert_kana($_POST['email'],"s", "utf-8"));
		$args = array($param['email']);

		if(empty($args['email'])){
			$err['email'] = 'メールアドレスを入力して下さい。';
		}else if(!isValidEmailFormat($args['email'])){
			$err['email'] = 'メールアドレスが正しくありません。';
		}else{
			$user = $conndb->checkExistEmail($args);
			$userid = $user['id'];
			if(!$userid) $err['email'] = 'メールアドレスのご登録がありません。';
		}
		
		if(empty($err)) jump('http://www.takahama428.com/design/designpost/transmit.php?ticket='.$_POST['token'].'&u='.$userid);
	}
	
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="euc-jp" />
<!-- m3 begin -->
	<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
<!-- m3 end -->
	<title>パスワードを忘れた方 ｜ オリジナルTシャツが早い、タカハマライフアート</title>
	<meta name="Description" content="オリジナルのTシャツを作るならタカハマライフアートが早い！タカハマライフアートをご利用のお客様へ、パスワードをお忘れですか？対処方法についての、ご案内をいたします。登録されたメールアドレスを入力してください。送信ボタンをクリックした後、登録された連絡先メールアドレス宛にお送り致します。">
	<meta name="keywords" content="オリジナル,tシャツ,パスワード">
	<link rel="shortcut icon" href="/icon/favicon.ico" />
<!-- m3 begin -->
	<link rel="stylesheet" type="text/css" href="/m3/common/css/common_responsive.css" media="all">
	<link rel="stylesheet" type="text/css" href="/m3/common/css/slidebars_responsive.css" media="all">
	<link rel="stylesheet" href="/m3/common/css/import_responsive.css">
	<link rel="stylesheet" href="/m3/items/css/detail_responsive.css">
<!-- m3 end -->
	<link rel="stylesheet" type="text/css" media="screen" href="/common/css/common_responsive.css">
	<link rel="stylesheet" type="text/css" media="screen" href="/common/css/base_responsive.css">
	<link rel="stylesheet" type="text/css" media="screen" href="/common/js/modalbox/css/jquery.modalbox.css">
	<link rel="stylesheet" type="text/css" media="screen" href="./css/style_responsive.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="./css/account_responsive.css" />
	<script type="text/javascript" src="/common/js/jquery.js"></script>
	<script type="text/javascript" src="/common/js/modalbox/jquery.modalbox-min.js"></script>
	<script type="text/javascript" src="./js/common.js"></script>
	<script type="text/javascript" src="./js/resendpass.js"></script>
	<!-- OGP -->
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#  website: http://ogp.me/ns/website#">
	<meta property="og:title" content="世界最速！？オリジナルTシャツを当日仕上げ！！" />
	<meta property="og:type" content="article" /> 
	<meta property="og:description" content="業界No. 1短納期でオリジナルTシャツを1枚から作成します。通常でも3日で仕上げます。" />
	<meta property="og:url" content="http://www.takahama428.com/" />
	<meta property="og:site_name" content="オリジナルTシャツ屋｜タカハマライフアート" />
	<meta property="og:image" content="http://www.takahama428.com/common/img/header/Facebook_main.png" />
	<meta property="fb:app_id" content="1605142019732010" />
	<!--  -->
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-11155922-2']);
		_gaq.push(['_trackPageview']);
		
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
	<!--m3 begin-->
	<script src="/m3/common/js/common1.js"></script>
	<!--m3 end-->
</head>
<body>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-T5NQFM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T5NQFM');</script>
<!-- End Google Tag Manager -->

	<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/header.php"; ?> 
	<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/globalmenu.php"; ?>
	<!-- m3 begin -->
	<header id="header" class="head2">
		<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc/header.html"); ?>
	</header>
	<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc/gnavi.html"); ?>
	<!-- m3 end -->
	<div id="container">
	
		<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/sidenavi.php"; ?>
		
		<div class="contents">
			
			<div class="toolbar">
				<div class="toolbar_inner clearfix">
					<div class="menu_wrap">
						<?php if(checkLogin()) echo $menu;?>
					</div>
					<h1>パスワードを忘れた方</h1>
				</div>
			</div>
			
			<div class="section">
				<form name="pass" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return false;">
					<table class="form_table me" id="pass_table">
						<caption>登録されたメールアドレスに仮パスワードを送信いたします。</caption>
						<tfoot>
							<tr>
								<td colspan="2">
									<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
									<input type="hidden" name="resend" value="1">
									<p><span class="ok_button">送信</span></p>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<th>メールアドレス</th>
								<td><input type="text" name="email" value="<?php echo $_POST['email'];?>"><br><ins class="err"> <?php echo $err['email']; ?></ins></td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
		</div>
	
	</div>
	
	<p class="scroll_top"><a href="#header">パスワードを忘れた方　ページトップへ</a></p>

	<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php"; ?>
	

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
	<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc/footer.html"); ?>
	<div class="sb-slidebar sb-right">
	<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/sidemenu.html"); ?>
	</div>
<!-- /container --></div>
</div>
<!-- m3 end -->

</body>
</html>