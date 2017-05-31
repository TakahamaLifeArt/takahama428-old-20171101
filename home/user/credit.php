<?php
require_once dirname(__FILE__).'/php_libs/funcs.php';

// ��������֤Υ����å�
$me = checkLogin();
if(!$me){
    jump('login.php');
}

// ̤�����ʸ�λ���
if(isset($_GET['oi'])){
    $orders_id = $_GET['oi'];
}

// TLA�ܵ�ID�����
$conndb = new Conndb(_API_U);
$u = $conndb->getUserList($me['id']);
$customerid = $u[0]['id'];

$conndb = new Conndb(_API);
$d = $conndb->getOroderHistory($customerid);

$idx = -1;
$ls = '';
for($i=0; $i<count($d); $i++){
    if($d[$i]['deposit']==2) continue;	// ̤����ȥ����ɷ�ѻ����¾�Ͻ���

    $ls .= '<li>';
    if( (empty($orders_id) && $idx==-1) || $d[$i]['orderid']==$orders_id){
        $ls .= $d[$i]['schedule2'].' ����ʸ���ꡡNo.'.$d[$i]['orderid'];
        $idx = $i;
        if(empty($orders_id))$orders_id = $d[$i]['orderid'];
    }else{
        $ls .= '<a href="'.$_SERVER['SCRIPT_NAME'].'?oi='.$d[$i]['orderid'].'">'.$d[$i]['schedule2'].' ����ʸ���ꡡNo.'.$d[$i]['orderid'].'</a>';
    }
    $ls .= '</li>';
}

if(!empty($ls)){
    $msg = "����ʧ����̤����Τ���ʸ";
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
            $items .= $thumb.'<span>���顼�� '.mb_convert_encoding($color, 'euc-jp', 'utf-8').'</span></td>';
            $size = '';
            $cost = '';
            $vol = '';
            $sub = '';
            for($t=0; $t<count($val); $t++){
                $price = $val[$t]['cost'] * $val[$t]['volume'];
                $size .= '<p>'.$val[$t]['size'].'</p>';
                $cost .= '<p>'.number_format($val[$t]['cost']).'<ins class="small">��</ins></p>';
                $vol .= '<p>'.number_format($val[$t]['volume']).'<ins class="small">��</ins></p>';
                $sub .= '<p>'.number_format($price).'<ins class="small">��</ins></p>';
                $subtotal += $price;
            }

            $items .= '<td class="toc">'.$size.'</td>';
            $items .= '<td class="tor">'.$cost.'</td>';
            $items .= '<td class="tor">'.$vol.'</td>';
            $items .= '<td class="tor">'.$sub.'</td>';
            $items .= '</tr>';
        }
    }
    $items .= '<tr><td colspan="3" class="toc">����</td>';
    $items .= '<td class="tor">'.number_format($d[$idx]['order_amount']).'<ins class="small">��</ins></td>';
    $items .= '<td class="tor">'.number_format($subtotal).'<ins class="small">��</ins></td></tr>';

    $discount_fee = $d[$idx]['discountfee'] + $d[$idx]['reductionfee'];
    $print_fee = $d[$idx]['printfee'] + $d[$idx]['exchinkfee'];
    $items .= '<tr><td colspan="4">�ץ�����</td><td class="tor">'.number_format($print_fee).'<ins class="small">��</ins></td></tr>';
    $items .= '<tr><td colspan="4">���</td><td class="tor fontred">��'.number_format($discount_fee).'<ins class="small">��</ins></td></tr>';
    $items .= '<tr><td colspan="4">����</td><td class="tor">'.number_format($d[$i]['carriagefee']).'<ins class="small">��</ins></td></tr>';

    $charge = array(
        'expressfee'=>'�õ�����',
        'codfee'=>'��������',
        'packfee'=>'�޵���',
        'designfee'=>'�ǥ�������',
        'additionalfee'=>mb_convert_encoding($d[$i]['additionalname'], 'euc-jp', 'utf-8')
    );
    foreach($charge as $charge_key=>$charge_name){
        if(empty($d[$idx][$charge_key])) continue;
        $items .= '<tr><td colspan="4">'.$charge_name.'</td><td class="tor">'.number_format($d[$idx][$charge_key]).'<ins class="small">��</ins></td></tr>';
    }

    $total = $d[$idx]['estimated'];
    $perone = ceil($total/$d[$idx]['order_amount']);
    $tax = $d[$idx]['salestax'];
    $credit = $d[$idx]['creditfee'];
    $base = ($d[$idx]['basefee']!=$total)? $d[$idx]['basefee']: 0;

    // POST����
    $redirect_ok = 'http://www.takahama428.com/user/receive_ok.php';
    $redirect_ng = 'http://www.takahama428.com/user/receive_ng.php';
    $redirect_can = 'http://www.takahama428.com'.$_SERVER['SCRIPT_NAME'].'?oi='.$orders_id;
    $redirect = 'http://www.takahama428.com/user/receive.php';
}else{
    $msg = "����ʧ�������ƺѤ�Ǥ���ޤ���";
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
        <title>����ʧ������ - TLA���С��� | �����ϥޥ饤�ե�����</title>
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
            <meta property="og:title" content="������®�������ꥸ�ʥ�T����Ĥ������ž夲����" />
            <meta property="og:type" content="article" /> 
            <meta property="og:description" content="�ȳ�No. 1ûǼ���ǥ��ꥸ�ʥ�T����Ĥ�1�礫��������ޤ����̾�Ǥ�3���ǻž夲�ޤ���" />
            <meta property="og:url" content="http://www.takahama428.com/" />
            <meta property="og:site_name" content="���ꥸ�ʥ�T����Ĳ��å����ϥޥ饤�ե�����" />
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
                    <div class="pagetitle"><h1>����ʧ������</h1></div>
                    <div class="section">
                        <h2><?php echo $msg; ?></h2>
                        <?php echo $ls; ?>
                    </div>
                    <div class="cretxt">���ұĶȻ��ֳ��ϡ����쥸�åȥ����ɤ�����η�̤��Ф�ޤǤ����֤��������礬�������ޤ���
                        �������λ�ȽФ�ޤǤ��Ф餯���Ԥ����������ޤ���
                        �ʤ������ٷ�Ѥ�Ԥ��ޤ���2�ŷ�Ѥˤʤ��ǽ�����������ޤ��Τǡ�����դ���������
                    </div>

                    <div class="section" <?php echo $display;?>>
                        <h2>����ʸ����</h2>

                        <p class="note tor">��ʸNo.<?php echo $d[$idx]['orderid'];?></p>
                        <div class="inner1">
                            <table class="form_table">
                                <thead>
                                    <tr>
                                        <th>����̾ / ���顼</th><th>������</th><th>ñ��</th><th>���</th><th>���</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <?php
                                    if($base>0){
                                        echo '<tr><th colspan="2"></th><td colspan="2">��</td><td class="tor"><ins>'.number_format($subtotal).'</ins> ��</td></tr>';
                                    }
                                    if($tax>0){
                                        echo '<tr><th colspan="2"></th><td colspan="2">������</td><td class="tor"><ins>'.number_format($tax).'</ins> ��</td></tr>';
                                    }
                                    if($credit>0){
                                        echo '<tr><th colspan="2"></th><td colspan="2">�����ɼ����</td><td class="tor"><ins>'.number_format($credit).'</ins> ��</td></tr>';
                                    }
                                    ?>
                                    <tr class="foot_total"><th colspan="2"></th><td colspan="2">����ʸ���</td><td class="tot"><ins><?php echo number_format($total);?></ins> ��</td></tr>
                                    <tr class="foot_perone"><th colspan="2"></th><td colspan="2">1�礢����</td><td class="tor"><ins><?php echo number_format($perone);?></ins> ��</td></tr>
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
                            //�֥���å� ID + ��|�� +  �������� ID + ��|�� +  ���Ѷ��   + ��|�� +  ������   + ��|�� +  ����åץѥ����   + ��|�� +  ���������
                            $password = $shopId . "|" . $orders_id . "|" . $total . "|" .$tax. "|" .$shoppass. "|".$now;
                            $password = md5($password);
                            $form .= '<input type="hidden" value="' .$password. '" name="ShopPassString">';
                            $form .= '<input type="hidden" value="'.$redirect.'" name="RetURL">';
                            $form .= '<input type="hidden" value="'.$redirect_can.'" name="CancelURL">';
                            //���쥸�åȥ�����
                            if($d[$idx]['payment']=='credit') {
                                $form .= '<input type="hidden" value="1" name="UseCredit">';
                                $form .= '<input type="hidden" value="CAPTURE" name="JobCd">';
                                $form .= '<input type="submit" value="�����ɷ�ѤΤ������Ϥ����餫��">';
                            } else {
                                $form .= '<input type="hidden" value="1" name="UseCvs">';
                                $form .= '<input type="hidden" value="ͭ�²�ҥ����ϥޥ饤�ե�����" name="ReceiptsDisp11">';
                                $form .= '<input type="hidden" value="03-5670-0787" name="ReceiptsDisp12">';
                                $form .= '<input type="hidden" value="09:00-18:00" name="ReceiptsDisp13">';
                                $form .= '<input type="submit" value="����ӥ˷�ѤΤ������Ϥ����餫��">';
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
                        <li class="btn-group"><a class="btn btn-default" href="#" data-featherlight="#fl1">����ʧ��ˡ�ˤĤ���</a></li>
                    </ul>

                    <div class="lightbox" id="fl1">

                        <div class="modal-content">
                            <div class="modal_window">
                                <h2>��å�����</h2>
                                <h3 class="syousai">��Կ���</h3><hr>
                                <p>�����θ��¤ˤ���������������</p>
                                <p>����˾��Ǽ�������2�����ޤǤˤ������򤪴ꤤ�פ��ޤ����������ˤ������ǧ���Ǥ��ʤ��ΤǤ���դ��������ˤ�����������ϡ������ͤΤ���ô�Ȥ����Ƥ��������Ƥ���ޤ���</p>
                                <dl class="list">
                                    <dt>���̾</dt>
                                    <dd>��ɩ����գƣʶ��</dd>
                                    <dt>��Ź̾</dt>
                                    <dd>�������Ź��744</dd>
                                    <dt>���¼���</dt>
                                    <dd>����</dd>
                                    <dt>�����ֹ�</dt>
                                    <dd>3716333</dd>
                                    <dt>����̾��</dt>
                                    <dd>��˥����ϥޥ饤�ե�����</dd>
                                </dl>
                                <hr><br><h3 class="syousai">������</h3><hr>
                                <p>�������������1��ˤĤ�&yen;800����ȴ�ˤ�����ޤ���
                                    ����ʧ����ۡʾ�����+������������������ܾ����ǡˤ������ȼԤˤ���ʧ������������
                                    �����ͤΤ��Թ�Ǥ���ʧ�������ʣ���ˤʤä���硢1��ˤĤ�&yen;800����ȴ�ˤ��ɲä����Ƥ��������ޤ���</p>
                                <hr><br><h3 class="syousai">�����ɷ��</h3><hr>
                                <p>�Ƽ說�쥸�åȥ����ɤ������Ѥ��������ޤ���
                                    ����˾��Ǽ�������2�����ޤǤ˥����ɷ�Ѽ�³���򤪴ꤤ�פ��ޤ���
                                    �������ˤ������ǧ���Ǥ��ʤ��ΤǤ���դ��������˥����ɷ�ѥ����ƥ���������5%�ˤϡ������ͤΤ���ô�Ȥ����Ƥ��������Ƥ���ޤ���
                                    ���ҤΡ֥ޥ��ڡ����ס�֤���ʧ�������ס�֥����ɷ�ѤΤ��������Ϥ����餫��פˤƷ�Ѥ���ǽ�Ǥ���</p>
                                <center><p><img width="60%" alt="�����ɼ���" src="/order/img/card.png"></p></center>
                                <!-- <hr><br><h3 class="syousai">����ӥ˷��</h3><hr>
<p>����Υ���ӥ˥��󥹥��ȥ��Ǥ���ʧ������ǽ�Ǥ���
��ʧ���ֹ�����餻�Ƥ��������᡼��˵��ܤ��Ƥ���ޤ���
��ʧ��������ʰ�Χ&yen;800�ˤϤ�������ô�Ȥ����Ƥ��������Ƥ���ޤ���
���ҤΡ֥ޥ��ڡ����ס�֤���ʧ�������ס�֥���ӥ˷�ѤΤ��������Ϥ����餫��פˤƷ�Ѥ���ǽ�Ǥ���</p>
<p><img width="100%" alt="�����ɼ���" src="/order/img/konnbini.png"></p> -->


                            </div>
                        </div>


                    </div>
                </div>

                <p class="scroll_top"><a href="#header">����ʧ���������ڡ����ȥåפ�</a></p>

                <?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php"; ?> 

                <script src="./js/jquery-1.7.0.min.js"></script>
                <script src="./js/featherlight.min.js" type="text/javascript" charset="utf-8"></script>


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