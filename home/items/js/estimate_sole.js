/**
*	Takahama Life Art
*	���ѡ���ñ�쾦�ʡ�
*	charset euc-jp
*/

$(function(){
	
	jQuery.extend({
		init: function(){
			$('#color_thumb li img').imagesLoaded(function(){$('#item_colors').fadeIn();});
			$.my.curitemid = _ITEM_ID;
			$.setPrintposEvent();
			var colorcode = $('#item_image_l').attr('src');
			colorcode = colorcode.slice(colorcode.lastIndexOf('_')+1, colorcode.lastIndexOf('.'));
			$.showSizeform($.my.curitemid, colorcode, []);
		},
		changeThumb: function(my){
		/*
		*	����ͥ�����ѹ�
		*/
			var colorcode = my.attr("alt");
			var colorname = my.attr("title");
			$(".notes_color").html(colorname);
			$(".color_thumb li.nowimg").removeClass("nowimg");
			my.parent().addClass("nowimg");
			
			var tmp = {};
			$('#price_wrap table tbody tr:odd td').each( function(index){
				var sizeid = $(this).attr('class').split('_')[1];
				tmp[sizeid] = $(this).find('input.forNum').val();
			});
			
			$.showSizeform($.my.curitemid, colorcode, tmp);
		},
		//input�ܥå����򥯥�å�����ȡ�0�פ�ä�
		focusNumber: function(my){
			if($(my).val()=="0") {
				$(my).val("");
			}
		},
		blurNumber: function(my){
			if($(my).val()=="") {
				$(my).val("0");
			}
		},
		showSizeform: function(itemid, colorcode, volume){
		/*
		*	���������Ȥ�������ϥե�����
		*	@itemid			�����ƥ�ID
		*	@colorcode		�����ƥ५�顼������
		*	@volume			������ID�򥭡��ˤ�������Υϥå���
		*/
			$.getJSON($.TLA.api+'?callback=?', {'act':'sizeprice', 'itemid':itemid, 'colorcode':colorcode, 'output':'jsonp'}, function(r){
		    	var pre_sizeid = 0;
		    	var cost = 0;
		    	var amount = 0;
		    	var size_head = '';
		    	var size_body = '';
	    		var sum = 0;
	    		var size_table = '';
		    	$.each(r, function(key, val){
		    		if(typeof volume[val.id]=='undefined'){
		    			amount = 0;
		    		}else{
		    			amount = volume[val.id]-0;
					}
					sum += amount;
					if(key==0){
						pre_sizeid = val['id'];
						cost = val['cost'];
						size_head = '<th></th><th>'+val['name']+'</th>';
						size_body = '<th data-label="1��ñ��">'+$.addFigure(val['cost'])+' ��</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'" data-label="'+val['name']+'">';
						size_body += '<input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" onchange="$.addOrder();" onblur="$.blurNumber(this);" /></td>';
					}else if(cost != val['cost'] || (val['id']>(++pre_sizeid) && val['id']>10)){	// ñ�����㤦���ޤ��ϡ�������160�ʲ��������������Ϣ³���Ƥ��ʤ�
						size_table += '<tr class="heading">'+size_head+'</tr>';
						size_table += '<tr>'+size_body+'</tr>';
						
						pre_sizeid = val['id'];
						cost = val['cost'];
						size_head = '<th></th><th>'+val['name']+'</th>';
						size_body = '<th data-label="1��ñ��">'+$.addFigure(val['cost'])+' ��</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'" data-label="'+val['name']+'">';
						size_body += '<input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" onchange="$.addOrder();" onblur="$.blurNumber(this);" /></td>';
					}else{
						pre_sizeid = val['id'];
						size_head += '<th>'+val['name']+'</th>';
						size_body += '<td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'" data-label="'+val['name']+'">';
						size_body += '<input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" onchange="$.addOrder();" onblur="$.blurNumber(this);" /></td>';
					}
		        });
		        size_table += '<tr class="heading">'+size_head+'</tr>';
				size_table += '<tr>'+size_body+'</tr>';
				$('table:first tbody', '#price_wrap').html(size_table);
				
				$.addOrder(false);
			});
		
		},
		setPrintposEvent: function(){
		/*
		*	�ץ��Ȱ��֤ȿ������ѹ����٥������
		*/
			$('#pos_wrap div').each( function(){
				$(this).children('img:not(:nth-child(1))').each(function() {
					var postfix = '_on';
					var img = $(this);
					var posid = img.parent().attr('class').split('_')[1];
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
						if(img.is('.cur')){
							img.attr('src', src).removeClass('cur');
							$('#inktarget'+posid).find('.pos-'+img.attr('class')).remove();
						}else{
							var cur = img.siblings('.cur');
							if (cur.length) {
								cur.attr('src',cur.attr('src').replace(/_on.png/,'.png')).removeClass('cur');
								$('#inktarget'+posid).find('.pos-'+cur.attr('class')).remove();
							}
							var ink = '<div class="inks pos-'+img.attr('class')+'">';
							ink += '<p class="posname_'+posid+'">'+img.attr('alt')+'</p>';
							ink += '<p>���ѥ���<select class="ink_'+posid+'" onchange="$.addOrder();"><option value="0" selected="selected">���򤷤Ƥ�������</option>';
							ink += '<option value="1">1��</option><option value="2">2��</option><option value="3">3��</option>';
							ink += '<option value="9">4���ʾ�</option></select></p>';
							ink += '</div>';
							
							img.attr('src', src_on).addClass('cur');
							$('#inktarget'+posid).append(ink);
						}
						
						$.addOrder();
					});
				});
			});
		},
		changeCategory: function(my){
		/*
		*	���ʥ��ƥ��꡼���ѹ�
		*
			var categoryid = my.options[my.selectedIndex].value;
			$.getJSON($.TLA.api+'?callback=?', {'act':'item', 'categoryid':categoryid, 'output':'jsonp'}, function(r){
				var option = '';
	        	jQuery.each(r, function(key, val){
	        		option += '<option value="'+val.id+'"';
	        		if(key==0){
	        			option += ' selected="selected"';
	        		}
					option += '>'+val.name+'</option>';
					$('#item_selector').html(option);
				});
				$.changeItem();
			});
		*/
		},
		changeItem: function(){
		/*
		*	���ʤ��ѹ�
		*
			$.my.curitemid = $('#item_selector').val();
			$.ajax({url:'/php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
				data:{'act':'price','itemid':$.my.curitemid}, success: function(r){
					var dat = r.split('|');
					if(dat[1]){
						$('#switch_color').show();
					}else{
						$('#switch_color').hide();
					}
					$('#price_wrap table tbody').html(dat[0]);
					$('#mode1').val(['white']);
				}
			});
			
			$.showPrintPosition();
		*/
		},
		showPrintPosition: function(){
		/*
		*	�ץ��Ȱ��ֲ����ʳ����ˤȥ��󥯿������������
		*
			$.ajax({url:'/php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
				data:{'act':'position','itemid':$.my.curitemid}, success: function(r){
					$('#pos_wrap tbody').html(r);
					$.setPrintposEvent();
					$.resetResult();
				}
			});
		*/
		},
		my:{
		/*
		*	�����ƥब����ξ����б�
		*/
			'curitemid': ''
		},
		printparam:{
		/*
		*	�ץ�����׻��ǻ��Ѥ���ѥ�᡼����
		*/
			'itemid':[],
			'sizeid':[],
			'size':[],
			'cost':[],
			'amount':[],
			'pos':[],
			'ink':[],
			'color':[]
		},
		clearparam: function(){
			$.printparam.itemid = [];
			$.printparam.sizeid = [];
			$.printparam.size = [];
			$.printparam.cost = [];
			$.printparam.amount = [];
			$.printparam.pos = [];
			$.printparam.ink = [];
			$.printparam.color = [];
		},
		resetResult: function(){
			$('#totamount span, #result span, #perone span, #baseprice span, #salestax span').text('0');
			if(arguments.length>0) $.msgbox(arguments[0]);
		},
		addOrder: function(){
		/*
		*	���ѷ׻��Υϥå������������
		*	@��������false: ����Υ����å���Ԥʤ�ʤ�
		*/
			var item_id = $.my.curitemid;
			var size_id = [];
			var size = [];
			var cost = [];
			var posi = [];
			var inks = [];
			var volm = [];
			var a = 0;
			
			$.clearparam();
			
			// �����ƥ५�顼̾
			var colorName = $('#price_wrap .item_colors .thumb_h .notes_color').text();
			
			/*
			$('#price_wrap table tbody tr').each( function(){
				var v = ($(this).find('input.forNum').val()-0);
				if(v==0) return true;
				size_id[a] = $(this).children('th').attr('class').split('_')[1];
				size[a] = $(this).children('th').text();
				cost[a] = $(this).children('td:first').text().split('��')[0].replace(/,/g, '');
				volm[a] = v;
				a++
			});
			*/
			
			$('#price_wrap table tbody tr:odd td').each( function(index){
				var v = $(this).find('input.forNum').val();
				if(v==0) return true;
				var param = $(this).attr('class').split('_');
				size_id[a] = param[1];
				size[a] = param[2];
				cost[a] = param[3];
				volm[a] = v;
				a++;
			});
			
			if(a==0 && arguments[0]!==false){
				$.resetResult('����򤴻��꤯��������');
				return;
			}
			
			a = 0;
//			$('#pos_wrap table select').each( function(){
			$('#pos_wrap select').each( function(){
				var ink = $(this).val()-0;
				var posname = $(this).parent().prev().text();
				if(ink>0){
					posi[a] = posname;
					inks[a] = ink;
					a++;
				}
			});
			
			if(a==0){
				$.resetResult();
				return;
			}
			
			for(var x=0; x<size.length; x++){	
				for(var y=0; y<posi.length; y++){
					$.printparam.itemid.push(item_id);
					$.printparam.sizeid.push(size_id[x]);
					$.printparam.size.push(size[x]);
					$.printparam.cost.push(cost[x]);
					$.printparam.amount.push(volm[x]);
					$.printparam.pos.push(posi[y]);
					$.printparam.ink.push(inks[y]);
					$.printparam.color.push(colorName);
				}
			}
			
			$.calcPrice();
		},
		calcPrice: function(){
		/*
		*	�ץ���������
		*/
			if($.printparam.itemid.length==0){
				$.resetResult();
				return;
			}
			
			var output = false;
			if(arguments.length>0) output=arguments[0];
			
			/*
			*	�����ƥ�Υ���������ݻ����Ƥ�������򥢥��ƥ���˽��פ���
			*	�ʥ����ƥ�ȥ������ξ���ˤʤäƤ��뤳�ȡ�
			*/
			var tmpVol = 0;
			var presize = 0;
			var itemsum = 0;			
			for(var i=0; i<$.printparam.itemid.length; i++){
				vol = $.printparam.amount[i]-0;
				if(i==0){
					tmpVol = $.printparam.amount[i]-0;
					itemsum = ($.printparam.cost[i]-0) * ($.printparam.amount[i]-0);
				}else if(presize!=$.printparam.size[i]){
					tmpVol += $.printparam.amount[i]-0;
					itemsum += ($.printparam.cost[i]-0) * ($.printparam.amount[i]-0);
				}
				presize = $.printparam.size[i];
			}
			
			var amount = $.printparam.amount.slice(0);
			for(var j=0; j<amount.length; j++){
				amount[j] = tmpVol;
			}
			
			var optionId = $.printparam.color[0]!='�ۥ磻��'? 1: 0;
			var inkjetOption = {};
			inkjetOption[optionId] = amount[0];
			var param = {'act':'printfee', 'output':'jsonp', 'args':[]};
			var args = [];
			var existPos = {};
			for (var i=0; i<$.printparam.itemid.length; i++) {
				if (existPos.hasOwnProperty($.printparam.pos[i])) continue;
				args[i] = { 
					'itemid':$.printparam.itemid[i], 
					'amount':amount[i], 
					'pos':$.printparam.pos[i], 
					'ink':$.printparam.ink[i],
					'size':0,
					'option':inkjetOption
				};
				existPos[$.printparam.pos[i]] = true;
			}
			param['args'] = args;
			$.getJSON($.TLA.api+'?callback=?', param, function(r){
//				var str = new String(r.volume);
//				if(!str.match(/^\d+$/)) return;
				
			    var base = itemsum + (r.printfee-0);
			    var tax = Math.floor( base * (r.tax/100) );
			    var result = Math.floor( base * (1+r.tax/100) );
				var perone = Math.ceil(result/tmpVol);
				
				if(!output){
					$('#baseprice span').text($.addFigure(base));
					$('#salestax span').text($.addFigure(tax));
					$('#result span').text($.addFigure(result));
					$('#totamount span').text(tmpVol);
					$('#perone span').text($.addFigure(perone));
				}else{
					if(typeof output.attr('value')=='undefined') output.text($.addFigure(perone));
					else output.val($.addFigure(perone));
				}
			});
		},
		delOrder: function(e){
		/*
		*	���ѥơ��֥뤫�龦�ʤ���
		*
			var row = $(e.target).closest('tr');
			var key = row.attr('class').split('_');
			row.remove();
			for(var i=0; i<$.printparam.itemid.length; i++){
				if($.printparam.itemid[i]==key[0] && $.printparam.sizeid[i]==key[1]){
					for(var key in $.printparam){
						$.printparam[key].splice(i,1);
					}
					i--;
				}
			}
			
			$.calcPrice();
		*/
		},
		changeAmount: function(e){
		/*	
		*	���ѥơ��֥����������ѹ�
		*	Ʊ���������ξ��ʤǻ��ꤵ��Ƥ���ץ��Ȱ��֤��б�����������ѹ�����
		*
			var row = $(e.target).closest('tr');
			var key = row.attr('class').split('_');
			var amount = $.check_NaN(e.target);
			for(var i=0; i<$.printparam.itemid.length; i++){
				if($.printparam.itemid[i]==key[0] && $.printparam.sizeid[i]==key[1]){
					$.printparam.amount[i] = amount;	// �������
				}
			}
			$.calcPrice();
		*/
		},
		show_mailform: function(){
			$.ajax({
				url:'/common/txt/mailform/multisend.txt', async:false, type:'get', dataType:'text',
				success: function(r){
					var self = $('<div>').html(r);
					var tr = $('#mail_text tr', self);
					$('td:first', tr[0]).html($('#result span').text()+' ��');
					$('td:first', tr[1]).text($('#perone span').text()+' ��');
					$('td:first', tr[2]).text($('#totamount span').text()+' ��');
					$('#mail_comment span', self).text(location.pathname);
					var form = self.children();
					$.msgbox(form, 692);
				}
			});
		},
		updateItem: function(){
		/*
		*	�����Ȥ˾��ʤ��ɲù���
		*/
			var postData = {};
			var mode = 'update';
			var isResult = false;
			
			postData = {'act':'update', 'mode':'items'};
			postData.categoryid = _CAT_ID;
			postData.categorykey = _CAT_KEY;
			postData.categoryname = _CAT_NAME;
			postData.itemid = $.my.curitemid;
			postData.itemcode = _ITEM_CODE;
			postData.itemname = _ITEM_NAME;
			postData.posid = _POS_ID;
//alert("updateItem 111");		
			// �����������񤹤�
			var color_name = $('.thumb_h .notes_color', '#price_wrap').text();
			var color_code = $('.color_thumb li.nowimg img', '#price_wrap').attr("alt");
			var totAmount = 0;
			postData['sizeid'] = [];
			postData['sizename'] = [];
			postData['cost'] = [];
			postData['amount'] = [];
			postData['colorcode'] = [];
			postData['colorname'] = [];
			
			$('#price_wrap table tbody tr:odd td').each( function(index){
//alert("updateItem 222");		
				var v = $(this).find('input.forNum').val();
				var param = $(this).attr('class').split('_');
				postData['sizeid'][index] = param[1];
				postData['sizename'][index] = param[2];
				postData['cost'][index] = param[3];
				postData['amount'][index] = v;
				postData['colorcode'][index] = color_code;
				postData['colorname'][index] = color_name;
				
				totAmount += v;
			});
			
//alert("updateItem 333");		
			if(totAmount==0){
//alert("updateItem 444");		
				return false;
			}
			
			var curRow = 0;
//alert("updateItem 555");		
			$.ajax({url:'/php_libs/orders.php', async:false, type:'POST', dataType:'json', data:postData, 
				success:function(r){
//alert("updateItem 666");		
					if(r.length!=0){
						isResult = true;
					}
				}
			});
		
			return isResult;
		},
		updatePosition: function(){
//alert("aaa");
		/*
		*	�ץ��Ȱ��֤ȥ��󥯿����򹹿�
		*/
			var posid = _POS_ID;
			var base = [];
			var posname = [];
			var ink = [];
			var attach = [];
			var isResult = false;
			
//			$('#pos_wrap table tbody tr:eq(3) td').each( function(){
			$('#pos_wrap div').each( function(){
				var div_id = $(this).attr("id");
				if(typeof div_id!='undefined' && div_id != null && div_id.indexOf("inktarget") != -1) {
					var base_name = $(this).attr('class');
					$('div.inks', this).each( function(){
	//alert("bbb");
						var v = $(this).find('select').val();
						if(v>0){
	//alert("ccc");
							base.push(base_name);
							posname.push( $(this).children('p:first').text() );
							ink.push( v );
							
							isResult = true;
						}
					});
				}
			});
			
//alert("ddd");
			if(isResult){
//alert("fff");
				$.ajax({url:'/php_libs/orders.php', async:false, type:'POST', dataType:'json', 
					data:{'act':'update','mode':'design', 'posid':posid, 'base':base, 'posname':posname, 'ink':ink, 'attachname':attach}, success: function(r){
						if(r.length!=0){
							isResult = r;
						}
					}
				});
			}
			
			return isResult;
		}
	});
	
	
	/* ��ʸ�ե���������� */
	$('#btnOrder, #btnOrder_up').click( function(){
		var f = $(this).closest("form");
		var func = function(){
			var step = 1;
			if($('#result span').text()!='0'){
				if($.updateItem()){
					if($.updatePosition()){
						step = 3;
					}
				}
			}
			document.getElementById("update").value = step;
			f.submit();
		};
		
		// �᡼�����֥��ʥå����פξ��˥ݥåץ��å�
		if($(this).hasClass('popup')){
			$.confbox.show(
				'<h3 class="fontred">���׳�ǧ</h3>'+
				'<div style="padding:0.5em;"><p>'+
					'���Υ����ƥ�ϥ᡼�����κ߸˾������԰���ʰ�<br>'+
					'���������ߥե����फ�餴����ĺ���ޤ�������κ߸˳�ǧ��Ԥä���<br>'+
					'���Ҥ���ֺ߸�̵ͭ��Ǽ���פΤ�Ϣ��򤵤���ĺ���ޤ���<br>'+
					'�᡼���˺߸ˤ�̵�����ϼ��������ȤʤꡢǼ����2~3����ĺ����礬�������ޤ���'+
				'</p>'+
				'<p class="note" style="margin-bottom:1em;"><span>��</span>�߸˾����ˤ�äƤϡ�����˾��ź���ʤ���礬�������ޤ���</p>'+
				'<p style="margin-bottom:1em;">���Ѥ����ؤ��������ޤ�������´���������ꤤ�פ��ޤ���</p>'+
				'<p>���ޤ������Ϥ����äǤΤ��䤤��碌�򤪴ꤤ���ޤ���</p>'+
				'<address>0120-130-428</address></div>', 
				function(){
					if($.confbox.result.data){
						func();
					}else{
						// Do nothing.
					}
				}
			);
		}else{
			func();
		}
	});
	
    
	/* �ߤ�ʤ˥᡼��������� */
	$('#mass-email').click( function(){ $.show_mailform(); });
	
	$('#multimailform .send_mail').on('click', function(){
		var email = [];
		var msg="";
		if($('#multimailform input[name="myname"]').val().trim()==""){
			msg = "��̾�������Ϥ��Ʋ�������";
		}else if($('#multimailform input[name="myemail"]').val().trim()==""){
			msg = "�����ԤΥ᡼�륢�ɥ쥹�����Ϥ��Ʋ�������";
		}else{
			var chk = false;
			$('.half_l .email, .half_r .email', '#multimailform').each( function(){
				var tmp = $(this).val().trim();
				if(tmp!=""){
					chk = true;
					email.push(tmp);
				}
			});
			if(!chk) msg = "������Υ᡼�륢�ɥ쥹�����Ϥ��Ʋ�������";
		}
		if(msg!=""){
			alert(msg);
			return;
		}else{
			jQuery.fn.modalBox('close');
		}
		
		var pos = [];
		$('#pos_wrap tbody tr:last td select').each( function(){
			var ink = $(this).val()-0;
			if(ink>0){
				var id = $(this).attr('class').split('_')[1];
				var posname = $('#pos_wrap tbody tr:eq(1)').find('.posname_'+id).text();
				if(ink!=9){
					posname += '�����󥯡�'+ink+' ��';
				}else{
					posname += '�����󥯡� �ե륫�顼';
				}
				pos.push(posname);
			}
		});
		
		var subject = $('#multimailform input[name="subject"]').val().trim();
		if(subject==""){
			subject = "�����Ѥ�";
		}
		
		var tr = $('#mail_text tr', '#multimailform');
		var total = $('td:first', tr[0]).html();
		var per = $('td:first', tr[1]).text();
		var amount = $('td:first', tr[2]).text();
		var item_name = $('#item_title h1').text();
		var color_name = $('#notes_color').text();
		var message = $('textarea', '#multimailform').val();
		var f = document.forms.multimailform;
		var prm = {'subject':subject, 'myname':f.myname.value, 'myemail':f.myemail.value, 'email':email, 'message':message,
					'pageurl':location.pathname, 'total':total, 'per':per, 'amount':amount,
					'item_name':item_name, 'color_name':color_name, 'pos':pos
				};
		
		$.ajax({
			url:'/php_libs/multisend.php', type:'get', async:false, dataType:'json', data:prm, success:function(r){
				if(r.length==0){
					alert("�᡼��������ǥ��顼��ȯ�����Ƥ��ޤ���\n��������ޤ������⤦���������򤪴ꤤ�פ��ޤ���");
				}else if(r[0]=='success'){
					alert('�����Ѥ�Υ᡼��������������ޤ�����');
				}else{
					var res = r.toString();
					res = res.replace(/,/g, "\n");
					alert('�ʲ��Υ��ɥ쥹�������Ǥ��ޤ���Ǥ�����\n�᡼�륢�ɥ쥹�򤴳�ǧ����������\n\n'+res);
				}
			}
		});
	});
	
	
	/* change thumbnails */
	$(".color_thumb li img").on('click', function(){
		$.changeThumb($(this));
	});


    /* initialize */
	$.init();


	/* �׻����� */
	//$('#calc').click( function(){ $.addOrder(); });
	
	
	/* �򿧤���ʳ����ѹ�
	$('#switch_color :radio').change( function(){
		var tmp = [];
		$('#price_wrap table tbody tr').each( function(index){
			tmp[index] = $(this).find('input.forNum').val();
		});
		var colormode = $(this).val();
		$.ajax({url:'/php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
			data:{'act':'price','itemid':$.my.curitemid, 'colormode':colormode}, success: function(r){
				var dat = r.split('|');
				$('#price_wrap table tbody').html(dat[0]);
				$('#price_wrap table tbody tr').each( function(index){
					$(this).find('input.forNum').val(tmp[index]);					
				});
				$('#price_wrap table input').change( function(){ $.addOrder(); });
				$.addOrder();
			}
		});
	});
	*/
});
