/*
*	Takahama Life Art
*	
*	request module
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
			if(f.customername.value.trim()==""){
				$.msgbox("��̾�������Ϥ��Ƥ���������");
				return false;
			}
			if(f.message.value.trim()==""){
				$.msgbox("��å����������Ϥ��Ƥ���������");
				return false;
			}
			if(f.addr0.value.trim()==""){
				$.msgbox("����������Ϥ��Ƥ���������");
				return false;
			}
			
			f.submit();
		},
		setAddr: function(zipcode,addr1,addr2,addr3){
			$('#zipcode').val(zipcode).blur();
			$('#addr0').val(addr1);
			$('#addr1').val(addr2);
			$('#addr2').val(addr3);
			$('.closeModalBox', '#modalBox').click();
		},
		check_zipcode:function(zipcode){
		  	if( ! zipcode ) return false;
		  	if( 0 == zipcode.length ) return false;
		  	if( ! zipcode.match( /^[0-9]{3}[-]?[0-9]{0,4}$/ ) ) return false;

		  	return true;
		}
	});
	
	
	/* ���긡�� */
	$('#find_addr').click( function(){
		var val = $('#zipcode').val();
		if(!$.check_zipcode(val)){
			$.msgbox('͹���ֹ�򤴳�ǧ������������ ����ʾ�����Ϥ�ɬ�פǤ���');
			return;
		}
		
		$.post('/php_libs/getAddr.php', {'mode':'zipcode','parm':val}, function(r){
			var addr = "";
			var list = '<ul class="address_list">';
			var lines = r.split(';');
			if(lines.length>1){
				for(var i=0; i<lines.length; i++){
					addr = lines[i].split(',');
					list += '<li onclick="$.setAddr(\''+addr[0]+'\',\''+addr[1]+'\',\''+addr[2]+'\',\''+addr[3]+'\')">'+addr[0]+' '+addr[1]+addr[2]+addr[3]+'</li>';
				}
				list += '</ul>';
				list += '<input class="closeModalBox" type="hidden" name="customCloseButton" value="" />';
				
				$.msgbox(list);
				
			}else{
				addr = lines[0].split(',');
				if(!addr[1]) return;
				$.setAddr(addr[0], addr[1], addr[2], addr[3]);
			}
		});
	});
	
	
	// ��̾���˥ե�������
	document.forms.request_form.customername.focus();
	
});
