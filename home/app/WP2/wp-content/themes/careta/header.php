<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<title><?php wp_title('&middot;', true, 'right'); ?></title>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
<!--
<meta name="Description" content="デザイン素材をご提供いたします！デザイン選んで文字を変えるだけ。最短でオリジナルプリントを作成したい方はタカハマライフアートへ！" />
	<meta name="Keywords" content="デザインテンプレート,プリント,オリジナル,作成,最短,早い,東京" />    -->
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
	<link rel="stylesheet" type="text/css" href="/common/css/base.css" media="screen" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/style.css" />
	<link rel="profile" href="//gmpg.org/xfn/11" />
	<link href="<?php echo get_template_directory_uri(); ?>/genericons/genericons.css" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>

<script src="/app/WP2/wp-includes/js/jquery.imageLoader.1.2.min.js"></script> 
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-11155922-2', 'auto');
  ga('send', 'pageview');

</script>
</head>
<style>

#gNaviPulldown .pull01 tr{
font-size: 12px;
}

#header a, #header .inner a {
    font-size: 95%;
}

#detailContactForm {
    margin: 0 auto;
    padding-left: 10px;
    padding-top: 30px;
    padding-right: 10px;
	width: 91%;
    max-width: 1140px;
}

#footer {
    border-top: none;
}

</style>

<body <?php body_class('lt-480'); ?>>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-T5NQFM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T5NQFM');</script>
<!-- End Google Tag Manager -->

<div id="page-wrap">
	<div id="page">
	<?php 
		$blogTitle = get_bloginfo('name'); 
		$headerImage = get_theme_mod('careta_header_image', '');
		$headerShowName = (bool)get_theme_mod('careta_header_showname', true); 
		$headerShowMenu = (bool)get_theme_mod('careta_header_showmenu', true);

	?>
	<a id="mobile-menu" href="#menu"></a>
	<div id="header">
		<div id="top_bar1">
			<div class="new">
				<ul>
	<li class="mh_logo"><a href="/"><img src="/m3/common/img/header/home_icon_01.png" width="100%" alt="Takahama LifeArt オリジナルTシャツ 早い 制作"></a></li>
	<li class="mh_tel"><a href="tel:0120130428" onclick="ga('send','event','tel','click','header');" class="bgbtn"><img src="/m3/common/img/header/sp_tel.png" width="100%" alt=" オリジナルTシャツ 早い 制作 電話問い合わせ"></a></li>
	<li class="mh_mail"><a href="/contact/" class="bgbtn"><img src="/m3/common/img/header/mail_icon.png" width="100%" alt="メール問い合わせ"></a></li>
	</ul>
			</div>

			<div id="warapper">
				<div class="inner1" style="width:99%;"></div>
				<div class="inner1" style="width:99%; background-color: #fffdfa;">
					<div class="h_wrap hw_1"><a href="//www.takahama428.com/"><img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/logo1.png" alt="オリジナルTシャツ屋"></a></div>
					<div class="h_wrap hw_2"><img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/no1_mark.png" alt="業界NO.1！スピード仕上げ 親切対応！"></div>
					<div class="h_wrap hw_3">
						<div class="h_tel">
							<a href="/contact/guide/">
								<p class="p1">お急ぎの方は<br>お電話下さい！</p>
								<p class="p2"></p>
								<p class="p3"><img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/tel.png" alt="電話"></p>
								<p class="p4">TEL</p>
								<p class="p5">0120-130-428</p>
								<p class="p6">受付時間：平日 10:00-18:00</p>
								<p class="p7"><img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/arrow_b.png"></p>
							</a>
						</div>
						<div class="h_mail">
							<a href="/contact/">
								<img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/mail.png" alt="メール">
								<span>MAIL</span>
								<p>お問い合わせ（相談）</p>
								<img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/arrow_w.png" class="h_arrow" style="margin-left: 40px;">
							</a>
						</div>
					</div><!--eof h_wrap hw_3-->


					<div id="top_menu">
						<div class="inner1" style="width:99%; background-color:#f0f0f0;">
							<div id="search" style="width:170px">
								<script>
									(function() {
										var cx = '006116556064305070768:k-y5qpzibqo';
										var gcse = document.createElement('script');
										gcse.type = 'text/javascript';
										gcse.async = true;
										gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
												'//cse.google.com/cse.js?cx=' + cx;
										var s = document.getElementsByTagName('script')[0];
										s.parentNode.insertBefore(gcse, s);
									})();
								</script>
								<gcse:searchbox-only></gcse:searchbox-only>
							</div>

							<ul class="sub_menu">
								<li><a href="/guide/orderflow.html">ご注文の流れ</a></li>
								<li><a href="/contact/request.html">無料！サンプル請求</a></li>
								<li><a href="/design/template_illust.html" onclick="ga('send','event','template_illust','click','header');">Ai入稿テンプレート</a></li>
								<li><a href="/guide/faq.html">よくあるご質問</a></li>
						<!--		<li class="no_bd"><div class="widget">
					<form role="search" method="get" id="searchform" class="searchform" action="http://www.takahama428.com/designtemplate/">
								<div class="serch">
									<label class="screen-reader-text" for="s"></label>
									<input type="text" value="" name="s" id="s" />
									<input type="submit" id="searchsubmit" value="検索" />
								</div>
							</form></div></li>   -->
							</ul>
			
							<div class="h_blue_btn1">
								<a href="/user/history.php"><p><img src="/common/img/header/login_btn.png"><span>マイページ</span></p></a>
							</div>
						</div>
					</div><!--eof top_menu-->
<div id="mypage">
			<p><a href="/user/about_tlamembers.php"><img src="/common/img/header/sankaku_bule.png">マイページとは </a></p>
						</div>	
					<div class="gro">
						<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/globalmenu_wp.php"; ?>
					</div>
				</div><!--eof inner1-->


				<div id="menu">
					<div class="contents" style="width:100%;">
						<ul class="pan" style="margin: 10px 5px 20px 5px;">
							<li><a href="/">オリジナルＴシャツ屋ＴＯＰ</a></li>
							<li><a href="/designtemplate">デザインテンプレート集</a></li>
						</ul>

					<h1 class="headLine01">デザインテンプレート集</h1>
					<div id="delivery_date"><img src="/app/WP2/wp-content/themes/careta/images/dt_img_main.png" alt="デザインテンプレート集" width="100%"></div>
					

				</div><!--eof menu-->

				<div id="footer" class="wrap">
	 				 <?php get_sidebar('footer'); ?>


					<div class="bord"></div>

				<!--
				<div class="widget">
					<form role="search" method="get" id="searchform" class="searchform" action="http://www.takahama428.com/designtemplate/">
						<div class="serch">
							<label class="screen-reader-text" for="s"></label>
							<input type="text" value="" name="s" id="s" />
							<input type="submit" id="searchsubmit" value="検索" />
						</div>
					</form>
				</div>

				<?php
					wp_nav_menu(array(
						'theme_location' => 'primary',
						'container' => false
					));
				?>
                -->

			</div><!--eof wrapper-->

		</div><!--epf top_bar1-->

	</div><!--eof header-->

	</div><!--epf page-->


<div id="main" class="wrap clear">
<div id="main_top">

		