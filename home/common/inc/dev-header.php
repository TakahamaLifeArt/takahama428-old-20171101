<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-11155922-2', 'auto');
  ga('send', 'pageview');

</script>
<div id="fb-root"></div>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.async = true;
	js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.0";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div id="header">

	<?php
		if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR)!==0 && strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'design'.DIRECTORY_SEPARATOR.'designpost')!==0){
			$html = '
			<div id="showcart_box">
				<table>
					<caption><img alt="カート" src="/order/img/cart.png" /></caption>
					<tbody>
						<tr><td><span>'.number_format($cart_amount).'</span>枚</td></tr>
						<tr class="total"><td><span>'.number_format($cart_total).'</span>円</td></tr>
					</tbody>
				</table>
				<a class="btn_sub" id="showcart" href="/order/index.php?update=2">カートを見る</a>
			</div>';
			echo $html;
		}
	
		if($_SERVER['SCRIPT_NAME']!=DIRECTORY_SEPARATOR.'order'.DIRECTORY_SEPARATOR.'index.php'){
			echo '<a href="/order/express/" id="oisogi"></a>';
		}
	?>
	
	<div id="top_bar">
		<div class="inner">
			<?php
				if($_SERVER['SCRIPT_NAME']==DIRECTORY_SEPARATOR.'index.html'){
					$html = '<h1>オリジナルTシャツ 作成が早い｜タカハマライフアート</h1>';
				}else if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'sameday'.DIRECTORY_SEPARATOR)===0){
					$html = '<p class="heading1">当日特急プラン｜オリジナルTシャツ作成ならタカハマライフアート</p>';
				}else if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'design'.DIRECTORY_SEPARATOR.'concierge'.DIRECTORY_SEPARATOR)===0){
					$html = '<h1>デザイン作成致します!タカハマライフアート</h1>';
				}else if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'scene'.DIRECTORY_SEPARATOR.'wedding.html'.DIRECTORY_SEPARATOR)===0){
					$html = '<p class="heading1">結婚式のTシャツなら即日作成のタカハマライフアート</p>';
				}else if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR)===0 && basename($_SERVER['SCRIPT_NAME'])=='index.html'){
					if(strpos($_SERVER['SCRIPT_NAME'], 't-shirts')!==false){
						$html = '<h1>オリジナルTシャツの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'long-shirts')!==false){
						$html = '<h1>オリジナルロングTシャツの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'polo-shirts')!==false){
						$html = '<h1>オリジナルポロシャツの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'sweat')!==false){
						$html = '<h1>オリジナルパーカー・スウェット作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'sportswear')!==false){
						$html = '<h1>オリジナルスポーツウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'drywear')!==false){
						$html = '<h1>オリジナルドライウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'outer')!==false){
						$html = '<h1>オリジナルブルゾン作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'workwear')!==false){
						$html = '<h1>オリジナルワークウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'baby')!==false){
						$html = '<h1>オリジナルベビーTシャツの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'cap')!==false){
						$html = '<h1>オリジナルキャップ・バンダナの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'bag')!==false){
						$html = '<h1>オリジナルバッグの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'apron')!==false){
						$html = '<h1>オリジナルエプロンの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'towel')!==false){
						$html = '<h1>オリジナルタオルの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'goods')!==false){
						$html = '<h1>オリジナルプレゼント・グッズの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'ladys')!==false){
						$html = '<h1>レディースTシャツ一覧【全7種類】</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'overall')!==false){
						$html = '<h1>オリジナルつなぎ作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'workwear')!==false){
						$html = '<h1>オリジナルワークウェアの作成が早い【当日発送】</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'aimy')!==false){
						$html = '<h1>AIMYオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'bees-beam')!==false){
						$html = '<h1>BEEAS BEAMオリジナルウェアの作成・プリントならタカハマライフアート </h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'crossandsttch')!==false){
						$html = '<h1>CROSS&STTCHオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'daluc')!==false){
						$html = '<h1>DALUCオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'glimmer')!==false){
						$html = '<h1>glimmerオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'printstar')!==false){
						$html = '<h1>Printstarオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'rucca')!==false){
						$html = '<h1>ruccaオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'seventeen-verglebee')!==false){
						$html = '<h1>Seventeen VergleBeeオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'sowa')!==false){
						$html = '<h1>SOWAオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'touchandgo')!==false){
						$html = '<h1>Touch&GOオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'truss')!==false){
						$html = '<h1>TRUSSオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'unitedathle')!==false){
						$html = '<h1>UnitedAthleオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'touchandgo')!==false){
						$html = '<h1>Touch&GOオリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'champion')!==false){
						$html = '<h1>チャンピオン（Champion）オリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'puma')!==false){
						$html = '<h1>プーマ（PUMA）オリジナルウェアの作成・プリントならタカハマライフアート</h1>';
					}else{
						$html = '<h1>業界最速!即日出荷!オリジナルTシャツ 作成はタカハマライフアート</h1>';
					}
				}else{
					$html = '<p class="heading1">業界最速！オリジナルTシャツ 作成ならタカハマライフアート</p>';
				}
				echo $html;
			?>
			<div id="sns">
			
				<!-- Google+ -->
				<div class="googleplus"><div class="g-plusone" data-href="http://www.takahama428.com" data-align="right"></div></div>

				<!-- twitter -->
				<div class="twitter"><a href='https://twitter.com/share' class='twitter-share-button' data-lang='ja'>ツイート</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>
			
				<!-- facebook -->
				<div class="facebook"><div class="fb-like" data-href="https://www.facebook.com/takahamalifeart" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div></div>
			</div>
		</div>
	</div>
	
	<div class="inner">
		<div class="h_wrap hw_1"><a href="/ "onclick="ga('send','event','logo','click','header');"><img src="/common/img/header/logo.png" alt="オリジナルTシャツ屋"></a></div>
		<div class="h_wrap hw_2"><img src="/common/img/header/no1_mark.png" alt="業界NO.1！スピード仕上げ 親切対応！"></div>
		
		<div class="h_wrap hw_3">
			<div class="h_tel">
				<a href="/contact/guide/ "onclick="ga('send','event','guide','click','header');">
					<p class="p1">お急ぎの方は<br>お電話下さい！</p>
					<p class="p2"></p>
					<p class="p3"><img src="/common/img/header/tel.png" alt="電話"></p>
					<p class="p4">TEL</p>
					<p class="p5">0120-130-428</p>
					<p class="p6">受付時間：平日 10:00-18:00</p>
					<p class="p7"><img src="/common/img/header/arrow_b.png"></p>
				</a>
			</div>
				
			<div class="h_mail">
				<a href="/contact/ "onclick="ga('send','event','contact','click','header');">
					<img src="/common/img/header/mail.png" alt="メール">
					<span>お問い合わせ（相談）はこちら</span>
				</a>
			</div>
		</div>
		
	</div>
		
	<div id="top_menu">
		<div class="inner">
			<div id="search">
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
			
			
			<ul>
				<li><a href="/guide/orderflow.html" onclick="ga('send','event','orderflow','click','header');">ご注文の流れ</a></li>
				<li><a href="/contact/request.html" onclick="ga('send','event','request','click','header');">無料！資料<!-- サンプル -->請求</a></li>
				<li><a href="/design/template_illust.html" onclick="ga('send','event','template_illust','click','header');">Ai入稿テンプレート</a></li>
				<li class="no_bd"><a href="/guide/faq.html" onclick="ga('send','event','faq','click','header');">よくあるご質問</a></li>
			</ul>
			<div class="login_btn"><a href="/user/history.php" id="mypage_btn"><img src="/common/img/header/login_btn.png">マイページ</a></div>
			<?php
				if(!empty($_SESSION['me'])){
					echo '<div class="logout_btn"><a href="/user/logout.php" id="mypage_btn">ログアウト</a></div>';
				}
			?>
		</div>
	</div>
		
	<div class="inner">
		<div class="how_to_mypage">
			<p><a href="/user/about_tlamembers.php" onclick="ga('send','event’,’how_to_mypage','click’,’top’);"> <img src="/common/img/header/sankaku_bule.png">マイページとは </a></p>
		</div>
		<?php
			if(!empty($_SESSION['me'])){
				$wel_name = mb_convert_encoding($_SESSION['me']['customername'], 'euc-jp', 'utf-8');
				echo '<p class="wel_name" style="font-weight:bold">ようこそ '.$wel_name.' 様</p>';
			}
		?>
	</div>


</div>
