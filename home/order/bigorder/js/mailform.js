/*
*	Takahama Life Art
*	
*	contacts module
*	charset euc-jp
*/
	
$(function(){

	jQuery.extend({
		sendmail_check:function(my){
			var f = my.form;
			if(!$.check_email(f.email.value)){
				return false;
			}
			if(f.customername.value.trim()==""){
				$.msgbox("お名前を入力してください。");
				return false;
			}
			if(f.customerruby.value.trim()==""){
				$.msgbox("フリガナを入力してください。");
				return false;
			}
			if(f.tel.value.trim()==""){
				$.msgbox("お電話番号を入力してください。");
				return false;
			}
			if(f.pref.value.trim()==""){
				$.msgbox("お届け先都道府県を入力してください。");
				return false;
			}
			if(!$(':radio[name="vol"]:checked').val()){
				$.msgbox("製作枚数をご指定ください。");
				return false;
			}
			if($(':checkbox[name^="youto"]:checked').length==0){
				$.msgbox("ご利用の用途をご指定ください。");
				return false;
			}
			
			if(f.mode.value=="confirm"){
				f.mode.value = 'send';
				my.value = '送　信';
				$('input[type="text"], :radio[name=repeater], textarea, .txt, label', f).addClass('confirmation');
				$('input[name="subtitle[]"]:checked').each( function(){
					var txt = $(this).closest('td').children('.txt');
					var chk = txt.html()==""? $(this).val(): ',　'+$(this).val();
					txt.append(chk);
				});
				$(':radio[name=repeater]:checked', f).each( function(){
					var v = $(this).val();
					$(this).closest('td').children('.txt').html(v);
				});
				$('input[type="text"]', f).each( function(){
					var v = $(this).val().replace('/(\r\n|\n|\r)/g', '<br>');
					$(this).prev().html(v);
				});
				var $textarea = $('textarea', f);
				var v = $textarea.val().replace(/(\r\n|\n|\r)/g, "<br>");
				$textarea.prev().html(v);
				$('input[type="reset"]').hide();
				$('.title_confirmation, .msg, #goback').show();
			}else if(f.mode.value=="send"){
				f.submit();
			}
		},
		add_attach:function(my){
			var new_row = '<p><input type="file" name="attachfile[]" /><ins class="abort">×取消</ins></p>';
			$(my).before(new_row);
		}	
	});
	
	
	// 追加した添付ファイルを削除
	$('.abort').on('click', function(){
    	$(this).closest('p').remove();
    });
	
	
	// 確認画面の戻るボタン
	$('#goback').click( function(){
		var f = $(this).closest('form').get(0);
		f.mode.value = 'confirm';
		$('#sendmail').val('入力内容の確認');
		$('input[type="text"], :radio[name=repeater], textarea, .txt, label', f).removeClass('confirmation');
		$('.txt', f).html('');
		$('input[type="reset"]').show();
		$('.title_confirmation, .msg, #goback').hide();
	});
	
	
	// お名前にフォーカス
	document.forms.bigorder_form.customername.focus();
	$.scrollto($('body'));
	
});
