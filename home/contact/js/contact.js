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
				$.msgbox("�᡼�륢�ɥ쥹�����Ϥ��Ƥ���������");
				return false;
			}
			if(f.tel.value.trim()==""){
				$.msgbox("�������ֹ�����Ϥ��Ƥ���������");
				return false;
			}
			if(f.customername.value.trim()==""){
				$.msgbox("��̾�������Ϥ��Ƥ���������");
				return false;
			}
			if(f.ruby.value.trim()==""){
				$.msgbox("�եꥬ�ʤ����Ϥ��Ƥ���������");
				return false;
			}
	/*				if(f.message.value.trim()==""){
				$.msgbox("��å����������Ϥ��Ƥ���������");
				return false;
			}
			
	if(!$(':radio[name=repeater]:checked').val()){
				$.msgbox("���Ҥ����ѤˤĤ��Ƥ����Ϥ��Ƥ���������");
				return false;
			}    */
			
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
		add_attach:function(id){
			var new_row = '<tr><td><div id="table_left">ź�եե�����</div>';
			new_row += '<div id="table_right"><input type="file" name="attachfile[]"></div><ins class="abort">�߼��</ins></td></tr>';
			$('#'+id+' tbody tr:last').before(new_row);
			// �ɲä���ź�եե��������
			$('.abort').on('click', function(){
		    	$(this).closest('tr').remove();
		    });
		}	
	});
	
	
	// �ɲä���ź�եե��������
	$('.abort').on('click', function(){
    	$(this).closest('tr').remove();
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

	
	/* calendar */
	if(typeof(datepicker)=='function'){
		$("#datepicker").datepicker();
	}
	
	
	// ź�եե�������ɲ�
	$('.add_attachfile', '#express_table').click( function(e){ $.add_attach(e.target) });
	
	
/*	// �ɲä���ź�եե��������
	$('.abort').on('click', function(){
    	$(this).closest('tr').remove();
    });
	
	
	// �᡼������
	$('#sendbtn').click( function(){
		$.sendmail_check(document.forms.express_form);
	});
	
*/
	
	
	// ��̾���˥ե�������
	document.forms.contact_form.customername.focus();
	$.scrollto($('body'));
	
});
