/*
 * jQuery File Upload Plugin JS Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global $, window */

$(function () {
	'use strict';

	// Input validation
	$('#uploadinfo .form_table').data('validation', 0);
	$('#uploadinfo .form_table tbody').on('change', 'input', function(){
		var valid = 1
		var elem = $(this).closest('tbody').find('input');
		$.each(elem, function (index) {
			if ($(elem[index]).prop('type')=='email') {
				document.forms.fileupload.email.value = $.trim(elem[index].value);
			}
			if ($(elem[index]).prop('required') && $.trim(elem[index].value)=="") {
				valid = 0;
				return false;
			}
		});
		$('#uploadinfo .form_table').data('validation', valid);
	});
	
	// For required-field message
	$('#fileupload').data('requiredMessage', 1);
	
	// ��祢�åץ���
	$('#fileupload .fileupload-buttonbar').on('click', '.start', function(e){
		e.preventDefault();
		$(e.currentTarget).data('active', 0);
		$('#fileupload').data('requiredMessage', 1);
	});
	
	// ���̥��åץ���
//	$('#fileupload-table').on('click', '.template-upload .start', function(e){
//		e.preventDefault();
//		if ($('#fileupload .fileupload-buttonbar .start').data('active')!=1) {
//			$('#fileupload').data('requiredMessage', 1);
//		}
//	});
	
	// Cancel button
	// ���ƺ��
//	$('#fileupload .fileupload-buttonbar').on('click', '.cancel', function(e){
//		e.preventDefault();
//		$('#fileupload-table tbody tr.template-download').remove();
//		$(this).removeClass('in');
//		$('#fileupload .fileupload-buttonbar .start').removeClass('in');
//	});

	// ���̥���󥻥�
	$('#fileupload-table tbody').on('click', '.template-upload .cancel', function(e){
		e.preventDefault();
		var len = $('#fileupload-table tbody .template-upload').length;
		if (len==1) {
			$('#fileupload .fileupload-buttonbar .start').removeClass('in');
		}
	});
	
	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: '/user/support/data/'
	}).on('fileuploadadd', function(e, data) {
		$('#fileupload .fileupload-buttonbar .start').addClass('in');
		$('#fileupload .fileupload-buttonbar .cancel').addClass('in');
	}).on('fileuploadsubmit', function (e, data) {
		var isValid = false;
		if ($('#uploadinfo .form_table').data('validation')==0) {
			if ($('#fileupload').data('requiredMessage')==1) {
				$('#fileupload').data('requiredMessage', 0);
				$.msgbox('��̾���ȥ᡼�륢�ɥ쥹��ɬ�ܤǤ���');
			}
		} else {
			isValid = true;
		}
		if (isValid===false) {
			data.context.find('.start').prop('disabled', false);
		}
		return isValid;
	}).on('fileuploadalways', function(e, data){
		var rest = 0;
		var idx = 0;
		var uri = [];
		$('#fileupload-table tbody tr').each(function(){
			var self = $(this);
			if (self.is('.template-download')) {
				uri[idx++] = self.find('.path').text();
			} else {
				if (self.find('.error').text()=="") {
					rest++;
				}
			}
		});
		if (rest==0 && uri.length>0) {
			var postData = {"act":"fileupload"};
			var elem = $('#uploadinfo .form_table tbody').find('input');
			$.each(elem, function (index) {
				postData[$(elem[index]).attr('name')] = $.trim(elem[index].value);
			});
			postData['message'] = $.trim($('#uploadinfo .form_table tbody textarea').val());
			postData['uploadfile'] = uri;
			
			$('#fileupload .fileupload-buttonbar .start').removeClass('in');
			
			$.ajax({
				type: "POST",
				url: "/user/php_libs/controller.php",
				async: true,
				timeout: 10000,
				cache: false,
				data: postData,
				dataType: 'json'
			}).done(function (data, textStatus, jqXHR) {
				if (data.response) {
					$.msgbox('���åץ��ɤ���λ�������ޤ�����');
				} else {
					$.msgbox("���åץ��ɤ���λ�������ޤ�����<hr>�᡼�������ǥ��顼��ȯ�����Ƥ��ޤ�����������ޤ�����<br>0120-130-428 �ޤǤ�Ϣ����������");
				}
			}).fail(function (jqXHR, textStatus, errorThrown) {
				$.msgbox("Error: �����С���ǥ��顼�����ä����������С��������������ޤ���Ǥ�����");

			}).always(function (data_or_jqXHR, textStatus, jqXHR_or_errorThrown) {
				// done,fail����鷺����˼¹Ԥ�������
			});
		}
	});

	// Enable iframe cross-domain access via redirect option:
	$('#fileupload').fileupload(
		'option',
		'redirect',
		window.location.href.replace(
			/\/[^\/]*$/,
			'/user/cors/result.html?%s'
		)
	);

	if (window.location.hostname === 'www.takahama428.com') {
		$('#fileupload').fileupload('option', {
			url: '/user/support/data/',
			// Enable image resizing, except for Android and Opera,
			// which actually support image resizing, but fail to
			// send Blob objects via XHR requests:
			disableImageResize: /Android(?!.*Chrome)|Opera/
				.test(window.navigator.userAgent),
			maxFileSize: 104857600,	// 100MB
			maxNumberOfFiles: 5,
			acceptFileTypes: /(\.|\/)(ai|jpe?g|png|psd|pdf|zip)$/i,
			messages: {
				maxNumberOfFiles: '���٤˥��åפǤ���������5�ġˤ�Ķ���Ƥ��ޤ�',
				acceptFileTypes: '�ե���������ϡ�jpeg, png, ai, psd, pdf �ΤߤǤ�',
				maxFileSize: '�ե����륵������100MB�ޤǤǤ�',
				minFileSize: 'File is too small'
			}
		});
		// Upload server status check for browsers with CORS support:
		if ($.support.cors) {
			$.ajax({
				url: '//www.takahama428.com/user/support/data/',
				type: 'HEAD'
			}).fail(function () {
				$('<div class="alert alert-danger"/>')
					.text('Upload server currently unavailable - ' +
						new Date())
					.appendTo('#fileupload');
			});
		}
	} else {
		// Load existing files:
		$('#fileupload').addClass('fileupload-processing');
		$.ajax({
			// Uncomment the following to send cross-domain cookies:
			//xhrFields: {withCredentials: true},
			url: $('#fileupload').fileupload('option', 'url'),
			dataType: 'json',
			context: $('#fileupload')[0]
		}).always(function () {
			$(this).removeClass('fileupload-processing');
		}).done(function (result) {
			$(this).fileupload('option', 'done')
				.call(this, $.Event('done'), {
					result: result
				});
		});
	}

});
