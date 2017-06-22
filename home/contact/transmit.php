<?php
	ini_set('memory_limit', '128M');
	include $_SERVER['DOCUMENT_ROOT']."/common/inc/pageinit.php";
	require_once $_SERVER['DOCUMENT_ROOT'].'/php_libs/mailer.php';
/*
 *	$info
 *		['title']			express
 *		['customername']
 *		['email']
 *		['tel']
 *		['message']
 *		['deliveryday']
 *		['category']
 *		['amount']
 *		['printinfo']
 *
 *	$_FILES
 *		['attachfile'][]	複数対応のため配列
 *		
 */


	if( isset($_POST['ticket'], $_SESSION['ticket']) ) {
		$titles = array(
			'info'=>'お問い合わせ',
			'request'=>'資料請求', 
			'estimate'=>'お見積問合せ',
			'test'=>'テスト',
			'minit'=>'ユニフォームミニTお申し込み',
			'illusttemplate'=>'イラレ入稿テンプレート',
			'repeat'=>'追加注文',
			'visit'=>'出張打ち合わせ',
			'expresstoday'=>'当日特急プラン',
			'towel'=>'オリジナルタオルお問い合わせ',
			'designconsierge'=>'デザインコンシェルジュ',
			'orange'=>'俺んじ君ワークショップお申し込み'

		);

		$title = $titles[$_POST['title']];
		//$customer = mb_convert_encoding($_POST['customername'], 'euc-jp', auto);
		$customer = $_POST['customername'];
		$mailer = new Mailer($_POST);
		if($_POST['title']!='repeat'){
			$isSend = $mailer->send();
		}else{
			$isSend = $mailer->send_repeat();
		}
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T5NQFM');</script>
<!-- End Google Tag Manager -->
	<meta charset="euc-jp" />
<!-- m3 begin -->
	<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
<!-- m3 end -->
	<title>メール送信完了　|　オリジナルTシャツ屋タカハマライフアート</title>
	<meta name="Description" content="1枚〜大量のプリントまで、トレーナー・ポロシャツ・オリジナルTシャツの作成・プリントは、東京都葛飾区のタカハマライフアートにお任せ下さい！団体やグループなどで着用し、文化祭、体育祭のイベントを盛り上げてください。" />
	<meta name="keywords" content="オリジナル,Tシャツ,東京,作成,プリント" />
	<meta name="google-site-verification" content="PfzRZawLwE2znVhB5M7mPaNOKFoRepB2GO83P73fe5M" />
	<link rel="shortcut icon" href="/icon/favicon.ico" />
<!-- m3 begin -->
	<link rel="stylesheet" type="text/css" href="/m3/common/css/common_responsive.css" media="all">
	<link rel="stylesheet" type="text/css" href="/m3/common/css/slidebars_responsive.css" media="all">
	<link rel="stylesheet" href="/m3/common/css/import_responsive.css">
	<link rel="stylesheet" href="/m3/items/css/detail_responsive.css">
<!-- m3 end -->
	<link rel="stylesheet" type="text/css" href="/common/css/common_responsive.css" media="all" />
	<link rel="stylesheet" type="text/css" href="/common/css/base_responsive.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="./css/finish_responsive.css" media="screen" />
	
	<script type="text/javascript" src="/common/js/jquery.js"></script>
	<script type="text/javascript" src="/common/js/tlalib.js"></script>
	<!--m3 begin-->
	<script src="/m3/common/js/common1.js"></script>
	<!--m3 end-->
	
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T5NQFM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

	<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/header.php"; ?>
	
	<!-- m3 begin -->
	<header id="header" class="head2">
		<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc/header.html"); ?>
	</header>
	<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc/gnavi.html"); ?>
	<!-- m3 end -->
	<div id="container">
						
		<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/globalmenu.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/sidenavi.php"; ?>
		
		<div class="contents">
			
			<?php
				$cst = 'cst';
				function cst($constant){
					return $constant;
				}
				if($isSend){
					$heading = '確認メールを返信しています。<br>ご確認ください！';
					$sub = 'Sending';
					$html = <<<DOC

				<h2 class="heading">{$title}</h2>
				<div class="inner">
					<p>{$customer}　様</p>
					<p>この度はタカハマライフアートをご利用いただき、誠にありがとうございます。</p>
					<p>内容を確認後、弊社スタッフからご連絡いたします。</p>
				</div>
				<div class="inner">
					<p class="red">制作を開始するにあたり、お電話によるご注文内容の最終確認をさせていただいております。</p>
					<p class="red">ご入稿いただいたデザインの内容とプリント位置などの打合せを行い、納期と正規見積りの最終確認をおこなっていただき注文確定となります。</p>
				</div>
				<div class="inner">
					<h3>【 親切対応でしっかりサポート 】</h3>
					<p>
						返信メールが届かない場合は、お手数ですが下記の連絡先までお問い合わせください。<br />
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
				<h2 class="heading">送信エラー</h2>
				<div class="inner">
					<p>{$customer}　様</p>
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
	
    <p class="scroll_top"><a href="#header">メール送信完了　ページトップへ</a></p>

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
