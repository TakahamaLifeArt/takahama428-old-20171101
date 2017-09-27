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

	// SessionStorage initialize
	if( ('sessionStorage' in window) && (window.sessionStorage !== null) ) {
		// available
	} else {
//		alert("Session �����ѤǤ��ޤ���");
	}
	sessionStorage.clear();

	// SessionStorage
	$.extend({
		getStorage: function(key){
		/**
		 *	sessionStorage�Υǡ��������
		 *	@param key		��������ǡ����Υ�������ꡢ̤����ʵ��Ȥʤ�0,"",null,undefined,false�ˤ����ƤΥǡ���
		 *	@return			���ǡ����ξ��:{key:[], key:[], ...}
		 * 					��������ξ��:[]
		 *					�����ޤ��ϥǡ�����¸�ߤ��ʤ����:null
		 */
			var sess = sessionStorage;
			var store = {};
			if(!key){
				for(var key in sess){
					if(sess.hasOwnProperty(key)) {
						store[key] = JSON.parse(sess.getItem(key));
					}
				}
			}else{
				store = JSON.parse(sess.getItem(key));
			}
			return store;
		},
		setStorage: function(list){
		/**
		 * sessionStorage�˳�Ǽ
		 * @param list	{"uploadfile":	{"name":�ե�����̾, "path":�ե�����ѥ�}}	// ���åץ��ɤ����ե�����̾�ȥե�����ѥ�
		 */
			var key = Object.keys(list)[0];
			var sess = sessionStorage;
			var store = $.getStorage(key);
			var isExist = false;
			if(store!==null){
				var len = store.length;
				for(var i=0; i<len; i++){
					if(list[key]["name"]!=store[i]["name"]) continue;
					// Ʊ���ե�����̾����
					isExist = i;
					store[i]["path"] = list[key]["path"];
					break;
				}
				if(isExist===false){
					store.push(list[key]);
				}
				sess.setItem(key, JSON.stringify(store));
			}else{
				store = [list[key]];
			}
			sess.setItem(key, JSON.stringify(store));
		}
	});

	// ��祢�åץ���
	$('#fileupload .fileupload-buttonbar').on('click', '.start', function(e){
		e.preventDefault();
		$(e.currentTarget).data('active', 0);
		$('#fileupload').data('requiredMessage', 1);
	});

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
		url: '/user/member/data/'
	}).on('fileuploadadd', function(e, data) {
		$('#fileupload .fileupload-buttonbar .start').addClass('in');
	}).on('fileuploadalways', function(e, data){
		var rest = 0;
		var idx = 0;
		var uri = [];
		var attach = "";
		$('#fileupload-table tbody tr').each(function(){
			var self = $(this);
			if (self.is('.template-download')) {
				var path = self.find('.path').text();
				var name = self.find('.name').children().text();
				uri[idx++] = name;
				attach += '<p><input type="hidden" name="uploadfilename[]" value="'+path+'">'+name+'</p>';
//				attach += '<p><input type="hidden" name="uploadfilename[]" value="'+encodeURIComponent(name)+'">'+name+'</p>';
			} else {
				if (self.find('.error').text()=="") {
					rest++;
				}
			}
		});
		
		// ��ǧ���̤򹹿�
		if (attach=="") attach = "�ʤ�";
		$('#conf_attach').html(attach);
		
		// ���ƤΥ��åץ��ɤ���λ
		if (rest==0) {
			$('#fileupload .fileupload-buttonbar .start').removeClass('in');
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
			url: '/user/member/data/',
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
			},
			autoUpload: true
		});
		// Upload server status check for browsers with CORS support:
		if ($.support.cors) {
			$.ajax({
				url: '//www.takahama428.com/user/member/data/',
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
