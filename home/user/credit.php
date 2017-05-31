<?php
require_once dirname(__FILE__).'/php_libs/funcs.php';

// ログイン状態のチェック
$me = checkLogin();
if(!$me){
    jump('login.php');
}

// 未決済注文の指定
if(isset($_GET['oi'])){
    $orders_id = $_GET['oi'];
}

// TLA顧客IDを取得
$conndb = new Conndb(_API_U);
$u = $conndb->getUserList($me['id']);
$customerid = $u[0]['id'];

$conndb = new Conndb(_API);
$d = $conndb->getOroderHistory($customerid);

$idx = -1;
$ls = '';
for($i=0; $i<count($d); $i++){
    if($d[$i]['deposit']==2) continue;	// 未入金とカード決済指定の他は除外

    $ls .= '<li>';
    if( (empty($orders_id) && $idx==-1) || $d[$i]['orderid']==$orders_id){
        $ls .= $d[$i]['schedule2'].' ご注文確定　No.'.$d[$i]['orderid'];
        $idx = $i;
        if(empty($orders_id))$orders_id = $d[$i]['orderid'];
    }else{
        $ls .= '<a href="'.$_SERVER['SCRIPT_NAME'].'?oi='.$d[$i]['orderid'].'">'.$d[$i]['schedule2'].' ご注文確定　No.'.$d[$i]['orderid'].'</a>';
    }
    $ls .= '</li>';
}

if(!empty($ls)){
    $msg = "お支払いが未確定のご注文";
    $ls = '<ol class="orders_list">'.$ls.'</ol>';

    foreach($d[$idx]['itemlist'] as $itemname=>$info){
        foreach($info as $color=>$val){
            if($val[0]['itemcode']!=''){
                $thumbName = $val[0]['itemcode'].'_'.$val[0]['colorcode'];
                $folder = $val[0]['categorykey'];
                $thumb = '<img alt="" src="'._IMG_PSS.'items/'.$folder.'/'.$val[0]['itemcode'].'/'.$thumbName.'_s.jpg" height="26" />';
            }else{
                $thumb = '';
            }
            $items .= '<tr>';
            $items .= '<td>'.mb_convert_encoding($itemname, 'euc-jp', 'utf-8').'<br/ >';
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
    $items .= '<td class="tor">'.number_format($d[$idx]['order_amount']).'<ins class="small">枚</ins></td>';
    $items .= '<td class="tor">'.number_format($subtotal).'<ins class="small">円</ins></td></tr>';

    $discount_fee = $d[$idx]['discountfee'] + $d[$idx]['reductionfee'];
    $print_fee = $d[$idx]['printfee'] + $d[$idx]['exchinkfee'];
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
        if(empty($d[$idx][$charge_key])) continue;
        $items .= '<tr><td colspan="4">'.$charge_name.'</td><td class="tor">'.number_format($d[$idx][$charge_key]).'<ins class="small">円</ins></td></tr>';
    }

    $total = $d[$idx]['estimated'];
    $perone = ceil($total/$d[$idx]['order_amount']);
    $tax = $d[$idx]['salestax'];
    $credit = $d[$idx]['creditfee'];
    $base = ($d[$idx]['basefee']!=$total)? $d[$idx]['basefee']: 0;

    // POST情報
    $redirect_ok = 'http://www.takahama428.com/user/receive_ok.php';
    $redirect_ng = 'http://www.takahama428.com/user/receive_ng.php';
    $redirect_can = 'http://www.takahama428.com'.$_SERVER['SCRIPT_NAME'].'?oi='.$orders_id;
    $redirect = 'http://www.takahama428.com/user/receive.php';
}else{
    $msg = "お支払いは全て済んでおります。";
    $display = "style='display:none;'";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="euc-jp" />
        <!-- m3 begin -->
        <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
        <!-- m3 end -->
        <title>お支払い状況 - TLAメンバーズ | タカハマライフアート</title>
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
        <link rel="stylesheet" type="text/css" media="screen" href="./css/featherlight.min.css" />





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
                    <div class="pagetitle"><h1>お支払い状況</h1></div>
                    <div class="section">
                        <h2><?php echo $msg; ?></h2>
                        <?php echo $ls; ?>
                    </div>
                    <div class="cretxt">弊社営業時間外は、クレジットカードお取引の結果が出るまでお時間がかかる場合がございます。
                        お取引完了と出るまでしばらくお待ちくださいませ。
                        なお、再度決済を行いますと2重決済になる可能性がございますので、ご注意ください。
                    </div>

                    <div class="section" <?php echo $display;?>>
                        <h2>ご注文情報</h2>

                        <p class="note tor">注文No.<?php echo $d[$idx]['orderid'];?></p>
                        <div class="inner1">
                            <table class="form_table">
                                <thead>
                                    <tr>
                                        <th>商品名 / カラー</th><th>サイズ</th><th>単価</th><th>枚数</th><th>金額</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <?php
                                    if($base>0){
                                        echo '<tr><th colspan="2"></th><td colspan="2">計</td><td class="tor"><ins>'.number_format($subtotal).'</ins> 円</td></tr>';
                                    }
                                    if($tax>0){
                                        echo '<tr><th colspan="2"></th><td colspan="2">消費税</td><td class="tor"><ins>'.number_format($tax).'</ins> 円</td></tr>';
                                    }
                                    if($credit>0){
                                        echo '<tr><th colspan="2"></th><td colspan="2">カード手数料</td><td class="tor"><ins>'.number_format($credit).'</ins> 円</td></tr>';
                                    }
                                    ?>
                                    <tr class="foot_total"><th colspan="2"></th><td colspan="2">ご注文金額</td><td class="tot"><ins><?php echo number_format($total);?></ins> 円</td></tr>
                                    <tr class="foot_perone"><th colspan="2"></th><td colspan="2">1枚あたり</td><td class="tor"><ins><?php echo number_format($perone);?></ins> 円</td></tr>
                                </tfoot>
                                <tbody><?php echo $items;?></tbody>
                            </table>
                        </div>

                        <?php
                        if($d[$idx]['payment']=='credit' || $d[$idx]['payment']=='conbi'){
                            $now = date('YmdHis');
                            $tax = "0";
                            $shopId = "9100596916967";
                            $shoppass = "h3xy5z6c";
                            $crediturl= 'https://p01.mul-pay.jp/link/'.$shopId.'/Multi/Entry';
                            $form = '<form action="'.$crediturl.'" method="post">';
                            $form .= '<input type="hidden" value="'.$shopId.'" name="ShopID">';
                            $form .= '<input type="hidden" value="'.$orders_id.'" name="OrderID">';
                            $form .= '<input type="hidden" value="'.$total.'" name="Amount">';
                            $form .= '<input type="hidden" value="'.$now.'" name="DateTime">';
                            $form .= '<input type="hidden" value="'.$tax.'" name="Tax">';
                            //「ショップ ID + “|” +  オーダー ID + “|” +  利用金額   + “|” +  税送料   + “|” +  ショップパスワード   + “|” +  日時情報」
                            $password = $shopId . "|" . $orders_id . "|" . $total . "|" .$tax. "|" .$shoppass. "|".$now;
                            $password = md5($password);
                            $form .= '<input type="hidden" value="' .$password. '" name="ShopPassString">';
                            $form .= '<input type="hidden" value="'.$redirect.'" name="RetURL">';
                            $form .= '<input type="hidden" value="'.$redirect_can.'" name="CancelURL">';
                            //クレジットカード
                            if($d[$idx]['payment']=='credit') {
                                $form .= '<input type="hidden" value="1" name="UseCredit">';
                                $form .= '<input type="hidden" value="CAPTURE" name="JobCd">';
                                $form .= '<input type="submit" value="カード決済のお申込はこちらから">';
                            } else {
                                $form .= '<input type="hidden" value="1" name="UseCvs">';
                                $form .= '<input type="hidden" value="有限会社タカハマライフアート" name="ReceiptsDisp11">';
                                $form .= '<input type="hidden" value="03-5670-0787" name="ReceiptsDisp12">';
                                $form .= '<input type="hidden" value="09:00-18:00" name="ReceiptsDisp13">';
                                $form .= '<input type="submit" value="コンビニ決済のお申込はこちらから">';
                            }

                            /*
				$form = '<form action="https://ec.nicos.co.jp/sitop_pccmn/EC.Entry.Mall.Handler.ashx" method="post">';
				$form .= '<input type="hidden" value="60051580" name="in_kamei_id">';
				$form .= '<input type="hidden" value="00055040" name="in_n">';
				$form .= '<input type="hidden" value="'.$redirect_ok.'" name="in_redirecturl_ok">';
				$form .= '<input type="hidden" value="'.$redirect_ng.'" name="in_redirecturl_ng">';
				$form .= '<input type="hidden" value="'.$redirect_can.'" name="in_redirecturl_can">';
				$form .= '<input type="hidden" value="0" name="in_mallact_kbn">';
				$form .= '<input type="hidden" value="101" name="in_shori_kbn">';
				$form .= '<input type="hidden" value="111" name="in_moushikomi_kbn01">';
				$form .= '<input type="hidden" value="'.$orders_id.'" name="in_chumon_no">';
				$form .= '<input type="hidden" value="'.$total.'" name="in_kingaku">';
*/
                            $form .= '</form>';
                            echo $form;
                        }
                        ?>
                    </div>

                    <ul>
                        <li class="btn-group"><a class="btn btn-default" href="#" data-featherlight="#fl1">お支払方法について</a></li>
                    </ul>

                    <div class="lightbox" id="fl1">

                        <div class="modal-content">
                            <div class="modal_window">
                                <h2>メッセージ</h2>
                                <h3 class="syousai">銀行振込</h3><hr>
                                <p>下記の口座にお振込ください。</p>
                                <p>ご希望の納品日より2日前までにお振込をお願い致します。（土日祝は入金確認ができないのでご注意ください）お振込手数料は、お客様のご負担とさせていただいております。</p>
                                <dl class="list">
                                    <dt>銀行名</dt>
                                    <dd>三菱東京ＵＦＪ銀行</dd>
                                    <dt>支店名</dt>
                                    <dd>新小岩支店　744</dd>
                                    <dt>口座種別</dt>
                                    <dd>普通</dd>
                                    <dt>口座番号</dt>
                                    <dd>3716333</dd>
                                    <dt>口座名義</dt>
                                    <dd>ユ）タカハマライフアート</dd>
                                </dl>
                                <hr><br><h3 class="syousai">代金引換</h3><hr>
                                <p>代金引換手数料は1件につき&yen;800（税抜）かかります。
                                    お支払い総額（商品代+送料＋代金引換手数料＋消費税）を配送業者にお支払いください。
                                    お客様のご都合でお支払い件数が複数になった場合、1件につき&yen;800（税抜）を追加させていただきます。</p>
                                <hr><br><h3 class="syousai">カード決済</h3><hr>
                                <p>各種クレジットカードがご利用いただけます。
                                    ご希望の納品日より2日前までにカード決済手続きをお願い致します。
                                    （土日祝は入金確認ができないのでご注意ください）カード決済システム利用料（5%）は、お客様のご負担とさせていただいております。
                                    弊社の「マイページ」＞「お支払い状況」＞「カード決済のお申し込はこちらから」にて決済が可能です。</p>
                                <center><p><img width="60%" alt="カード種類" src="/order/img/card.png"></p></center>
                                <!-- <hr><br><h3 class="syousai">コンビニ決済</h3><hr>
<p>指定のコンビニエンスストアでお支払いが可能です。
支払い番号は送らせていただくメールに記載しております。
支払い手数料（一律&yen;800）はお客様負担とさせていただいております。
弊社の「マイページ」＞「お支払い状況」＞「コンビニ決済のお申し込はこちらから」にて決済が可能です。</p>
<p><img width="100%" alt="カード種類" src="/order/img/konnbini.png"></p> -->


                            </div>
                        </div>


                    </div>
                </div>

                <p class="scroll_top"><a href="#header">お支払い状況　ページトップへ</a></p>

                <?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php"; ?> 

                <script src="./js/jquery-1.7.0.min.js"></script>
                <script src="./js/featherlight.min.js" type="text/javascript" charset="utf-8"></script>


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