/*
*	Takahama Life Art
*	
*	enquete module
*	charset euc-jp
*/
	
$(function(){

	jQuery.extend({
		sendmail_check:function(my){
			var f = my.form;
			/*
			if(f.customername.value.trim()==""){
				$.msgbox("��̾�������Ϥ��Ƥ���������");
				return false;
			}
			if(f.zipcode.value.trim()==""){
				$.msgbox("͹���ֹ�����Ϥ��Ƥ���������");
				return false;
			}
			if(f.addr.value.trim()==""){
				$.msgbox("����������Ϥ��Ƥ���������");
				return false;
			}
			*/
			var a1 = $(':radio[name=a1]:checked').length;
			var a5 = $(':radio[name=a5]:checked').length;
			var a6 = $(':radio[name=a6]:checked').length;
			var a7 = $(':radio[name=a7]:checked').length;
			var a14 = $(':radio[name=a14]:checked').length;
			if(a1==0 || a5==0 || a6==0 || a7==0 || a14==0){
				$.msgbox("�����å����ܤ����򤵤�Ƥ��ʤ����ܤ��������ޤ�������ǧ��������");
				return false;
			}
			
			var a2 = $('textarea[name=a2]').val().trim();
			var a8 = $('textarea[name=a8]').val().trim();
			var a9 = $('textarea[name=a9]').val().trim();
			var a10 = $('textarea[name=a10]').val().trim();
			var a12 = $('textarea[name=a12]').val().trim();
			var a13 = $('textarea[name=a13]').val().trim();
			if(a2=="" || a8=="" || a9=="" || a10=="" || a12=="" || a13==""){
				$.msgbox("̤�����ι��ܤ��������ޤ�������ǧ��������");
				return false;
			}
			
			f.submit();
		}
	});
	
	
	// ��̾���˥ե�������
	//document.forms.contact_form.customername.focus();
	
});
