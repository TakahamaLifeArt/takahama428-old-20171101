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
					<caption><img alt="������" src="/order/img/cart.png" /></caption>
					<tbody>
						<tr><td><span>'.number_format($cart_amount).'</span>��</td></tr>
						<tr class="total"><td><span>'.number_format($cart_total).'</span>��</td></tr>
					</tbody>
				</table>
				<a class="btn_sub" id="showcart" href="/order/index.php?update=2">�����Ȥ򸫤�</a>
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
					$html = '<h1>���ꥸ�ʥ�T����� �������ᤤ�å����ϥޥ饤�ե�����</h1>';
				}else if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'sameday'.DIRECTORY_SEPARATOR)===0){
					$html = '<p class="heading1">�����õޥץ��å��ꥸ�ʥ�T����ĺ����ʤ饿���ϥޥ饤�ե�����</p>';
				}else if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'design'.DIRECTORY_SEPARATOR.'concierge'.DIRECTORY_SEPARATOR)===0){
					$html = '<h1>�ǥ���������פ��ޤ�!�����ϥޥ饤�ե�����</h1>';
				}else if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'scene'.DIRECTORY_SEPARATOR.'wedding.html'.DIRECTORY_SEPARATOR)===0){
					$html = '<p class="heading1">�뺧����T����Ĥʤ�¨�������Υ����ϥޥ饤�ե�����</p>';
				}else if(strpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR)===0 && basename($_SERVER['SCRIPT_NAME'])=='index.html'){
					if(strpos($_SERVER['SCRIPT_NAME'], 't-shirts')!==false){
						$html = '<h1>���ꥸ�ʥ�T����Ĥκ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'long-shirts')!==false){
						$html = '<h1>���ꥸ�ʥ���T����Ĥκ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'polo-shirts')!==false){
						$html = '<h1>���ꥸ�ʥ�ݥ���Ĥκ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'sweat')!==false){
						$html = '<h1>���ꥸ�ʥ�ѡ��������������åȺ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'sportswear')!==false){
						$html = '<h1>���ꥸ�ʥ륹�ݡ��ĥ������κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'drywear')!==false){
						$html = '<h1>���ꥸ�ʥ�ɥ饤�������κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'outer')!==false){
						$html = '<h1>���ꥸ�ʥ�֥륾��������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'workwear')!==false){
						$html = '<h1>���ꥸ�ʥ����������κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'baby')!==false){
						$html = '<h1>���ꥸ�ʥ�٥ӡ�T����Ĥκ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'cap')!==false){
						$html = '<h1>���ꥸ�ʥ륭��åס��Х���ʤκ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'bag')!==false){
						$html = '<h1>���ꥸ�ʥ�Хå��κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'apron')!==false){
						$html = '<h1>���ꥸ�ʥ륨�ץ��κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'towel')!==false){
						$html = '<h1>���ꥸ�ʥ륿����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'goods')!==false){
						$html = '<h1>���ꥸ�ʥ�ץ쥼��ȡ����å��κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'ladys')!==false){
						$html = '<h1>��ǥ�����T����İ�������7�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'overall')!==false){
						$html = '<h1>���ꥸ�ʥ�Ĥʤ��������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'workwear')!==false){
						$html = '<h1>���ꥸ�ʥ����������κ������ᤤ������ȯ����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'aimy')!==false){
						$html = '<h1>AIMY���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'bees-beam')!==false){
						$html = '<h1>BEEAS BEAM���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե����� </h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'crossandsttch')!==false){
						$html = '<h1>CROSS&STTCH���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'daluc')!==false){
						$html = '<h1>DALUC���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'glimmer')!==false){
						$html = '<h1>glimmer���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'printstar')!==false){
						$html = '<h1>Printstar���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'rucca')!==false){
						$html = '<h1>rucca���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'seventeen-verglebee')!==false){
						$html = '<h1>Seventeen VergleBee���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'sowa')!==false){
						$html = '<h1>SOWA���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'touchandgo')!==false){
						$html = '<h1>Touch&GO���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'truss')!==false){
						$html = '<h1>TRUSS���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'unitedathle')!==false){
						$html = '<h1>UnitedAthle���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'touchandgo')!==false){
						$html = '<h1>Touch&GO���ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'champion')!==false){
						$html = '<h1>�����ԥ����Champion�˥��ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else if(strpos($_SERVER['SCRIPT_NAME'], 'puma')!==false){
						$html = '<h1>�ס��ޡ�PUMA�˥��ꥸ�ʥ륦�����κ������ץ��Ȥʤ饿���ϥޥ饤�ե�����</h1>';
					}else{
						$html = '<h1>�ȳ���®!¨���в�!���ꥸ�ʥ�T����� �����ϥ����ϥޥ饤�ե�����</h1>';
					}
				}else{
					$html = '<p class="heading1">�ȳ���®�����ꥸ�ʥ�T����� �����ʤ饿���ϥޥ饤�ե�����</p>';
				}
				echo $html;
			?>
			<div id="sns">
			
				<!-- Google+ -->
				<div class="googleplus"><div class="g-plusone" data-href="http://www.takahama428.com" data-align="right"></div></div>

				<!-- twitter -->
				<div class="twitter"><a href='https://twitter.com/share' class='twitter-share-button' data-lang='ja'>�ĥ�����</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>
			
				<!-- facebook -->
				<div class="facebook"><div class="fb-like" data-href="https://www.facebook.com/takahamalifeart" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div></div>
			</div>
		</div>
	</div>
	
	<div class="inner">
		<div class="h_wrap hw_1"><a href="/ "onclick="ga('send','event','logo','click','header');"><img src="/common/img/header/logo.png" alt="���ꥸ�ʥ�T����Ĳ�"></a></div>
		<div class="h_wrap hw_2"><img src="/common/img/header/no1_mark.png" alt="�ȳ�NO.1�����ԡ��ɻž夲 �����б���"></div>
		
		<div class="h_wrap hw_3">
			<div class="h_tel">
				<a href="/contact/guide/ "onclick="ga('send','event','guide','click','header');">
					<p class="p1">���ޤ�������<br>�����ò�������</p>
					<p class="p2"></p>
					<p class="p3"><img src="/common/img/header/tel.png" alt="����"></p>
					<p class="p4">TEL</p>
					<p class="p5">0120-130-428</p>
					<p class="p6">���ջ��֡�ʿ�� 10:00-18:00</p>
					<p class="p7"><img src="/common/img/header/arrow_b.png"></p>
				</a>
			</div>
				
			<div class="h_mail">
				<a href="/contact/ "onclick="ga('send','event','contact','click','header');">
					<img src="/common/img/header/mail.png" alt="�᡼��">
					<span>���䤤��碌�����̡ˤϤ�����</span>
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
				<li><a href="/guide/orderflow.html" onclick="ga('send','event','orderflow','click','header');">����ʸ��ή��</a></li>
				<li><a href="/contact/request.html" onclick="ga('send','event','request','click','header');">̵��������<!-- ����ץ� -->����</a></li>
				<li><a href="/design/template_illust.html" onclick="ga('send','event','template_illust','click','header');">Ai���ƥƥ�ץ졼��</a></li>
				<li class="no_bd"><a href="/guide/faq.html" onclick="ga('send','event','faq','click','header');">�褯���뤴����</a></li>
			</ul>
			<div class="login_btn"><a href="/user/history.php" id="mypage_btn"><img src="/common/img/header/login_btn.png">�ޥ��ڡ���</a></div>
			<?php
				if(!empty($_SESSION['me'])){
					echo '<div class="logout_btn"><a href="/user/logout.php" id="mypage_btn">��������</a></div>';
				}
			?>
		</div>
	</div>
		
	<div class="inner">
		<div class="how_to_mypage">
			<p><a href="/user/about_tlamembers.php" onclick="ga('send','event��,��how_to_mypage','click��,��top��);"> <img src="/common/img/header/sankaku_bule.png">�ޥ��ڡ����Ȥ� </a></p>
		</div>
		<?php
			if(!empty($_SESSION['me'])){
				$wel_name = mb_convert_encoding($_SESSION['me']['customername'], 'euc-jp', 'utf-8');
				echo '<p class="wel_name" style="font-weight:bold">�褦���� '.$wel_name.' ��</p>';
			}
		?>
	</div>


</div>
