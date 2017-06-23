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
				$.msgbox("お名前を入力してください。");
				return false;
			}
			if(f.zipcode.value.trim()==""){
				$.msgbox("郵便番号を入力してください。");
				return false;
			}
			if(f.addr.value.trim()==""){
				$.msgbox("ご住所を入力してください。");
				return false;
			}
			*/
			var a1 = $(':radio[name=a1]:checked').length;
			var a5 = $(':radio[name=a5]:checked').length;
			var a6 = $(':radio[name=a6]:checked').length;
			var a7 = $(':radio[name=a7]:checked').length;
			var a14 = $(':radio[name=a14]:checked').length;
			if(a1==0 || a5==0 || a6==0 || a7==0 || a14==0){
				$.msgbox("チェック項目で選択されていない項目がございます。ご確認下さい。");
				return false;
			}
			
			var a2 = $('textarea[name=a2]').val().trim();
			var a8 = $('textarea[name=a8]').val().trim();
			var a9 = $('textarea[name=a9]').val().trim();
			var a10 = $('textarea[name=a10]').val().trim();
			var a12 = $('textarea[name=a12]').val().trim();
			var a13 = $('textarea[name=a13]').val().trim();
			if(a2=="" || a8=="" || a9=="" || a10=="" || a12=="" || a13==""){
				$.msgbox("未回答の項目がございます。ご確認下さい。");
				return false;
			}
			
			f.submit();
		}
	});
	
	
	// お名前にフォーカス
	//document.forms.contact_form.customername.focus();
	
});
