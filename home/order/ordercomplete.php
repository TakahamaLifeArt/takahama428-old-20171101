<?php
	ini_set('memory_limit', '256M');
	require_once dirname(__FILE__).'/../php_libs/t_orders.php';
	require_once dirname(__FILE__).'/ordermail.php';
	
	$customer = mb_convert_encoding($_SESSION['orders']['customer']['customername'], 'euc-jp', auto);
//	if( isset($_POST['ticket'], $_SESSION['ticket'], $_SESSION['orders']) && $_POST['ticket']==$_SESSION['ticket'] ) {
	if ( isset($_SESSION['orders']) ) {
		$email = $_SESSION['orders']['customer']['email'];
		$ordermail = new Ordermail();
		$isSend = $ordermail->send();
	} else {
		$isSend = false;
	}
	
	/* ��ʸ�ե��Υ��å������˴� */
	if ($isSend) {
		unset($_SESSION['ticket']);
		$_SESSION['orders'] = array();
//		setcookie(session_name(), "", time()-86400, "/");
		unset($_SESSION['orders']);
	}
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="euc-jp" />
	<title>���������ߥ᡼���������λ��|�����ꥸ�ʥ�T����Ĳ������ϥޥ饤�ե�����</title>
	<meta name="Description" content="Web�ǥ��󥿥�˥��ꥸ�ʥ�T����ĤΤ��������ߤ��Ǥ��ޤ�����ñ���Ϥǽֻ���������ܰ¤��狼��ޤ����ȥ졼�ʡ����ݥ���ġ����ꥸ�ʥ�T����Ĥκ������ץ��Ȥϡ�����Գ����Υ����ϥޥ饤�ե����Ȥˤ�Ǥ����������" />
	<meta name="keywords" content="��ʸ,����������,���ꥸ�ʥ�,T�����,�ᤤ,���" />
	<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
	<meta name="google-site-verification" content="PfzRZawLwE2znVhB5M7mPaNOKFoRepB2GO83P73fe5M" />
	<link rel="shortcut icon" href="/icon/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="/common/css/common_responsive.css" media="all" />
	<link rel="stylesheet" type="text/css" href="/common/css/base_responsive.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="./css/finish_responsive.css" media="screen" />
	<!--m3 begin-->
	<link rel="stylesheet" type="text/css" href="/m3/common/css/common_responsive.css" media="all">
	<link rel="stylesheet" type="text/css" href="/m3/common/css/slidebars_responsive.css" media="all">
	<link rel="stylesheet" href="/m3/common/css/import_responsive.css">
	<link rel="stylesheet" href="/m3/items/css/detail_responsive.css">
	<!--m3 end-->
	
	<script type="text/javascript" src="/common/js/jquery.js"></script>
	<script type="text/javascript" src="/common/js/tlalib.js"></script>
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
					$heading = '�������������Ƥγ�ǧ�᡼����ֿ���Ǥ���<br>ɬ������ǧ����������';
					$sub = 'Sending';
					$html = <<<DOC
				<div class="inner">
					<p>{$customer}����</p>
					<p>�����٤ϥ����ϥޥ饤�ե����Ȥ����Ѥ������������ˤ��꤬�Ȥ��������ޤ���</p>
				</div>
				
				<div class="remarks">
					<h3>����γ��ϤˤĤ���</h3>
					<p>�������Ǥϡ�<span class="highlights">����ʸ�ϳ��ꤷ�Ƥ��ޤ���</span></p>
					<p>
						����򳫻Ϥ���ˤ����ꡢ�����äˤ��ǥ�����γ�ǧ���ä���ʸ����Ȥ����Ƥ��������Ƥ���ޤ���<br>
						���Ҥ���渫�Ѥ�᡼������ꤤ�����ޤ��Τǡ�
						���Ѥ�����Ǥ���������ǧ��ե꡼�������<ins> {$cst(_TOLL_FREE)} </ins>�ޤǤ�Ϣ����������
					</p>
				</div>
				
				<div class="inner">
					<h3>�� <span class="highlights">��ǧ�᡼�뤬�Ϥ��ʤ����</span> ��</h3>
					<p>
						������ĺ�����᡼�륢�ɥ쥹 {$email} ���ˡ��������������Ƥγ�ǧ�᡼����������Ƥ��ޤ���<br>
						�����ͤ˳�ǧ�᡼�뤬�Ϥ��Ƥ��ʤ���硢���Ҥˤ��������ߥ᡼�뤬�Ϥ��Ƥ��ʤ���ǽ�����������ޤ��Τǡ�<br>
						��������ޤ������ե꡼�������<ins> {$cst(_TOLL_FREE)} </ins>�ޤǤ��䤤��碌��������
					</p>
				</div>
				
				<div class="inner">
					<h3>�� ����ʸ�˴ؤ��뤪�䤤��碌 ��</h3>
					<p>
						���ޤ��Τ����ͤϡ��ե꡼������� {$cst(_TOLL_FREE)} �ޤǤ����ڤˤ�Ϣ����������
					</p>
					<p><a href="/contact/">�᡼��ǤΤ��䤤��碌�Ϥ����餫��</a></p>
					<hr />
					<p class="gohome"><a href="/">�ۡ���ڡ��������</a></p>
				</div>

DOC;
				}else{
					$heading = '�������顼��';
					$sub = 'Error';
					$html = <<<DOC
				<div class="inner">
					<p>{$customer}����</p>
					<div class="remarks">
						<h3>���������ߥ᡼�������������ޤ���Ǥ�����</h3>
						<p>���������ߥ᡼���������˥��顼��ȯ���������ޤ�����</p>
					</div>
					<p>��������ޤ��������� <a href="/order/">���������ߥե�����</a> ����� [ ��ʸ���� ] �ܥ���򥯥�å����Ʋ�������</p>
				</div>
				<div class="inner">
					<h3>�� ����ʸ�˴ؤ��뤪�䤤��碌 ��</h3>
					<p class="note">���ޤ��Τ����ͤϡ��ե꡼������� {$cst(_TOLL_FREE)} �ޤǤ����ڤˤ�Ϣ����������</p>
					<p><a href="/contact/">�᡼��ǤΤ��䤤��碌�Ϥ����餫��</a></p>
					<hr />
					<p class="gohome"><a href="/order/">���������ߥե���������</a></p>
				</div>
DOC;
				}
				
			?>
			
			<div class="heading1_wrapper">
				<h1><?php echo $heading;?></h1>
				<p class="comment"></p>
				<p class="sub"><?php echo $sub;?></p>
			</div>
			<p class="heading"></p>
			<?php echo $html;?>
		</div>
		
	</div>
	
    <p class="scroll_top"><a href="#header">���������ߥ᡼���������λ���ڡ����ȥåפ�</a></p>

	<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php"; ?>


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
