/*
*	Takahama Life Art
*	
*	charset euc-jp
*/
	
$(function(){

	jQuery.extend({
		itemparam: {
		/*
		*	�����ȤΥ����ƥ�ǡ���
		*/
			'categoryid'	: '',
			'categorykey'	: '',
			'categoryname'	: '',
			'itemid'		: '',
			'itemcode'		: '',
			'itemname'		: '',
			'colorcode'		: '',
			'colorname'		: ''
		},
		temp: {
			'categoryid'	: '',
			'itemid'		: '',
			'colorcode'		: [],
			'volume'		: []
		},
		showPrintPosition: function(){
		/*
		*	�ץ��Ȱ��ֲ����ʳ����ˤȥ��󥯿�������Υ�������
		*/
			var isResult = false;
			$.ajax({url:'/php_libs/orders.php', async:false, type:'POST', dataType:'text', data:{'act':'orderposition', 'itemid':$.itemparam.itemid, 'catid':$.itemparam.categoryid}, 
				success: function(r){
					if(r!=''){
						isResult = true;
						var val = r.split('|');
						$('#noprint').val([val[1]]);
						if(val[1]==1){
							$('#pos_wrap').hide();
						}else{
							$('#pos_wrap').show();
						}
						$('#pos_wrap').html('<h3>'+$.itemparam.categoryname+'<span>���ƥ��꡼</span></h3>'+val[0]);
						$.setPrintposEvent();
					}
				}
			});
			
			return isResult;
		},
		setPrintposEvent: function(){
		/* 
		*	�ץ��Ȱ��ֲ����Υ��륪���С��ȥ���å����٥��
		*	ʣ������ġ�����å��ǻ��������
		*/
			if($('#pos_wrap').children('div').attr('class').split('_')[1]==46) return;		// �ץ��Ȥʤ�����
			$('select', '#pos_wrap').uniform({fileDefaultText:''});
			$('#pos_wrap .posimg').each( function(){
				$(this).children('img:not(:nth-child(1))').each(function() {
					var postfix = '_on';
					var img = $(this);
					var posid = img.parent().parent().attr('class').split('_')[1];
					var src = img.attr('src');
					var src_on = src.substr(0, src.lastIndexOf('.'))
					           + postfix
					           + src.substring(src.lastIndexOf('.'));
					$('<img>').attr('src', src_on);
					img.hover(
						function() {
							img.not('.cur').attr('src', src_on);
						},
						function() {
							img.not('.cur').attr('src', src);
						}
					).click( function(e){
						var tbl = img.parent().next().children('table');
						var tbody = tbl.children('tbody');
						var base = tbl.children('caption').text();
						if(img.is('.cur')){
							img.attr('src', src).removeClass('cur');
							tbody.find('tr.pos-'+img.attr('class')).remove();
						}else{
							var posname = img.attr('alt');
							var tr = '<tr class="pos-'+img.attr('class')+'">';
							tr += '<th>'+posname+'</th>';
							tr += '<td><select><option value="0" selected="selected">���򤷤Ƥ�������</option>';
							tr += '<option value="1">1��</option><option value="2">2��</option><option value="3">3��</option>';
							tr += '<option value="9">4���ʾ�</option></select></td>';
							
							/*
							tr += '<td>';
							tr += '<form name="uploaderform" action="/php_libs/orders.php" target="upload_iframe" method="post" enctype="multipart/form-data">';
							tr += '<input type="hidden" name="act" value="update" />';
							tr += '<input type="hidden" name="mode" value="attach" />';
							tr += '<input type="hidden" name="posid" value="'+posid+'" />';
							tr += '<input type="hidden" name="base" value="'+base+'" />';
							tr += '<input type="hidden" name="posname" value="'+posname+'" />';
							tr += '<input type="hidden" name="attachname[]" value="" />';
							tr += '<input type="file" name="attach[]" class="attach" onchange="this.form.submit()" /><img alt="���" src="/common/img/delete.png" class="del_attach" />';
							tr += '</form>';
							tr += '</td>';
							*/
							
							tr += '</tr>';
							img.attr('src', src_on).addClass('cur');
							tbody.append(tr);
							
							var added = tbody.children('tr:last');
							added.find('select').uniform({fileDefaultText:''});
						}
						
					});
				});
			});
			
			$('#pos_wrap .inkbox').each( function(){
				var posimg = $(this).prev('.posimg');
				var img = '';
				$('table tbody tr', this).each( function(){
					var posname = $(this).find('th:first').text();
					if(posname==""){
						$(this).remove();
						return true;
					}
					img = posimg.children('img[alt="'+posname+'"]');
					$(this).attr('class', 'pos-'+img.attr('class'));
					img.attr('src',img.attr('src').replace(/.png/,'_on.png')).addClass('cur');
					
					/*
					var attachname = $(this).find('form input[name^=attachname]').val();
					$(this).find('form .uploader .filename').text(attachname);
					*/
					
				});
			});
		},
		chkInks: function(){
		/*
		*	���󥯻���γ�ǧ
		*/
			var isInks = false;
			if($('#pos_wrap .inkbox select').length>0 && !$('#noprint').prop('checked')){
				$('#pos_wrap .inkbox select').each( function(){
					if($(this).val()!=0){
						isInks = true;
						return false;	// break;
					}
				});
			}else{
				isInks = true;	// �ץ��Ȥʤ����ʤϥ��󥯻����ʤ�
			}
			return isInks;
		},
		updatePosition: function(){
		/*
		*	�ץ��Ȱ��֤ȥ��󥯿���
		*/
			var box = $('#pos_wrap div[class^=ppid_]');
			var noprint = $('#noprint').is(':checked')? 1: 0;
			var ink = 0;
			var posname = "";
			var html = "";
			
			if(noprint==1){
				html = '<input type="hidden" name="noprint" value="1">';
			}else{
				box.each( function(){
					$('.inkbox table tbody tr', this).each( function(){
						ink = $(this).find('select').val();
						if(ink==0) return true;	// continue
						posname = $(this).children('th:first').text();
						html += '<input type="hidden" name="printpos[]" value="'+posname+'_'+ink+'">';
					});
				});
			}
			$("#pos_info").html(html);
		},
		add_attach:function(id){
			var new_row = '<tr><th>ź�եե�����</th><td>&nbsp;</td>';
			new_row += '<td><input type="file" name="attachfile[]" /><ins class="abort">�߼��</ins></td></tr>';
			$('#'+id+' tbody tr:last').before(new_row);
		},
		sendmail_check:function(){
			var f = document.forms.contact_form;
			if(f.S_001.value.trim()==0 && f.M_001.value.trim()==0 && f.L_001.value.trim()==0 && f.XL_001.value.trim()==0 && f.S_005.value.trim()==0 && f.M_005.value.trim()==0 && f.L_005.value.trim()==0 && f.XL_005.value.trim()==0){
				$.msgbox("��������Ϥ��Ƥ���������");
				return false;
			}
			if(!$.chkInks()){
				$.msgbox("�ץ��Ȥ�����֤ȥǥ�����ο�������ꤷ�Ƥ���������");
				return false;
				
			}
			if(f.tel.value.trim()==""){
				$.msgbox("�������ֹ�����Ϥ��Ƥ���������");
				return false;
			}
			
			if(!$.check_email(f.email.value)){
				$.msgbox("�᡼�륢�ɥ쥹�����Ϥ��Ƥ���������");
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
			if(f.addr0.value.trim()=="" || f.addr1.value.trim()==""){
				$.msgbox("����������Ϥ��Ƥ���������");
				return false;
			}
			
			$.updatePosition();	// �ץ��Ȱ��֤ȿ����λ������Ƥ򹹿�
			f.submit();
		}
	});
	
	
	// �ɲä���ź�եե��������
	$('.abort').live('click', function(){
    	$(this).closest('tr').remove();
	});
	
	
	// �᡼������
	$("#sendmail").click( function(){
		$.sendmail_check();
	});
	
	
	// init
	$.itemparam.categoryid = 1;
	$.itemparam.categorykey = 't-shirts';
	$.itemparam.categoryname = 'T�����';
	$.itemparam.itemid = 4;
	$.itemparam.itemcode = '085-cvt';
	$.itemparam.itemname = '�إӡ���������T�����';
	$.itemparam.colorname = '�ۥ磻��';
	$.itemparam.colorcode = '001';
	$.showPrintPosition();
	
});
