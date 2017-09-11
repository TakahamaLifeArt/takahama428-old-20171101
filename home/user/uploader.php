<?php
require_once dirname(__FILE__).'/php_libs/funcs.php';
if($_SERVER['REQUEST_METHOD']!='POST'){
	setToken();
}else{
	chkToken();
}
?>
<!DOCTYPE html>
<html>

	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#  website: http://ogp.me/ns/website#">
		<meta charset="euc-jp" />
		<title>�ǥ�����Υ��åץ��� - TLA���С��� | �����ϥޥ饤�ե�����</title>
		<link rel="shortcut icon" href="/icon/favicon.ico" />
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
		<!--	<link rel="stylesheet" href="/common/css/mdb.min.css">-->
<!--
		<link rel="stylesheet" type="text/css" media="screen" href="/common/css/common.css">
		<link rel="stylesheet" type="text/css" media="screen" href="/common/css/base.css">
-->
		<link rel="stylesheet" type="text/css" media="screen" href="/common/css/dev-common_responsive.css">
		<link rel="stylesheet" type="text/css" media="screen" href="/common/css/dev-base_responsive.css">
		<link rel="stylesheet" type="text/css" media="screen" href="./css/style.css">
		<link rel="stylesheet" type="text/css" media="screen" href="./js/upload/jquery.fileupload.css">
		<link rel="stylesheet" type="text/css" media="screen" href="./js/upload/jquery.fileupload-ui.css">
		<link rel="stylesheet" type="text/css" media="screen" href="./css/uploader.css">
		<!-- OGP -->
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
				var ga = document.createElement('script');
				ga.type = 'text/javascript';
				ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ga, s);
			})();

		</script>
	</head>

	<body>

		<!-- Google Tag Manager -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-T5NQFM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>
			(function(w, d, s, l, i) {
				w[l] = w[l] || [];
				w[l].push({
					'gtm.start': new Date().getTime(),
					event: 'gtm.js'
				});
				var f = d.getElementsByTagName(s)[0],
					j = d.createElement(s),
					dl = l != 'dataLayer' ? '&l=' + l : '';
				j.async = true;
				j.src =
					'//www.googletagmanager.com/gtm.js?id=' + i + dl;
				f.parentNode.insertBefore(j, f);
			})(window, document, 'script', 'dataLayer', 'GTM-T5NQFM');

		</script>
		<!-- End Google Tag Manager -->

		<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/dev-header.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/globalmenu.php"; ?>
		<div id="container">

			<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/dev-sidenavi.php"; ?>

			<div class="contents">

				<div class="toolbar">
					<div class="toolbar_inner clearfix">
						<h1>�ǥ�����Υ��åץ���</h1>
					</div>
				</div>

				<div>
					<h2>����ʸ�������</h2>
					<ul>
						<li>�ե����륵�������礭�����ϡ������餫�饢�åץ��ɤ򤪴ꤤ�פ��ޤ���</li>
						<li>��祢�åץ��ɤ���ޤ��ȡ��ե����뤬���Ҥؼ�ư��������ޤ���</li>
						<li>(���б��ե�����ϡ�jpg.png.ai �ΤߤǤ�����λ������������)</li>
					</ul>
				</div>
				
				<form id="uploadinfo" name="uploadinfo" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return false;">
					<table class="form_table">
						<tfoot>
							<tr>
								<td colspan="2">
									<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
									<input type="hidden" name="designfile" value="1">
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<th>��̾��</th>
								<td>
									<input type="text" name="customername" placeholder="ɬ�ܤǤ�" required>
								</td>
							</tr>
							<tr>
								<th>�᡼�륢�ɥ쥹</th>
								<td>
									<input type="email" name="email" placeholder="ɬ�ܤǤ�" required>
								</td>
							</tr>
							<tr>
								<th>��å�����</th>
								<td>
									<textarea name="message" cols="30" rows="10"></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</form>
				
				<form id="fileupload" name="fileupload" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="email" value="">
					
					<!-- Redirect browsers with JavaScript disabled to the origin page -->
<!--					<noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>-->
					<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
					<div class="fileupload-buttonbar">
						<div class="">
							<!-- The fileinput-button span is used to style the file input field as button -->
							<span class="btn btn-success fileinput-button">
								<i class="fa fa-plus" aria-hidden="true"></i>
								<span>�ե����������...</span>
								<input type="file" name="files[]" multiple>
							</span>
							<button type="submit" class="btn btn-primary start fade">
								<i class="fa fa-cloud-upload" aria-hidden="true"></i>
								<span>��祢�åץ���</span>
							</button>
							<!-- The global file processing state -->
							<span class="fileupload-process"></span>
						</div>
						
						<div class="drop-area">
							<p>�����˥ե������ɥ�åפǤ��ޤ�</p>
						</div>
						
						<!-- The global progress state -->
						<div class="fileupload-progress fade">
							<!-- The global progress bar -->
							<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
								<div class="progress-bar progress-bar-success" style="width:0%;"></div>
							</div>
							<!-- The extended global progress state -->
							<div class="progress-extended">&nbsp;</div>
						</div>
					</div>
					<!-- The table listing the files available for upload/download -->
					<table role="presentation" class="table table-striped" id="fileupload-table">
						<tbody class="files"></tbody>
					</table>
				</form>

<script id="template-upload" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-upload fade">
		<td>
			<span class="preview"></span>
		</td>
		<td>
			<p class="name">{%=file.name%}</p>
			<strong class="error text-danger"></strong>
		</td>
		<td>
			<p class="size">Processing...</p>
			<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
				<div class="progress-bar progress-bar-success" style="width:0%;"></div>
			</div>
		</td>
		<td>
			{% if (!i && !o.options.autoUpload) { %}
			<button class="btn btn-primary start" hidden disabled>
				<i class="fa fa-cloud-upload" aria-hidden="true"></i>
				<span>���åץ���</span>
			</button>
			{% } %} 
			{% if (!i) { %}
			<button class="btn btn-warning cancel">
				<i class="fa fa-ban" aria-hidden="true"></i>
				<span>����󥻥�</span>
			</button> {% } %}
		</td>
	</tr>
	{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-download fade">
		<td>
			<span class="preview">
			{% if (file.thumbnailUrl) { %}
				<img src="{%=file.thumbnailUrl%}">
			{% } %}
			</span>
		</td>
		<td>
			<p class="name">
				<span>{%=file.name%}</span>
			</p>
				<span class="path" hidden>{%=file.url%}</span>
			{% if (file.error) { %}
				<div><span class="label label-danger">Error</span> {%=file.error%}</div>
			{% } else { %}
				<div><span class="label" style="font-size:1.2rem;font-weight:bold;color:#0275d8;">��λ</span></div>
			{% } %}
		</td>
		<td>
			<span class="size">{%=o.formatFileSize(file.size)%}</span>
		</td>
		<td>
			{% if (file.deleteUrl) { %}
				
			{% } else { %}
			<button class="btn btn-warning cancel">
				<i class="fa fa-ban" aria-hidden="true"></i>
				<span>����󥻥�</span>
			</button> {% } %}
		</td>
	</tr>
	{% } %}
</script>

			</div>
		</div>

		<p class="scroll_top"><a href="#top">�ǥ�����Υ��åץ��ɡ��ڡ����ȥåפ�</a></p>

		<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/footer.php"; ?>

		<div id="msgbox" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">��å�����</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
		
		<script src="//code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
		<!--	<script src="/common/js/mdb.min.js"></script>-->
		<script src="js/upload/vendor/jquery.ui.widget.js"></script>
		<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
		<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
		<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
		<script src="js/upload/jquery.iframe-transport.js"></script>
		<script src="js/upload/jquery.fileupload.js"></script>
		<script src="js/upload/jquery.fileupload-process.js"></script>
		<script src="js/upload/jquery.fileupload-image.js"></script>
		<script src="js/upload/jquery.fileupload-validate.js"></script>
		<script src="js/upload/jquery.fileupload-ui.js"></script>
		<script src="./js/upload/main.js"></script>
		<script src="/common/js/tlalib.js"></script>
	</body>

</html>
