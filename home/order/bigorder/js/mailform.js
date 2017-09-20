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
				$.msgbox("��̾�������Ϥ��Ƥ���������");
				return false;
			}
			if(f.customerruby.value.trim()==""){
				$.msgbox("�եꥬ�ʤ����Ϥ��Ƥ���������");
				return false;
			}
			if(f.tel.value.trim()==""){
				$.msgbox("�������ֹ�����Ϥ��Ƥ���������");
				return false;
			}
			if(f.pref.value.trim()==""){
				$.msgbox("���Ϥ�����ƻ�ܸ������Ϥ��Ƥ���������");
				return false;
			}
			if(!$(':radio[name="vol"]:checked').val()){
				$.msgbox("��������򤴻��꤯��������");
				return false;
			}
			if($(':checkbox[name^="youto"]:checked').length==0){
				$.msgbox("�����Ѥ����Ӥ򤴻��꤯��������");
				return false;
			}
			
			if(f.mode.value=="confirm"){
				f.mode.value = 'send';
				my.value = '������';
				$('input[type="text"], :radio[name=repeater], textarea, .txt, label', f).addClass('confirmation');
				$('input[name="subtitle[]"]:checked').each( function(){
					var txt = $(this).closest('td').children('.txt');
					var chk = txt.html()==""? $(this).val(): ',��'+$(this).val();
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
			var new_row = '<p><input type="file" name="attachfile[]" /><ins class="abort">�߼��</ins></p>';
			$(my).before(new_row);
		}	
	});
	
	
	// �ɲä���ź�եե��������
	$('.abort').on('click', function(){
    	$(this).closest('p').remove();
    });
	
	
	// ��ǧ���̤����ܥ���
	$('#goback').click( function(){
		var f = $(this).closest('form').get(0);
		f.mode.value = 'confirm';
		$('#sendmail').val('�������Ƥγ�ǧ');
		$('input[type="text"], :radio[name=repeater], textarea, .txt, label', f).removeClass('confirmation');
		$('.txt', f).html('');
		$('input[type="reset"]').show();
		$('.title_confirmation, .msg, #goback').hide();
	});
	
	
	// ��̾���˥ե�������
	document.forms.bigorder_form.customername.focus();
	$.scrollto($('body'));
	
});
