<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/jd/myCalendar.php';
list($year, $month) = explode(' ', date("Y n"));
$myCalendar = new myCalendar($year, $month);
list($year2, $month2) = explode(' ', date('Y n', mktime(0, 0, 0, date('n')+1, 1, date('Y'))));
$myCalendar2 = new myCalendar($year2, $month2);
?>
<div class="snavi">
<?php
	$info = pathinfo($_SERVER['SCRIPT_NAME']);
	$fname = basename($_SERVER['SCRIPT_NAME'], '.'.$info['extension']);
	$root = DIRECTORY_SEPARATOR;
	if($info['dirname']!=DIRECTORY_SEPARATOR.$_SERVER['SERVER_NAME']){
		if($info['dirname']==$root.'design'){
			$html = <<<EOD
	<div class="box">
		<p class="heading">デザイン</p>
		<ul>
			<li><a href="/design/designguide.html">デザインの作り方</a></li>
			<li><a href="/design/printing.html">プリント方法の種類</a></li>
			<li><a href="/design/gallery.html">デザイン実例</a></li>
			<li><a href="/design/designtemp.html">無料の素材集</a></li>
			<li><a href="/design/template_illust.html">イラレ・テンプレート</a></li>
			<li><a href="/design/fontcolor.html">インクとフォント</a></li>
		</ul>
	</div>
EOD;
			echo $html;
		}else if($fname=='estimate'){
			$html = <<<EOD
	<div class="box">
		<p class="heading">料金</p>
		<ul>
			<li><a href="/price/standard.html">料金の目安</a></li>
			<li><a href="/guide/#discount">お得な割引メニュー</a></li>
			<li><a href="/guide/#carriage">送料</a></li>
			<li><a href="/guide/#payment">お支払方法</a></li>
		</ul>
	</div>
EOD;
			echo $html;
		}else if($info['dirname']==$root.'corporate' || $info['dirname']==$root.'corporate/profile'){
			$html = <<<EOD
	<div class="box">
		<p class="heading">会社案内</p>
		<ul>
			<li><a href="/corporate/profile/staff.html">スタッフ紹介</a></li>
			<li><a href="/corporate/profile/manager.html">社長インタビュー</a></li>
			<li><a href="/corporate/overview.html">会社概要</a></li>
			<li><a href="/corporate/commercial_trans.html">特定商取引法</a></li>
			<li><a href="/corporate/commercial_trans.html#privacy">プライバシーポリシー</a></li>
		</ul>
	</div>
EOD;
			echo $html;
		}else if($info['dirname']==$root.'archive'){
			$html = <<<EOD
	<p class="title">トピックス</p>
	<div class="box" id="toggle">
		<h4 class="toggler">オリジナルＴシャツ活用法</h4>
		<ul class="topics">
			<li><a href="/archive/repo-1.html">ダンスシーンはオリジナルプリントで</a></li>
			<li><a href="/archive/repo-2.html">文化祭、体育祭を盛り上げる！オリジナルＴシャツ</a></li>
			<li><a href="/archive/repo-3.html">イベントに必須！自分たちの“色”を前面に</a></li>
			<li><a href="/archive/repo-4.html">スポーツといえばオリジナルウェア</a></li>
			<li><a href="/archive/repo-5.html">盛り上がる結婚式にオリジナルグッズ</a></li>
			<li><a href="/archive/repo-6.html">大好きなTシャツコーディネート</a></li>
			<li><a href="/archive/repo-7.html">Happyになるオリジナルプレゼント</a></li>
			<li><a href="/archive/repo-8.html">スタッフユニフォームは、オリジナルプリント</a></li>
			<li><a href="/archive/repo-9.html">チームがひとつになる方法</a></li>
			<li><a href="/archive/repo-10.html">販促ツールにオリジナルプリントの活用術</a></li>
		</ul>
		<h4 class="toggler">目的別・用途別・シーン別</h4>
		<ul class="topics">
			<li><a href="/archive/repo-11.html">感動させるならオリジナルもの</a></li>
			<li><a href="/archive/repo-12.html">場の雰囲気を作るならこれでしょう</a></li>
			<li><a href="/archive/repo-13.html">気持ちを伝えたい口下手な人に</a></li>
			<li><a href="/archive/repo-14.html">創作する時間が思い出時間</a></li>
			<li><a href="/archive/repo-15.html">団体でお揃いを作るなら</a></li>
			<li><a href="/archive/repo-16.html">アピールするならオリジナルＴシャツ</a></li>
			<li><a href="/archive/repo-17.html">狙ったシーンにオリジナル</a></li>
		</ul>
		<h4 class="toggler">プリントにまつわるお話し</h4>
		<ul class="topics">
			<li><a href="/archive/repo-18.html">オリジナルプリントするのって難しい？</a></li>
			<li><a href="/archive/repo-19.html">何をプリントするか悩んでる人へ</a></li>
			<li><a href="/archive/repo-20.html">プリント方法はご存じ？</a></li>
			<li><a href="/archive/repo-21.html">あれもこれもプリントしたい！</a></li>
			<li><a href="/archive/repo-22.html">味があるTシャツになりたい</a></li>
			<li><a href="/archive/repo-23.html">プリントの失敗談</a></li>
		</ul>
		<h4 class="toggler">オリジナルTシャツのデザイン</h4>
		<ul class="topics">
			<li><a href="/archive/repo-24.html">How to デザイン -1-</a></li>
			<li><a href="/archive/repo-25.html">How to デザイン -2-</a></li>
			<li><a href="/archive/repo-26.html">イラスト・写真・色・文字</a></li>
			<li><a href="/archive/repo-27.html">デザインのレイアウト・バランス・色</a></li>
			<li><a href="/archive/repo-28.html">五感のイメージからデザイン</a></li>
			<li><a href="/archive/repo-29.html">雰囲気からデザイン -1-</a></li>
			<li><a href="/archive/repo-30.html">雰囲気からデザイン -2-</a></li>
			<li><a href="/archive/repo-31.html">オリジナルデザインの作品として</a></li>
		</ul>
		<h4 class="toggler">オリジナルTシャツ屋の雑記</h4>
		<ul class="topics">
			<li><a href="/archive/repo-32.html">ペアルックについて語る</a></li>
			<li><a href="/archive/repo-33.html">Tシャツの手入れ方法</a></li>
			<li><a href="/archive/repo-34.html">リメイクって楽しい</a></li>
			<li><a href="/archive/repo-35.html">かゆいところに手が届く接客</a></li>
			<li><a href="/archive/repo-36.html">時間の空いた休日の過ごし方</a></li>
			<li><a href="/archive/repo-37.html">Tシャツの歴史</a></li>
			<li><a href="/archive/repo-38.html">子供とより楽しく過ごす方法</a></li>
			<li><a href="/archive/repo-39.html">環境を考える世の中はエライ！</a></li>
			<li><a href="/archive/repo-40.html">帽子を愛してやまない訳</a></li>
		</ul>
		<h4 class="toggler">オリジナルTシャツ制作の秘訣</h4>
		<ul class="topics">
			<li><a href="/archive/repo-41.html">カッコいいオリジナルTシャツを作りたい！失敗しないデザインとは？</a></li>
			<li><a href="/archive/repo-42.html">オリジナルTシャツのデザインの魅力に迫る</a></li>
			<li><a href="/archive/repo-43.html">文化祭で着るTシャツだからこそこだわりたい！</a></li>
			<li><a href="/archive/repo-44.html">オレ流にこだわりたいならオリジナルTシャツを作る！</a></li>
			<li><a href="/archive/repo-45.html">こだわりのあるオリジナル作業着を安く作るコツ</a></li>
			<li><a href="/archive/repo-46.html">安くて丈夫！！やさしい色合いのポロシャツ</a></li>
			<li><a href="/archive/repo-47.html">イベント成功の鍵！人気のグッズと作り方！？</a></li>
			<li><a href="/archive/repo-48.html">見てくれ！！オレのマイ作業着！！</a></li>
			<li><a href="/archive/repo-49.html">お年よりも欲しがる？可愛いポロシャツ</a></li>
			<li><a href="/archive/repo-50.html">Tシャツを作るとき、入れたい文字は？</a></li>
			<li><a href="/archive/repo-51.html">クラスTシャツ、プリントカラーは何色がいい？</a></li>
			<li><a href="/archive/repo-52.html">絵心がない人でもオリジナルTシャツは作れる！</a></li>
			<li><a href="/archive/repo-53.html">スタイリッシュにみせるTシャツは</a></li>
		</ul>
		<h4 class="toggler">作る前に知っておきたいこと</h4>
		<ul class="topics">
			<li><a href="/archive/repo-54.html">Tシャツの生地、薄地派？厚地派？</a></li>
			<li><a href="/archive/repo-55.html">急ぎでTシャツを作りたい場合に気をつけるべき点とは？</a></li>
			<li><a href="/archive/repo-56.html">安心して依頼出来るＴシャツ作成業者に共通している点とは？</a></li>
			<li><a href="/archive/repo-57.html">体育祭で団結を固くする為に効果的なアイテムとは？</a></li>
			<li><a href="/archive/repo-58.html">Tシャツの生地、綿派？ドライ派？</a></li>
			<li><a href="/archive/repo-59.html">イベント主催者必見！！定番のノベルティって？</a></li>
			<li><a href="/archive/repo-60.html">Tシャツを作る際にかかる費用の相場とは？</a></li>
			<li><a href="/archive/repo-61.html">オリジナルTシャツを作って販売！売れるTシャツを作るには？</a></li>
			<li><a href="/archive/repo-62.html">バスケやるのに合う生地は</a></li>
			<li><a href="/archive/repo-63.html">汗を乾かしてくれるTシャツとは</a></li>
			<li><a href="/archive/repo-64.html">オリジナルポロシャツ 人気の色は</a></li>
			<li><a href="/archive/repo-65.html">スポーツに向いているTシャツは？</a></li>
			<li><a href="/archive/repo-66.html">スポーツに向いているポロシャツは？</a></li>
			<li><a href="/archive/repo-67.html">ダンスチームの必須アイテムとは？</a></li>
			<li><a href="/archive/repo-68.html">ヨガのとき着たいTシャツの形状は</a></li>
			<li><a href="/archive/repo-69.html">文化祭Tシャツ、何色が人気？</a></li>
			<li><a href="/archive/repo-70.html">体型に合ったTシャツの選び方とは？</a></li>
			<li><a href="/archive/repo-71.html">クラスTシャツ、いくらぐらいが相場？</a></li>
			<li><a href="/archive/repo-72.html">Tシャツのデザインはどんなのが多い？</a></li>
			<li><a href="/archive/repo-73.html">部活で着るTシャツに適した素材とは？</a></li>
			<li><a href="/archive/repo-74.html">日本とアメリカのサイズ感ってどのくらい違うの？</a></li>
			<li><a href="/archive/repo-75.html">丈夫なTシャツの素材はどんなもの？</a></li>
		</ul>
		<h4 class="toggler">オリジナルTシャツ活躍例</h4>
		<ul class="topics">
			<li><a href="/archive/repo-76.html">おそろいで作った会社のロンT！！全然丈夫！！</a></li>
			<li><a href="/archive/repo-77.html">念願のお店のTシャツ！！最高傑作！！</a></li>
			<li><a href="/archive/repo-78.html">イベント大成功！！Tシャツが心をつないだ！！</a></li>
			<li><a href="/archive/repo-79.html">フットサルチームでTシャツをつくりました！！</a></li>
			<li><a href="/archive/repo-80.html">オリジナルサッカーシャツを作ってチームの団結を強化！</a></li>
			<li><a href="/archive/repo-81.html">個人名と背番号が入った我がチームのTシャツ</a></li>
			<li><a href="/archive/repo-82.html">ランニング女子、おそろいのTシャツ</a></li>
			<li><a href="/archive/repo-83.html">チームのテンションを上げる最強アイテム！？</a></li>
			<li><a href="/archive/repo-84.html">冬のスポーツ観戦。何着て凌ぐ？</a></li>
			<li><a href="/archive/repo-85.html">日本代表の応援、何色のTシャツを着る？</a></li>
		</ul>
	</div>
EOD;
			echo $html;
		}
		
		$html = <<<EOD
		<p class="title">アイテム一覧</p>
		<div class="box list">
			<ul>
				<li><a href="/items/t-shirts/" onclick="ga('send','event','tshirts','click','side');"><img alt="Tシャツ" src="/common/img/thumbs/item/t-shirts.png" />Ｔシャツ</a></li>
				<li><a href="/items/sweat/"><img alt="パーカー・スウェット" src="/common/img/thumbs/item/sweat.png" />パーカー・スウェット</a></li>
				<li><a href="/items/outer/"><img alt="アウター" src="/common/img/thumbs/item/outer.png" />ブルゾン・ジャンパー</a></li>
				<li><a href="/items/polo-shirts/"><img alt="ポロシャツ" src="/common/img/thumbs/item/polo.png" />ポロシャツ</a></li>
				<li><a href="/items/sportswear/"><img alt="スポーツウェア" src="/common/img/thumbs/item/sports.png" />スポーツウェア</a></li>
				<li><a href="/items/long-shirts/"><img alt="ロングTシャツ" src="/common/img/thumbs/item/long-t.png" />ロングＴシャツ</a></li>
				<li><a href="/items/towel/"><img alt="タオル" src="/common/img/thumbs/item/towel.png" />タオル</a></li>
				<li><a href="/items/tote-bag/"><img alt="バッグ" src="/common/img/thumbs/item/tote-bag.png" />バッグ・トートバッグ</a></li>
				<li><a href="/items/ladys/"><img alt="レディース" src="/common/img/thumbs/item/ladys.png" />レディースウェア</a></li>
				<li><a href="/items/apron/"><img alt="エプロン" src="/common/img/thumbs/item/apron.png" />エプロン</a></li>
				<li><a href="/items/baby/"><img alt="ベビー" src="/common/img/thumbs/item/baby.png" />ベビーウェア</a></li>
				<li><a href="/items/overall/"><img alt="つなぎ" src="/common/img/thumbs/item/overall.png" />つなぎ</a></li>
				<li><a href="/items/cap/"><img alt="キャップ・バンダナ" src="/common/img/thumbs/item/cap.png" />キャップ・バンダナ</a></li>
				<li><a href="/items/goods/"><img alt="プレゼント・グッズ" src="/common/img/thumbs/item/goods.png" />記念品・プレゼント</a></li>
			</ul>
		</div>
		
		<p class="title">ブランド一覧</p>
		<div class="box list">
			<ul>
				<li><a href="/items/printstar/"><img alt="Printstar" src="/common/img/thumbs/brand/printstar.png" />Printstar</a></li>
				<li><a href="/items/unitedathle/"><img alt="UnitedAthle" src="/common/img/thumbs/brand/unitedathle.png" />UnitedAthle</a></li>
				<li><a href="/items/crossandsttch/"><img alt="CROSS&STTCH" src="/common/img/thumbs/brand/crossandsttch.png" />CROSS&STTCH</a></li>
				<li><a href="/items/truss/"><img alt="TRUSS" src="/common/img/thumbs/brand/truss.png" />TRUSS</a></li>
				<li><a href="/items/wundou/"><img alt="wundou" src="/common/img/thumbs/brand/wundou.png" />wundou</a></li>
				<li><a href="/items/glimmer/"><img alt="glimmer" src="/common/img/thumbs/brand/glimmer.png" />glimmer</a></li>
				<li><a href="/items/rucca/"><img alt="rucca" src="/common/img/thumbs/brand/rucca.png" />rucca</a></li>
				<li><a href="/items/bees-beam/"><img alt="BEES BEAM" src="/common/img/thumbs/brand/beesbeam.png" />BEES BEAM</a></li>
				<li><a href="/items/seventeen-verglebee/"><img alt="Seventeen VergleBee" src="/common/img/thumbs/brand/seventeen-verglebee.png" /><p>Seventeen<br>VergleBee</p></a></li>
				<li><a href="/items/touchandgo/"><img alt="Touch&GO" src="/common/img/thumbs/brand/touchandgo.png" />Touch&GO</a></li>
				<li><a href="/items/cross/"><img alt="CROSS" src="/common/img/thumbs/brand/cross.png" />CROSS</a></li>
				<li><a href="/items/daluc/"><img alt="DALUC" src="/common/img/thumbs/brand/daluc.png" />DALUC</a></li>
				<li><a href="/items/sowa/"><img alt="SOWA" src="/common/img/thumbs/brand/sowa.png" />SOWA</a></li>
				<li><a href="/items/aimy/"><img alt="AIMY" src="/common/img/thumbs/brand/aimy.png" />AIMY</a></li>
				<li><a href="/items/gildan/"><img alt="GILDAN" src="/common/img/thumbs/brand/gildan.png" />GILDAN</a></li>
				<li><a href="/items/anvil/"><img alt="ANVIL" src="/common/img/thumbs/brand/anvil.png" />ANVIL</a></li>
				<li><a href="/items/comfortcolors/"><img alt="COMFORT COLORS" src="/common/img/thumbs/brand/comfortcolors.png" />ComfortColors</a></li>
			</ul>
		</div>
		
		<p class="title">シーン</p>
		<div class="box list">
			<ul>
				<li><a href="/scene/campus.html"><img alt="文化祭・体育祭" src="/common/img/thumbs/scene/festival.png" width="30" />文化祭・体育祭</a></li>
				<li><a href="/scene/sports.html"><img alt="スポーツ" src="/common/img/thumbs/scene/sport.png" width="30" />スポーツ</a></li>
				<li><a href="/scene/dance.html"><img alt="ダンス" src="/common/img/thumbs/scene/dance.png" width="30" />ダンス</a></li>
				<li><a href="/scene/team.html"><img alt="チーム・仲間" src="/common/img/thumbs/scene/team.png" width="30" />チーム・仲間</a></li>
				<li><a href="/scene/event.html"><img alt="イベント" src="/common/img/thumbs/scene/events.png" width="30" />イベント</a></li>
				<li><a href="/scene/uniform.html"><img alt="スタッフユニフォーム" src="/common/img/thumbs/scene/uniform.png" width="30" />スタッフユニフォーム</a></li>
				<li><a href="/scene/promotion.html"><img alt="販促ツール" src="/common/img/thumbs/scene/sp.png" width="30" />販促ツール</a></li>
				<li><a href="/scene/gift.html"><img alt="プレゼント" src="/common/img/thumbs/scene/gift.png" width="30" />プレゼント</a></li>
				<li><a href="/scene/wedding.html"><img alt="結婚式" src="/common/img/thumbs/scene/marriage.png" width="30" />結婚式</a></li>
			</ul>
		</div>

EOD;

	}else{
		$html = "";
	}

	echo $html;
?>
	
	<div class="workday_calendar_wrap">
		<table class="workday_calendar">
			<caption><?php echo "{$year} 年{$month} 月　営業日"; ?></caption>
			<thead>
		    	<tr>
			        <td class="sun">日</td>
			        <td>月</td>
			        <td>火</td>
			        <td>水</td>
			        <td>木</td>
			        <td>金</td>
			        <td class="sat">土</td>
			    </tr>
		    </thead>
		    <tbody><?php echo $myCalendar->makeCalendar(); ?></tbody>
		</table>
		<table class="workday_calendar">
			<caption><?php echo "{$year2} 年{$month2} 月　営業日"; ?></caption>
			<thead>
		    	<tr>
			        <td class="sun">日</td>
			        <td>月</td>
			        <td>火</td>
			        <td>水</td>
			        <td>木</td>
			        <td>金</td>
			        <td class="sat">土</td>
			    </tr>
		    </thead>
		    <tbody><?php echo $myCalendar2->makeCalendar(); ?></tbody>
		</table>
		<dl class="list">
			<dt>営業時間：</dt><dd>10:00&#65374;18:00</dd>
			<dt>定休日：</dt><dd>土日祝</dd>
			<p>東京下町本格プリント営業中！</p>
		</dl>
	</div>
	
	<div class="banner1">
		<a href="/contact/line/" class="sns01"></a><p>友達1000人突破!!<br>作成に関する疑問解消中!!</p>
		<a href="https://ja-jp.facebook.com/takahamalifeart" class="sns02"></a><p>オリジナルTシャツに関するニュースや<br>日常風景をアップしていきます!!</p>
		<a href="https://twitter.com/takahamalifeart" class="sns03"></a><p>デザインをメインにお客様の声を<br>呟いていきます!!</p>
		<a href="https://www.instagram.com/takahamalifeart/" class="sns04"></a><p>プリントの風景やお客様のデザインを<br>アップしていきます!! </p>
	</div>
<!--
	<div class="box">
		<p class="heading">写真投稿サービス</p>
		<ul>
			<li><a href="/design/designpost/">お客様写真館</a></li>
			<li><a href="/design/designpost/userpolicy.php">ご利用規約</a></li>
			<li><a href="/design/designpost/register.php">ユーザー登録</a></li>
		</ul>
	</div>
-->	
	
	<div class="box">
		<p class="heading">納期</p>
		<ul>
			<li><a href="/delivery/">お届日を確認する</a></li>
			<li><a href="/guide/#express">2日・翌日仕上げ</a></li>
		</ul>
	</div>
	
<?php
	if($fname!='estimate'){
		$html = <<<EOD
	<div class="box">
		<p class="heading">料金</p>
		<ul>
			<li><a href="/price/standard.html">料金の目安</a></li>
			<li><a href="/guide/#discount">お得な割引メニュー</a></li>
			<li><a href="/guide/#carriage">送料</a></li>
			<li><a href="/guide/#payment">お支払方法</a></li>
		</ul>
	</div>
EOD;
		echo $html;
	}

	if($info['dirname']!=$root.'design'){
		$html = <<<EOD
	<div class="box">
		<p class="heading">プリントするデザイン</p>
		<ul>
			<li><a href="/design/designguide.html">デザインの作り方</a></li>
			<li><a href="/design/printing.html">プリント方法の種類</a></li>
			<li><a href="/design/gallery.html">デザイン実例</a></li>
			<li><a href="/design/designtemp.html">無料の素材集</a></li>
			<li><a href="/design/template_illust.html">イラレ・テンプレート</a></li>
			<li><a href="/design/fontcolor.html">インクとフォント</a></li>
		</ul>
	</div>
EOD;
		echo $html;
	}
?>
	
	<div class="banner1">
		<a href="/app/WP/?cat=4" class="staffblog"></a>
		<p>タカハマの日常、全スタッフの日常</p>
	</div>

	<div class="banner1">
		<a href="/blog/thanks_blog/" class="okyakusama"></a>
		<p>お客様の笑顔と作品集</p>
	</div>
	
	<div class="banner1">
		<a href="/glossary/a/" class="yougo2"></a>
		<p>Tシャツ作成が捗る大辞典</p>
	</div>
	
	<div class="banner1">
		<a href="http://www.jota.or.jp/contest_2017.html" target="_blank" rel="nofollow"><img alt="オリTグランプリ" src="/img/banner/orit2.png" width="200" /></a>
		<p>熱海で開かれる大イベント！</p>
	</div>
	
	<div class="banner1">
		<a href="http://jota.or.jp/" target="_blank" rel="nofollow"><img alt="オリジナルTシャツ協会" src="/img/banner/original-tee-japan.png" width="200" /></a>
		<p>様々な活動を行っています！</p>
	</div>
	
	<div class="banner1">
		<a href="http://jota.or.jp/ippan/tsukuru/school.html" target="_blank" rel="nofollow" class="jota-oyako"></a>
		<p>親子で家族T作りませんか？</p>
	</div>
	
	<div class="box banner1">
		<a href="/m3/"><img alt="スマホ版" src="/img/banner/smartphone.png" width="198" /></a>
	</div>
	
<?php
	if($info['dirname']!=$root.'archive'){
		$html = <<<EOD
	<p class="title">トピックス</p>
	<div class="box" id="toggle">
		<h4 class="toggler">オリジナルＴシャツ活用法</h4>
		<ul class="topics">
			<li><a href="/archive/repo-1.html">ダンスシーンはオリジナルプリントで</a></li>
			<li><a href="/archive/repo-2.html">文化祭、体育祭を盛り上げる！オリジナルＴシャツ</a></li>
			<li><a href="/archive/repo-3.html">イベントに必須！自分たちの“色”を前面に</a></li>
			<li><a href="/archive/repo-4.html">スポーツといえばオリジナルウェア</a></li>
			<li><a href="/archive/repo-5.html">盛り上がる結婚式にオリジナルグッズ</a></li>
			<li><a href="/archive/repo-6.html">大好きなTシャツコーディネート</a></li>
			<li><a href="/archive/repo-7.html">Happyになるオリジナルプレゼント</a></li>
			<li><a href="/archive/repo-8.html">スタッフユニフォームは、オリジナルプリント</a></li>
			<li><a href="/archive/repo-9.html">チームがひとつになる方法</a></li>
			<li><a href="/archive/repo-10.html">販促ツールにオリジナルプリントの活用術</a></li>
		</ul>
		<h4 class="toggler">目的別・用途別・シーン別</h4>
		<ul class="topics">
			<li><a href="/archive/repo-11.html">感動させるならオリジナルもの</a></li>
			<li><a href="/archive/repo-12.html">場の雰囲気を作るならこれでしょう</a></li>
			<li><a href="/archive/repo-13.html">気持ちを伝えたい口下手な人に</a></li>
			<li><a href="/archive/repo-14.html">創作する時間が思い出時間</a></li>
			<li><a href="/archive/repo-15.html">団体でお揃いを作るなら</a></li>
			<li><a href="/archive/repo-16.html">アピールするならオリジナルＴシャツ</a></li>
			<li><a href="/archive/repo-17.html">狙ったシーンにオリジナル</a></li>
		</ul>
		<h4 class="toggler">プリントにまつわるお話し</h4>
		<ul class="topics">
			<li><a href="/archive/repo-18.html">オリジナルプリントするのって難しい？</a></li>
			<li><a href="/archive/repo-19.html">何をプリントするか悩んでる人へ</a></li>
			<li><a href="/archive/repo-20.html">プリント方法はご存じ？</a></li>
			<li><a href="/archive/repo-21.html">あれもこれもプリントしたい！</a></li>
			<li><a href="/archive/repo-22.html">味があるTシャツになりたい</a></li>
			<li><a href="/archive/repo-23.html">プリントの失敗談</a></li>
		</ul>
		<h4 class="toggler">オリジナルTシャツのデザイン</h4>
		<ul class="topics">
			<li><a href="/archive/repo-24.html">How to デザイン -1-</a></li>
			<li><a href="/archive/repo-25.html">How to デザイン -2-</a></li>
			<li><a href="/archive/repo-26.html">イラスト・写真・色・文字</a></li>
			<li><a href="/archive/repo-27.html">デザインのレイアウト・バランス・色</a></li>
			<li><a href="/archive/repo-28.html">五感のイメージからデザイン</a></li>
			<li><a href="/archive/repo-29.html">雰囲気からデザイン -1-</a></li>
			<li><a href="/archive/repo-30.html">雰囲気からデザイン -2-</a></li>
			<li><a href="/archive/repo-31.html">オリジナルデザインの作品として</a></li>
		</ul>
		<h4 class="toggler">オリジナルTシャツ屋の雑記</h4>
		<ul class="topics">
			<li><a href="/archive/repo-32.html">ペアルックについて語る</a></li>
			<li><a href="/archive/repo-33.html">Tシャツの手入れ方法</a></li>
			<li><a href="/archive/repo-34.html">リメイクって楽しい</a></li>
			<li><a href="/archive/repo-35.html">かゆいところに手が届く接客</a></li>
			<li><a href="/archive/repo-36.html">時間の空いた休日の過ごし方</a></li>
			<li><a href="/archive/repo-37.html">Tシャツの歴史</a></li>
			<li><a href="/archive/repo-38.html">子供とより楽しく過ごす方法</a></li>
			<li><a href="/archive/repo-39.html">環境を考える世の中はエライ！</a></li>
			<li><a href="/archive/repo-40.html">帽子を愛してやまない訳</a></li>
		</ul>
		<h4 class="toggler">オリジナルTシャツ制作の秘訣</h4>
		<ul class="topics">
			<li><a href="/archive/repo-41.html">カッコいいオリジナルTシャツを作りたい！失敗しないデザインとは？</a></li>
			<li><a href="/archive/repo-42.html">オリジナルTシャツのデザインの魅力に迫る</a></li>
			<li><a href="/archive/repo-43.html">文化祭で着るTシャツだからこそこだわりたい！</a></li>
			<li><a href="/archive/repo-44.html">オレ流にこだわりたいならオリジナルTシャツを作る！</a></li>
			<li><a href="/archive/repo-45.html">こだわりのあるオリジナル作業着を安く作るコツ</a></li>
			<li><a href="/archive/repo-46.html">安くて丈夫！！やさしい色合いのポロシャツ</a></li>
			<li><a href="/archive/repo-47.html">イベント成功の鍵！人気のグッズと作り方！？</a></li>
			<li><a href="/archive/repo-48.html">見てくれ！！オレのマイ作業着！！</a></li>
			<li><a href="/archive/repo-49.html">お年よりも欲しがる？可愛いポロシャツ</a></li>
			<li><a href="/archive/repo-50.html">Tシャツを作るとき、入れたい文字は？</a></li>
			<li><a href="/archive/repo-51.html">クラスTシャツ、プリントカラーは何色がいい？</a></li>
			<li><a href="/archive/repo-52.html">絵心がない人でもオリジナルTシャツは作れる！</a></li>
			<li><a href="/archive/repo-53.html">スタイリッシュにみせるTシャツは</a></li>
		</ul>
		<h4 class="toggler">作る前に知っておきたいこと</h4>
		<ul class="topics">
			<li><a href="/archive/repo-54.html">Tシャツの生地、薄地派？厚地派？</a></li>
			<li><a href="/archive/repo-55.html">急ぎでTシャツを作りたい場合に気をつけるべき点とは？</a></li>
			<li><a href="/archive/repo-56.html">安心して依頼出来るＴシャツ作成業者に共通している点とは？</a></li>
			<li><a href="/archive/repo-57.html">体育祭で団結を固くする為に効果的なアイテムとは？</a></li>
			<li><a href="/archive/repo-58.html">Tシャツの生地、綿派？ドライ派？</a></li>
			<li><a href="/archive/repo-59.html">イベント主催者必見！！定番のノベルティって？</a></li>
			<li><a href="/archive/repo-60.html">Tシャツを作る際にかかる費用の相場とは？</a></li>
			<li><a href="/archive/repo-61.html">オリジナルTシャツを作って販売！売れるTシャツを作るには？</a></li>
			<li><a href="/archive/repo-62.html">バスケやるのに合う生地は</a></li>
			<li><a href="/archive/repo-63.html">汗を乾かしてくれるTシャツとは</a></li>
			<li><a href="/archive/repo-64.html">オリジナルポロシャツ 人気の色は</a></li>
			<li><a href="/archive/repo-65.html">スポーツに向いているTシャツは？</a></li>
			<li><a href="/archive/repo-66.html">スポーツに向いているポロシャツは？</a></li>
			<li><a href="/archive/repo-67.html">ダンスチームの必須アイテムとは？</a></li>
			<li><a href="/archive/repo-68.html">ヨガのとき着たいTシャツの形状は</a></li>
			<li><a href="/archive/repo-69.html">文化祭Tシャツ、何色が人気？</a></li>
			<li><a href="/archive/repo-70.html">体型に合ったTシャツの選び方とは？</a></li>
			<li><a href="/archive/repo-71.html">クラスTシャツ、いくらぐらいが相場？</a></li>
			<li><a href="/archive/repo-72.html">Tシャツのデザインはどんなのが多い？</a></li>
			<li><a href="/archive/repo-73.html">部活で着るTシャツに適した素材とは？</a></li>
			<li><a href="/archive/repo-74.html">日本とアメリカのサイズ感ってどのくらい違うの？</a></li>
			<li><a href="/archive/repo-75.html">丈夫なTシャツの素材はどんなもの？</a></li>
		</ul>
		<h4 class="toggler">オリジナルTシャツ活躍例</h4>
		<ul class="topics">
			<li><a href="/archive/repo-76.html">おそろいで作った会社のロンT！！全然丈夫！！</a></li>
			<li><a href="/archive/repo-77.html">念願のお店のTシャツ！！最高傑作！！</a></li>
			<li><a href="/archive/repo-78.html">イベント大成功！！Tシャツが心をつないだ！！</a></li>
			<li><a href="/archive/repo-79.html">フットサルチームでTシャツをつくりました！！</a></li>
			<li><a href="/archive/repo-80.html">オリジナルサッカーシャツを作ってチームの団結を強化！</a></li>
			<li><a href="/archive/repo-81.html">個人名と背番号が入った我がチームのTシャツ</a></li>
			<li><a href="/archive/repo-82.html">ランニング女子、おそろいのTシャツ</a></li>
			<li><a href="/archive/repo-83.html">チームのテンションを上げる最強アイテム！？</a></li>
			<li><a href="/archive/repo-84.html">冬のスポーツ観戦。何着て凌ぐ？</a></li>
			<li><a href="/archive/repo-85.html">日本代表の応援、何色のTシャツを着る？</a></li>
		</ul>
	</div>
EOD;
		echo $html;
	}
?>
	<div class="box">
		<p class="heading">ご注文・お問い合わせ</p>
		<ul>
			<li><a href="/order/"><img alt="お申し込み" src="/common/img/cart_black.png" width="23" />お申込みフォーム</a></li>
			<li><a href="/contact/faxorderform.pdf" target="_blank"><img alt="手描きFAX用紙" src="/common/img/pdf_icon.png" width="23" />ＦＡＸ用注文用紙</a></li>
			<li><a href="/guide/faq.html"><img alt="オリジナルTシャツQ&A" src="/common/img/help_icon.png" width="23" />よくある質問</a></li>
			<li><a href="/contact/"><img alt="お問い合わせ" src="/common/img/mail_icon_s.png" width="23" />お問い合わせ</a></li>
			<li><a href="/contact/request.html">無料サンプル資料請求</a></li>
			<li><a href="/sitemap/">サイトマップ</a></li>
		</ul>
	</div>
</div>
