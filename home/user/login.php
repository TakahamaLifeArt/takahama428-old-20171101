<?php
require_once dirname(__FILE__).'/php_libs/funcs.php';

// �����󤷤Ƥ������TOP��
$me = checkLogin();
if($me){
	jump('history.php');
}

if(isset($_REQUEST['login']) && empty($_SESSION['me'])){
	
	$args = array($_REQUEST['email']);
	$conndb = new Conndb(_API_U);
	
	// ���顼�����å�
	if(empty($_REQUEST['email'])) {
		$err = '�᡼�륢�ɥ쥹�����Ϥ��Ʋ�������';
	}else if(!$conndb->checkExistEmail($args)) {
		$err = "���Υ᡼�륢�ɥ쥹����Ͽ����Ƥ��ޤ���";
	}else if(empty($_REQUEST['pass'])) {
		$err = '�ѥ���ɤ����Ϥ��Ʋ�������';
	}else{
		$args = array('email'=>$_REQUEST['email'], 'pass'=>$_REQUEST['pass']);
		$me = $conndb->getUser($args);
		if(!$me){
			$err = "�᡼�륢�ɥ쥹���ѥ���ɤ�ǧ���Ǥ��ޤ��󡣤���ǧ��������";
		}
	}
	
	if(empty($err)){
		// ���å����ϥ�����å��к�
		session_regenerate_id(true);
		
		// ��������֤��ݻ�
		if($_REQUEST['save']) {
			//setcoocie(session_name(), sesion_id(), time()+60*60*24*7);
		}
		
		$_SESSION['me'] = $me;
		jump('history.php');
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="euc-jp" />
	<title>������ �� ���ꥸ�ʥ�T����Ĥ��ᤤ�������ϥޥ饤�ե�����</title>
	<meta name="Description" content="�ᤤ��T����Ĥǥ��ꥸ�ʥ���������ʤ饿���ϥޤء������ϥޥ饤�ե����ȤΥ�������̤Ǥ����᡼�륢�ɥ쥹�ȥѥ���ɤ�����Ƥ����������ޥ��ڡ������餴��ʸ����ʤɤ򤴳�ǧ���뤳�Ȥ��Ǥ��ޤ���������ˤ���٤Υѥ���ɤ�˺������Ϥ����顣">
	<meta name="keywords" content="���ꥸ�ʥ�,t�����,���С�">
<!-- m3 begin -->
	<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
<!-- m3 end -->
	<link rel="shortcut icon" href="/icon/favicon.ico" />
<!-- msgbox begin-->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
<!-- msgbox end-->
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
	<link rel="stylesheet" type="text/css" media="screen" href="./css/login_responsive.css" />
<!-- msgbox begin-->
<!--
	<script type="text/javascript" src="/common/js/jquery.js"></script>
	<script type="text/javascript" src="/common/js/modalbox/jquery.modalbox-min.js"></script>
-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
	<script src="//ajaxzip3.github.io/ajaxzip3.js" charset="utf-8"></script>
<!-- msgbox end-->
	<script type="text/javascript" src="./js/common.js"></script>
	<script type="text/javascript" src="./js/login.js"></script>
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
		_LOGIN_STATE = '<?php echo $err; ?>';
		
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-11155922-2']);
		_gaq.push(['_trackPageview']);
		
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
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
			
			<div class="toolbar">
				<div class="toolbar_inner clearfix">
					<h1>������</h1>
				</div>
			</div>
			
			<div id="loginform_wrapper" class="section">
				<form class="form_m" name="loginform" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return false;">
					<div class="close_form"></div>
					<label>�᡼�륢�ɥ쥹</label>
					<input type="text" value="" name="email" autofocus />
					<label>�ѥ����</label>
					<input type="password" value="" name="pass" />
					<div class="resend_pass"><a href="resend_pass.php">�ѥ���ɤ�˺�줿���Ϥ������</a></div>
	 				<div class="btn_wrap">
						<div id="login_button"></div>
  					<p style="display:none;"><a href="register.php">�桼������Ͽ</a></p>
					</div>
					<input type="hidden" name="login" value="1">
					<input type="hidden" name="reg_site" value="1">
				</form>
			</div>

			<p class="txtttl"><span class="red">��</span>�᡼�륢�ɥ쥹���ѹ����������Ϥޤ������ҤޤǤ�Ϣ����������</p>
				<p class="txtttl">info@takahama428.com</p>
				<p class="txtttl">�����ϥޥ饤�ե����ȥ��ݡ��ȥ�����</p>
		</div>
	</div>
	
	<p class="scroll_top"><a href="#header">�����󡡥ڡ����ȥåפ�</a></p>

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
<!-- msgbox begin-->
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
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- msgbox end-->

</body>
</html>
