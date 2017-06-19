<?php
require_once dirname(__FILE__).'/php_libs/funcs.php';

// ログイン状態のチェック
$me = checkLogin();
if(!$me){
	jump('login.php');
}

// TLA顧客IDを取得
$conndb = new Conndb(_API_U);
$u = $conndb->getUserList($me['id']);
$customerid = $u[0]['id'];

// 注文履歴
$orderid = 0;
if(isset($_POST['oi'])){
	$orderid = $_POST['oi'];
}
$conndb = new Conndb(_API);
$d = $conndb->getOroderHistory($customerid);
for($i=0; $i<count($d); $i++){
	$data .= '<thead><tr><th>注文No.<br>発送日</th><th>アイテム</th><th>枚数</th><th>金額</th></tr></thead>';
	if($d[$i]['orderid']==$orderid) $cur = $i;
	$volume = array();
	$data .= '<tr>';
	$data .= '<td class="toc">';
	$data .= '<form action="'.$_SERVER["SCRIPT_NAME"].'" method="post">';
	$data .= '<span>'.$d[$i]['orderid'].'</span>';
	$data .= '<input type="hidden" name="oi" value="'.$d[$i]['orderid'].'">';
	$data .= '<input type="button" value="明細" class="show_detail" onclick="this.form.submit();">';
	$data .= '</form>';
	$data .='<p>'.$d[$i]['schedule3'].'</p></td>';
	$data .= '<td>';
	foreach($d[$i]['itemlist'] as $itemname=>$val){
		$data .= '<p id="list_itemname">'.mb_convert_encoding($itemname, 'euc-jp', 'utf-8').'</p>';
		$volume[] = $d[$i]['itemamount'][$itemname];
	}
	$data .= '</td>';
	$data .= '<td class="tor">';
	for($t=0; $t<count($volume); $t++){
		$data .= '<p>'.number_format($volume[$t]).'<ins class="small">枚</ins></p>';
	}
	$data .= '</td>';
	$data .= '<td class="tor" rowspan="3">'.number_format($d[$i]['estimated']).'<ins class="small">円</ins></td>';
	$data .= '<tr><th>状況</th><th>追加注文</th><th>印刷</th></tr>';
	$data .= '<tr>';
	if($d[$i]['shipped']==2){
		$data .= '<td class="toc">発送済</td>';
	}else{
		if($d[$i]['progress_id']==4){
			$stat = '製作中<br><a href="progress.php?oi='.$d[$i]['orderid'].'" class="f-small">進行状況<img src="/common/img/dotarrow_right.png" class="anchor_arrow"></a>';
		}else{
			$stat = mb_convert_encoding($d[$i]['progressname'], 'euc-jp', 'utf-8');
		}
		$data .= '<td class="toc">'.$stat.'</td>';
	}
	$data .= '<td class="toc"><a href="repeatorder.php?oi='.$d[$i]['orderid'].'" class="btn-f"  class="f-small">追加注文<img src="/common/img/dotarrow_right.png" class="anchor_arrow"></a></td>';
	$data .= '<td class="toc">';
	$data .= '<input type="button" value="請求書" name="id_'.$d[$i]['orderid'].'" class="btn_bill">';
	
	/* 廃止
	if($d[$idx]['deposit']==2){
		$data .= '<input type="button" value="領収書" name="id_'.$d[$i]['orderid'].'" class="btn_receipt">';
	}
	*/
	
	if($d[$i]['shipped']==2){		// 発送済み
		$data .= '<br><input type="button" value="納品書" name="id_'.$d[$i]['orderid'].'" class="btn_invoice">';
	}
	$data .= '</td>';
	$data .= '</tr>';
}

/*
*	注文明細
*	履歴が複数ある場合は最後の注文
*/
$i = isset($cur)? $cur: --$i;
$orders_id = $d[$i]['orderid'];
$subtotal = 0;
$total = $d[$i]['estimated'];
$perone = ceil($d[$i]['estimated'] / $d[$i]['order_amount']);
$tax = $d[$i]['salestax'];
$credit = $d[$i]['creditfee'];
$base = ($d[$i]['basefee']!=$total)? $d[$i]['basefee']: 0;
foreach($d[$i]['itemlist'] as $itemname=>$info){
	foreach($info as $color=>$val){
		if($val[0]['itemcode']!=''){
			$thumbName = $val[0]['itemcode'].'_'.$val[0]['colorcode'];
			$folder = $val[0]['categorykey'];
			$thumb = '<img alt="" src="'._IMG_PSS.'items/'.$folder.'/'.$val[0]['itemcode'].'/'.$thumbName.'_s.jpg" height="26" />';
		}else{
			$thumb = '';
		}
		$items .= '<tr>';
		$items .= '<td><p>'.mb_convert_encoding($itemname, 'euc-jp', 'utf-8').'</p>';
		$items .= $thumb.'<span>カラー： '.mb_convert_encoding($color, 'euc-jp', 'utf-8').'</span></td>';
		$size = '';
		$cost = '';
		$vol = '';
		$sub = '';
		for($t=0; $t<count($val); $t++){
			$price = $val[$t]['cost'] * $val[$t]['volume'];
			$size .= '<p>'.$val[$t]['size'].'</p>';
			$cost .= '<p>'.number_format($val[$t]['cost']).'<ins class="small">円</ins></p>';
			$vol .= '<p>'.number_format($val[$t]['volume']).'<ins class="small">枚</ins></p>';
			$sub .= '<p>'.number_format($price).'<ins class="small">円</ins></p>';
			$subtotal += $price;
		}
		
		$items .= '<td class="toc">'.$size.'</td>';
		$items .= '<td class="tor">'.$cost.'</td>';
		$items .= '<td class="tor">'.$vol.'</td>';
		$items .= '<td class="tor">'.$sub.'</td>';
		$items .= '</tr>';
	}
}
$items .= '<tr><td colspan="3" class="toc">小計</td>';
$items .= '<td class="tor">'.number_format($d[$i]['order_amount']).'<ins class="small">枚</ins></td>';
$items .= '<td class="tor">'.number_format($subtotal).'<ins class="small">円</ins></td></tr>';

$discount_fee = $d[$i]['discountfee'] + $d[$i]['reductionfee'];
$print_fee = $d[$i]['printfee'] + $d[$i]['exchinkfee'];
$items .= '<tr><td colspan="4">プリント代</td><td class="tor">'.number_format($print_fee).'<ins class="small">円</ins></td></tr>';
$items .= '<tr><td colspan="4">割引</td><td class="tor fontred">▲'.number_format($discount_fee).'<ins class="small">円</ins></td></tr>';
$items .= '<tr><td colspan="4">送料</td><td class="tor">'.number_format($d[$i]['carriagefee']).'<ins class="small">円</ins></td></tr>';

$charge = array(
	'expressfee'=>'特急料金',
	'codfee'=>'代引手数料',
	'packfee'=>'袋詰代',
	'designfee'=>'デザイン代',
	'additionalfee'=>mb_convert_encoding($d[$i]['additionalname'], 'euc-jp', 'utf-8')
);
foreach($charge as $charge_key=>$charge_name){
	if(empty($d[$i][$charge_key])) continue;
	$items .= '<tr><td colspan="4">'.$charge_name.'</td><td class="tor">'.number_format($d[$i][$charge_key]).'<ins class="small">円</ins></td></tr>';
}



// プリント情報
$printing = '';
$p = $conndb->getDetailsPrint($orders_id);
foreach($p as $category_name=>$val){
	$cat = mb_convert_encoding($category_name, 'euc-jp', 'utf-8');
	for($i=0; $i<count($val); $i++){
		// 絵型
		$print_pos = '';
		$fp = fopen('../common/'.$val[$i]['area_path'], 'r');
		if($fp){
			flock($fp, LOCK_SH);
			$img = fgets($fp);
			$img = str_replace('src="./img/', 'src="./', $img);
			preg_match('/src=\"(.\/[^\"]*)\"/', $img, $src);
			$src1 = str_replace('./', '', $src[1]);
			$print_pos .= '<img alt="プリント位置" src="'._IMG_PSS.$src1.'" />';	// ボディ画像
			while(!feof($fp)){
				$buffer = fgets($fp);	// プリント位置ごとに処理
				if(strpos($buffer, '"'.$val[$i]['select_key'].'"')!==false){
					$buffer = str_replace('src="./img/', 'src="'._IMG_PSS, $buffer);
					$buffer = mb_convert_encoding($buffer, 'euc-jp', 'utf-8');
					if($val[$i]['category_id']!=99){
						$print_pos .= str_replace('.png', '_on.png', $buffer);
					}else{
						$print_pos .= $buffer;
					}
				}
			}
			flock($fp, LOCK_UN);
		}
		fclose($fp);
		
		// デザイン
		$design = '';
		if(!empty($val[$i]['design_path'])){
			$design = '<img src="'.$val[$i]['design_path'].'" width="200">';
		}
		
		$printing .= '<tr>';
		$printing .= '<td>'.$cat.'</td>';
		$printing .= '<td><div class="pos_wrap">'.$print_pos.'</div></td>';
		//$printing .= '<td>'.$design.'</td>';
		$printing .= '</tr>';
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
	<title>ご注文履歴 - TLAメンバーズ | タカハマライフアート</title>
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
	<link rel="stylesheet" type="text/css" media="screen" href="/common/css/printposition_responsive.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="./css/style_responsive.css" />
	<script type="text/javascript" src="/common/js/jquery.js"></script>
	<script type="text/javascript" src="/common/js/modalbox/jquery.modalbox-min.js"></script>
	<script type="text/javascript" src="./js/common.js"></script>
	<script type="text/javascript" src="./js/history.js"></script>
	<script type="text/javascript">
		var _CUR_ORDER = <?php echo $orderid?>;
	</script>
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
						<?php echo $menu;?>
					</div>
				</div>
			</div>
			<div class="pagetitle"><h1>ご注文履歴</h1></div>

			<table class="form_table" id="history_table">
				<h2>ご注文一覧</h2>
				<tbody>
					<?php echo $data; ?>
				</tbody>
			</table>
			
			<h2>注文明細</h2>
			<p class="note tor">注文No.<?php echo $orders_id;?></p>
			<table class="form_table" id="detail_item">
				<thead>
				</thead>
				<tfoot>
				<?php
					if($base>0){
						echo '<tr class="foot_sub"><th colspan="2"></th><td colspan="2">計</th><td class="base"><ins>'.number_format($base).'</ins> 円</td></tr>';
					}
					if($tax>0){
						echo '<tr class="foot_sub"><th colspan="2"></th><td colspan="2">消費税</th><td class="tax"><ins>'.number_format($tax).'</ins> 円</td></tr>';
					}
					if($credit>0){
						echo '<tr class="foot_sub"><th colspan="2"></th><td colspan="2">カード手数料</th><td class="credit"><ins>'.number_format($credit).'</ins> 円</td></tr>';
					}
				?>
					<tr class="foot_total"><th colspan="2"></th><td colspan="2">合計</th><td class="tot"><ins><?php echo number_format($total); ?></ins> 円</td></tr>
					<tr class="foot_perone"><th colspan="2"></th><td colspan="2">1枚あたり</th><td class="per"><ins><?php echo number_format($perone); ?></ins> 円</td></tr>
				</tfoot>
				<tbody><?php echo $items;?></tbody>
			</table>
			
			<table class="form_table" id="detail_print">
				<caption>プリント情報</caption>
				<thead>
					<tr>
						<th>カテゴリー</th><th>プリント位置</th>
					</tr>
				</thead>
				<tbody><?php echo $printing;?></tbody>
			</table>
			
		</div>
	</div>
	
	<p class="scroll_top"><a href="#header">ご注文履歴　ページトップへ</a></p>
	
	<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php"; ?> 
	
	<div id="printform_wrapper"><iframe id="printform" name="printform"></iframe></div>

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