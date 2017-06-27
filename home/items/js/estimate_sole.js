/**
*	Takahama Life Art
*	見積　（単一商品）
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
		*	サムネイルの変更
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
		//inputボックスをクリックすると「0」を消す
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
		*	サイズごとの枚数入力フォーム
		*	@itemid			アイテムID
		*	@colorcode		アイテムカラーコード
		*	@volume			サイズIDをキーにした枚数のハッシュ
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
						size_body = '<th data-label="1枚単価">'+$.addFigure(val['cost'])+' 円</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'" data-label="'+val['name']+'">';
						size_body += '<input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" onchange="$.addOrder();" onblur="$.blurNumber(this);" /></td>';
					}else if(cost != val['cost'] || (val['id']>(++pre_sizeid) && val['id']>10)){	// 単価が違うかまたは、サイズ160以下を除きサイズが連続していない
						size_table += '<tr class="heading">'+size_head+'</tr>';
						size_table += '<tr>'+size_body+'</tr>';
						
						pre_sizeid = val['id'];
						cost = val['cost'];
						size_head = '<th></th><th>'+val['name']+'</th>';
						size_body = '<th data-label="1枚単価">'+$.addFigure(val['cost'])+' 円</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'" data-label="'+val['name']+'">';
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
		*	プリント位置と色数の変更イベント設定
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
							ink += '<p>使用インク<select class="ink_'+posid+'" onchange="$.addOrder();"><option value="0" selected="selected">選択してください</option>';
							ink += '<option value="1">1色</option><option value="2">2色</option><option value="3">3色</option>';
							ink += '<option value="9">4色以上</option></select></p>';
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
		*	商品カテゴリーの変更
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
		*	商品の変更
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
		*	プリント位置画像（絵型）とインク色数指定の生成
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
		*	アイテムが固定の場合に対応
		*/
			'curitemid': ''
		},
		printparam:{
		/*
		*	プリント代計算で使用するパラメーター
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
		*	見積計算のハッシュを生成する
		*	@第一引数　false: 枚数のチェックを行なわない
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
			
			// アイテムカラー名
			var colorName = $('#price_wrap .item_colors .thumb_h .notes_color').text();
			
			/*
			$('#price_wrap table tbody tr').each( function(){
				var v = ($(this).find('input.forNum').val()-0);
				if(v==0) return true;
				size_id[a] = $(this).children('th').attr('class').split('_')[1];
				size[a] = $(this).children('th').text();
				cost[a] = $(this).children('td:first').text().split('円')[0].replace(/,/g, '');
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
				$.resetResult('枚数をご指定ください。');
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
		*	プリント代を取得
		*/
			if($.printparam.itemid.length==0){
				$.resetResult();
				return;
			}
			
			var output = false;
			if(arguments.length>0) output=arguments[0];
			
			/*
			*	アイテムのサイズ毎で保持している枚数をアイテム毎に集計する
			*	（アイテムとサイズの昇順になっていること）
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
			
			var optionId = $.printparam.color[0]!='ホワイト'? 1: 0;
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
		*	見積テーブルから商品を削除
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
		*	見積テーブル内で枚数の変更
		*	同じサイズの商品で指定されているプリント位置に対応する枚数も変更する
		*
			var row = $(e.target).closest('tr');
			var key = row.attr('class').split('_');
			var amount = $.check_NaN(e.target);
			for(var i=0; i<$.printparam.itemid.length; i++){
				if($.printparam.itemid[i]==key[0] && $.printparam.sizeid[i]==key[1]){
					$.printparam.amount[i] = amount;	// 枚数を上書
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
					$('td:first', tr[0]).html($('#result span').text()+' 円');
					$('td:first', tr[1]).text($('#perone span').text()+' 円');
					$('td:first', tr[2]).text($('#totamount span').text()+' 枚');
					$('#mail_comment span', self).text(location.pathname);
					var form = self.children();
					$.msgbox(form, 692);
				}
			});
		},
		updateItem: function(){
		/*
		*	カートに商品を追加更新
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
			// 全サイズを上書する
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
		*	プリント位置とインク色数を更新
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
	
	
	/* 注文フォームへ遷移 */
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
		
		// メーカー「ザナックス」の場合にポップアップ
		if($(this).hasClass('popup')){
			$.confbox.show(
				'<h3 class="fontred">★要確認</h3>'+
				'<div style="padding:0.5em;"><p>'+
					'このアイテムはメーカーの在庫状況が不安定な為<br>'+
					'お申し込みフォームからご指定頂きました枚数の在庫確認を行った後<br>'+
					'弊社から「在庫有無・納期」のご連絡をさせて頂きます。<br>'+
					'メーカに在庫が無い場合は受注生産となり、納期を2~3週間頂く場合がございます。'+
				'</p>'+
				'<p class="note" style="margin-bottom:1em;"><span>※</span>在庫状況によっては、ご希望に添えない場合がございます。</p>'+
				'<p style="margin-bottom:1em;">大変ご不便おかけしますが、何卒宜しくお願い致します。</p>'+
				'<p>お急ぎの方はお電話でのお問い合わせをお願いします。</p>'+
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
	
    
	/* みんなにメール一斉送信 */
	$('#mass-email').click( function(){ $.show_mailform(); });
	
	$('#multimailform .send_mail').on('click', function(){
		var email = [];
		var msg="";
		if($('#multimailform input[name="myname"]').val().trim()==""){
			msg = "お名前を入力して下さい。";
		}else if($('#multimailform input[name="myemail"]').val().trim()==""){
			msg = "送信者のメールアドレスを入力して下さい。";
		}else{
			var chk = false;
			$('.half_l .email, .half_r .email', '#multimailform').each( function(){
				var tmp = $(this).val().trim();
				if(tmp!=""){
					chk = true;
					email.push(tmp);
				}
			});
			if(!chk) msg = "送信先のメールアドレスを入力して下さい。";
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
					posname += '　インク：'+ink+' 色';
				}else{
					posname += '　インク： フルカラー';
				}
				pos.push(posname);
			}
		});
		
		var subject = $('#multimailform input[name="subject"]').val().trim();
		if(subject==""){
			subject = "お見積り";
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
					alert("メールの送信でエラーが発生しています。\n恐れ入りますが、もう一度送信をお願い致します。");
				}else if(r[0]=='success'){
					alert('お見積りのメールを送信いたしました。');
				}else{
					var res = r.toString();
					res = res.replace(/,/g, "\n");
					alert('以下のアドレスに送信できませんでした。\nメールアドレスをご確認ください。\n\n'+res);
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


	/* 計算開始 */
	//$('#calc').click( function(){ $.addOrder(); });
	
	
	/* 白色と白以外の変更
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
