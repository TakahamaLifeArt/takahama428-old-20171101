/*
*	Takahama Life Art
*	注文フォーム
*	charset euc-jp
*	log
*	2017-05-25	プリント代計算の仕様変更
*/

$(function(){
	
	/********************************
	*	縦スクロールに合わせて要素を移動
	*/
	$(window).scroll(function () {
		var box = $('#floatingbox');
		var parent_contents = $('#container');
		var padding = parseInt(box.css("padding-top").substring(0,box.css("padding-top").indexOf("px")));
		var above = 0;  	// 0 is the above element height.
		var initTop = 100;	// top
		var boxYloc = padding;
		var bottomPos = parent_contents.height() - (box.height() + boxYloc); //get the maximum scrollTop value
		var offset = $(document).scrollTop();
		offset = offset>bottomPos? bottomPos: offset<=above? initTop: offset-above;
		box.animate({top:offset+"px"},{duration:500,queue:false});
	});
	
	
	/********************************
	*	ページを離れる前に確認メッセージ
	*
	window.addEventListener('beforeunload', function(event) {
		return event.returnValue = 'カートの内容は後で確認することが出来ます。;
    }, false);
   	
   	*/
   	
	/********************************
	*	次へボタン
	*/
	$('.step_next').click(function(e){
		/*
		if($(this).is('.disable_arrow')){
			$.msgbox('<p style="font-size:18px;color:#e1630e;"><img alt="" src="./img/alert.png" />　選択項目をご確認ください。</p>');
			return;
		}
		*/
		
		if($(this).is('.goto_position')){
			$('input[type="number"]', '#step2').blur();
			var totAmount = 0;
			var colorcodehash = {};
			var isCheck = true;
			$('.size_table', '#step2').each( function(index){
				var amount = 0;
				var color_code = $(this).closest('.pane').children('.thumb_wrap').find('.item_image img').attr('alt').split('_')[1];
				$('tbody tr:not(".heading") td[class*="size_"]', this).each( function(){
					amount += $(this).children('input').val()-0;
				});
				
				if(amount!=0 && colorcodehash[color_code]){
					isCheck = false;
					return false;
				}
				
				colorcodehash[color_code] = true;
				totAmount += amount;
			});
			
			if(!isCheck){
				$.msgbox('アイテムカラーの指定が重複しています。ご確認ください。。');
				return;
			}
			
			if(totAmount==0){
				$.msgbox('アイテムの枚数を指定してください。');
				return;
			}
			
			if(!$.showPrintPosition()){
				$.msgbox('アイテムの枚数を確認してください。');
			}
			
		}else if($(this).is('.goto_cart')){
		// Step4へ
			if($.chkInks()){
				var r1 = $.updateItem();
				var r2 = $.updatePosition();
				r2['category'] = r1['category'];
				$.setCart(r2);
			}
		}else if($(this).is('.goto_user')){
		// Step5へ
			if($('#nodeliday').prop('checked')){
				$.next(4);
			}else{
				if($('#deliveryday').val()==""){
					$.msgbox('ご希望納期を指定してください。');
				}else{
					$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'details'}, 
						success: function(r){
							if(r.options.expressError!=""){
								$.msgbox(r.options.expressError+"\nご希望納期をご確認ください。");
							}else{
								$.next(4);
							}
						}
					});
				}
			}
//			$.setCustomer(false);
			$.setCustomer2();
		}else if($(this).is('.goto_confirm')){
		// Step6へ
			if($('#agree').prop('checked')){
				$('#sendorder').removeClass('disable_button');
			}else{
				$('#sendorder').addClass('disable_button');
			}
			$.confirm();
//		}else{
//			$.next();
		}
		
	});
	
	
	/********************************
	*	届き先選択
	*/
	$('#delivery_customer').change( function(){
			var postData = {'getcustomer':'true'};
			$.ajax({
				url: 'user_login.php',
				type: 'post',
				data: postData,
				dataType: 'json',
				async: true
			}).done(function(me){
    		if(typeof me.id !='undefined'){
						if($('#delivery_customer').val() == "-1") {
							$('#tel').val(me.tel);
							$('#zipcode1').val(me.zipcode);
							$('#addr0').val(me.addr0);
							$('#addr1').val(me.addr1);
							$('#addr2').val(me.addr2);
						} else {
							for(var i=0; i<me.delivery.length; i++) {
								if($('#delivery_customer').val() == me.delivery[i].id) {
									$('#tel').val(me.delivery[i].delitel);
									$('#zipcode1').val(me.delivery[i].delizipcode);
									$('#addr0').val(me.delivery[i].deliaddr0);
									$('#addr1').val(me.delivery[i].deliaddr1);
									$('#addr2').val(me.delivery[i].deliaddr2+me.delivery[i].deliaddr3+me.delivery[i].deliaddr4);
								}
							}
						}
				}
			}).fail(function(xhr, status, error){
				alert("Error: "+error+"<br>xhr: "+xhr);
			});
	});
	
	/********************************
	*	戻るボタン
	*/
	$('.prev').click( function(){
		var pageIndex = $(this).data('back');
		$.back(pageIndex);
		/*
		if($(this).attr('rel')=='0'){
			$.back(0);
		}else{
			$.back();
		}
		*/
	});
	
	
	/********************************
	*	カートを見る
	*/
	$('.viewcart').click( function(){
		$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'details'}, 
			success: function(r){
				if(r.design.length==0 && r.options.noprint==0){
					$.msgbox('プリントするデザインの色数を指定してください。');
				}else{
					$.setCart(r);
				}
			}
		});
	});
	
	
	/********************************
	*	ガイダンスのポップアップ
	*/
//	$('.pop_size', '#step2').on('click', function(){
	$('#step2').on('click', '.pop_size', function(){
		var msg = '<h3>サイズの目安</h3><hr>';
		msg += '<p class="toc"><img alt="サイズ" src="./img/measuretable.jpg"></p>';
		msg += '<p class="note toc"><span>※</span>寸法（cm）はあくまでも目安です。メーカーや商品によって違いがございます。</p>';
		$.msgbox(msg);
	});
	
	$('#pop_express').click( function(){
		var msg = '<h3>特急料金について</h3><hr>';
		msg += '<p>通常納期では間に合わないというお急ぎのお客様には、特急製作として2日仕上げ、翌日仕上げ、当日仕上げがございます。それぞれ別途割増料金がかかります。</p>';
		msg += '<dl class="list"><dt>2日仕上げ</dt><dd>通常料金の1.3倍</dd><dt>翌日仕上げ</dt><dd>通常料金の1.5倍</dd><dt>当日仕上げ</dt><dd>通常料金の2倍</dd></ul>';
		$.msgbox(msg);
	});
	
	$('#pop_pack').click( function(){
		var msg = '<h3>袋詰</h3><hr>';
		msg += '<p><img width="100%" alt="袋詰あり" src="./img/img_packing.jpg"><img width="100%" alt="袋詰なし" src="./img/img_nopacking.jpg"></p>';
		msg += '<p class="note">袋詰あり（左）と袋詰なし（右）の参考写真です。</p>';
		$.msgbox(msg, 750);
	});
	
	$('#pop_payment').click( function(){
		var msg = '<h3 class="syousai">銀行振込</h3><hr>';
		msg += '<p>下記の口座にお振込ください。</p>';
		msg += '<p>ご希望の納品日より2日前までにお振込をお願い致します。（土日祝は入金確認ができないのでご注意ください）お振込手数料は、お客様のご負担とさせていただいております。</p>';
		msg += '<dl class="list">';
		msg += '<dt>銀行名</dt>';
		msg += '<dd>三菱東京ＵＦＪ銀行</dd>';
		msg += '<dt>支店名</dt>';
		msg += '<dd>新小岩支店　744</dd>';
		msg += '<dt>口座種別</dt>';
		msg += '<dd>普通</dd>';
		msg += '<dt>口座番号</dt>';
		msg += '<dd>3716333</dd>';
		msg += '<dt>口座名義</dt>';
		msg += '<dd>ユ）タカハマライフアート</dd>';
		msg += '</dl>';

		msg += '<hr><br><h3 class="syousai">代金引換</h3><hr>';
		msg += '代金引換手数料は1件につき&yen;800（税抜）かかります。';
		msg += 'お支払い総額（商品代+送料＋代金引換手数料＋消費税）を配送業者にお支払いください。';
		msg += 'お客様のご都合でお支払い件数が複数になった場合、1件につき&yen;800（税抜）を追加させていただきます。';

		msg += '<hr><br><h3 class="syousai">カード決済</h3><hr>';
		msg += '各種クレジットカードがご利用いただけます。';
		msg += 'ご希望の納品日より2日前までにカード決済手続きをお願い致します。';
		msg += '（土日祝は入金確認ができないのでご注意ください）カード決済システム利用料（5%）は、お客様のご負担とさせていただいております。';
		msg += '弊社の「マイページ」＞「お支払い状況」＞「カード決済のお申し込はこちらから」にて決済が可能です。';
		msg += '<center><p><img width="60%" alt="カード種類" src="./img/card.png"></p></center>';
		$.msgbox(msg);
	});
	
	
	
	
	/*---------- STEP 1 ----------*/
	
	/********************************
	*	カテゴリー変更でアイテム一覧表示を更新
	*/
	$('#category_selector').change( function(){
		$.itemparam.categoryid = $('option:selected', this).attr('rel');
		$.itemparam.categorykey = $(this).val();
		$.itemparam.categoryname = $('option:selected', this).text();
		$.ajax({url:'/php_libs/items.php', type:'get', dataType:'json', async:true, data:{'category_key':$.itemparam.categorykey}, 
			success: function(res){
				var ls='<ul class="listitems clearfix">';
				var recomend = '<ul class="recommend_item clearfix">';
				var i=0;
				var firstlist = '';
				var suffix = '';
				//var folder = ($.itemparam.categorykey=='baby')? 't-shirts': $.itemparam.categorykey;
				var tmp = [];
				
				// ソート
				var x=[];
			    for(key in res){
			        x.push([key,res[key]]);
			    }
			    x.sort(function(a,b){
			        return a[1]['item_row']-b[1]['item_row'];
			    });
				for(var s=0; s<x.length; s++){
					var code = x[s][0];
					var v = x[s][1];
					var folder = v['category_key'];
					
					// 人気商品
					if($.itemparam.categorykey=='t-shirts'){
						if(code=='085-cvt') tmp[0] = {'085-cvt':v};
						if(code=='300-act') tmp[1] = {'300-act':v};
						if(code=='5806') tmp[2] = {'5806':v};
					}else if($.itemparam.categorykey=='polo-shirts'){
						if(code=='141-nvp') tmp[0] = {'141-nvp':v};
					}else if($.itemparam.categorykey=='sportswear'){
						/*
						if(code=='5088') tmp[0] = {'5088':v};
						if(code=='5900') tmp[1] = {'5900':v};
						if(code=='300-act') tmp[2] = {'300-act':v};
						*/
					}
					
					if(i%4==0){
						firstlist = ' firstlist';
					}else{
						firstlist = '';
					}
					if( (code.indexOf('p-')==0 && v['initcolor']=="") || code=='ss-9999' || code=='ss-9999-96'){
						suffix = '_style_0'; 
					}else{ 
						suffix = '_'+v['initcolor']; 
					}
					ls += '<li class="listitems_ex'+firstlist+'" id="itemid_'+v['item_id']+'_'+v['pos_id']+'">'+
								'<ul class="maker_'+v['maker_id']+'">'+
									'<li class="point_s">'+v['features']+'</li>'+
									'<li class="item_name_s">'+
										'<ul>'+
											'<li class="item_name_kata">'+code.toUpperCase()+'</li>'+
											'<li class="item_name_name">'+v['item_name']+'</li>'+
										'</ul>'+
									'</li>'+
									'<li class="item_image_s">'+
										'<img src="'+_IMG_PSS+'items/list/'+folder+'/'+code+'/'+code+suffix+'.jpg" width="100%" height="100%" alt="'+code.toUpperCase()+'">'+
										'<img src="./img/crumbs_next.png" alt="" class="icon_arrow">'+
									'</li>'+
									'<li class="item_info_s clearfix">'+
										'<div class="colors">'+v['colors']+'</div>'+
										'<div class="sizes">'+v['sizes']+'</div>'+
										'<p class="price_s" style="white-space: nowrap;"><p style="display:inline-block;">TAKAHAMA価格</p><span id="price_cost" style="white-space: nowrap;"><span>'+v['minprice']+'</span>円~</span></p>'+
									'</li>'+
								'</ul>'+
								'<p class="tor"><a href="../items/'+folder+'/item.html?id='+code+'">アイテムの詳細へ</a></p>'+
							'</li>';
					i++;
				}
				ls += '</ul>';
				$('#h3_itemlist span').text('（'+i+'アイテム）');
				
				if(tmp.length>0){
				// 人気商品がある場合
					var lastli = '';
					for(i=0; i<tmp.length; i++){
						for(var c in tmp[i]){
							v = tmp[i][c];
							folder = v['category_key'];
							if(i==2) lastli = ' lastli';
							recomend += '<li class="recitembox'+lastli+'" id="itemid_'+v['item_id']+'_'+v['pos_id']+'">'+
								'<img class="rankno" src="./img/no'+(i+1)+'.png" width="60" height="55" alt="No1">'+
								'<ul class="maker_'+v['maker_id']+'">'+
									'<li class="item_name">'+
										'<p>'+v['features']+'</p>'+
										'<ul class="popu_item_name">'+
											'<li class="item_name_kata">'+c.toUpperCase()+'</li>'+
											'<li class="item_name_name">'+v['item_name']+'</li>'+
										'</ul>'+
									'</li>'+
									'<li class="item_image">'+
										'<img src="'+_IMG_PSS+'items/'+folder+'/'+c+'/'+c+'_'+v['initcolor']+'.jpg" width="250" alt="'+c.toUpperCase()+'">'+
										'<img src="./img/crumbs_next.png" alt="" class="icon_arrow">'+
									'</li>'+
									'<li class="item_info clearfix">'+
										'<div class="color">'+v['colors']+'</div>'+
										'<div class="size">'+v['sizes']+'</div>'+
										'<p class="price" style="white-space: nowrap;"><p style="display:inline-block;">TAKAHAMA価格</p><span id="price_cost" style="white-space: nowrap;"><span>'+v['minprice']+'</span>円~</span></p>'+
									'</li>'+
								'</ul>'+
							'</li>';
						}
					}
					recomend += '</ul>';
					
					ls = recomend+ls;
				}
				$('#itemlist_wrap').html(ls);
			}
		});
		
	});
	
	
	/********************************
	*	アイテムの指定でStep2へ
	*/
//	$('.recitembox > ul, .listitems_ex > ul', '#itemlist_wrap').on('click', function(){
	$('#itemlist_wrap').on('click', '.recitembox > ul, .listitems_ex > ul', function(){
		var maker_id = $(this).attr('class').split('_')[1];
		var self = $(this).parent();
		var ids = self.attr('id').split('_');	// [ , item_id, position_id]
		var item_name = $('.item_name_s ul .item_name_name, ul .item_name ul .item_name_name', this).text();
		var func = function(){
			$('#cur_item_name').attr('class','prop_'+ids[2]+'_'+maker_id).text(item_name);
			$.changeItem(ids[1]);
			$.next(1);
		};
		if(maker_id==10){
		// ザナックス
			$.showPop(func);
		}else{
			func();
		}
	});
	
	
	/*---------- STEP 2 ----------*/
	
	/********************************
	*	アイテムカラーの変更
	*/
//	$('.color_thumb li img').on('click', function(){
	$('#step2').on('click', '.color_thumb li img', function(){
		if($(this).parent().is('.nowimg')) return;
		
		var $pane = $(this).closest('.pane');
		var imgname = $(this).attr('src').replace('_s.jpg', '.jpg');
		var colorcode = $(this).attr('alt');
		var code = $.itemparam.itemcode+'_'+colorcode;
		var colorname = $(this).attr('title');
		
		$(this).parent().siblings('li.nowimg').removeClass('nowimg');
		$(this).parent().addClass('nowimg');
		
		$(this).closest('.thumb_wrap').find('.item_image img').attr({'src':imgname, 'alt':code});
		$(this).closest('.item_thumb').find('.notes_color').html(colorname);
		
		$.itemparam.colorname = colorname;
		$.itemparam.colorcode = colorcode;
		
		$.getPage($.itemparam.itemid, colorcode);
		var args = '';
		if($.temp.colorcode.length>0){
			args = $.temp.volume[0];
		}
		$.showSizeform($.itemparam.itemid, colorcode, args, $pane);
	});
	
	
	/********************************
	*	別のアイテムカラーを追加
	*/
	$('#add_item_color').click( function(){
		var target = $(this).parent('.btn_line');
		var clone = $('.pane:first', '#step2').clone();
		
		// 指定カラーを初期化
		clone.find('.color_thumb li.nowimg').removeClass('nowimg');
		var my = clone.find('.color_thumb li:first');
		my.addClass('nowimg');
		var imgname = $('img', my).attr('src').replace('_s.jpg', '.jpg');
		var colorcode = $('img', my).attr('alt');
		var code = $.itemparam.itemcode+'_'+colorcode;
		var colorname = $('img', my).attr('title');
		clone.find('.item_image img').attr({'src':imgname, 'alt':code});
		clone.find('.notes_color').html(colorname);
		
		// パラメーターを設定
		$.itemparam.colorname = colorname;
		$.itemparam.colorcode = colorcode;
		
		// サイズテーブルを初期化
		$.showSizeform($.itemparam.itemid, colorcode, [], clone);
		
		// 削除ボタンを追加
		clone.prepend('<p class="btn_line"><ins class="del_item_color"><img src="/common/img/delete.png" alt="取消">取消</ins></p>');
		
		clone.insertBefore(target);
	});
	
	
	/********************************
	*	アイテムカラーを削除
	*/
//	$('.del_item_color').on('click', function(){
	$('#step2').on('click', '.del_item_color', function(){
		$(this).closest('.pane').slideUp('normal', function(){$(this).remove();});
	});
	
	
	/********************************
	*	枚数のテキストボックスの表示クリア（未使用）
	*/
	$('.clear_amount').click( function(e){
		$('.size_table tbody tr:not(".heading") td input').each( function(){
			$(this).val('0');
		});
		$('.cur_amount').text('0');
	});
	
	
	/********************************
	*	枚数の合計表示
	*/
//	$('.size_table tbody tr:not(".heading") td[class*="size_"] input').on('change', function(){
	$('#step2').on('change', '.size_table tbody tr:not(".heading") td[class*="size_"] input', function(){
		$.check_NaN(this);
		var amount = 0;
		$(this).closest('.size_table').find('tbody tr:not(".heading") td[class*="size_"] input').each( function(){
			if($(this).val()-0!=0) amount+=($(this).val()-0);
		});
		$(this).closest('.size_table').next('.btmline').find('.cur_amount').text($.addFigure(amount));
		
		// 合計枚数
		var tot = 0;
		$('.cur_amount').each( function(){
			tot += $(this).text().replace(/,/g, '')-0;
		});
		$('#tot_amount').text($.addFigure(tot));
	});
	
	
	/*---------- STEP 3 ----------
		STEP4への遷移でプリント位置とインク色数を送信
	*/
	
	/********************************
	*	プリントなしで購入
	*/		
	$('#noprint').change( function(){
		var isFunProcessed = false;
		var func = function(){
			var postData = {'act':'update', 'mode':'options', 'key':'noprint', 'val':val};
			$.ajax({url:'/php_libs/t_orders.php', type:'post', dataType:'json', async:true, data:postData,
				success:function(r){
					if($.chkInks()){
						$.updateItem();
						$.updatePosition();
					}
					if(r.options.length==0) return;
					$.updateOptions(r);
				}
			});
		};
		var val = $(this).prop('checked')? 1: 0;
		if(val==1){
			if($('#pos_wrap .inkbox .ink').length>0){
				$('#pos_wrap .inkbox .ink').each( function(){
					$(this).val("0").prev("span").text("選択してください");
				});
			}
			$('#pos_wrap').hide();
			func();
		}else{
			$.confbox.show("プリントなしでカートに入れた商品は全て削除されます。よろしいいですか？", 
				function(){
					if($.confbox.result.data){
						$('#pos_wrap').show();
						$.updateItem(["","","",""]);
						if(isFunProcessed == false) {
							func();
							isFunProcessed = true;
						}
					}else{
						$('#noprint').prop('checked', true);
					}
				}
			);
		}
	});
	
	
	/********************************
	*	刺繍を希望の場合のテキストエリア
	*/
	$('#note_printmethod').focusout( function(){
		var key = $(this).attr('name');
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'customer'};
		postData[key] = val;
		$.ajax({url:'/php_libs/t_orders.php', async:false, type:'POST', dataType:'text', data:postData});
	});
	
	
	/*---------- STEP 4 ----------*/
	
	/********************************
	*	カートで枚数の変更
	*/
//	$('#estimation_wrap tbody input[type="number"]').on( 'change', function(){
	$('#estimation_wrap').on( 'change','tbody input[type="number"]', function(){
		$.check_NaN(this, '1');
		var args = $(this).attr('class').split('_');
		var amount = $(this).val();
		$.ajax({url:'/php_libs/t_orders.php', async:false, type:'POST', dataType:'json', 
			data:{'act':'update','mode':'amount', 'categoryid':args[1], 'itemid':args[2], 'colorcode':args[3], 'sizeid':args[4], 'amount':amount}, success: function(r){
				if(r.length!=0){
					$.updateEstimation(r);
				}
			}
		});
	});


	/********************************
	*	割引などのチェック
	*/
	$('input[type=radio]', '#option_table').change( function(){
		var key = $(this).attr('name');
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'options', 'key':key, 'val':val};
		$.ajax({url:'/php_libs/t_orders.php', type:'post', dataType:'json', async:true, data:postData,
			success:function(r){
				if(r.options.length==0) return;
				
				$.updateOptions(r);
			}
		});
	});
	
	
	/********************************
	*	納期の指定なし
	*/
	$('#nodeliday').change( function(){
		var key = $(this).attr('name');
		var val = 0;
		if($(this).prop('checked')){
			var val = $(this).val();
			$('#deliveryday').val('').prop('disabled', true);
		}else{
			$('#deliveryday').prop('disabled', false);
		}
		var postData = {'act':'update', 'mode':'options', 'key':key, 'val':val};
		$.ajax({url:'/php_libs/t_orders.php', type:'post', dataType:'json', async:true, data:postData,
			success:function(r){
				if(r.options.length==0) return;
				if(val==1){
					// 納期指定なしの場合は納期を削除
					var postData = {'act':'update', 'mode':'options', 'key':'deliveryday', 'val':""};
					$.ajax({url:'/php_libs/t_orders.php', type:'post', dataType:'json', async:true, data:postData,
						success:function(r){
							if(r.options.length==0) return;
							$.updateOptions(r);
						}
					});
				}else{
					$.updateOptions(r);
				}
			}
		});
	});
	
	
	/********************************
	*	お届け時間指定
	*/
	$('#deliverytime').change( function(){
		var key = $(this).attr('name');
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'options', 'key':key, 'val':val};
		$.ajax({url:'/php_libs/t_orders.php', type:'post', dataType:'json', async:true, data:postData});
	});
	
	
	/********************************
	*	添付ファイルの追加
	*/
	$('.add_attach', '#uploaderform').click( function(){
		if($('input[type=file]', '#uploaderform').length>3){
			$.msgbox('一度に添付できるファイルは4つまでです。');
			return;
		}
		$(this).parent().before('<p><input type="file" onChange="this.form.submit()" name="attach[]" size="19" title="デザインファイルを指定してください" /><span class="del_attach"><img src="/common/img/delete.png" alt="取消">取消</span></p>');
	});
	
	
	/********************************
	*	添付ファイルの取消（非同期）
	*/
	$('#uploaderform').on('click', '.del_attach', function(){
		if($('input[type=file]', '#uploaderform').length==1){
			$(this).parent().before('<p><input type="file" onChange="this.form.submit()" name="attach[]" size="19" title="デザインファイルを指定してください" /><span class="del_attach"><img src="/common/img/delete.png" alt="取消">取消</span></p>');
		}
		$(this).parent().remove();
		document.forms.uploaderform.submit();
	});
	
	
	/********************************
	*	デザインの備考とインク色指定のテキストエリア
	*/
	$('#note_design, #note_printcolor').focusout( function(){
		var key = $(this).attr('name');
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'customer'};
		postData[key] = val;
		$.ajax({url:'/php_libs/t_orders.php', async:false, type:'POST', dataType:'text', data:postData});
	});
	
	
	/*---------- STEP 5 ----------*/
	
	/********************************
	*	フォーム内でエンターによるログイン
	*/
	$('#loginform_wrapper form').on("keypress", "input", function(e){
		var code=(e.charCode) ? e.charCode : ((e.which) ? e.which : e.keyCode);
		if(code == 13 || code == 3) $("#login_button").click();
	});
	
	
	/********************************
	*	ログインボタンの表示
	*/
	$("#show_loginwrap").click( function(){
		$("#login_wrap").show();
		$("#userinfo").hide();
		$('#step5 .goto_confirm').hide();
		$('input[type="text"], textarea', '#userinfo').val("");
		$(":radio[name='repeater']", "#userinfo").attr("checked",false);
		$("#deli_selector_wrap").hide();
		$('#deli').attr("checked",false);
		$('#deli_list').hide();
	});
	
	/********************************
	*	ログインボタン
	*/
	$("#login_button").click( function(){
//alert("login_button");
		var f = document.forms.loginform;
		var email = f.email.value.trim();
		var pass = f.pass.value.trim();
		if(email==""){
			alert('メールアドレスを入力してください。');
			return;
		}
		if(pass==""){
			alert('パスワードを入力してください。');
			return;
		}
		
		var f=document.forms.loginform;
		var fd = new FormData(f);
		$.login(fd);
	});
	
	/********************************
	*	登録済みのお届け先を選ぶ
	*/
	$("#deli_selector").change( function(){
		var idx = $(this).val();
		$("#deli_id").val($.user.deli[idx]["id"]);
		$("#organization").val($.user.deli[idx]["organization"]);
		$("#delitel").val($.user.deli[idx]["delitel"]).blur();
		$("#zipcode2").val($.user.deli[idx]["delizipcode"]).blur();
		$("#deliaddr0").val($.user.deli[idx]["deliaddr0"]);
		$("#deliaddr1").val($.user.deli[idx]["deliaddr1"]);
		$("#deliaddr2").val($.user.deli[idx]["deliaddr2"]);
	});
	
	/********************************
	*	初めての方はお客様情報入力
	*/
	$("#show_userinfo").click( function(){
		$.setCustomer(true);
	});
	
	/********************************
	*	お届け先住所入力の表示・非表示の切替
	*/
	$('#deli').change( function(){
		if($(this).is(':checked')){
			$('#deli_list').fadeIn();
		}else{
			$('#deli_list').fadeOut();
		}
	});
	
	/********************************
	*	デザイン掲載のチェック
	*/
	$(':radio[name="publish"]', '#step5').change( function(){
		var key = $(this).attr('name');
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'options', 'key':key, 'val':val};
		$.ajax({url:'/php_libs/t_orders.php', type:'post', dataType:'json', async:true, data:postData});
	});
	
	
	/********************************
	*	メールアドレスの登録確認
	*/
//	$('#email').change( function(){
//		var val = $(this).val();
//		$.checkEmail(val);
//	});
	
	/********************************
	*	ログインボタン
	*/
	$('#member_login').on('click', function(){
		var email = $('#login_input_email').val().trim();
		var pass = $('#login_input_pass').val().trim();
		if(!$.check_email(email)){
			return;
		}
		var required = [];
		if(email=='') required.push('<li>メールアドレス</li>');
		if(pass=='') required.push('<li>パスワード</li>');
		var required_list = '<ul class="msg">'+required.toString().replace(/,/g,'')+'</ul>';
		if(required.length>0){
			$.msgbox("必須項目の入力をご確認ください。<br />"+required_list);
			return;
		}

		//セッションにユーザーを保存
		//画面を再表示
		$.ajax({url:'user_login.php', type:'get', dataType:'json', async:false, data:{'login':'true', 'email':email,'pass':pass}, 
			success: function(r){
				if(r.length!=0){
					if(typeof r.id=='undefined') {
						$.msgbox(r);
					} else {
						$.setCustomer2();
					}
				}
			}
		});
	});
	
	/*---------- STEP 6 ----------*/
	
	/********************************
	*	確認チェックで注文ボタンを有効
	*/
	$('#agree').change( function(){
		if($(this).prop('checked')){
			$('#sendorder').removeClass('disable_button');
		}else{
			$('#sendorder').addClass('disable_button');
		}
	});
	
	
	/********************************
	*	注文ボタン
	*/
	$('#sendorder').click( function(){
		if($(this).is('.disable_button')){
			$.msgbox('注意事項をご確認の上、【確認しました】チェックをクリックしてください。');
		}else{
			var func = function(){
				document.forms.orderform.submit();
/*
				var args = $("#conf_email").text();
				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'checkemail', 'args':args}, 
					success: function(r){
						if(r.length!=0){
//							var msg = '<h1>登録済みのメールアドレスです！</h1>';
//							msg += '<p>E-mail：　'+args+'</p>';
//							msg += '<p>すでにご登録されているユーザー情報でお申し込みを受付いたします。<br>';
//							msg += 'マイページでログインしてください。</p>';
//							$.msgbox(msg);
							document.forms.orderform.submit();
						}else{
							document.forms.orderform.submit();
							//alert("ok");
						}
					}
				});
*/
			};
			if($(this).hasClass('popup')){
				$.showPop(func, true);
			}else{
				func();
			}
		}
	});
	
	
	/********************************
	*	文字打ちの確認
	*
	$('#note_write').change( function(){
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'options', 'key':'note_write', 'val':val};
		$.ajax({url:'/php_libs/orders.php', type:'post', dataType:'text', async:true, data:postData});
	});
	*/
	
	
	/*---------- Estimation box ----------*/
	
	/********************************
	*	割引明細の表示・非表示
	*
	$('#discount_detail').click( function(){
		var self = $('#discount_detail');
		$('#floatingbox .estimate_body .discount_list p').toggle('1000', function(){
			if($(this).is(':visible')){
				self.attr('src', './img/icon_up.png');
			}else{
				self.attr('src', './img/icon_down.png');
			}
		});
	});
	*/
	
	/********************************
	*	見積ボックス内で変更
	*
	$('#pack, #discount_student, #discount_blog, #discount_illust').change( function(){
		var key = $(this).attr('name');
		var val = $(this).val();
		if(!$(this).is('#discount_student')){
			if(!$(this).is(':checked')) val = 0;
		}
		var postData = {'act':'update', 'mode':'options', 'key':key, 'val':val};
		$.getJSON('/php_libs/orders.php', postData, function(r){
			var optionfee = 0;
			var discountname = '';
			$.each(r, function(key, val){
				if(key=='discountname' && r.discountname.length>0){
					discountname = '（'+r.discountname.toString()+'）';
				}else{
					$('#est_'+key).text($.addFigure(val));
					optionfee += val-0;
				}
			});
			var sum = $('#est_price').text().replace(/,/g, '')-0;
			var printfee = $('#est_printfee').text().replace(/,/g, '')-0;
			var volume = $('#est_amount').text().replace(/,/g, '')-0;
			var total = sum + printfee + optionfee;
			var perone = Math.round(total/volume);
			var tot = $.addFigure(total);
			var per = $.addFigure(perone);
			$('#est_total_price').text(tot);
			$('#est_perone').text(per);
			
			if($('#conf_discount').length>0){
				$('#spec_discount').text(discountname);
				$('#conf_discount').text($.addFigure(r.discount));
				$('#conf_carriage').text($.addFigure(r.carriage));
				$('#conf_pack').text($.addFigure(r.pack));
				$('#conf_item tfoot .tot ins').text(tot);
				$('#conf_item tfoot .per ins').text(per);
			}
			
		});
	});
	*/
	
	
	/********************************
	*	見積から削除
	*
	$('.del_cart').on('click', function(e){
		var tr = $(e.target).closest('tr');
		var args = tr.attr('class').split('_').slice(1);
		var msg = tr.children('td:eq(0)').text()+'<br />カラー：'+tr.children('td:eq(1)').text();
		msg += ' の '+tr.children('td:eq(2)').text()+'を取消ます。<br />よろしいですか？'
		$.confbox.show(
			msg, 
			function(){if($.confbox.result.data){
				$.back(0);
				$.updateItem(args);
			} 
		});
		e.preventDefault();
	});
	*/
	/********************************
	*	見積ボックスから商品を指定して表示
	*/
	/*
	$('#item_detail tr').on('click', function(e){
		if($(e.target).is('.del_cart')) return;
		$.back(0);
		
		var p = $(this).attr('class').split('_');
		$.temp.categoryid = p[1];
		$.temp.itemid = p[2];
		$.temp.colorcode = p[3];
		$.temp.volume = {};
		$.setPage();
	});
	*/
	
	
	jQuery.extend({
		itemparam: {
		/*
		*	カレントのアイテムデータ
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
		user: {
			'deli'			:[]
		},
		init: function(){
		/*
		*	initialize
		*/
			if(_UPDATED==1){
				$.editItem(_CAT_KEY, _ITEM_ID);
			}else if(_UPDATED==2){
			// カートを見るBOXのボタンで遷移
				$.itemparam.categoryid = 1;
				$.itemparam.categorykey = 't-shirts';
				$.itemparam.categoryname = 'Tシャツ';
				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'details'}, 
					success: function(r){
						if(r.design.length==0 && r.options.noprint==0){
							$.msgbox('プリントするデザインの色数を指定してください。');
						}else{
							$.setCart(r);
						}
					}
				});
			}else if(_UPDATED==3){
			// 商品詳細とシーン別の見積が計算済み状態での遷移
				var param = $.getPage(_ITEM_ID, '');
				$.itemparam.categoryid = param[0]['categoryid'];
				$.itemparam.categorykey = param[0]['categorykey'];
				$.itemparam.categoryname = param[0]['categoryname'];
				
				$.itemparam.itemid = param[0]['itemid'];
				$.itemparam.itemcode = param[0]['itemcode'];
				$.itemparam.itemname = param[0]['itemname'];
				
				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'details'}, 
					success: function(r){
						$.setCart(r);
					}
				});
				/*
				$.showPrintPosition(false);
				$.changeItem(_ITEM_ID);
				*/
			}else{
				$.itemparam.categoryid = 1;
				$.itemparam.categorykey = 't-shirts';
				$.itemparam.categoryname = 'Tシャツ';
				
				$.itemparam.itemid = 4;
				$.itemparam.itemcode = '085-cvt';
				$.itemparam.itemname = 'ヘビーウエイトＴシャツ';
			}
			
			$.itemparam.colorname = 'ホワイト';
			$.itemparam.colorcode = '001';
			

			$('.datepicker', '#step4').datepicker({
				beforeShowDay: function(date){
					var weeks = date.getDay();
					var texts = "";
					if(weeks == 0) texts = "休日";
					var YY = date.getFullYear();
					var MM = date.getMonth() + 1;
					var DD = date.getDate();
					var currDate = YY + "/" + MM + "/" + DD;
					var datesec = Date.parse(currDate)/1000;
					if(!$.TLA.holidayInfo[YY+"_"+MM]){
						$.TLA.holidayInfo[YY+"_"+MM] = new Array();
						$.ajax({url: '/php_libs/checkHoliday.php',
								type: 'GET',
								dataType: 'text',
								data: {'datesec':datesec},
								async: false,
								success: function(r){
									if(r!=""){
										var info = r.split(',');
										for(var i=0; i<info.length; i++){
											$.TLA.holidayInfo[YY+"_"+MM][info[i]] = info[i];
										}
									}
								}
							   });
					}
					if($.TLA.holidayInfo[YY+"_"+MM][DD] && weeks!=6) weeks = 0;
					if(weeks == 0) return [true, 'days_red', texts];
					else if(weeks == 6) return [true, 'days_blue'];
					return [true];
				},
				onClose: function(dateText, inst){
					var yy, mm, dd;
//					if(dateText.match(/^(\d{4})-([01]?\d{1})-([0123]?\d{1})$/)){
//						var res = dateText.split('-');

					if(dateText.match(/^(\d{4})[\/-]([01]?\d{1})[\/-]([0123]?\d{1})$/)){
						var res = dateText.split(/[\/-]/);
						yy = res[0]-0;
						mm = res[1]-0;
						dd = res[2]-0;
					}
					var date = new Date(yy, mm-1, dd);
					if(!(yy==date.getFullYear() && mm-1==date.getMonth() && dd==date.getDate())){
						dateText = '';
						$(this).datepicker('setDate', "");
					}
					
					// update the session
					var postData = {'act':'update', 'mode':'options', 'key':'deliveryday', 'val':dateText.replace(/[\/-]/g, "-")};
					$.ajax({url:'/php_libs/t_orders.php', async:true, type:'POST', dataType:'json', data:postData,
						success:function(r){
							if(r.options.length==0) return;
							$.updateOptions(r);
						}
					});
				}
			});
			
			// 注文するボタンのチェック
			$('#agree').prop('checked', false);
			$('#sendorder').addClass('disable_button');
		},
		showPop: function(func){
		/*
		*	メーカー「ザナックス」の場合に在庫確認が必要な旨ポップアップ
		*
		*	@第二引数		なし：default、　あり：確認チェックを使用する
		*/
			var args = 1;
			var chk = '';
			if(arguments.length==2){
				chk = '<p style="margin-top:1em;font-size:125%;"><label><input type="checkbox" id="agree_stock" value="1" onChange="$.checkAgreeStock(this);"> 確認しました</label>';
				chk += '<ins class="fontred" style="font-size:80%;"> （チェックしてください）</ins></p>';
				args = 0;
			}
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
				'<address>0120-130-428</address>'+chk+'</div>', 
				function(){
					if($.confbox.result.data){
						func();
					}else{
						// Do nothing.
					}
				}, args
			);
		},
		checkAgreeStock: function(my){
		/*
		*	ザナックスの在庫確認メッセージの確認チェックでポップアップのＯＫボタンの有効・無効を切替える
		*/
			if($(my).is(':checked')){
				$('input.confirm_ok').prop('disabled', false);
			}else{
				$('input.confirm_ok').prop('disabled', true);
			}
		},
		setCustomer: function(args){
		/*
		*	@args	true: 初めての方の入力	false: Step4から遷移
		*
		*/
			if(args){
				$('#userinfo input[type="text"]').removeAttr("readonly");
				$(":radio[name='repeater']", "#userinfo").val([1]);
				$("#login_wrap").hide();
				$("#userinfo").show();
				$('#step5 .goto_confirm').show();
				$('#member').val(0);
			}else{
				//$("#login_wrap").hide();
				var postData = {'act':'register'};
				$.ajax({
					url: '/php_libs/t_orders.php',
					type: 'post',
					data: postData,
					dataType: 'json',
					async: true
				}).done(function(r){
					var data = r.orders;
					var me = r.me;
					if(data.customer){
						$("#customername").val(data.customer["customername"]);
						$("#customerruby").val(data.customer["customerruby"]);
						$("#email").val(data.customer["email"]);
						$("#tel").val(data.customer["tel"]).blur();
						$("#zipcode1").val(data.customer["zipcode"]).blur();
						$("#addr0").val(data.customer["addr0"]);
						$("#addr1").val(data.customer["addr1"]);
						$("#addr2").val(data.customer["addr2"]);
						
						$(":radio[name='repeater']", "#userinfo").val([2]);
						
						$("#deli_selector_wrap").hide();
						if(data.customer["deli"]==1){
							$('#deli').attr("checked","checked");
							$('#deli_list').show();
							$("#deli_id").val(data.customer["deli_id"]);
							$("#organization").val(data.customer["organization"]);
							$("#delitel").val(data.customer["delitel"]).blur();
							$("#zipcode2").val(data.customer["delizipcode"]).blur();
							$("#deliaddr0").val(data.customer["deliaddr0"]);
							$("#deliaddr1").val(data.customer["deliaddr1"]);
							$("#deliaddr2").val(data.customer["deliaddr2"]);
						}else{
							$('#deli').attr("checked",false);
							$('#deli_list').hide();
						}
						
						if(data.customer["member"]>0){
							var postData = {};
							$.ajax({
								url: '/php_libs/check_login.php',
								type: 'post',
								data: postData,
								dataType: 'json',
								async: true
							}).done(function(data){
								if(data.delivery){
									var deli = data.delivery;
									var opt = "";
									for(var i=0; i<deli.length; i++){
										opt += '<option value="'+i+'">'+deli[i]["organization"]+'</option>';
									}
									$("#deli_selector_wrap").show();
									$("#deli_selector").html(opt);
									
									$.user.deli = data.delivery;
								}
							}).fail(function(xhr, status, error){
								alert("Error: "+error+"<br>xhr: "+xhr);
							});
							$(":radio[name='repeater']", "#userinfo").val([2]);
							//$('#userinfo input[type="text"]').attr("readonly", "readonly");
						}else{
							$(":radio[name='repeater']", "#userinfo").val([1]);
							$('#userinfo input[type="text"]').removeAttr("readonly");
						}
						
						$("#login_wrap").hide();
						$("#userinfo").show();
						$('#step5 .goto_confirm').show();
						
					}else{
						$.login("");
					}
				}).fail(function(xhr, status, error){
					alert("Error: "+error+"<br>xhr: "+xhr);
				});
			}
		},
		setCustomer2: function(args){
				var postData = {'getcustomer':'true'};
				$.ajax({
					url: 'user_login.php',
					type: 'post',
					data: postData,
					dataType: 'json',
					async: true
				}).done(function(me){
					// ログインしない場合、通常表示
	    		if(typeof me.id =='undefined'){
						$('#email').attr('readonly',false);
						$('.login_nodisplay').show();
						$('#pass').show();
						$('#mypage_msg').hide();
						$('#member_login').show();
						$('#customername').val("");
						$('#customerruby').val("");
						$('.login_display').hide();
						$('#delivery_customer').hide();
						$('#customername').attr('readonly',false);
						$('#customerruby').attr('readonly',false);
						$('#tel').attr('readonly',false);
						$('#zipcode1').attr('readonly',false);
						$('#addr0').attr('readonly',false);
						$('#addr1').attr('readonly',false);
						$('#addr2').attr('readonly',false);
					} else {
						$('.g_ft').attr('style','');
						// ログインした場合、住所リストを作成
						$('#email').val(me.email);
						$('.login_nodisplay').hide();
						$('#pass').hide();
						$('#mypage_msg').show();
						$('#member_login').hide();
						$('#customername').val(me.customername);
						$('#customerruby').val(me.customerruby);
						$('.login_display').show();
						$('#delivery_customer').show();
						$('#delivery_customer').children().remove();
						$('#delivery_customer').append($('<option>').attr({ value: '-1' }).text('●ご住所'));
						if(typeof me.delivery != 'undefined'){
							for(var i=0; i<me.delivery.length; i++){
								$('#delivery_customer').append($('<option>').attr({ value: me.delivery[i].id }).text('●お届先' + (i+1)));
							}
						}

						$('#email').attr('readonly',true);
						$('#customername').attr('readonly',true);
						$('#customerruby').attr('readonly',true);
						$('#tel').attr('readonly',true);
						$('#zipcode1').attr('readonly',true);
						$('#addr0').attr('readonly',true);
						$('#addr1').attr('readonly',true);
						$('#addr2').attr('readonly',true);

						if(typeof me.zipcode=='undefined' || me.zipcode == null || me.zipcode == "" ){
							$('#email').attr('readonly',false);
							$('#customername').attr('readonly',false);
							$('#customerruby').attr('readonly',false);
							$('#tel').attr('readonly',false);
							$('#zipcode1').attr('readonly',false);
							$('#addr0').attr('readonly',false);
							$('#addr1').attr('readonly',false);
							$('#addr2').attr('readonly',false);
						}

						if($('#tel').val() == "") {
							$('#tel').val(me.tel);
							$('#zipcode1').val(me.zipcode);
							$('#addr0').val(me.addr0);
							$('#addr1').val(me.addr1);
							$('#addr2').val(me.addr2);
						}
					}
				}).fail(function(xhr, status, error){
					alert("Error: "+error+"<br>xhr: "+xhr);
				});
		},
		login: function(fd){
			$.ajax({
				url: '/php_libs/check_login.php',
				type: 'post',
				processData: false,
				contentType: false,
				data: fd,
				dataType: 'json',
				crossDomain: false,
				async: true
			}).done(function(data){
				if(!data){
					return;
				}else if(data.user["error"]){
					$.msgbox(data.user["error"]);
					return;
				}

				$("#customername").val(data.user["customername"]);
				$("#customerruby").val(data.user["customerruby"]);
				$("#email").val(data.user["email"]);
				$("#tel").val(data.user["tel"]).blur();
				$("#zipcode1").val(data.user["zipcode"]).blur();
				$("#addr0").val(data.user["addr0"]);
				$("#addr1").val(data.user["addr1"]);
				$("#addr2").val(data.user["addr2"]);
				
				$(":radio[name='repeater']", "#userinfo").val([2]);
				
				if(data.delivery){
					var deli = data.delivery;
					$("#deli_id").val(deli[0]["id"]);
					$("#organization").val(deli[0]["organization"]);
					$("#delitel").val(deli[0]["delitel"]).blur();
					$("#zipcode2").val(deli[0]["delizipcode"]).blur();
					$("#deliaddr0").val(deli[0]["deliaddr0"]);
					$("#deliaddr1").val(deli[0]["deliaddr1"]);
					$("#deliaddr2").val(deli[0]["deliaddr2"]);
					
					var opt = "";
					for(var i=0; i<deli.length; i++){
						opt += '<option value="'+i+'">'+deli[i]["organization"]+'</option>';
					}
					$("#deli_selector_wrap").show();
					$("#deli_selector").html(opt);
				}
				
				$.user.deli = data.delivery;
				//$('#userinfo input[type="text"]').attr("readonly", "readonly");
				$("#login_wrap").hide();
				$("#userinfo").show();
				$('#step5 .goto_confirm').show();
				$('#member').val(data.user["id"]);
				var f=document.forms.loginform;
				f.email.value="";
				f.pass.value="";
			}).fail(function(xhr, status, error){
				alert("Error: "+error+"<br>xhr: "+xhr);
			});
		},
		checkEmail: function(email){
			var postData = {'check':'1', 'email':email};
			$.ajax({
				url: '/php_libs/check_login.php',
				type: 'post',
				data: postData,
				dataType: 'json',
				async: true
			}).done(function(data){
				if(!data.user){
					return;
				}
				$.msgbox("メールアドレス："+email+"は登録済みです。<br>ログイン画面からお願いいたします。<br><a href='/user/resend_pass.php' target='_brank'>パスワードを忘れた方はこちらへ</a>");
				$("#show_loginwrap").click();
				
			}).fail(function(xhr, status, error){
				alert("Error: "+error+"<br>xhr: "+xhr);
			});
		},
		getPage: function(itemid, colorcode){
		/*
		*	アイテムカラーとサイズごとの枚数を設定してセッション情報を返す
		*
		*	return		[{'cateogryid','categorykey','categoryname','itemie','itemcode','itemname','posid','colorcode',vol:{size_id:枚数}}]
		*/
			var res = [];
			$.temp.colorcode = [];
			$.temp.volume = [];
			$.ajax({
				url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'page', 'itemid':itemid, 'colorcode':colorcode}, 
				success:function(r){
					if(r.length==0) return;
					res = r;
					for(var i=0; i<r.length; i++){
						$.temp.colorcode[i] = r[i].colorcode;
						$.temp.volume[i] = r[i].vol;
					}
				}
			});
			
			return res;
		},
		setCart: function(r){
		/*
		*	カートページのアイテム情報を生成
		*	@r		reqDetail
		*/
			$('.itemsum, .printfee, .totamount, .total, .perone, .base, .tax, .credit', '#estimation_wrap').text('0');
			var dat = '';
			if(r.category.length==0){
				$('#estimation_wrap tbody').html('<tr><td colspan="7"></td><td class="last"></td></tr>');
				$.updateOptions(r);
				$.msgbox('カートに商品はありません。<hr><br>カートの商品は出し入れ自由です。お気軽にお見積りをご確認ください。');
			}else{
				var c = '';
				for(var i=0; i<r.category.length; i++){
					c = r['category'][i];
					var thumbName = c['itemcode']+'_'+c['colorcode'];
					/*
					if(c['colorcode']!=''){
						thumbName += '_'+c['colorcode'];
					}
					*/
					//var folder = (c['categorykey']=='baby')? 't-shirts': c['categorykey'];
					var folder = c['categorykey'];
					var thumb = '<img alt="" src="'+_IMG_PSS+'items/'+folder+'/'+c['itemcode']+'/'+thumbName+'_s.jpg" height="26" />';
					for(var sizeid in c['size']){
						if(c['size'][sizeid]['amount']==0) continue;
						var hash = c['size'][sizeid];
						var sub = hash.cost * hash.amount;
						dat += '<tr>';
						dat += '<td><span class="btn_sub" onclick="$.editItem(\''+c.categorykey+'\','+c.itemid+');">変更</span>'+thumb+'<p>'+c.itemname+'<p/>カラー：　'+c.colorname+'</td>';
						/*dat += '<td></td>';*/
						dat += '<td class="ac">'+hash.sizename+'</td>';
						dat += '<td class="ar">'+hash.cost+'</td>';
						dat += '<td class="ac"><input type="number" value="'+hash.amount+'" min="1" step="1" class="args_'+c.categoryid+'_'+c.itemid+'_'+c.colorcode+'_'+sizeid+'" /> 枚</td>';
						dat += '<td class="ar"><p>'+$.addFigure(sub)+'</p><span class="btn_sub del_cart" onclick="$.deleteitem(this,'+c.categoryid+','+c.itemid+',\''+c.colorcode+'\','+sizeid+');">削除</span></td></tr>';
						/*dat += '<td class="ac"></td>';*/
					}
				}
				
				$('#estimation_wrap tbody').html(dat);
				
				/* 見積り金額の更新 */
				$.updateEstimation(r);
				$.next(3);
			}
		},
		editItem: function(catkey, itemid){
		/*
		*	カートの[変更]ボタンでStep2へ遷移
		*/
			$.itemparam.itemid = itemid;
			$.itemparam.categoryid = $('#category_selector option[value='+catkey+']').attr('rel');
			$.itemparam.categorykey = catkey;
			$.itemparam.categoryname = $('#category_selector option[value='+catkey+']').text();
			$.changeItem(itemid);
			$.next(1);
		},
		changeItem: function(itemid){
		/*
		*	商品の指定でStep2 のタグ生成
		*/
			$('.pane:gt(0)', '#step2').remove();
			// var folder = ($.itemparam.categorykey=='baby')? 't-shirts': $.itemparam.categorykey;
			$.itemparam.itemid = itemid;
			$.getPage(itemid, '');
			var args = ['',''];
			if($.temp.colorcode.length>0){
				args[0] = $.temp.colorcode[0];
				args[1] = $.temp.volume[0];
			}
			$.showSizeform(itemid, args[0], args[1]);
			$('.thumb_wrap', '#step2').addClass('throbber');
			$('.item_image', '#step2').fadeOut('fast');
			$('.color_thumb').fadeOut('fast', function(e){
				$.getJSON($.TLA.api+'?callback=?', {'act':'itemattr', 'itemid':itemid, 'output':'jsonp'}, function(r){
					var color_count = 0;
					var thumbs = '';
					var folder = '';
					$.each(r.category, function(categorykey, categoryname){
						folder = categorykey;
					});
					$.each(r.name, function(itemcode, itemname){
						$.itemparam.itemcode = itemcode;
						$.itemparam.itemname = itemname;
					});
					$('#cur_item_name').attr('class','prop_'+r.ppid+'_'+r.maker).text($.itemparam.itemname);
					var path = folder+'/'+$.itemparam.itemcode;
					$.each(r.code, function(code, colorname){
        				color_count++;
        				var colorcode = code.split('_')[1];
        				thumbs += '<li';
						if((color_count==1 && $.temp.colorcode.length==0) || $.temp.colorcode[0]==colorcode){
							$.itemparam.colorname = colorname;
							$.itemparam.colorcode = colorcode;
							$('.item_image', '#step2').html('<img alt="'+code+'" src="'+_IMG_PSS+'items/'+path+'/'+code+'.jpg" width="300">');
							thumbs += ' class="nowimg"';
						}
						thumbs += '><img alt="'+colorcode+'" title="'+colorname+'" src="'+_IMG_PSS+'items/'+path+'/'+code+'_s.jpg" /></li>';
					});
					$('.color_thumb').html(thumbs);
					$('.num_of_color').text(color_count);
					$('.notes_color').text($.itemparam.colorname);
					$('.color_thumb li img').imagesLoaded( function(){
						$('.color_thumb', '#step2').fadeIn('fast', function(){
							$('.item_image', '#step2').fadeIn('fast', function(){
								if($.temp.colorcode.length>1) $.showSelectedItemcolors();
							});
						});
						$('.thumb_wrap', '#step2').removeClass('throbber');
					});
					$.temp.categoryid = '';
					$.temp.itemid = '';
				});
			});
			
			/* タグで受取るタイプ
			$('#color_thumb, #item_image').fadeOut('fast', function(){
				$.ajax({url:'../php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
					data:{'act':'itemattr','itemid':itemid}, success: function(r){
						dat = r.split('|');
						$('#item_image').html(dat[0]);
						$('#color_thumb').html(dat[1]);
						$('#num_of_color').text(dat[2]);
						$('#notes_color').text(dat[3]);
						
						$('#color_thumb li img').imagesLoaded( function(){
							$('#color_thumb, #item_image').fadeIn();
							
							var code = $('#item_image img').attr('alt').split('_');
							
							$.itemparam.itemcode = code[0];
							$.itemparam.itemname = $('#item_selector option:selected').text();
							
							$.itemparam.colorname = $('#color_thumb li.nowimg img').attr('alt');
							$.itemparam.colorcode = code[1];
						});
					}
				});
			});
			*/
			
		},
		showSelectedItemcolors: function(){
		/*
		*	複数のカラーが選択されている場合のStep2のタグ生成
		*/
			
			var target = $('.btn_line', '#step2');
			
			for(var i=1; i<$.temp.colorcode.length; i++){
				var clone = $('.pane:first', '#step2').clone();
				// 指定カラーを設定
				var colorcode = $.temp.colorcode[i];
				clone.find('.color_thumb li.nowimg').removeClass('nowimg');
				var my = clone.find('.color_thumb li img[alt="'+colorcode+'"]').parent('li');
				my.addClass('nowimg');
				var imgname = $('img', my).attr('src').replace('_s.jpg', '.jpg');
				var code = $.itemparam.itemcode+'_'+colorcode;
				var colorname = $('img', my).attr('title');
				clone.find('.item_image img').attr({'src':imgname, 'alt':code});
				clone.find('.notes_color').html(colorname);
				
				// パラメーターを設定
				$.itemparam.colorname = colorname;
				$.itemparam.colorcode = colorcode;
				
				// サイズテーブルを初期化
				$.showSizeform($.itemparam.itemid, colorcode, $.temp.volume[i], clone);
				
				// 削除ボタンを追加
				clone.prepend('<p class="btn_line"><ins class="del_item_color"><img src="/common/img/delete.png" alt="削除">削除</ins></p>');
				
				clone.insertBefore(target);
			}
			
		},
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
		*	@target			サイズテーブルの親要素のjQueryオブジェクト（.pane）
		*/
			var pane = (arguments.length==4)? arguments[3]: $('.pane', '#step2');
			$.itemparam.itemid = itemid;
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
						size_body = '<th>'+$.addFigure(val['cost'])+' 円</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'"><input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" onblur="$.blurNumber(this);"/></td>';
					}else if(cost != val['cost'] || (val['id']>(++pre_sizeid) && val['id']>10)){	// 単価が違うかまたは、サイズ160以下を除きサイズが連続していない
						size_table += '<tr class="heading">'+size_head+'</tr>';
						size_table += '<tr>'+size_body+'<td>枚</td></tr>';
						
						pre_sizeid = val['id'];
						cost = val['cost'];
						size_head = '<th></th><th>'+val['name']+'</th>';
						size_body = '<th>'+$.addFigure(val['cost'])+' 円</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'"><input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" onblur="$.blurNumber(this);"/></td>';
					}else{
						pre_sizeid = val['id'];
						size_head += '<th>'+val['name']+'</th>';
						size_body += '<td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'"><input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" /></td>';
					}
		        });
		        size_table += '<tr class="heading">'+size_head+'</tr>';
				size_table += '<tr>'+size_body+'<td>枚</td></tr>';
				$('.sizeprice .size_table tbody', pane).html(size_table);
				$('.sizeprice .size_table caption', pane).html(r[0]["master_id"]);
				pane.find('.cur_amount').text($.addFigure(sum));
				
				// 合計枚数
				var tot = 0;
				$('.cur_amount').each( function(){
					tot += $(this).text().replace(/,/g, '')-0;
				});
				$('#tot_amount').text($.addFigure(tot));
				
				//volume = {};
		    });
		},
		showPrintPosition: function(){
		/*
		*	プリント位置画像（絵型）とインク色数指定のタグ生成
		*	@第一引数が有る場合は移動しない
		*/
			var isResult = false;
			$.ajax({url:'/php_libs/t_orders.php', async:false, type:'POST', dataType:'text', data:{'act':'orderposition', 'itemid':$.itemparam.itemid, 'catid':$.itemparam.categoryid}, 
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
						$('#note_printmethod').val(val[2]);
						$('#pos_wrap').html('<h3>'+$.itemparam.categoryname+'<span>カテゴリー</span></h3>'+val[0]);
						$.setPrintposEvent();
					}
				}
			});
			
			if(isResult && arguments.length==0){
				$.next(2);
			}
			
			return isResult;
		},
		setPrintposEvent: function(){
		/* 
		*	プリント位置画像のロールオーバーとクリックイベント
		*	複数指定可、クリックで指定を切替
		*/
			if($('#pos_wrap').children('div').attr('class').split('_')[1]==46) return;		// プリントなし商品
//			$('select', '#pos_wrap').uniform({fileDefaultText:''});
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
//							img.attr('src', src).removeClass('cur');
//							tbody.find('tr.pos-'+img.attr('class')).remove();
						} else {
							var cur = img.siblings('.cur');
							if (cur.length) {
								cur.attr('src',cur.attr('src').replace(/_on.png/,'.png')).removeClass('cur');
								tbody.find('tr.pos-'+cur.attr('class')).remove();
							}
							var posname = img.attr('alt');
							var tr = '<tr class="pos-'+img.attr('class')+'">';
							tr += '<th>'+posname+'</th>';
							tr += '<td><select class="areasize">';
							tr += '<option value="0" selected="selected">大</option><option value="1">中</option><option value="2">小</option>';
							tr += '</select></td>';
							tr += '<td><select class="ink"><option value="0" selected="selected">選択してください</option>';
							tr += '<option value="1">1色</option><option value="2">2色</option><option value="3">3色</option>';
							tr += '<option value="9">4色以上</option></select></td>';
							
							/*
							tr += '<td>';
							tr += '<form name="uploaderform" action="/php_libs/orders.php" target="upload_iframe" method="post" enctype="multipart/form-data">';
							tr += '<input type="hidden" name="act" value="update" />';
							tr += '<input type="hidden" name="mode" value="attach" />';
							tr += '<input type="hidden" name="posid" value="'+posid+'" />';
							tr += '<input type="hidden" name="base" value="'+base+'" />';
							tr += '<input type="hidden" name="posname" value="'+posname+'" />';
							tr += '<input type="hidden" name="attachname[]" value="" />';
							tr += '<input type="file" name="attach[]" class="attach" onchange="this.form.submit()" /><img alt="取消" src="/common/img/delete.png" class="del_attach" />';
							tr += '</form>';
							tr += '</td>';
							*/
							
							tr += '</tr>';
							img.attr('src', src_on).addClass('cur');
							tbody.append(tr);
							
//							var added = tbody.children('tr:last');
//							added.find('select').uniform({fileDefaultText:''});
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
		*	インク指定の確認
		*/
			var isInks = false;
			if($('#pos_wrap .inkbox .ink').length>0 && !$('#noprint').prop('checked')){
				$('#pos_wrap .inkbox .ink').each( function(){
					if($(this).val()!=0){
						isInks = true;
						return false;	// break;
					}
				});
			}else{
				isInks = true;	// プリントなし商品はインク指定もなし
			}
			
			if(!isInks){
				$.msgbox('プリントするデザインの色数を指定してください。');
			};
			
			return isInks;
		},
		updateItem: function(){
		/*
		*	カートに商品を追加、枚数の更新、削除　枚数合計が0枚の場合は処理を中止してメッセージを出す
		*	@引数がある場合は削除
		*/
			var postData = {};
			var mode = 'update';
			var isResult = false;
			if(arguments.length>0){
				mode = 'remove';
				var args = arguments[0];
				postData = {'act':'remove', 'mode':'items', 'categoryid':args[0], 'itemid':args[1], 'colorcode':args[2], 'sizeid':args[3]};
			}else{
				postData = {'act':'update', 'mode':'items'};
				for(var key in $.itemparam){
					postData[key] = $.itemparam[key];
				}
				var prop = $('#cur_item_name').attr('class').split('_');
				postData['posid'] = prop[1];
				postData['makerid'] = prop[2];
				postData['noprint'] = $('#noprint').prop('checked')? 1: 0;
				
				// 全サイズを上書する
				var color_code = [];
				var color_name = [];
				$('.pane .thumb_wrap', '#step2').each( function(index){
					color_code[index] = $('.item_image img', this).attr('alt').split('_')[1];
					color_name[index] = $(this).find('.notes_color').text();
				});
				var totAmount = 0;
				postData['sizeid'] = [];
				postData['sizename'] = [];
				postData['cost'] = [];
				postData['amount'] = [];
				postData['colorcode'] = [];
				postData['colorname'] = [];
				postData['master_id'] = [];
				$('.size_table', '#step2').each( function(index){
					var master_id = $(this).find('caption').text();
					$('tbody tr:not(".heading") td[class*="size_"]', this).each( function(){
						var amount = $(this).children('input').val()-0;
						var tmp = $(this).attr('class').split('_');
						postData['sizeid'].push(tmp[1]);
						postData['sizename'].push(tmp[2]);
						postData['cost'].push(tmp[3]);
						postData['amount'].push(amount);
						postData['colorcode'].push(color_code[index]);
						postData['colorname'].push(color_name[index]);
						postData['master_id'].push(master_id);
						totAmount += amount;
					});
				});
				
				if(totAmount==0){
					$.msgbox('アイテムの枚数を指定してください。');
					return false;
				}
			}
			var curRow = 0;
			$.ajax({url:'/php_libs/t_orders.php', async:false, type:'POST', dataType:'json', data:postData, 
				success:function(r){
					if(r.length!=0){
						isResult = r;
					}
				}
			});
		
			return isResult;
		},
		updatePosition: function(){
		/*
		*	プリント位置とインク色数
		*/
			var box = $('#pos_wrap div[class^=ppid_]');
			var posid = $(box[0]).attr('class').split('_')[1];
			var base = [];
			var poskey = [];
			var posname = [];
			var cat_type = [];
			var item_type = [];
			var area_key = [];
			var area_size = [];
			var ink = [];
			var isResult = false;
			var noprint = $('#noprint').prop('checked')? 1: 0;
			
			box.each( function(){
				var base_name = $('.inkbox table caption', this).text();
				$('.inkbox table tbody tr', this).each( function(){
					var my = $(this);
					base.push(base_name);
					posname.push( my.children('th:first').text() );
					if(posid!=46 && noprint==0){
						ink.push( my.find('.ink').val() );
						area_size.push( my.find('.areasize').val() );
						poskey.push( my.attr("class").split("-")[1] );
					}else{
						ink.push(0);
						area_size.push(0);
						poskey.push("");
					}
					var $tfoot = my.parent().siblings("tfoot").find("tr:first");
					cat_type.push( $tfoot.find("td:first").text() );
					item_type.push( $tfoot.find("td:eq(1)").text() );
					area_key.push( $tfoot.find("td:last").text() );
				});
//				if ($.isset(function(){return base[base_name]})==false) {
//					base.push(base_name);
//					posname.push("");
//					ink.push(0);
//					area_size.push(0);
//					poskey.push("");
//					cat_type.push("");
//					item_type.push("");
//					area_key.push("");
//				}
			});
			
			$.ajax({url:'/php_libs/t_orders.php', async:false, type:'POST', dataType:'json', 
				data:{'act':'update','mode':'design', 'posid':posid, 'base':base, 'poskey':poskey, 'areasize':area_size, 'posname':posname, 'ink':ink, 'categorytype':cat_type, 'itemtype':item_type, 'areakey':area_key}, success: function(r){
					if(r.length!=0){
						isResult = r;
					}
				}
			});
			
			return isResult;
		},
		deleteitem: function(my, catid, itemid, colorcode, sizeid){
		/*
		*	見積り明細テーブルでアイテムの削除
		*/
			var args = [catid, itemid, colorcode, sizeid];
			var tr = $(my).closest('tr');
			var msg = tr.children('td:eq(1)').html()+'<br />サイズ：'+tr.children('td:eq(2)').text()+' を削除します。よろしいですか？';
			$.confbox.show(
				msg, 
				function(){
					if($.confbox.result.data){
						var dat = $.updateItem(args);
						if(dat.category.length==0){
							$.back(0);
						}else{
							tr.remove();
						}
						$.updateEstimation(dat);
					} 
				}
			);
		},
		updateEstimation: function(r){
			$('#estimation_wrap .itemsum').text($.addFigure(r.itemprice));
			$('#estimation_wrap .printfee').text($.addFigure(r.printprice));
			$('#estimation_wrap .totamount').text(r.amount);
			var sizeName = ['大', '中', '小'];
			var printName = {'silk':'シルク', 'digit':'転写', 'inkjet':'インクジェット'};
			var print_size = '';
			var print_pos = '';
			var ink_count = '';
			for(var i=0; i<r.design.length; i++){
//				var sect = r.design[i]['pos']+'-'+r.design[i]['size']+'-'+r.design[i]['ink'];
//				for (var printCode in r.printing[sect]) {
//					var fee = r.printing[sect][printCode];
//					ink_count += '<p>'+printName[printCode]+':'+fee+'円</p>';
//				}
				var ink = r.design[i]['ink']==9? '4色以上': r.design[i]['ink']+'色';
				print_size += '<p>'+sizeName[r.design[i]['size']]+'</p>';
				print_pos += '<p>'+r.design[i]['pos']+'</p>';
				ink_count += '<p>'+ink+'</p>';
			}
			$('#estimation_wrap .print_size').html(print_size);
			$('#estimation_wrap .print_pos').html(print_pos);
			$('#estimation_wrap .ink_count').html(ink_count);
			
			$.updateOptions(r);
		},
		updateOptions: function(r){
			var base = r.options.itemprice + r.options.printprice + r.options.optionfee;
			var tax = Math.floor(base*_TAX);
			var total = Math.floor(base*(1+_TAX));
			var credit = 0;
			if(r.options.payment==3){
				credit = Math.ceil(total*_CREDIT_RATE);
				total += credit;
			}
			var perone = Math.ceil(total/r.options.amount);
			$('#estimation_wrap .base').text($.addFigure(base));
			$('#estimation_wrap .tax').text($.addFigure(tax));
			$('#estimation_wrap .credit').text($.addFigure(credit));
			$('#estimation_wrap .total').text($.addFigure(total));
			$('#estimation_wrap .perone').text($.addFigure(perone));
			$('#estimation_wrap .carriage').text(r.options.carriage);
			$('#estimation_wrap .codfee').text(r.options.codfee);
			$('#estimation_wrap .conbifee').text(r.options.conbifee);
			$('#estimation_wrap .discountfee').text($.addFigure(r.options.discount));
			$('#estimation_wrap .package').text(r.options.packfee);
			$('#estimation_wrap .expressfee').text($.addFigure(r.options.expressfee));
			
			// 割引種類
			var spec = '';
			if(r.options.discount!=0){
				spec = r.options['discountname'].toString();
			}
			$('.discountname', '#estimation_wrap').text(spec);
			
			// 製作日数不足
			if(r.options.expressError!=""){
				$.msgbox(r.options.expressError+"\nご希望納期をご確認ください。");
				$('#express_notice ins').text("ご希望納期をご確認ください。").closest('p').show();
				$('#deliveryday').val("");
			}else{
			// 特急料金の種類
				spec = '';
				if(r.options.expressfee!=0){
					spec = r.options['expressInfo'];
					$('#express_notice ins').text('特急料金がかかります。（'+spec+'）').closest('p').show();
					
					// 翌日仕上げの場合に袋詰めを選択不可にする
					if (spec=='翌日仕上げ') {
						var isPack = $(':radio[name=pack]:checked', '#option_table').val();
						if (isPack==0) {
							$(':radio[name=pack]', '#option_table').prop('disabled', true);
						} else if (r.options.packfee==0) {
							$(':radio[name=pack]', '#option_table').val([0]).prop('disabled', true);
						} else {
							$(':radio[name=pack]', '#option_table').prop('disabled', false);
						}
					} else {
						$(':radio[name=pack]', '#option_table').prop('disabled', false);
					}
				}else{
					$('#express_notice ins').text('').closest('p').hide();
					$(':radio[name=pack]', '#option_table').prop('disabled', false);
				}
				$('.expressinfo', '#estimation_wrap').text(spec);
			}
			
			// お見積りBOX
			$.digits( $('tr.total td span', '#floatingbox'), total);
			$('tr.total td span', '#showcart_box').text($.addFigure(total));
			$('tr:first td span', '#floatingbox, #showcart_box').text(r.options.amount);
			$('tr:last td span', '#floatingbox').text($.addFigure(perone));
		},
		updateUser: function(){
		/*
		*	ユーザー情報の登録
		*/
			var postData = {'act':'update', 'mode':'customer'};
			$('input[type=text]:not([name$=_text]), :radio[name=repeater]:checked, #deli, #deli_id, textarea, select, #pass, #member', '#userinfo').each( function(){
				var key = $(this).attr('name');
				if(key==="undefined"){
					return true;	// continue
				}else if(key=="deli"){
					if($(this).is(":checked")){
						postData[key] = 1;
					}else{
						postData[key] = 0;
					}
				}else{
					postData[key] = $(this).val();
				}
			});
			$.ajax({url:'/php_libs/t_orders.php', async:false, type:'POST', dataType:'text', data:postData});
		},
		confirm: function(){
		/*
		*	ユーザー情報の必須項目の入力確認と注文内容の表示
		*	['customer']['customername']
		*				['email']
		*				['tel']
		*				['zipcode']
		*				['addr1']
		*				['addr2']
		*				['comment']
		*				['repeater']
		*/
			var email = $('#email').val().trim();
			if (!$.check_email(email)) {
				return;
			}
			
			// 新規ユーザーの場合、ユーザー存在チェック
			var isExistUser = false;
			if ($('#pass').is(':visible')) {
//				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'checkemail', 'email':$("#email").val(), 'reg_site':'1'},
				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'checkemail', 'args':[email]}, 
					success: function(r){
						if(r.length!=0){
							var msg = '<h1>登録済みのメールアドレスです！</h1>';
							msg += '<p>E-mail：　'+email+'</p>';
							msg += '<p>ログインしてください。</p>';
							msg += '<p><a href="/user/resend_pass.php" target="_brank">パスワードの再発行はこちらへ</a></p>';
							$.msgbox(msg);
							isExistUser = true;
							return;
						}
					}
				});
			}
			if (isExistUser) {
				return;
			}
			
			var required = [];
			if($('#customername').val().trim()=='') required.push('<li>お名前</li>');
			if(email=='') required.push('<li>メールアドレス</li>');
			if($('#pass').is(':visible') && $('#pass').val().trim()=='') required.push('<li>パスワード</li>');
			if($('#tel').val().trim()=='') required.push('<li>お電話番号</li>');
			if($('#addr1').val().trim()=='') required.push('<li>ご住所</li>');
			if(!$(':radio[name=repeater]:checked').val()) required.push('<li>弊社ご利用について</li>');
			if($('#deli').is(':checked')){
				if($('#organization').val().trim()=='') required.push('<li>宛名</li>');
				if($('#delitel').val().trim()=='') required.push('<li>お届け先のお電話番号</li>');
				if($('#deliaddr1').val().trim()=='') required.push('<li>お届け先ご住所</li>');
			}
			var required_list = '<ul class="msg">'+required.toString().replace(/,/g,'')+'</ul>';
			if(required.length>0){
				$.msgbox("必須項目の入力をご確認ください。<br />"+required_list);
				return;
			}
			
			// ユーザー情報の登録
			$.updateUser();
			
			var isError = false;
			$.ajax({url:'/php_libs/t_orders.php', async:false, type:'POST', dataType:'json', 
				data:{'act':'confirm'}, success: function(r){
					if(r.length==0){
						isError = true;
						return;
					}
					var isPopup = false;
					var item_price = 0;
					var amount = 0;
					var thumbName = '';
					var folder = '';
					var thumb = '';
					var tr = '';
					var items = r.items;
					for(var i=0; i<items.length; i++){
						thumbName = items[i]['code'] + '_'+items[i]['colorcode'];
						folder = (items[i]['categorykey']=='baby')? 't-shirts': items[i]['categorykey'];
						thumb = '<img alt="" src="'+_IMG_PSS+'items/'+folder+'/'+items[i]['code']+'/'+thumbName+'_s.jpg" height="26" />';
						tr += '<tr>';
						tr += '<td>'+items[i]['name']+'<br/ >'+thumb+'<span>カラー： '+items[i]['color']+'</span></td>';
						
						var size = '';
						var cost = '';
						var vol = '';
						var sub = '';
						for(var j=0; j<items[i]['size'].length; j++){
							size += '<p>'+items[i]['size'][j]['sizename']+'</p>';
							cost += '<p>'+$.addFigure(items[i]['size'][j]['cost'])+'</p>';
							vol += '<p>'+items[i]['size'][j]['amount']+'<span>枚</span></p>';
							sub += '<p>'+$.addFigure(items[i]['size'][j]['subtotal'])+'<span>円</span></p>';
							amount += items[i]['size'][j]['amount']-0;
							item_price += items[i]['size'][j]['subtotal']-0;
						}
						tr += '<td class="ac">'+size+'</td>';
						tr += '<td class="ar">'+cost+'</td>';
						tr += '<td class="ar">'+vol+'</td>';
						tr += '<td class="ar">'+sub+'</td>';
						tr += '</tr>';
						
						// メーカー「ザナックス」の有無を確認
						if(items[i]['makerid']==10 || items[i]['code']=='du-001'){
							isPopup = true;
						}
					}
					
					if(isPopup){
						$('#sendorder').addClass('popup');
					}else{
						$('#sendorder').removeClass('popup');
					}
					
					var option = r.option;
					tr += '<tr><td colspan="3" class="ac">小計</td><td class="ar">'+$.addFigure(amount)+'<span>枚</span></td><td class="ar">'+$.addFigure(item_price)+'<span>円</span></td></tr>';
					tr += '<tr><td colspan="4">プリント代</td><td class="ar">'+$.addFigure(option.printprice)+'<span>円</span></td></tr>';
					
					var optionfee = 0;
					var optname = {'discount':'割引', 'carriage':'送料', 'codfee':'代引手数料', 'conbifee': 'コンビニ手数料', 'packfee':'袋詰代', 'expressfee':'特急料金'};
					var pack = '希望しない';
					if(r.option.pack==1){
						pack = '希望する';
					}else if(r.option.pack==2){
						pack = '袋のみ';
					}
					for(var m in option){
						var spec = '';
						if(m=='discount'){
							if(option[m]!=0) spec = '（'+option['discountname'].toString()+'）';
							else spec = '';
							tr += '<tr><td colspan="4">'+optname[m]+'<span id="spec_discount">'+spec+'</span></td><td class="ar fontred">▲<ins id="conf_discount">'+$.addFigure(option[m])+'<ins><span>円</span></td>';
						}else if(m=='carriage'){
							tr += '<tr><td colspan="4">'+optname[m]+'<span>（30,000円以上で<ins class="fontred">送料無料</ins>）</span></td><td class="ar"><ins id="conf_carriage">'+option[m]+'</ind><span>円</span></td>';
						}else if(m=='codfee' && option[m]!=0){
							tr += '<tr><td colspan="4">'+optname[m]+'</td><td class="ar">'+option[m]+'<span>円</span></td>';
						}else if(m=='conbifee' && option[m]!=0){
							tr += '<tr><td colspan="4">'+optname[m]+'</td><td class="ar">'+option[m]+'<span>円</span></td>';
						}else if(m=='packfee'){
							tr += '<tr><td colspan="4">'+optname[m]+'<span>（'+pack+'）</span></td><td class="ar"><ins id="conf_pack">'+$.addFigure(option[m])+'</ind><span>円</span></td>';
						}else if(m=='expressfee'&& option['expressInfo']!=''){
							tr += '<tr><td colspan="4">'+optname[m]+'<span>（'+option['expressInfo']+'）</span></td><td class="ar"><ins id="conf_expressfee">'+$.addFigure(option[m])+'</ind><span>円</span></td>';
						}
					}
					$('#conf_item tbody').html(tr);
					
					var base = item_price + option.printprice + option.optionfee;
					var tax = Math.floor(base*_TAX);
					var total = Math.floor(base*(1+_TAX));
					var credit = 0;
					if(option.payment==3){
						credit = Math.ceil(total*_CREDIT_RATE);
						total += credit;
					}
					var perone = Math.ceil(total/amount);
					$('#conf_item tfoot .base ins').text($.addFigure(base));
					$('#conf_item tfoot .tax ins').text($.addFigure(tax));
					$('#conf_item tfoot .credit ins').text($.addFigure(credit));
					$('#conf_item tfoot .tot ins').text($.addFigure(total));
					$('#conf_item tfoot .per ins').text($.addFigure(perone));
					
					var attachfiles = '';
					if(r.option.attach.length==0){
						attachfiles = 'なし';
					}else{
						for(var a=0; a<r.option.attach.length; a++){
							attachfiles += '<p>'+r.option.attach[a]+'</p>';
						}
					}
					$('#conf_attach').html(attachfiles);
					
					$('#conf_note_design').html('');
					for(var u in r.user){
						if(u=='deli'){
							continue;
						}else if(u=='repeater'){
							if(r.user[u]==1){
								$('#conf_'+u).html('初めてのご利用');
							}else if(r.user[u]==2){
								$('#conf_'+u).html('以前にも注文したことがある');
							}else{
								$('#conf_'+u).html('');
							}
						}else if(u.match(/^deli/) && r.user.deli!=1){
							var tmp = u.slice(4);
							$('#conf_'+u).html(r.user[tmp]);
						}else if(u=='organization' && r.user.deli!=1){
							$('#conf_'+u).html(r.user.customername);
						}else if (u=='note_printmethod' || u=='note_design') {
							var txt = $('#conf_note_design').html();
							if (txt=="") {
								$('#conf_note_design').html(r.user[u]);
							} else {
								txt += "<br>"+r.user[u];
								$('#conf_note_design').html(txt);
							}
						} else {
							$('#conf_'+u).html(r.user[u]);
						}
					}
					
					if(r.option['publish']==0){
						$('#conf_publish').text('掲載可');
					}else{
						$('#conf_publish').text('掲載不可');
					}
					
					$('#conf_deliveryday').text(r.option.deliveryday);
					$('#conf_deliverytime').text(r.option.deliverytime);
					
					var payment = ['銀行振込', '代金引換', '工場受取で現金払い', 'カード決済', 'コンビニ決済'];
					$('#conf_payment').text(payment[r.option.payment]);
					
					
					// プリント情報
					var tbody = '';
					var curitemname = '';
					var inks = 0;
					var sizeName = ['大', '中', '小'];
//					var printMethod = {'silk':'シルク', 'digit':'デジタル転写','inkjet':'インクジェット'};
					for(var i=0; i<r.design.length; i++){
						if(curitemname!=r.design[i]['itemname']){
							if(curitemname!='' && inks==0){
								tbody = '<tr><th>'+curitemname+'</th><td colspan="3">プリントなし</td></tr>';
							}
							curitemname = r.design[i]['itemname'];
						}
						if(r.design[i]['ink']==0) continue;
						tbody += '<tr>';
						tbody += '<th>'+r.design[i]['itemname']+'</th>';
						tbody += '<td class="ac">'+r.design[i]['posname']+'</td>';
						tbody += '<td class="ac">'+sizeName[r.design[i]['areasize']]+'</td>';
//						tbody += '<td class="ac">'+printMethod[r.design[i]['printing']]+': '+sizeName[r.design[i]['areasize']]+'</td>';
						tbody += '<td class="ac">'+r.design[i]['ink']+'</td>';
						tbody += '</tr>';
						
						inks += r.design[i]['ink'];
					}
					
					if(inks==0) tbody = '<tr><th>'+curitemname+'</th><td colspan="3">プリントなし</td></tr>';
					$('#conf_print tbody').html(tbody);
				}
			});
			
			if(isError){
				$.msgbox("通信エラーが発生しています。<br>お手数ですがStep1に戻ってください。");
				$.updateItem(["","","",""]);
				$.back(0);
				$('tr.total td span', '#floatingbox, #showcart_box').text("0");
				$('tr:first td span', '#floatingbox, #showcart_box').text("0");
				$('tr:last td span', '#floatingbox').text("0");
				return;
			}
			
			$.next(5);
			/*
			$('#conf_print').html('');
			var isPos = false; 
			var pos = $('#pos_wrap').clone();
			pos.children('div').each( function(){
				var cnt = 0;
				var $posimg = $(this).find('.posimg img');
				$(this).find('.inkbox tbody tr').each( function(){
					var td0 = $('td:eq(0)', this);
					var ink_txt = td0.find('.selector span').text();
					if(ink_txt.indexOf('選択')!=-1){
						var pos_class = $(this).attr('class').split('-')[1];
						var img = $(this).closest('.inkbox').prev().find('img.'+pos_class+'.cur');
						img.attr('src',img.attr('src').replace(/_on.png/,'.png')).removeClass('cur');
						$(this).remove();
					}else{
						td0.html(ink_txt);
						cnt++;
					}
				});
				if(cnt==0){
					$(this).remove();
				}else{
					isPos = true;
				}
			});
			if(isPos){
				pos.children().not('h3').appendTo($('#conf_print'));
			}else{
				$('#conf_print').html('<p>プリントなし</p>');
			}
			*/
			
		},
/*
		next: function(){
			var box = $('#gall');
			var boxXloc = 0;
			var targetOffset = $('#header').offset().top;
			if(arguments.length>0){
				boxXloc = -1*750*(arguments[0]-0);
			}else{
				boxXloc = parseInt(box.css("margin-left").substring(0,box.css("margin-left").indexOf("px"))) - 750;
			}
	    	$($.browser.opera ? document.compatMode == 'BackCompat' ? 'body' : 'html' :'html,body')
	        .animate({scrollTop: targetOffset}, 300, 'easeOutExpo');
			$('#gall').animate({'marginLeft':boxXloc+'px'}, 500, 'swing');
		},
		back: function(){
			var box = $('#gall');
			var boxXloc = parseInt(box.css("margin-left").substring(0,box.css("margin-left").indexOf("px")));
			if(boxXloc==0) return;
			if(arguments.length>0){
				boxXloc = arguments[0];
			}else{
				boxXloc += 750;
			}
			var targetOffset = $('#header').offset().top;
	    	$($.browser.opera ? document.compatMode == 'BackCompat' ? 'body' : 'html' :'html,body')
	        .animate({scrollTop: targetOffset}, 300, 'easeOutExpo');
			box.animate({'marginLeft':boxXloc+'px'}, 500, 'swing');
		},
*/
		next: function(pageIndex){
			$('#gall > .is-appear').slideUp().removeClass('is-appear');
			var targetOffset = $('#gall > div:eq('+pageIndex+')').addClass('is-appear').slideDown().offset().top;
	    	$('html,body').animate({scrollTop: targetOffset}, 300, 'easeOutExpo');
		},
		back: function(pageIndex){
			$('#gall > .is-appear').slideUp().removeClass('is-appear');
			var targetOffset = $('#gall > div:eq('+pageIndex+')').addClass('is-appear').slideDown().offset().top;
	    	$('html,body').animate({scrollTop: targetOffset}, 300, 'easeOutExpo');
		},
		resizeHeight: function(args){
			var h = $("#"+args).height();
			$("#container .contents").height(h);
		},
		check_zipcode:function(zipcode){
		  if( ! zipcode ) return false;
		  if( 0 == zipcode.length ) return false;
		  if( ! zipcode.match( /^[0-9]{3}[-]?[0-9]{0,4}$/ ) ) return false;
		  return true;
		},
		digits:function($target, args){
		/*
		*	金額の増減を可視化
		*	@target	jQuery オブジェクト
		*	@args		変更後の金額
		*/
			var price = $target.text().replace(/,/g,"") - 0;	// 元の金額
			var plus = Math.abs(args - price);					// 差額
			if(plus==0) return;
			var limit = 10;										// 書換える回数
			var inc = 0;										// 一回の増減額
			var strUA = navigator.userAgent.toLowerCase();		// ブラウザを識別する為のユーザエージェントを取得
			if(strUA.indexOf("msie") != -1){					// 書換える回数を調整する
				limit = 10;
				inc = 100;
			}else if(strUA.indexOf("firefox") != -1){
				limit = 25;
				inc = 40;
			}else{
				limit = 100;
				inc = 10;
			}
			if(plus<=1000){										// 差額の多寡が実行時間に影響しないように調整
				limit = Math.floor(plus / inc);
			}else{
				inc = Math.floor(plus / limit);
			}
			if(args-price<0){inc = -1*inc;}					// 金額が減る場合
			var str = 0;										// 桁区切り済みの表示金額
			var cnt = 0;										// インクリメント回数
			var intervalID = setInterval( function(){			// 1ミリ秒ごとに実行
				price += inc;
				str = new String(price);
				while(str != (str = str.replace(/^(-?\d+)(\d{3})/, "$1,$2")));
				$target.text(str);
				
				cnt++;
				if(cnt>=limit){
					clearInterval(intervalID);
					args = $.addFigure(args);
					$target.text(args);
				}
			},1);
		},
		screenOverlay: function(mode){
			var body_w = $(document).width();
			var body_h = $(document).height();
			if(mode){
				$('#overlay').css({'width': body_w+'px',
									'height': body_h+'px',
									'opacity': 0.5}).show();
				if(arguments.length>1){
					$('#loadingbar').css({'top': body_h/2+'px', 'left': body_w/2-150+'px'}).show();
				}
			}else{
				if($('#loadingbar:visible').length>0) $('#loadingbar').hide();
				$('#overlay').css({'width': '0px',	'height': '0px'}).hide('1000');
			}
		}
	});


	/********************************
	*	initialize
	*/
	$.init();
});
