<?php
	$_FLG_ITEM_ID = isset($_REQUEST['item_id'])? $_REQUEST['item_id'] : 1;
	$_FLG_COLORCODE = isset($_REQUEST['color_code'])? $_REQUEST['color_code']: '';
	$_UPDATED = empty($_REQUEST['update'])? 0: $_REQUEST['update'];

	require_once dirname(__FILE__).'/../php_libs/t_orders.php';
	$order = new Orders();


//�桼������������
	$me = $_SESSION['me'];
	if(!empty($_SESSION['me'])) {
		$order->userAuto($me);
	}
/*
	if(isset($_REQUEST["login"])){
		$me = $conndb->getUser($_REQUEST);
	}
*/
	// ������
	$tax = $order->salestax();
	$tax /= 100;


	// ���ƥ��꡼��������
	//$itemattr = $conn->itemAttr($_FLG_ITEM_ID);
	list($itemattr, $categories) = $order->getCategoryData($_FLG_ITEM_ID);
	
	list($categorykey, $categoryname) = each($itemattr['category']);
	$categoryname = mb_convert_encoding($categoryname,'euc-jp','utf-8');
	list($itemcode, $itemname) = each($itemattr['name']);
	list($code, $colorname) = each($itemattr['code']);
	$itemname = mb_convert_encoding($itemname,'euc-jp','utf-8');
	$curcolor = mb_convert_encoding($colorname,'euc-jp','utf-8');

	// ���ʾܺ٤ȥ������̤�������ܤ��Ƥ����Ȥ��Υѥ�᡼��
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

	// �����ƥ������������
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

	// ���ƥ��꡼���쥯����
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
	<title>���������ߥե����� �� ���ꥸ�ʥ�T����Ĥ��ᤤ�������ϥޥ饤�ե�����</title>
	<meta name="Description" content="���������ߥե����फ�饫�󥿥�˥��ꥸ�ʥ�T����Ĥ�����ʸ�Ǥ��ޤ���Web��Ƕ�ۤ��ǧ���ʤ���ʤ��Τǰ¿��Ǥ����б����ᤤ����������ڡ���ǥ���饤���Ѥ������ꤪ�¤��ʤ뤫�⡩�ȥ졼�ʡ����ݥ���ġ����ꥸ�ʥ�T����Ĥκ������ץ��Ȥϡ�����Գ����Υ����ϥޥ饤�ե����Ȥˤ�Ǥ����������" />
	<meta name="keywords" content="��ʸ,����������,���ꥸ�ʥ�,T�����,����" />

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
	<meta property="og:title" content="������®�������ꥸ�ʥ�T����Ĥ������ž夲����" />
	<meta property="og:type" content="article" /> 
	<meta property="og:description" content="�ȳ�No. 1ûǼ���ǥ��ꥸ�ʥ�T����Ĥ�1�礫��������ޤ����̾�Ǥ�3���ǻž夲�ޤ���" />
	<meta property="og:url" content="http://www.takahama428.com/" />
	<meta property="og:site_name" content="���ꥸ�ʥ�T����Ĳ��å����ϥޥ饤�ե�����" />
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
				<li><a href="/">���ꥸ�ʥ�ԥ���Ĳ��ԣϣ�</a></li>
				<li>����������</li>
			</ul>

			<div class="heading1_wrapper">
				<h1>���������ߥե�����</h1>
				<p class="comment">
					FAX�ǤΤ��������ߤ򤴴�˾�����ϡ�<br><a href="/contact/faxorderform.pdf" target="_blank">FAX�ѥե�����</a>��ץ��Ȥ��������Ǥ��ޤ���<br>
					<a href="/design/designguide_campus.html" target="_blank" class="comment_des">�������Υǥ�����Ǻ�ꤿ����������Ϥ����顪</a>
				</p>
				<img src="./img/order.png" class="order_flow" style="margin-top:10px; border: 1px solid #efefef;" width="100%">
			</div>

			<div id="gall">
				<div id="step1" class="is-appear">
					<div class="heading"></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first">
							<p>Step1</p>
							<div>�ץ��Ȥ���<br>�����ƥ������</div>
						</div>
						<div class="crumbs">
							<p>Step2</p>
							<div>���顼��������<br>�������</div>
						</div>
						<div class="crumbs">
							<p>Step3</p>
							<div>�ץ��Ȱ���<br>�����</div>
						</div>
						<div class="crumbs">
							<p>Step4</p>
							<div><img alt="������" src="./img/cart.png" />������<br>�������</div>
						</div>
						<div class="crumbs">
							<p>Step5</p>
							<div>�����;���<br>������</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">���Ƥ��ǧ����<br>����������</div>
						</div>
					</div>

                    <div class="step_inner">
                        <h2><ins>Step1</ins>�ץ��Ȥ��륢���ƥ�����Ӥ�������</h2>

                        <div class="category_list">
                            <h3><ins>��.</ins>���ƥ��꡼�����</h3>
                            <?php echo $category_selector; ?>
                        </div>

                        <h3 id="h3_itemlist"><ins>��.</ins>�����ƥ�����򤷤Ƥ�������<span>��<?php echo count($res); ?> �����ƥ��</span></h3>
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
                                                        <p style="display:inline-block;">TAKAHAMA����<p/>
                                                        <span id="price_cost" style="white-space: nowrap;"><span>'.$v['minprice'].'</span>��&#12316;</span>
                                                    </p>

                                                </li>
                                            </ul>
                                            <p class="tor"><a href="../items/'.$folder.'/item.html?id='.$code.'">�����ƥ�ξܺ٤�</a></p>
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
                                                  	<p style="display:inline-block;">TAKAHAMA����<p/>
                                                    <span id="price_cost" style="white-space: nowrap;"><span>'.$v['minprice'].'</span>��&#12316;</span>
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
					<div class="heading clearfix"><p class="arrow prev" data-back="0"><span>���</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>�ץ��Ȥ���<br>�����ƥ������</div>
						</div>
						<div class="crumbs pass">
							<p>Step2</p>
							<div>���顼��������<br>�������</div>
						</div>
						<div class="crumbs">
							<p>Step3</p>
							<div>�ץ��Ȱ���<br>�����</div>
						</div>
						<div class="crumbs">
							<p>Step4</p>
							<div><img alt="������" src="./img/cart.png" />������<br>�������</div>
						</div>
						<div class="crumbs">
							<p>Step5</p>
							<div>�����;���<br>������</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">���Ƥ��ǧ����<br>����������</div>
						</div>
					</div>
					<div class="step_inner">
						<h2><ins>Step2</ins>���顼�����������������ꤷ�Ƥ�������</h2>

						<p id="cur_item_name_wrap">�����ƥ�̾����<span id="cur_item_name" class="prop_1_1"><?php echo $itemname;?></span></p>

						<div class="pane">
							<h3><ins>1.</ins>�����ƥ५�顼�λ���</h3>
							<div class="thumb_wrap clearfix">
								<div class="item_thumb">
				                	<p class="thumb_h"><span>Color</span>��<span class="num_of_color"><?php echo $color_count; ?></span>��<span class="notes_color"><?php echo $curcolor; ?></span></p>
				                    <ul class="color_thumb"><?php echo $thumbs; ?></ul>
								</div>
								<div class="item_image"><?php echo $itemimage; ?></div>
							</div>

							<div class="sizeprice">
								<h3>
									<ins>2.</ins>������������λ��ꡡ����
								</h3>
								<table class="size_table">
									<caption></caption>
									<tbody><tr><td></td></tr></tbody>
								</table>
								<div class="btmline">����<span class="cur_amount"><?php echo $sum; ?></span>��</div>
							</div>
						</div>

						<div class="btn_line">
							<span>��</span>���㤤�Υ����ƥ�����٤ޤ�
							<div id="add_item_color" class="btn_sub">�̤Υ��顼���ɲä���</div>
						</div>

						<div class="arrow_line">
							<div style="display:inline-block;">���<span id="tot_amount">0</span>��</div>
							<div class="arrow prev" data-back="0"><span>���</span></div>
							<div class="step_next goto_position" onclick="ga('send','event','step2','click','order',5300);">���ؿʤ�</div>
						</div>
					</div>
        		</div>

				<div id="step3">
					<div class="heading clearfix"><p class="arrow prev" data-back="1"><span>���</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>�ץ��Ȥ���<br>�����ƥ������</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step2</p>
							<div>���顼��������<br>�������</div>
						</div>
						<div class="crumbs pass">
							<p>Step3</p>
							<div>�ץ��Ȱ���<br>�����</div>
						</div>
						<div class="crumbs">
							<p>Step4</p>
							<div><img alt="������" src="./img/cart.png" />������<br>�������</div>
						</div>
						<div class="crumbs">
							<p>Step5</p>
							<div>�����;���<br>������</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">���Ƥ��ǧ����<br>����������</div>
						</div>
					</div>
					<div class="step_inner">
						<h2><ins>Step3</ins>�ץ��Ȥ�����֤ȥǥ�����ο�������ꤷ�Ƥ�������</h2>

						<div>
							<p><label><input type="checkbox" name="noprint" id="noprint" value="1"> �ץ��Ȥʤ��ǹ�������</label></p>
							<p class="note"><span>��</span>�ץ��Ȥʤ��ξ��1�������ˤʤ�ޤ���</p>
						</div>

						<div id="pos_wrap"></div>
						
						<div>
							<h3 class="heading_mark">�ɽ��򤴴�˾�����Ϥ�������������</h3>
							<p class="note">�㡡�����ǡ��ɽ�</p>
							<textarea id="note_printmethod"  name="note_printmethod"></textarea>
						</div>
                        <div class="bdrtxt"><p><span class="demoSpan1"></span>�ɽ��Τ����Ѷ�ۤϴޤޤ�ƤϤ���ޤ������Ӥ����Ѥꤷ�Ƥ�Ϣ������ĺ���ޤ���</p></div>
						<div class="arrow_line"><div class="arrow prev" data-back="1"><span>���</span></div><div class="step_next goto_cart" onclick="ga('send','event','step3','click','order',5300);">�����Ȥ������</div></div>
                    </div>
				</div>

				<div id="step4">
					<div class="heading clearfix"><p class="arrow prev" data-back="0"><span>�̤ξ��ʤ򸫤�</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>�ץ��Ȥ���<br>�����ƥ������</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step2</p>
							<div>���顼��������<br>�������</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step3</p>
							<div>�ץ��Ȱ���<br>�����</div>
						</div>
						<div class="crumbs pass">
							<p>Step4</p>
							<div><img alt="������" src="./img/cart.png" />������<br>�������</div>
						</div>
						<div class="crumbs">
							<p>Step5</p>
							<div>�����;���<br>������</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">���Ƥ��ǧ����<br>����������</div>
						</div>
					</div>
					<div class="step_inner">
						<h2><ins>Step4</ins>������</h2>

						<div id="estimation_wrap">
							<table>
								<caption>�����Ѥ�</caption>
								<thead>
									<tr><th>����̾ / ���顼</th><th>������</th><th>ñ��</th><th>���</th><th>���</th></tr>
								</thead>
								<tfoot>
									<tr><td colspan="3">�������</td><td class="ac"><ins class="totamount">0</ins> ��</td><td class="itemsum">0</td></tr>
									<tr><td colspan="1">�ץ�����</td><td class="print_size ac"></td><td class="print_pos ac"></td><td class="ink_count ac"></td><td class="printfee">0</td></tr>
									<tr><td colspan="4">����</td><td class="carriage">0</td></tr>
									<tr><td colspan="4">��������</td><td class="codfee">0</td></tr>
									<tr><td colspan="4">����ӥ˼����</td><td class="conbifee">0</td></tr>
									<tr><td colspan="4">�޵���</td><td class="package">0</td></tr>
									<tr><td colspan="1">���</td><td colspan="3" class="discountname"></td><td class="discountfee">0</td></tr>
									<tr><td colspan="1">�õ�����</td><td colspan="3" class="expressinfo"></td><td class="expressfee">0</td></tr>
									<tr class="foot_sub"><td colspan="4">��</td><td class="base">0</td></tr>
									<tr class="foot_sub"><td colspan="4">������</td><td class="tax">0</td></tr>
									<tr class="foot_sub"><td colspan="4">�����ɷ�ѥ����ƥ�������</td><td class="credit">0</td></tr>
									<tr class="foot_total"><td colspan="4">�����Ѥ���</td><td class="total">0</td></tr>
									<tr class="foot_perone"><td colspan="4">1�礢����</td><td class="perone">0</td></tr>
								</tfoot>
								<tbody>
									<tr><td colspan="7"></td><td class="last"></td></tr>
								</tbody>
							</table>
							<p class="note"><span>��</span>�����Ѥ�ϳ����Ǥ����ǥ���������Ƥˤ�ä��ѹ��ˤʤ��礬�������ޤ���</p>
						</div>

						<div class="inner option_wrap">
							<table id="option_table">
								<caption class="highlights">���Ŭ��</caption>
								<p class="note"><span>��</span>���ʤΤߤ���ʸ�ξ��ϳ����Ŭ�Ѥ���ޤ���</p>
								<tbody>
									<tr>
										<th>��������Ǥ���</th>
										<td>
											<label><input type="radio" name="student" value="0" <?php if(empty($regist['options']['student'])) echo 'checked="checked"'; ?> />������</label>
											<label><input type="radio" name="student" value="3" <?php if($regist['options']['student']==3) echo 'checked="checked"'; ?> />�Ϥ�<ins>3%OFF</ins></label>
											<label><input type="radio" name="student" value="5" <?php if($regist['options']['student']==5) echo 'checked="checked"'; ?> />2���饹<ins>5%OFF</ins></label>
											<label><input type="radio" name="student" value="7" <?php if($regist['options']['student']==7) echo 'checked="checked"'; ?> />3���饹<ins>7%OFF</ins></label>
										</td>
									</tr>
									<tr>
										<th>��ӥ塼��Ǻܤ��ޤ���</th>
										<td>
											<label><input type="radio" name="blog" value="0" <?php if(empty($regist['options']['blog'])) echo 'checked="checked"'; ?> />������</label>
											<label><input type="radio" name="blog" value="3" <?php if($regist['options']['blog']==3) echo 'checked="checked"'; ?> />�Ϥ�<ins>3%OFF</ins></label>
										</td>
									</tr>
									<tr>
										<th>Illustrator�����Ƥ��ޤ���</th>
										<td>
											<label><input type="radio" name="illust" value="0" <?php if(empty($regist['options']['illust'])) echo 'checked="checked"'; ?> />������</label>
											<label><input type="radio" name="illust" value="1" <?php if($regist['options']['illust']==1) echo 'checked="checked"'; ?> />�Ϥ�<ins>1,000��OFF</ins></label>
										</td>
									</tr>
									<tr>
										<th>���ҤΤ����ͤ���Τ��Ҳ�Ǥ���</th>
										<td>
											<label><input type="radio" name="intro" value="0" <?php if(empty($regist['options']['intro'])) echo 'checked="checked"'; ?> />������</label>
											<label><input type="radio" name="intro" value="3" <?php if($regist['options']['intro']==3) echo 'checked="checked"'; ?> />�Ϥ�<ins>3%OFF</ins></label>
										</td>
									</tr>
									<tr class="separate">
										<th>�޵ͤᡡ<span class="anchor" id="pop_pack">�޵ͤ�Ȥ�</span></th>
										<td>
											<label><input type="radio" name="pack" value="0" <?php if(empty($regist['options']['pack'])) echo 'checked="checked"'; ?> />��˾���ʤ�</label>
											<label><input type="radio" name="pack" value="2" <?php if($regist['options']['pack']==2) echo 'checked="checked"'; ?> />�ޤΤ�Ʊ����10��/1���</label>
											<br>
											<label><input type="radio" name="pack" value="1" <?php if($regist['options']['pack']==1) echo 'checked="checked"'; ?> />��˾�����50��/1���</label>

										</td>
									</tr>
									<tr>
										<th>����ʧ��ˡ��<span class="anchor" id="pop_payment">�����</span></th>
										<td>
											<label><input type="radio" name="payment" value="0" <?php if(empty($regist['options']['payment'])) echo 'checked="checked"'; ?> />��Կ���</label>
										<!--	<label><input type="radio" name="payment" value="2" <?php if($regist['options']['payment']==2) echo 'checked="checked"'; ?> />����ʹ���Ǽ����</label>  -->
											<label><input type="radio" name="payment" value="1" <?php if($regist['options']['payment']==1) echo 'checked="checked"'; ?> />�������ʼ����800�ߡ�</label>
											<br>
											<label><input type="radio" name="payment" value="3" <?php if($regist['options']['payment']==3) echo 'checked="checked"'; ?> />�����ɷ�ѡʥ����ƥ�������5���</label>
<!--											<label><input type="radio" name="payment" value="4" <?php if($regist['options']['payment']==4) echo 'checked="checked"'; ?> />����ӥ˷�ѡʼ����800�ߡ�</label>-->
										</td>
									</tr>
								</tbody>
							</table>

							<div class="line">
								<label class="title">����˾Ǽ��</label><input class="datepicker" id="deliveryday" type="text" size="14" name="deliveryday" value="<?php echo $regist['options']['deliveryday']; ?>" <?php if($regist['options']['nodeliday']==1) echo 'disabled'; ?> />
								<label><input type="checkbox" name="nodeliday" id="nodeliday" value="1" <?php if($regist['options']['nodeliday']==1) echo 'checked="checked"'; ?> > Ǽ���λ���ʤ�</label>
								<p id="express_notice"><span class="highlights">��<ins></ins></span><span class="anchor" id="pop_express">�õ�����ˤĤ���</span></p>
								<p class="note"><span>��</span>�޵ͤ�10��ʾ�����������˥ץ饹1�����������ޤ���</p>
								<p>
									<label class="title">���ϻ����Ӥλ���</label>
				 					<select name="deliverytime" id="deliverytime">
				 					<?php
										$option = '<option value="0">---</option>
				 						<option value="1">������</option>
				 						<option value="3">14:00-16:00</option>
				 						<option value="4">16:00-18:00</option>
				 						<option value="5">18:00-20:00</option>
				 						<option value="6">19:00-21:00</option>';
										$option = str_replace('value="'.$regist['options']['deliverytime'].'"', 'value="'.$regist['options']['deliverytime'].'" selected="selected"', $option);
										echo $option;
									?>
				 					</select>
			 					</p>
			 					<p class="note"><span>��</span>�����֤򤴻��ꤷ��ĺ���Ƥ�ŷ�������̻����ϰ�ˤ�ꤴ��˾��ź���ʤ���礬�������ޤ��Τǡ�ͽ�ᤴλ���ꤤ�ޤ���</p>
							</div>
						</div>

						<div class="inner">
							<h3 class="heading_mark">�ǥ�����β����ե�����򤪻��������Ϥ����餫��ź�դ��Ƥ�������</h3>
							<form enctype="multipart/form-data" method="post" target="upload_iframe" action="/php_libs/t_orders.php" name="uploaderform" id="uploaderform">
								<input type="hidden" value="update" name="act" />
								<input type="hidden" value="attach" name="mode" />
								<input type="hidden" value="<?php echo $regist['attach'][0]['img']['name']; ?>" name="attachname[]" />
								<p><input type="file" onChange="this.form.submit()" name="attach[]" size="19" title="�ǥ�����ե��������ꤷ�Ƥ�������" /><span class="del_attach"><img src="/common/img/delete.png" alt="���">���</span></p>
								<p><span class="add_attach btn_sub">�̤�ź�եե�������ɲ�</span></p>
								<h4>�ǥ�����ˤĤ��ƤΤ���˾�ʤ�</h4>
								<textarea id="note_design" name="note_design"><?php echo $user['note_design']; ?></textarea>
							</form>

							<div class="chapter">
								<h4>�������Υǥ�����ʥ�ե����å��ˤǥץ��Ȥ򤴴�˾�ξ��</h4>
								<p>FAX�ǤΤ��������ߤ��Ǥ��ޤ���<a href="/contact/faxorderform.pdf" target="_blank">FAX�ѥե�����</a>��ץ��Ȥ����������Ƥ���������</p>
								<p>FAX: <?php echo _OFFICE_FAX;?></p>
							</div>
						</div>

						<div class="inner">
							<h3 class="heading_mark">�ץ��Ȥ���ǥ�����ο�������ޤ�����Ϥ�������������</h3>
							<p class="note">�㡡������åɡ������̢��ۥ磻��</p>
							<textarea id="note_printcolor" name="note_printcolor"><?php echo $user['note_printcolor']; ?></textarea>
						</div>

						<div class="arrow_line"><div class="arrow prev" data-back="0"><span>�̤ξ��ʤ򸫤�</span></div><div class="step_next goto_user" onclick="ga('send','event','step4','click','order',5300);">���ؿʤ�</div></div>
                    </div>
				</div>

				<div id="step5">
					<div class="heading clearfix"><p class="arrow prev" data-back="3"><span>���</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>�ץ��Ȥ���<br>�����ƥ������</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step2</p>
							<div>���顼��������<br>�������</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step3</p>
							<div>�ץ��Ȱ���<br>�����</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step4</p>
							<div><img alt="������" src="./img/cart.png" />������<br>�������</div>
						</div>
						<div class="crumbs pass">
							<p>Step5</p>
							<div>�����;���<br>������</div>
						</div>
						<div class="crumbs step_fin">
							<p>Step6</p>
							<div class="fin">���Ƥ��ǧ����<br>����������</div>
						</div>
					</div>
					<div class="step_inner">
						<h2><ins>Step5</ins>�����;�������Ϥ��Ƥ�������</h2>
						<div id="userinfo" class="clearfix">
							<p class="comment">��<span>��</span>�װ���ɬ�����ϤǤ���</p>
							<div id="user_wrap" class="clearfix inner">
								<div class="g_ft" style="width=98%;border-bottom: 1px solid #d8d8d8;margin-top:20px;padding-bottom:20px;">
									<div class="ft">
										<ul>
											<h1 class="login_nodisplay">�ޥ��ڡ����򤪻��������Ϥ�����</h1>
											<li id= "login_email" class="login_nodisplay"><h2>�᡼�륢�ɥ쥹:<span class="fontred">��</span></h2><input type="text" id="login_input_email" name="login_input_email" value="<?php echo $user['email']; ?>" /></li>
											<li class="login_nodisplay"><h2>�ѥ���ɡ���:<span class="fontred">��</span></h2><input type="password" value="<?php echo $user['password']; ?>" id="login_input_pass"  name="login_input_pass" /></li>
										</ul>
									</div>
									<div class="ft">
										<ul>
											<li class="login_nodisplay"><input type="button" id="member_login" value="������" /></li>
											<div class="login_nodisplay"><span class="fontred">��</span><a href="/user/resend_pass.php">�ѥ���ɤ�˺�줿���Ϥ������</a></div>
										</ul>
									</div>
								</div>
									<div class="ft">
										<ul>
											<h1 class="login_nodisplay" style="margin-top:35px;">���Ƥ����Ϥ�����</h1>
											<li id= "login_email"><h2>�᡼�륢�ɥ쥹:<span class="fontred">��</span></h2><input type="text" id="email" name="email" value="<?php echo $user['email']; ?>" /></li>
											<li class="login_nodisplay"><h2>���� �ѥ����:<span class="fontred">��</span></h2><input type="password" value="<?php echo $user['password']; ?>" id="pass"  name="pass" /></li>
											<li class="login_nodisplay"><span class="fontred">��</span>���������ϡ��������ѥ���ɤ����Ϥ��ޤ���Ⱦ�ѱѿ���4ʸ���ʾ�16ʸ�����⡣</li>
										</ul>
									</div>
								<div class="fl">
									<ul>
										<li><h2>��̾��:<span class="fontred">��</span></h2>
											<input type="text" id="customername" name="customername" value="<?php echo $user['customername']; ?>">����
										</li>
										<li>
											<h2>�եꥬ��:</h2><input type="text" id="customerruby" name="customerruby" value="<?php echo $user['customerruby']; ?>">����
										</li>
										<li><h2>�������ֹ�:<span class="fontred">��</span></h2><input type="text" id="tel" name="tel" class="forPhone"  value="<?php echo $user['tel']; ?>" /></li>
<!--
										<li><h2>���Ҥ����ѤˤĤ���:<span class="fontred">��</span></h2>
											<label class="lbl"><input type="radio" name="repeater" value="1" <?php if($user['repeater']==1) echo 'checked="checked"'; ?> /> ���ƤΤ�����</label>
											<label class="lbl"><input type="radio" name="repeater" value="2" <?php if($user['repeater']==2) echo 'checked="checked"'; ?> /> �����ˤ���ʸ�������Ȥ�����</label>
										</li>
-->
									</ul>
								</div>
								<div class="fr">
									<ul>
										<li><h2 class="login_nodisplay">������:<span class="fontred">��</span></h2></li>
										<li><h2 class="login_display">���Ϥ���:<span class="fontred">��</span></h2></li>
					 					<li><p><select name="delivery_customer" id="delivery_customer"></select></p></li>
					 					<li>
											<p>��<input type="text" name="zipcode" class="forZip" id="zipcode1" value="<?php echo $user['zipcode']; ?>" onChange="AjaxZip3.zip2addr(this,'','addr0','addr1');" /></p>
											<p><input type="text" name="addr0" id="addr0" value="<?php echo $user['addr0']; ?>" placeholder="��ƻ�ܸ�" maxlength="4" /></p>
											<p><input type="text" name="addr1" id="addr1" value="<?php echo $user['addr1']; ?>" placeholder="ʸ����������28ʸ����Ⱦ��56ʸ���Ǥ�" maxlength="56" class="restrict" /></p>
											<p><input type="text" name="addr2" id="addr2" value="<?php echo $user['addr2']; ?>" placeholder="ʸ����������16ʸ����Ⱦ��32ʸ���Ǥ�" maxlength="32" class="restrict" /></p>
										</li>
										<li><h2>����˾��������ʤ�:</h2><textarea cols="30" rows="5" name="comment"><?php echo $user['comment']; ?></textarea></li>
									</ul>
								</div>
							</div>

							<table class="inner">
								<tbody>
									<tr>
										<th>
											�ǥ�����ηǺܤˤĤ���:
										</th>
										<td>
											<p class="txt">���ꥸ�ʥ�ץ��Ȥ������������λ��ͤˡ����ͤΥǥ������WEB���<br>�Ǻܤ�����ĺ���Ƥ���ޤ�������������򤪴ꤤ�פ��ޤ���</p>
											<p class="line">
												<label><input type="radio" name="publish" value="0" <?php if(empty($regist['options']['publish'])) echo 'checked="checked"'; ?> /> �Ǻܲ�</label>
												<label><input type="radio" name="publish" value="1" <?php if($regist['options']['publish']==1) echo 'checked="checked"'; ?> /> �Ǻ��Բ�</label>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="arrow_line"><div class="arrow prev" data-back="3"><span>���</span></div><div class="step_next goto_confirm" onclick="ga('send','event','step5','click','order',5300);">��ǧ���̤�</div></div>
					</div>
				</div>

				<div id="step6">
					<div class="heading clearfix"><p class="arrow prev" data-back="4"><span>���</span></p></div>
					<div class="crumbs_wrap">
						<div class="crumbs pass step_first passed">
							<p>Step1</p>
							<div>�ץ��Ȥ���<br>�����ƥ������</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step2</p>
							<div>���顼��������<br>�������</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step3</p>
							<div>�ץ��Ȱ���<br>�����</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step4</p>
							<div><img alt="������" src="./img/cart.png" />������<br>�������</div>
						</div>
						<div class="crumbs pass passed">
							<p>Step5</p>
							<div>�����;���<br>������</div>
						</div>
						<div class="crumbs pass step_fin">
							<p>Step6</p>
							<div class="fin">���Ƥ��ǧ����<br>����������</div>
						</div>
					</div>

                    <div class="step_inner">
						<h2><ins>Step6</ins>�������������Ƥ򤴳�ǧ��������</h2>

						<form id="orderform" name="orderform" method="post" action="./ordercomplete.php" onSubmit="return false;">
							<?php
								$ticket = htmlspecialchars(md5(uniqid().mt_rand()), ENT_QUOTES);
								$_SESSION['ticket'] = $ticket;
							?>
							<input type="hidden" name="ticket" value="<?php echo $ticket; ?>">

							<div class="inner1">
								<table id="conf_item">
									<caption>�����ƥ�</caption>
									<thead>
										<tr>
											<th>����̾ / ���顼</th><th>������</th><th>ñ��</th><th>���</th><th>���</th>
										</tr>
									</thead>
									<tfoot>
										<tr class="foot_sub"><th colspan="4">��</th><td class="base"><ins>0</ins> ��</td></tr>
										<tr class="foot_sub"><th colspan="4">������</th><td class="tax"><ins>0</ins> ��</td></tr>
										<tr class="foot_sub"><th colspan="4">�����ɷ�ѥ����ƥ�������</th><td class="credit"><ins>0</ins> ��</td></tr>
										<tr class="foot_total"><th colspan="4">�����Ѥ���</th><td class="tot"><ins>0</ins> ��</td></tr>
										<tr class="foot_perone"><th colspan="4">1�礢����</th><td class="per"><ins>0</ins> ��</td></tr>
									</tfoot>
									<tbody></tbody>
								</table>
							</div>

							<div class="inner1">
								<table id="conf_print">
									<caption>�ץ��Ⱦ���</caption>
									<thead>
										<tr>
											<th>�����ƥ�</th><th>�ץ��Ȱ���</th><th>�ǥ�����ο���</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								<table id="conf_option">
									<tbody>
										<tr><th>ź�եե�����</th><td id="conf_attach"></td></tr>
										<tr><th>�ǥ����������</th><td id="conf_note_design"></td></tr>
										<tr><th>�ǥ�����ο�����</th><td id="conf_note_printcolor"></td></tr>
									</tbody>
								</table>
							</div>

							<div class="inner1">
								<table id="conf_user">
									<caption>�����;���</caption>
									<thead>
										<tr>
											<th>����</th><th>��������</th>
										</tr>
									</thead>
									<tbody>
										<tr><th>��̾��</th><td id="conf_customername"></td></tr>
										<tr><th>�եꥬ��</th><td id="conf_customerruby"></td></tr>
										<tr><th>�᡼�륢�ɥ쥹</th><td id="conf_email"></td></tr>
										<tr><th>�������ֹ�</th><td id="conf_tel"></td></tr>
										<tr><th>������</th><td>��<ins id="conf_zipcode"></ins><br /><ins id="conf_addr0"></ins><ins id="conf_addr1"></ins><ind id="conf_addr2"></ind></td></tr>
										<tr><th>�ǥ�����Ǻ�</th><td id="conf_publish"></td></tr>
										<tr><th>����˾Ǽ��</th><td id="conf_deliveryday"></td></tr>
										<tr><th>���Ϥ�����</th><td id="conf_deliverytime"></td></tr>
										<tr><th>����ʧ��ˡ</th><td id="conf_payment"></td></tr>
										<tr><th>����˾��������ʤ�</th><td id="conf_comment"></td></tr>
									</tbody>
								</table>
							</div>

							<fieldset class="sendorder_wrap">
								<legend class="highlights">������</legend>
								<div class="inner">
									<h3>��ջ���</h3>
									<p>
										����򳫻Ϥ���ˤ����ꡢ�����äˤ��ǥ�����γ�ǧ�򤵤��Ƥ��������Ƥ���ޤ���<br>
										���Ҥ�ꤪ���ꤹ��渫�Ѥ�᡼��򤴳�ǧ�����������塢
										�ե꡼�������<ins class="highlights"><?php echo _TOLL_FREE;?></ins>�ޤǤ����ä���������
										��ʿ��10:00-18:00��
									</p>
									<img src="./img/order_6.png" width="100%" style="margin-top:10px; border:1px solid #efefef;">
								</div>
								<p><input type="checkbox" value="1" name="agree" id="agree"><label for="agree">��ǧ���ޤ���</label></p>

								<div>
									<p class="pointer">�����å���</p>
									<div id="sendorder" class="disable_button" onclick="ga('send','event','step6','click','order',5300);">��ʸ����</div>
								</div>
							</fieldset>

							<div class="arrow_line"><div class="arrow prev" data-back="4"><span>���</span></div></div>
						</form>

					</div>
				</div>
			</div>

		</div>
		
		<div id="floatingbox">
			<table>
				<caption>�����Ѥ�</caption>
				<tbody>
					<tr><th>�������</th><td><span><?php echo number_format($data['amount']); ?></span>��</td></tr>
					<tr class="total"><th>��׶��</th><td><span><?php echo number_format($total); ?></span>��</td></tr>
					<tr><th>1�礢����</th><td><span><?php echo number_format($perone); ?></span>��</td></tr>
				</tbody>
			</table>
			<div class="btn_sub viewcart"><img alt="������" src="./img/cart.png" />�����Ȥ򸫤�</div>
		</div>

	</div>

    <p class="scroll_top"><a href="#header">���������ߥե����ࡡ�ڡ����ȥåפ�</a></p>

	<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php"; ?>

	<iframe name="upload_iframe" style="display: none;"></iframe>


	<!--Yahoo!�����ޥ͡����㡼Ƴ�� 2014.04 -->
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
					<h4 class="modal-title">��å�����</h4>
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
