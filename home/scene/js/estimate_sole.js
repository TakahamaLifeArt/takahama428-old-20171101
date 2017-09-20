/**
*	Takahama Life Art
*	見積　（単一商品）
*	charset euc-jp
*/

$(function(){
	
	jQuery.extend({
		init: function(){
			$('#color_thumb li img').imagesLoaded(function(){$('#item_colors').fadeIn();});
			$('#category_selector').val('t-shirts');
			$('#item_selector').val($.prop.curitemid);
			$.setPrintposEvent();
			$.showSizeform($.prop.curitemid, '001', []);
		},
		msgbox_old: function(msg){
		/*
		*	メッセージボックス
		*	@msg		表示するメッセージ文
		*	@args[1]	任意の幅(Integre)	52 is padding
		*/
			if($('#message_wrapper').length==0){
			// タグ要素がない場合に書き込む
				$('html body').append('<div id="message_wrapper" style="display:none;"></div>');
			}
			$('#message_wrapper').html(msg);
			var w = 600;
			if(arguments.length>1) w = 52+(arguments[1]-0);
			jQuery.fn.modalBox({
				directCall : {
					element : '#message_wrapper'
				},
				positionTop : $(window).scrollTop(),
				setWidthOfModalLayer : w
			});
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
			
			$.showSizeform($.prop.curitemid, colorcode, tmp);
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
						size_body = '<th>'+$.addFigure(val['cost'])+' 円</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'">';
						size_body += '<input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onchange="$.addOrder();" /></td>';
					}else if(cost != val['cost'] || (val['id']>(++pre_sizeid) && val['id']>10)){	// 単価が違うかまたは、サイズ160以下を除きサイズが連続していない
						size_table += '<tr class="heading">'+size_head+'</tr>';
						size_table += '<tr>'+size_body+'</tr>';
						
						pre_sizeid = val['id'];
						cost = val['cost'];
						size_head = '<th></th><th>'+val['name']+'</th>';
						size_body = '<th>'+$.addFigure(val['cost'])+' 円</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'">';
						size_body += '<input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onchange="$.addOrder();" /></td>';
					}else{
						pre_sizeid = val['id'];
						size_head += '<th>'+val['name']+'</th>';
						size_body += '<td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'">';
						size_body += '<input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onchange="$.addOrder();" /></td>';
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
			$('#pos_wrap table tr:eq(1) div').each( function(){
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
						var tbl = img.parent().next().children('table');
						var tbody = tbl.children('tbody');
						var base = tbl.children('caption').text();
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
		changeItem: function(){
		/*
		*	商品の変更
		*/
			$.prop.curitemid = $('#item_selector').val();
			$('.color_thumb').html('');
			$('.item_colors', '#price_wrap').addClass('throbber');
			$('.color_thumb').fadeOut('fast', function(e){
				$.getJSON($.TLA.api+'?callback=?', {'act':'itemattr', 'itemid':$.prop.curitemid, 'output':'jsonp'}, function(r){
					var color_count = 0;
					var thumbs = '';
					var categorykey = '';
					var itemcode = '';
					var itemname = '';
					var curcolorcode = '';
					var curcolorname = '';
					var path = '';
					$.each(r.category, function(cat, catname){
						categorykey = cat;
					});
					$.each(r.name, function(itemcode, itemname){
						switch(itemcode){
							case '5404':
							case '101-lvc':
							case '138-rbb':
							case '139-rls':
							case '5010':
							case '159-hgl':
							case '1244':
							case '1246':
							case '1247':
								categorykey = 'long-shirts';
								break;
						}
						path = categorykey+'/'+itemcode;
					});
					$.each(r.code, function(code, colorname){
        				color_count++;
        				var colorcode = code.split('_')[1];
        				thumbs += '<li';
						if(color_count==1){
							curcolorname = colorname;
							curcolorcode = colorcode;
							thumbs += ' class="nowimg"';
						}
						thumbs += '><img alt="'+colorcode+'" title="'+colorname+'" src="'+_IMG_PSS+'items/'+path+'/'+code+'_s.jpg" /></li>';
					});
					
					
					$('.color_thumb').html(thumbs);
					$('.num_of_color').text(color_count);
					$('.notes_color').text(curcolorname);
					$('.color_thumb li img').imagesLoaded( function(){
						$('.color_thumb').show();
						$('.item_colors', '#price_wrap').removeClass('throbber');
					});
					$.showSizeform($.prop.curitemid, curcolorcode, []);
					$.showPrintPosition();
				});
			});
		
			/*
			$.ajax({url:'/php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
				data:{'act':'price','itemid':$.prop.curitemid}, success: function(r){
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
		*/
			$.ajax({url:'/php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
				data:{'act':'position','itemid':$.prop.curitemid, 'mode':2}, success: function(r){
					$('#pos_wrap tbody').html(r);
					$.setPrintposEvent();
					$.resetResult();
				}
			});
		},
		prop:{
		/*
		*	アイテムが固定の場合に対応
		*/
			'curitemid': '4'
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
			if(arguments.length>0) $.msgbox_old(arguments[0]);
		},
		addOrder: function(){
		/*
		*	見積計算のハッシュを生成する
		*	@第一引数　false: 枚数のチェックを行なわない
		*/
			var item_id = $.prop.curitemid;
			var size_id = [];
			var size = [];
			var cost = [];
			var posi = [];
			var inks = [];
			var volm = [];
			var color = [];
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
			$('#pos_wrap table select').each( function(){
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
			*	アイテム別にサイズ毎の保持している枚数を集計する
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
			for (var i=0; i<$.printparam.itemid.length; i++) {
				args[i] = { 
					'itemid':$.printparam.itemid[i], 
					'amount':amount[i], 
					'pos':$.printparam.pos[i], 
					'ink':$.printparam.ink[i],
					'size':0,
					'option':inkjetOption
				};
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
		*/
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
		},
		changeAmount: function(e){
		/*	
		*	見積テーブル内で枚数の変更
		*	同じサイズの商品で指定されているプリント位置に対応する枚数も変更する
		*/
			var row = $(e.target).closest('tr');
			var key = row.attr('class').split('_');
			var amount = $.check_NaN(e.target);
			for(var i=0; i<$.printparam.itemid.length; i++){
				if($.printparam.itemid[i]==key[0] && $.printparam.sizeid[i]==key[1]){
					$.printparam.amount[i] = amount;	// 枚数を上書
				}
			}
			$.calcPrice();
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
					$.msgbox_old(form, 692);
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
			postData.categoryid = $('#category_selector option:selected').attr('rel');
			postData.categorykey = $('#category_selector').val();
			postData.categoryname = $('#category_selector option:selected').text();
			postData.itemid = $.prop.curitemid;
			postData.itemcode = $('#item_selector option:selected').attr('rel');
			postData.itemname = $('#item_selector option:selected').text();
			postData.posid = $('#pos_wrap table tbody tr:first').attr('class').split('_')[1];

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
			
			if(totAmount==0){
				return false;
			}
			
			var curRow = 0;
			$.ajax({url:'/php_libs/orders.php', async:false, type:'POST', dataType:'json', data:postData, 
				success:function(r){
					if(r.length!=0){
						isResult = true;
					}
				}
			});
		
			return isResult;
		},
		updatePosition: function(){
		/*
		*	プリント位置とインク色数を更新
		*/
			var posid = $('#pos_wrap table tbody tr:first').attr('class').split('_')[1];
			var base = [];
			var posname = [];
			var ink = [];
			var attach = [];
			var isResult = false;
			
			$('#pos_wrap table tbody tr:eq(3) td').each( function(){
				var base_name = $(this).attr('class');
				$('div.inks', this).each( function(){
					var v = $(this).find('select').val();
					if(v>0){
						base.push(base_name);
						posname.push( $(this).children('p:first').text() );
						ink.push( v );
						
						isResult = true;
					}
				});
			});
			
			if(isResult){
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
	
    
	/*
	*	注文フォームへ遷移 
	*	見積りあり：Step3へ(index:2)
	*	見積りなし：Step2へ(index:1)
	*/
	$('#btnOrder').click( function(){
		var step = 1;
		if($('#result span').text()!='0'){
			if($.updateItem()){
				if($.updatePosition()){
					step = 3;
				}
			}
		}
		document.forms.f1.update.value = step;
		document.forms.f1.item_id.value = $.prop.curitemid;
		document.forms.f1.submit();
	});
	
    
	/* みんなにメール一斉送信 */
	$('#mass-email').click( function(){ $.show_mailform(); });
	
	$('#multimailform .send_mail').live('click', function(){
		var email = [];
		var msg="";
		if($('#multimailform input[name="myname"]')[1].value ==""){
			msg = "お名前を入力して下さい。";
		}else if($('#multimailform input[name="myemail"]')[1].value ==""){
			msg = "送信者のメールアドレスを入力して下さい。";
		}else{
			var chk = false;
			$('.half_l .email, .half_r .email', '[id^=multimailform]').each( function(){
				var tmp = $(this).val();
				if(tmp!="" && typeof tmp !='undefined'){
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
				var posname = $(this).closest('.inks').find('.posname_'+id).text();
				if(ink!=9){
					posname += '　インク：'+ink+' 色';
				}else{
					posname += '　インク： フルカラー';
				}
				pos.push(posname);
			}
		});

		var subject = $('#multimailform input[name="subject"]')[1].value;
		if(subject==""){
			subject = "お見積り";
		}

		var total = $('#result span').text();
		var per = $('#perone span').text();
		var amount = $('#totamount span').text();
		var item_name = $('#item_selector option:selected').text();
		var color_name = $('#price_wrap .notes_color').text();
		var message = $('#multimailform textarea')[1].value;
		var myname = $('#multimailform input[name="myname"]')[1].value;
		var myemail = $('#multimailform input[name="myemail"]')[1].value;

		var prm = {'subject':subject, 'myname':myname, 'myemail':myemail, 'email':email, 'message':message,
					'pageurl':location.pathname, 'total':total, 'per':per, 'amount':amount,
					'item_name':item_name, 'color_name':color_name, 'pos':pos
				};
		
		$.ajax({
			url:'/php_libs/multisend.php', type:'post', dataType:'json', async:false, data:prm, success:function(r){
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
	
	
	/* change category */
	$('#category_selector').change( function(){
		var categoryid = $('option:selected', this).attr('rel');
		var categorykey = $(this).val();
		$.getJSON($.TLA.api+'?callback=?', {'act':'item', 'categoryid':categoryid, 'show_site':$.TLA.show_site, 'output':'jsonp'}, function(r){
			var option = '';
        	jQuery.each(r, function(key, val){
        		if(val['code']=='ss-9999-96') return true;
        		option += '<option value="'+val.id+'" rel="'+val['code']+'"';
        		if(key==0){
        			option += ' selected="selected"';
        		}
				option += '>'+val.name+'</option>';
			});
			$('#item_selector').html(option);
			$.changeItem();
		});
		
		/*
		$.ajax({url:'/php_libs/items.php', type:'get', dataType:'json', async:false, data:{'subcategory':1}, 
			success:function(r){
				var subcat = r;
				$.getJSON($.TLA.api+'?callback=?', {'act':'item', 'categoryid':categoryid, 'output':'jsonp'}, function(r){
					var option = '';
		        	jQuery.each(r, function(key, val){
		        		if(categoryid==1){
		        			if(categorykey=='t-shirts'){
		        				if(subcat['long-shirts'][val['code']] || subcat['baby'][val['code']]) return true;
		        			}else{
		        				if(!subcat[categorykey][val['code']]) return true;
		        			}
		        		}
		        		if(val['code']=='ss-9999-96') return true;
		        		option += '<option value="'+val.id+'" rel="'+val['code']+'"';
		        		if(key==0){
		        			option += ' selected="selected"';
		        		}
						option += '>'+val.name+'</option>';
					});
					$('#item_selector').html(option);
					$.changeItem();
				});
			}
		});
		*/
	});


	/* change thumbnails */
	$(".color_thumb li img").live('click', function(){
		$.changeThumb($(this));
	});


    /* initialize */
    $.init();
	
	
	/* 計算開始
	$('#calc').click( function(){ $.addOrder(); });
	*/
	
	/* 白色と白以外の変更　2013-03-25 廃止
	$('#switch_color :radio').change( function(){
		var tmp = [];
		$('#price_wrap table tbody tr').each( function(index){
			tmp[index] = $(this).find('input.forNum').val();
		});
		var colormode = $(this).val();
		$.ajax({url:'/php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
			data:{'act':'price','itemid':$.prop.curitemid, 'colormode':colormode}, success: function(r){
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
