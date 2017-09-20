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

	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: '/user/support/data/'
	}).on('fileuploadadd', function(e, data) {
//		$('#fileupload .fileupload-buttonbar .start').addClass('in');
//		$('#fileupload .fileupload-buttonbar .cancel').addClass('in');
	}).on('fileuploadsubmit', function (e, data) {
		var isValid = false;
		if ($('#fileupload').data('confirm')==1) {
			$('#fileupload').data('onfirm', 0);
			isValid = true;
		}
		return isValid;
	}).on('fileuploadalways', function(e, data){
		var attach = "";
		$('#fileupload-table tbody tr').each(function(){
			var self = $(this);
			if (self.is('.template-download')) {
				var path = self.find('.path').text();
				attach += '<input type="hidden" name="uploadfilename[]" value="'+path+'">';
				//				attach += '<p><input type="hidden" name="uploadfilename[]" value="'+encodeURIComponent(name)+'">'+name+'</p>';
			}
		});
		$('#conf_attach').html(attach);
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
				maxNumberOfFiles: '一度にアップできる最大数（5個）を超えています',
				acceptFileTypes: 'ファイル形式は、jpeg, png, ai, psd, pdf のみです',
				maxFileSize: 'ファイルサイズは100MBまでです',
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
