<?php
	ini_set('memory_limit', '128M');
	include $_SERVER['DOCUMENT_ROOT']."/common/inc/pageinit.php";
	require_once $_SERVER['DOCUMENT_ROOT'].'/php_libs/mailer.php';
/*
 *	$info
 *		['title']			express
 *		['customername']
 *		['email']
 *		['tel']
 *		['message']
 *		['deliveryday']
 *		['category']
 *		['amount']
 *		['printinfo']
 *
 *	$_FILES
 *		['attachfile'][]	ʣ���б��Τ�������
 *		
 */


	if( isset($_POST['ticket'], $_SESSION['ticket']) ) {
		$titles = array(
			'info'=>'���䤤��碌',
			'request'=>'��������', 
			'estimate'=>'��������礻',
			'test'=>'�ƥ���',
			'minit'=>'��˥ե�����ߥ�T����������',
			'illusttemplate'=>'��������ƥƥ�ץ졼��',
			'repeat'=>'�ɲ���ʸ',
			'visit'=>'��ĥ�Ǥ���碌',
			'expresstoday'=>'�����õޥץ��',
			'towel'=>'���ꥸ�ʥ륿���뤪�䤤��碌',
			'designconsierge'=>'�ǥ����󥳥󥷥��른��',
			'orange'=>'���󤸷��������åפ���������'

		);

		$title = $titles[$_POST['title']];
		//$customer = mb_convert_encoding($_POST['customername'], 'euc-jp', auto);
		$customer = $_POST['customername'];
		$mailer = new Mailer($_POST);
		if($_POST['title']!='repeat'){
			$isSend = $mailer->send();
		}else{
			$isSend = $mailer->send_repeat();
		}
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T5NQFM');</script>
<!-- End Google Tag Manager -->
	<meta charset="euc-jp" />
<!-- m3 begin -->
	<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
<!-- m3 end -->
	<title>�᡼��������λ��|�����ꥸ�ʥ�T����Ĳ������ϥޥ饤�ե�����</title>
	<meta name="Description" content="1������̤Υץ��Ȥޤǡ��ȥ졼�ʡ����ݥ���ġ����ꥸ�ʥ�T����Ĥκ������ץ��Ȥϡ�����Գ����Υ����ϥޥ饤�ե����Ȥˤ�Ǥ�������������Τ䥰�롼�פʤɤ����Ѥ���ʸ���ס��ΰ�פΥ��٥�Ȥ�����夲�Ƥ���������" />
	<meta name="keywords" content="���ꥸ�ʥ�,T�����,���,����,�ץ���" />
	<meta name="google-site-verification" content="PfzRZawLwE2znVhB5M7mPaNOKFoRepB2GO83P73fe5M" />
	<link rel="shortcut icon" href="/icon/favicon.ico" />
<!-- m3 begin -->
	<link rel="stylesheet" type="text/css" href="/m3/common/css/common_responsive.css" media="all">
	<link rel="stylesheet" type="text/css" href="/m3/common/css/slidebars_responsive.css" media="all">
	<link rel="stylesheet" href="/m3/common/css/import_responsive.css">
	<link rel="stylesheet" href="/m3/items/css/detail_responsive.css">
<!-- m3 end -->
	<link rel="stylesheet" type="text/css" href="/common/css/common_responsive.css" media="all" />
	<link rel="stylesheet" type="text/css" href="/common/css/base_responsive.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="./css/finish_responsive.css" media="screen" />
	
	<script type="text/javascript" src="/common/js/jquery.js"></script>
	<script type="text/javascript" src="/common/js/tlalib.js"></script>
	<!--m3 begin-->
	<script src="/m3/common/js/common1.js"></script>
	<!--m3 end-->
	
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T5NQFM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

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
					$heading = '��ǧ�᡼����ֿ����Ƥ��ޤ���<br>����ǧ����������';
					$sub = 'Sending';
					$html = <<<DOC

				<h2 class="heading">{$title}</h2>
				<div class="inner">
					<p>{$customer}����</p>
					<p>�����٤ϥ����ϥޥ饤�ե����Ȥ����Ѥ������������ˤ��꤬�Ȥ��������ޤ���</p>
					<p>���Ƥ��ǧ�塢���ҥ����åդ��餴Ϣ�������ޤ���</p>
				</div>
				<div class="inner">
					<p class="red">����򳫻Ϥ���ˤ����ꡢ�����äˤ�뤴��ʸ���Ƥκǽ���ǧ�򤵤��Ƥ��������Ƥ���ޤ���</p>
					<p class="red">�����Ƥ����������ǥ���������Ƥȥץ��Ȱ��֤ʤɤ��ǹ礻��Ԥ���Ǽ�����������Ѥ�κǽ���ǧ�򤪤��ʤäƤ���������ʸ����Ȥʤ�ޤ���</p>
				</div>
				<div class="inner">
					<h3>�� �����б��Ǥ��ä��ꥵ�ݡ��� ��</h3>
					<p>
						�ֿ��᡼�뤬�Ϥ��ʤ����ϡ�������Ǥ���������Ϣ����ޤǤ��䤤��碌����������<br />
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
				<h2 class="heading">�������顼</h2>
				<div class="inner">
					<p>{$customer}����</p>
					<div class="remarks">
						<p><strong>�᡼�������������ޤ���Ǥ�����</strong></p>
						<p>�᡼���������˥��顼��ȯ���������ޤ�����</p>
					</div>
					<p>��������ޤ��������� [ ���� ] �ܥ���򥯥�å����Ʋ�������</p>
				</div>
				<div class="inner">
					<h3>�� �����б��Ǥ��ä��ꥵ�ݡ��� ��</h3>
					<p class="note">���ޤ��Τ����ͤϡ��ե꡼������� {$cst(_TOLL_FREE)} �ޤǤ����ڤˤ�Ϣ����������</p>
					<p><a href="/contact/">�᡼��ǤΤ��䤤��碌�Ϥ����餫��</a></p>
					<hr />
					<p class="gohome"><a href="/">�ۡ���ڡ��������</a></p>
				</div>
DOC;
				}
			?>
				
			<div class="heading1_wrapper">
				<h1><?php echo $heading;?></h1>
				<p class="comment"></p>
				<p class="sub"><?php echo $sub;?></p>
			</div>
			<?php echo $html;?>
			
		</div>
		
	</div>
	
    <p class="scroll_top"><a href="#header">�᡼��������λ���ڡ����ȥåפ�</a></p>

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
