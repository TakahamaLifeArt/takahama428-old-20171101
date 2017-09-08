<?php
	$_FLG_ITEM_ID = isset($_REQUEST['item_id'])? $_REQUEST['item_id'] : 1;
	$_FLG_COLORCODE = isset($_REQUEST['color_code'])? $_REQUEST['color_code']: '';
	$_UPDATED = empty($_REQUEST['update'])? 0: $_REQUEST['update'];

	require_once dirname(__FILE__).'/../php_libs/t_orders.php';
	$order = new Orders();


//ユーザー情報を取得
	$me = $_SESSION['me'];
	if(!empty($_SESSION['me'])) {
		$order->userAuto($me);
	}
/*
	if(isset($_REQUEST["login"])){
		$me = $conndb->getUser($_REQUEST);
	}
*/
	// 消費税
	$tax = $order->salestax();
	$tax /= 100;


	// カテゴリー情報を取得
	//$itemattr = $conn->itemAttr($_FLG_ITEM_ID);
	list($itemattr, $categories) = $order->getCategoryData($_FLG_ITEM_ID);
	
	list($categorykey, $categoryname) = each($itemattr['category']);
	$categoryname = mb_convert_encoding($categoryname,'euc-jp','utf-8');
	list($itemcode, $itemname) = each($itemattr['name']);
	list($code, $colorname) = each($itemattr['code']);
	$itemname = mb_convert_encoding($itemname,'euc-jp','utf-8');
	$curcolor = mb_convert_encoding($colorname,'euc-jp','utf-8');

	// 商品詳細とシーン別からの遷移してきたときのパラメータ
	$folder = $categorykey;
	if($_UPDATED!=0){
		/*
		$subcat = Items::getSubCategory();
		if($categorykey=='t-shirts'){
			if($subcat['long-shirts'][$itemcode]){
				$categorykey = 'long-shirts';
				$folder = 'long-shirts';
			}else if($subcat['baby'][$itemcode]){
				$categorykey = 'baby';
			}
		}
		*/
		$_CAT_KEY = $categorykey;
	}

	// アイテム一覧情報を取得
	$ite = new Items($categorykey);
	$res = $ite->getItemlist();

	// estimattion data
	$data = $order->reqDetails();
	$total = $data['total']*(1+$tax);
	if($data['options']['payment']==3) $total = $total*(1+_CREDIT_RATE);
	$cart_amount = $data['amount'];
	if($data['amount'] > 0) {
		$perone = floor($total/$data['amount']);
	} else {
		$perone = 0;
	}
	$total = floor($total);
	$cart_total = $total;

	// user info
	foreach((array)$regist['customer'] as $key=>$val){
		$user[$key] = mb_convert_encoding($val, 'euc-jp', auto);
	}

	// カテゴリーセレクター
	$category_selector = '<select id="category_selector">';
	for($i=0; $i<count($categories); $i++){
		$category_selector .= '<option value="'.$categories[$i]['code'].'" rel="'.$categories[$i]['id'].'">'.mb_convert_encoding($categories[$i]['name'],'euc-jp','utf-8').'</option>';
	}
	$category_selector .= '</select>';
	$category_selector = str_replace('value="'.$categorykey.'"', 'value="'.$categorykey.'" selected="selected"', $category_selector);
?>
<!DOCTYPE html>
<html>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#  website: http://ogp.me/ns/website#">
	<meta charset="euc-jp" />
	<title>お申し込みフォーム ｜ オリジナルTシャツが早い、タカハマライフアート</title>
	<meta name="Description" content="お申し込みフォームからカンタンにオリジナルTシャツがご注文できます。Web上で金額を確認しながら進めるので安心です。対応も早い！割引キャンペーンでオンライン見積の料金よりお安くなるかも？トレーナー・ポロシャツ・オリジナルTシャツの作成・プリントは、東京都葛飾区のタカハマライフアートにお任せ下さい！" />
	<meta name="keywords" content="注文,お申し込み,オリジナル,Tシャツ,作成" />

	<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
	<link rel="shortcut icon" href="/icon/favicon.ico" />
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="/common/css/common_responsive.css" media="all" />
	<link rel="stylesheet" type="text/css" href="/common/css/base_responsive.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/common/js/ui/flick/jquery.ui.core.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/common/js/ui/flick/jquery.ui.datepicker.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/common/js/ui/flick/jquery.ui.theme.css" media="screen" />

	<link rel="stylesheet" type="text/css" href="/common/js/modalbox/css/jquery.modalbox.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/common/js/uniform/css/uniform.default.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/common/css/printposition_responsive.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="./css/order_responsive.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../../css/media-style.css" media="all">
	<!--m3 begin-->
	<link rel="stylesheet" type="text/css" href="/m3/common/css/common_responsive.css" media="all">
	<link rel="stylesheet" type="text/css" href="/m3/common/css/slidebars_responsive.css" media="all">
	<link rel="stylesheet" href="/m3/common/css/import_responsive.css">
	<link rel="stylesheet" href="/m3/items/css/detail_responsive.css">
	<!--m3 end-->

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
	<script src="//ajaxzip3.github.io/ajaxzip3.js" charset="utf-8"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script type="text/javascript" src="/common/js/tlalib.js"></script>
	<script type="text/javascript" src="./js/t_orderform.js"></script>
	<script type="text/javascript">
		var _UPDATED = <?php echo $_UPDATED; ?>;
		var _ITEM_ID = <?php echo $_FLG_ITEM_ID; ?>;
		var _CAT_KEY = '<?php echo $_CAT_KEY; ?>';
		var _TAX = <?php echo $tax; ?>;
		var _CREDIT_RATE = <?php echo _CREDIT_RATE; ?>;
		var _IMG_PSS = '<?php echo _IMG_PSS;?>';
	</script>
	<!--m3 begin-->
	<script src="/m3/common/js/common1.js"></script>
	<!--m3 end-->

	<!-- OGP -->
	<meta property="og:title" content="世界最速！？オリジナルTシャツを当日仕上げ！！" />
	<meta property="og:type" content="article" /> 
	<meta property="og:description" content="業界No. 1短納期でオリジナルTシャツを1枚から作成します。通常でも3日で仕上げます。" />
	<meta property="og:url" content="http://www.takahama428.com/" />
	<meta property="og:site_name" content="オリジナルTシャツ屋｜タカハマライフアート" />
	<meta property="og:image" content="http://www.takahama428.com/common/img/header/Facebook_main.png" />
	<meta property="fb:app_id" content="1605142019732010" />
	<!--  -->
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
	<!-- m3 begin -->
	<header id="header" class="head2">
		<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc/header.html"); ?>
	</header>
	<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc/gnavi.html"); ?>
	<!-- m3 end -->

	<div id="container">
		<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/globalmenu.php"; ?>

		<div class="contents" style="z-index:5;">

			<ul class="pan">
				<li><a href="/">オリジナルＴシャツ屋ＴＯＰ</a></li>
				<li>お申し込み</li>
			</ul>

			<div class="heading1_wrapper">
				<h1>お申し込みフォーム</h1>
				<p class="comment">
					FAXでのお申し込みをご希望の方は、<br><a href="/contact/faxorderform.pdf" target="_blank">FAX用フォーム</a>をプリントして送信できます。<br>
					<a href="/design/designguide_campus.html" target="_blank" class="comment_des">手描きのデザインで作りたい学生さんはこちら！</a>
				</p>
				<img src="./img/order.png" class="order_flow" style="margin-top:10px; border: 1px solid #efefef;" width="100%">
			</div>

			<div id="gall">
				<div id="step1" class="is-appear">
					<div class="heading"></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first">
							<p>Step1</p>
							<div>プリントする<br>アイテムを選択</div>
						</div>
						<div class="crumbs">
							<p>Step2</p>
							<div>カラー・サイズ<br>枚数入力</div>
						</div>
						<div class="crumbs">
							<p>Step3</p>
							<div>プリント位置<br>を指定</div>
						</div>
						<div class="crumbs">
							<p>Step4</p>
							<div><img alt="カート" src="./img/cart.png" />カート<br>に入れる</div>
						</div>
						<div class="crumbs">
							<p>Step5</p>
							<div>お客様情報<br>の入力</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">内容を確認して<br>お申し込み</div>
						</div>
					</div>

                    <div class="step_inner">
                        <h2><ins>Step1</ins>プリントするアイテムをお選びください</h2>

                        <div class="category_list">
                            <h3><ins>１.</ins>カテゴリーを指定</h3>
                            <?php echo $category_selector; ?>
                        </div>

                        <h3 id="h3_itemlist"><ins>２.</ins>アイテムを選択してください<span>（<?php echo count($res); ?> アイテム）</span></h3>
                        <div id="itemlist_wrap">
                        <?php
                            $recomend = '';
                            $ls='';
                            $tmp = array();
                            $i=0;
                            foreach($res as $code=>$v){
                                if($code=='085-cvt') $tmp[0] = array($code=>$res[$code]);
                                if($code=='300-act') $tmp[1] = array($code=>$res[$code]);
                                if($code=='5806') $tmp[2] = array($code=>$res[$code]);
                                
                                if($i%4==0){
                                    $firstlist = ' firstlist';
                                }else{
                                    $firstlist = '';
                                }
                                if( preg_match('/^p-/',$code) || $code=='ss-9999'){
									$suffix = '_style_0';
								}else{
									$suffix = '_'.$v['initcolor'];
								}
                                $ls .= '<li class="listitems_ex'.$firstlist.'" id="itemid_'.$v['item_id'].'_'.$v['pos_id'].'">
                                            <ul class="maker_'.$v['maker_id'].'">
                                                <li class="point_s">'.mb_convert_encoding($v['features'],'euc-jp','utf-8').'</li>
                                                <li class="item_name_s">
                                                    <ul>
                                                        <li class="item_name_kata">'.strtoupper($code).'</li>
                                                        <li class="item_name_name">'.mb_convert_encoding($v['item_name'],'euc-jp','utf-8').'</li>
                                                    </ul>
                                                </li>
                                                <li class="item_image_s">
                                                    <img src="'._IMG_PSS.'items/list/'.$folder.'/'.$code.'/'.$code.$suffix.'.jpg" width="100%" height="100%" alt="'.strtoupper($code).'">
                                                    <img src="./img/crumbs_next.png" alt="" class="icon_arrow">
                                                </li>
                                                <li class="item_info_s">
                                                    <div class="colors">'.$v['colors'].'</div>
                                                    <div class="sizes">'.$v['sizes'].'</div>
                                                    <p class="price_s" style="white-space: nowrap;">
                                                        <p style="display:inline-block;">TAKAHAMA価格<p/>
                                                        <span id="price_cost" style="white-space: nowrap;"><span>'.$v['minprice'].'</span>円&#12316;</span>
                                                    </p>

                                                </li>
                                            </ul>
                                            <p class="tor"><a href="../items/'.$folder.'/item.html?id='.$code.'">アイテムの詳細へ</a></p>
                                        </li>';
                                $i++;
                            }

                            if(!empty($tmp)){
                                for($i=0; $i<count($tmp); $i++){
                                    list($code, $v) = each($tmp[$i]);
                                    if($i==2) $lastli = ' lastli';
                                    $recomend .= '<li class="recitembox'.$lastli.'" id="itemid_'.$v['item_id'].'_'.$v['pos_id'].'">
                                        <img class="rankno" src="./img/no'.($i+1).'.png" width="60" height="55" alt="No1">
                                        <ul class="maker_'.$v['maker_id'].'">
                                            <li class="item_name">

                                                <p>'.mb_convert_encoding($v['features'],'euc-jp','utf-8').'</p>
                                                <ul class="popu_item_name">
                                                    <li class="item_name_kata">'.strtoupper($code).'</li>
                                                    <li class="item_name_name">'.mb_convert_encoding($v['item_name'],'euc-jp','utf-8').'</li>
                                                </ul>
                                            </li>
                                            <li class="item_image">
                                                <img src="'._IMG_PSS.'items/'.$folder.'/'.$code.'/'.$code.'_'.$v['initcolor'].'.jpg" width="250" alt="'.strtoupper($code).'">
                                                <img src="./img/crumbs_next.png" alt="" class="icon_arrow">
                                            </li>
                                            <li class="item_info clearfix">
                                                <div class="color">'.$v['colors'].'</div>
                                                <div class="size">'.$v['sizes'].'</div>
                                                <p class="price" style="white-space: nowrap;">
                                                  	<p style="display:inline-block;">TAKAHAMA価格<p/>
                                                    <span id="price_cost" style="white-space: nowrap;"><span>'.$v['minprice'].'</span>円&#12316;</span>
                                                </p>
                                            </li>
                                        </ul>
                                    </li>';
                                }

                                echo '<ul class="recommend_item clearfix">'.$recomend.'</ul>';
                            }

                            echo '<ul class="listitems clearfix">'.$ls.'</ul>';
                        ?>
                        </div>
					</div>
				</div>

				<div id="step2">
					<div class="heading clearfix"><p class="arrow prev" data-back="0"><span>戻る</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>プリントする<br>アイテムを選択</div>
						</div>
						<div class="crumbs pass">
							<p>Step2</p>
							<div>カラー・サイズ<br>枚数入力</div>
						</div>
						<div class="crumbs">
							<p>Step3</p>
							<div>プリント位置<br>を指定</div>
						</div>
						<div class="crumbs">
							<p>Step4</p>
							<div><img alt="カート" src="./img/cart.png" />カート<br>に入れる</div>
						</div>
						<div class="crumbs">
							<p>Step5</p>
							<div>お客様情報<br>の入力</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">内容を確認して<br>お申し込み</div>
						</div>
					</div>
					<div class="step_inner">
						<h2><ins>Step2</ins>カラー・サイズ・枚数を指定してください</h2>

						<p id="cur_item_name_wrap">アイテム名：　<span id="cur_item_name" class="prop_1_1"><?php echo $itemname;?></span></p>

						<div class="pane">
							<h3><ins>1.</ins>アイテムカラーの指定</h3>
							<div class="thumb_wrap clearfix">
								<div class="item_thumb">
				                	<p class="thumb_h"><span>Color</span>全<span class="num_of_color"><?php echo $color_count; ?></span>色<span class="notes_color"><?php echo $curcolor; ?></span></p>
				                    <ul class="color_thumb"><?php echo $thumbs; ?></ul>
								</div>
								<div class="item_image"><?php echo $itemimage; ?></div>
							</div>

							<div class="sizeprice">
								<h3>
									<ins>2.</ins>サイズと枚数の指定　　　
								</h3>
								<table class="size_table">
									<caption></caption>
									<tbody><tr><td></td></tr></tbody>
								</table>
								<div class="btmline">小計<span class="cur_amount"><?php echo $sum; ?></span>枚</div>
							</div>
						</div>

						<div class="btn_line">
							<span>※</span>色違いのアイテムを選べます
							<div id="add_item_color" class="btn_sub">別のカラーを追加する</div>
						</div>

						<div class="arrow_line">
							<div style="display:inline-block;">合計<span id="tot_amount">0</span>枚</div>
							<div class="arrow prev" data-back="0"><span>戻る</span></div>
							<div class="step_next goto_position" onclick="ga('send','event','step2','click','order',5300);">次へ進む</div>
						</div>
					</div>
        		</div>

				<div id="step3">
					<div class="heading clearfix"><p class="arrow prev" data-back="1"><span>戻る</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>プリントする<br>アイテムを選択</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step2</p>
							<div>カラー・サイズ<br>枚数入力</div>
						</div>
						<div class="crumbs pass">
							<p>Step3</p>
							<div>プリント位置<br>を指定</div>
						</div>
						<div class="crumbs">
							<p>Step4</p>
							<div><img alt="カート" src="./img/cart.png" />カート<br>に入れる</div>
						</div>
						<div class="crumbs">
							<p>Step5</p>
							<div>お客様情報<br>の入力</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">内容を確認して<br>お申し込み</div>
						</div>
					</div>
					<div class="step_inner">
						<h2><ins>Step3</ins>プリントする位置とデザインの色数を指定してください</h2>

						<div>
							<p><label><input type="checkbox" name="noprint" id="noprint" value="1"> プリントなしで購入する</label></p>
							<p class="note"><span>※</span>プリントなしの場合1割増しになります。</p>
						</div>

						<div id="pos_wrap"></div>
						
						<div>
							<h3 class="heading_mark">刺繍をご希望の方はご記入ください</h3>
							<p class="note">例　左そで：刺繍</p>
							<textarea id="note_printmethod"  name="note_printmethod"></textarea>
						</div>
                        <div class="bdrtxt"><p><span class="demoSpan1"></span>刺繍のお見積金額は含まれてはおりません。別途お見積りしてご連絡させて頂きます。</p></div>
						<div class="arrow_line"><div class="arrow prev" data-back="1"><span>戻る</span></div><div class="step_next goto_cart" onclick="ga('send','event','step3','click','order',5300);">カートに入れる</div></div>
                    </div>
				</div>

				<div id="step4">
					<div class="heading clearfix"><p class="arrow prev" data-back="0"><span>別の商品を見る</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>プリントする<br>アイテムを選択</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step2</p>
							<div>カラー・サイズ<br>枚数入力</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step3</p>
							<div>プリント位置<br>を指定</div>
						</div>
						<div class="crumbs pass">
							<p>Step4</p>
							<div><img alt="カート" src="./img/cart.png" />カート<br>に入れる</div>
						</div>
						<div class="crumbs">
							<p>Step5</p>
							<div>お客様情報<br>の入力</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">内容を確認して<br>お申し込み</div>
						</div>
					</div>
					<div class="step_inner">
						<h2><ins>Step4</ins>カート</h2>

						<div id="estimation_wrap">
							<table>
								<caption>お見積り</caption>
								<thead>
									<tr><th>商品名 / カラー</th><th>サイズ</th><th>単価</th><th>枚数</th><th>金額</th></tr>
								</thead>
								<tfoot>
									<tr><td colspan="3">商品代計</td><td class="ac"><ins class="totamount">0</ins> 枚</td><td class="itemsum">0</td></tr>
									<tr><td colspan="1">プリント代</td><td class="print_size ac"></td><td class="print_pos ac"></td><td class="ink_count ac"></td><td class="printfee">0</td></tr>
									<tr><td colspan="4">送料</td><td class="carriage">0</td></tr>
									<tr><td colspan="4">代引手数料</td><td class="codfee">0</td></tr>
									<tr><td colspan="4">コンビニ手数料</td><td class="conbifee">0</td></tr>
									<tr><td colspan="4">袋詰代</td><td class="package">0</td></tr>
									<tr><td colspan="1">割引</td><td colspan="3" class="discountname"></td><td class="discountfee">0</td></tr>
									<tr><td colspan="1">特急料金</td><td colspan="3" class="expressinfo"></td><td class="expressfee">0</td></tr>
									<tr class="foot_sub"><td colspan="4">計</td><td class="base">0</td></tr>
									<tr class="foot_sub"><td colspan="4">消費税</td><td class="tax">0</td></tr>
									<tr class="foot_sub"><td colspan="4">カード決済システム利用料</td><td class="credit">0</td></tr>
									<tr class="foot_total"><td colspan="4">お見積り合計</td><td class="total">0</td></tr>
									<tr class="foot_perone"><td colspan="4">1枚あたり</td><td class="perone">0</td></tr>
								</tfoot>
								<tbody>
									<tr><td colspan="7"></td><td class="last"></td></tr>
								</tbody>
							</table>
							<p class="note"><span>※</span>お見積りは概算です。デザインの内容によって変更になる場合がございます。</p>
						</div>

						<div class="inner option_wrap">
							<table id="option_table">
								<caption class="highlights">割引適用</caption>
								<p class="note"><span>※</span>商品のみご注文の場合は割引は適用されません</p>
								<tbody>
									<tr>
										<th>学生さんですか</th>
										<td>
											<label><input type="radio" name="student" value="0" <?php if(empty($regist['options']['student'])) echo 'checked="checked"'; ?> />いいえ</label>
											<label><input type="radio" name="student" value="3" <?php if($regist['options']['student']==3) echo 'checked="checked"'; ?> />はい<ins>3%OFF</ins></label>
											<label><input type="radio" name="student" value="5" <?php if($regist['options']['student']==5) echo 'checked="checked"'; ?> />2クラス<ins>5%OFF</ins></label>
											<label><input type="radio" name="student" value="7" <?php if($regist['options']['student']==7) echo 'checked="checked"'; ?> />3クラス<ins>7%OFF</ins></label>
										</td>
									</tr>
									<tr>
										<th>レビューを掲載しますか</th>
										<td>
											<label><input type="radio" name="blog" value="0" <?php if(empty($regist['options']['blog'])) echo 'checked="checked"'; ?> />いいえ</label>
											<label><input type="radio" name="blog" value="3" <?php if($regist['options']['blog']==3) echo 'checked="checked"'; ?> />はい<ins>3%OFF</ins></label>
										</td>
									</tr>
									<tr>
										<th>Illustratorで入稿しますか</th>
										<td>
											<label><input type="radio" name="illust" value="0" <?php if(empty($regist['options']['illust'])) echo 'checked="checked"'; ?> />いいえ</label>
											<label><input type="radio" name="illust" value="1" <?php if($regist['options']['illust']==1) echo 'checked="checked"'; ?> />はい<ins>1,000円OFF</ins></label>
										</td>
									</tr>
									<tr>
										<th>弊社のお客様からのご紹介ですか</th>
										<td>
											<label><input type="radio" name="intro" value="0" <?php if(empty($regist['options']['intro'])) echo 'checked="checked"'; ?> />いいえ</label>
											<label><input type="radio" name="intro" value="3" <?php if($regist['options']['intro']==3) echo 'checked="checked"'; ?> />はい<ins>3%OFF</ins></label>
										</td>
									</tr>
									<tr class="separate">
										<th>袋詰め　<span class="anchor" id="pop_pack">袋詰めとは</span></th>
										<td>
											<label><input type="radio" name="pack" value="0" <?php if(empty($regist['options']['pack'])) echo 'checked="checked"'; ?> />希望しない</label>
											<label><input type="radio" name="pack" value="2" <?php if($regist['options']['pack']==2) echo 'checked="checked"'; ?> />袋のみ同封（10円/1枚）</label>
											<br>
											<label><input type="radio" name="pack" value="1" <?php if($regist['options']['pack']==1) echo 'checked="checked"'; ?> />希望する（50円/1枚）</label>

										</td>
									</tr>
									<tr>
										<th>お支払方法　<span class="anchor" id="pop_payment">注意点</span></th>
										<td>
											<label><input type="radio" name="payment" value="0" <?php if(empty($regist['options']['payment'])) echo 'checked="checked"'; ?> />銀行振込</label>
										<!--	<label><input type="radio" name="payment" value="2" <?php if($regist['options']['payment']==2) echo 'checked="checked"'; ?> />現金（工場で受取）</label>  -->
											<label><input type="radio" name="payment" value="1" <?php if($regist['options']['payment']==1) echo 'checked="checked"'; ?> />代金引換（手数料800円）</label>
											<br>
											<label><input type="radio" name="payment" value="3" <?php if($regist['options']['payment']==3) echo 'checked="checked"'; ?> />カード決済（システム利用料5％）</label>
<!--											<label><input type="radio" name="payment" value="4" <?php if($regist['options']['payment']==4) echo 'checked="checked"'; ?> />コンビニ決済（手数料800円）</label>-->
										</td>
									</tr>
								</tbody>
							</table>

							<div class="line">
								<label class="title">ご希望納期</label><input class="datepicker" id="deliveryday" type="text" size="14" name="deliveryday" value="<?php echo $regist['options']['deliveryday']; ?>" <?php if($regist['options']['nodeliday']==1) echo 'disabled'; ?> />
								<label><input type="checkbox" name="nodeliday" id="nodeliday" value="1" <?php if($regist['options']['nodeliday']==1) echo 'checked="checked"'; ?> > 納期の指定なし</label>
								<p id="express_notice"><span class="highlights">※<ins></ins></span><span class="anchor" id="pop_express">特急料金について</span></p>
								<p class="note"><span>※</span>袋詰め10枚以上で製作日数にプラス1日いただきます。</p>
								<p>
									<label class="title">お届時間帯の指定</label>
				 					<select name="deliverytime" id="deliverytime">
				 					<?php
										$option = '<option value="0">---</option>
				 						<option value="1">午前中</option>
				 						<option value="3">14:00-16:00</option>
				 						<option value="4">16:00-18:00</option>
				 						<option value="5">18:00-20:00</option>
				 						<option value="6">19:00-21:00</option>';
										$option = str_replace('value="'.$regist['options']['deliverytime'].'"', 'value="'.$regist['options']['deliverytime'].'" selected="selected"', $option);
										echo $option;
									?>
				 					</select>
			 					</p>
			 					<p class="note"><span>※</span>お時間をご指定して頂いても天候、交通事情、地域によりご希望に添えない場合がございますので、予めご了承願います。</p>
							</div>
						</div>

						<div class="inner">
							<h3 class="heading_mark">デザインの画像ファイルをお持ちの方はこちらから添付してください</h3>
							<form enctype="multipart/form-data" method="post" target="upload_iframe" action="/php_libs/t_orders.php" name="uploaderform" id="uploaderform">
								<input type="hidden" value="update" name="act" />
								<input type="hidden" value="attach" name="mode" />
								<input type="hidden" value="<?php echo $regist['attach'][0]['img']['name']; ?>" name="attachname[]" />
								<p><input type="file" onChange="this.form.submit()" name="attach[]" size="19" title="デザインファイルを指定してください" /><span class="del_attach"><img src="/common/img/delete.png" alt="取消">取消</span></p>
								<p><span class="add_attach btn_sub">別の添付ファイルを追加</span></p>
								<h4>デザインについてのご要望など</h4>
								<textarea id="note_design" name="note_design"><?php echo $user['note_design']; ?></textarea>
							</form>

							<div class="chapter">
								<h4>手描きのデザイン（ラフスケッチ）でプリントをご希望の場合</h4>
								<p>FAXでのお申し込みができます、<a href="/contact/faxorderform.pdf" target="_blank">FAX用フォーム</a>をプリントして送信してください。</p>
								<p>FAX: <?php echo _OFFICE_FAX;?></p>
							</div>
						</div>

						<div class="inner">
							<h3 class="heading_mark">プリントするデザインの色がお決まりの方はご記入ください</h3>
							<p class="note">例　前→レッド、　背面→ホワイト</p>
							<textarea id="note_printcolor" name="note_printcolor"><?php echo $user['note_printcolor']; ?></textarea>
						</div>

						<div class="arrow_line"><div class="arrow prev" data-back="0"><span>別の商品を見る</span></div><div class="step_next goto_user" onclick="ga('send','event','step4','click','order',5300);">次へ進む</div></div>
                    </div>
				</div>

				<div id="step5">
					<div class="heading clearfix"><p class="arrow prev" data-back="3"><span>戻る</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>プリントする<br>アイテムを選択</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step2</p>
							<div>カラー・サイズ<br>枚数入力</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step3</p>
							<div>プリント位置<br>を指定</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step4</p>
							<div><img alt="カート" src="./img/cart.png" />カート<br>に入れる</div>
						</div>
						<div class="crumbs pass">
							<p>Step5</p>
							<div>お客様情報<br>の入力</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">内容を確認して<br>お申し込み</div>
						</div>
					</div>
					<div class="step_inner">
						<h2><ins>Step5</ins>お客様情報を入力してください</h2>
						<div id="userinfo" class="clearfix">
							<p class="comment">「<span>※</span>」印は必須入力です。</p>
							<div id="user_wrap" class="clearfix inner">
								<div class="g_ft" style="width=98%;border-bottom: 1px solid #d8d8d8;margin-top:20px;padding-bottom:20px;">
									<div class="ft">
										<ul>
											<h1 class="login_nodisplay">マイページをお持ちの方はこちら</h1>
											<li id= "login_email" class="login_nodisplay"><h2>メールアドレス:<span class="fontred">※</span></h2><input type="text" id="login_input_email" name="login_input_email" value="<?php echo $user['email']; ?>" /></li>
											<li class="login_nodisplay"><h2>パスワード　　:<span class="fontred">※</span></h2><input type="password" value="<?php echo $user['password']; ?>" id="login_input_pass"  name="login_input_pass" /></li>
										</ul>
									</div>
									<div class="ft">
										<ul>
											<li class="login_nodisplay"><input type="button" id="member_login" value="ログイン" /></li>
											<div class="login_nodisplay"><span class="fontred">※</span><a href="/user/resend_pass.php">パスワードを忘れた方はこちらへ</a></div>
										</ul>
									</div>
								</div>
									<div class="ft">
										<ul>
											<h1 class="login_nodisplay" style="margin-top:35px;">初めての方はこちら</h1>
											<li id= "login_email"><h2>メールアドレス:<span class="fontred">※</span></h2><input type="text" id="email" name="email" value="<?php echo $user['email']; ?>" /></li>
											<li class="login_nodisplay"><h2>新規 パスワード:<span class="fontred">※</span></h2><input type="password" value="<?php echo $user['password']; ?>" id="pass"  name="pass" /></li>
											<li class="login_nodisplay"><span class="fontred">※</span>新規の方は、新しくパスワードを入力します。半角英数字4文字以上16文字以内。</li>
										</ul>
									</div>
								<div class="fl">
									<ul>
										<li><h2>お名前:<span class="fontred">※</span></h2>
											<input type="text" id="customername" name="customername" value="<?php echo $user['customername']; ?>">　様
										</li>
										<li>
											<h2>フリガナ:</h2><input type="text" id="customerruby" name="customerruby" value="<?php echo $user['customerruby']; ?>">　様
										</li>
										<li><h2>お電話番号:<span class="fontred">※</span></h2><input type="text" id="tel" name="tel" class="forPhone"  value="<?php echo $user['tel']; ?>" /></li>
<!--
										<li><h2>弊社ご利用について:<span class="fontred">※</span></h2>
											<label class="lbl"><input type="radio" name="repeater" value="1" <?php if($user['repeater']==1) echo 'checked="checked"'; ?> /> 初めてのご利用</label>
											<label class="lbl"><input type="radio" name="repeater" value="2" <?php if($user['repeater']==2) echo 'checked="checked"'; ?> /> 以前にも注文したことがある</label>
										</li>
-->
									</ul>
								</div>
								<div class="fr">
									<ul>
										<li><h2 class="login_nodisplay">ご住所:<span class="fontred">※</span></h2></li>
										<li><h2 class="login_display">お届け先:<span class="fontred">※</span></h2></li>
					 					<li><p><select name="delivery_customer" id="delivery_customer"></select></p></li>
					 					<li>
											<p>〒<input type="text" name="zipcode" class="forZip" id="zipcode1" value="<?php echo $user['zipcode']; ?>" onChange="AjaxZip3.zip2addr(this,'','addr0','addr1');" /></p>
											<p><input type="text" name="addr0" id="addr0" value="<?php echo $user['addr0']; ?>" placeholder="都道府県" maxlength="4" /></p>
											<p><input type="text" name="addr1" id="addr1" value="<?php echo $user['addr1']; ?>" placeholder="文字数は全角28文字、半角56文字です" maxlength="56" class="restrict" /></p>
											<p><input type="text" name="addr2" id="addr2" value="<?php echo $user['addr2']; ?>" placeholder="文字数は全角16文字、半角32文字です" maxlength="32" class="restrict" /></p>
										</li>
										<li><h2>ご要望・ご質問など:</h2><textarea cols="30" rows="5" name="comment"><?php echo $user['comment']; ?></textarea></li>
									</ul>
								</div>
							</div>

							<table class="inner">
								<tbody>
									<tr>
										<th>
											デザインの掲載について:
										</th>
										<td>
											<p class="txt">オリジナルプリントを作成される方の参考に、皆様のデザインをWEB上に<br>掲載させて頂いております。下記の選択をお願い致します。</p>
											<p class="line">
												<label><input type="radio" name="publish" value="0" <?php if(empty($regist['options']['publish'])) echo 'checked="checked"'; ?> /> 掲載可</label>
												<label><input type="radio" name="publish" value="1" <?php if($regist['options']['publish']==1) echo 'checked="checked"'; ?> /> 掲載不可</label>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="arrow_line"><div class="arrow prev" data-back="3"><span>戻る</span></div><div class="step_next goto_confirm" onclick="ga('send','event','step5','click','order',5300);">確認画面へ</div></div>
					</div>
				</div>

				<div id="step6">
					<div class="heading clearfix"><p class="arrow prev" data-back="4"><span>戻る</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>プリントする<br>アイテムを選択</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step2</p>
							<div>カラー・サイズ<br>枚数入力</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step3</p>
							<div>プリント位置<br>を指定</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step4</p>
							<div><img alt="カート" src="./img/cart.png" />カート<br>に入れる</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step5</p>
							<div>お客様情報<br>の入力</div>
						</div>
						<div class="crumbs pass step_fin">
							<p>Step6</p>
							<div class="fin">内容を確認して<br>お申し込み</div>
						</div>
					</div>

                    <div class="step_inner">
						<h2><ins>Step6</ins>お申し込み内容をご確認ください</h2>

						<form id="orderform" name="orderform" method="post" action="./ordercomplete.php" onSubmit="return false;">
							<?php
								$ticket = htmlspecialchars(md5(uniqid().mt_rand()), ENT_QUOTES);
								$_SESSION['ticket'] = $ticket;
							?>
							<input type="hidden" name="ticket" value="<?php echo $ticket; ?>">

							<div class="inner1">
								<table id="conf_item">
									<caption>アイテム</caption>
									<thead>
										<tr>
											<th>商品名 / カラー</th><th>サイズ</th><th>単価</th><th>枚数</th><th>金額</th>
										</tr>
									</thead>
									<tfoot>
										<tr class="foot_sub"><th colspan="4">計</th><td class="base"><ins>0</ins> 円</td></tr>
										<tr class="foot_sub"><th colspan="4">消費税</th><td class="tax"><ins>0</ins> 円</td></tr>
										<tr class="foot_sub"><th colspan="4">カード決済システム利用料</th><td class="credit"><ins>0</ins> 円</td></tr>
										<tr class="foot_total"><th colspan="4">お見積り合計</th><td class="tot"><ins>0</ins> 円</td></tr>
										<tr class="foot_perone"><th colspan="4">1枚あたり</th><td class="per"><ins>0</ins> 円</td></tr>
									</tfoot>
									<tbody></tbody>
								</table>
							</div>

							<div class="inner1">
								<table id="conf_print">
									<caption>プリント情報</caption>
									<thead>
										<tr>
											<th>アイテム</th><th>プリント位置</th><th>デザインの色数</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								<table id="conf_option">
									<tbody>
										<tr><th>添付ファイル</th><td id="conf_attach"></td></tr>
										<tr><th>デザインの備考</th><td id="conf_note_design"></td></tr>
										<tr><th>デザインの色指定</th><td id="conf_note_printcolor"></td></tr>
									</tbody>
								</table>
							</div>

							<div class="inner1">
								<table id="conf_user">
									<caption>お客様情報</caption>
									<thead>
										<tr>
											<th>項目</th><th>入力内容</th>
										</tr>
									</thead>
									<tbody>
										<tr><th>お名前</th><td id="conf_customername"></td></tr>
										<tr><th>フリガナ</th><td id="conf_customerruby"></td></tr>
										<tr><th>メールアドレス</th><td id="conf_email"></td></tr>
										<tr><th>お電話番号</th><td id="conf_tel"></td></tr>
										<tr><th>ご住所</th><td>〒<ins id="conf_zipcode"></ins><br /><ins id="conf_addr0"></ins><ins id="conf_addr1"></ins><ind id="conf_addr2"></ind></td></tr>
										<tr><th>デザイン掲載</th><td id="conf_publish"></td></tr>
										<tr><th>ご希望納期</th><td id="conf_deliveryday"></td></tr>
										<tr><th>お届け時間</th><td id="conf_deliverytime"></td></tr>
										<tr><th>お支払方法</th><td id="conf_payment"></td></tr>
										<tr><th>ご要望・ご質問など</th><td id="conf_comment"></td></tr>
									</tbody>
								</table>
							</div>

							<fieldset class="sendorder_wrap">
								<legend class="highlights">★重要</legend>
								<div class="inner">
									<h3>注意事項</h3>
									<p>
										制作を開始するにあたり、お電話によるデザインの確認をさせていただいております。<br>
										弊社よりお送りする御見積りメールをご確認いただいた後、
										フリーダイヤル<ins class="highlights"><?php echo _TOLL_FREE;?></ins>までお電話ください。
										（平日10:00-18:00）
									</p>
									<img src="./img/order_6.png" width="100%" style="margin-top:10px; border:1px solid #efefef;">
								</div>
								<p><input type="checkbox" value="1" name="agree" id="agree"><label for="agree">確認しました</label></p>

								<div>
									<p class="pointer">チェック！</p>
									<div id="sendorder" class="disable_button" onclick="ga('send','event','step6','click','order',5300);">注文する</div>
								</div>
							</fieldset>

							<div class="arrow_line"><div class="arrow prev" data-back="4"><span>戻る</span></div></div>
						</form>

					</div>
				</div>
			</div>

		</div>
		
		<div id="floatingbox">
			<table>
				<caption>お見積り</caption>
				<tbody>
					<tr><th>商品枚数</th><td><span><?php echo number_format($data['amount']); ?></span>枚</td></tr>
					<tr class="total"><th>合計金額</th><td><span><?php echo number_format($total); ?></span>円</td></tr>
					<tr><th>1枚あたり</th><td><span><?php echo number_format($perone); ?></span>円</td></tr>
				</tbody>
			</table>
			<div class="btn_sub viewcart"><img alt="カート" src="./img/cart.png" />カートを見る</div>
		</div>

	</div>

    <p class="scroll_top"><a href="#header">お申し込みフォーム　ページトップへ</a></p>

	<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php"; ?>

	<iframe name="upload_iframe" style="display: none;"></iframe>


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
	<div id="msgbox" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">メッセージ</h4>
				</div>
				<div class="modal-body">
					<p></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary is-ok" data-dismiss="modal">OK</button>
					<button type="button" class="btn btn-default is-cancel" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<!-- m3 begin -->
	<div id="phonepage">
		<div id="fb-root"></div>
		<div id="container">
			<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/inc/footer.html"); ?>
			<div class="sb-slidebar sb-right">
			<?php include($_SERVER['DOCUMENT_ROOT']."/m3/common/sidemenu.html"); ?>
			</div>
		</div>
	</div>
	<!-- m3 end -->
</body>
</html>
