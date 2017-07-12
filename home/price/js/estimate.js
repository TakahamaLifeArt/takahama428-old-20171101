/**
*	Takahama Life Art
*	見積
*	charset euc-jp
*/

$(function(){

	jQuery.extend({
		init: function(){
			$('#category_selector').val(1);
			$('#color_wrap input[name=color]').val(['0']);
			$('#color_wrap').show();
			$.clearItems();
			var sess = sessionStorage;
			$.getJSON($.TLA.api+'?callback=?', {'act':'itemdetail', 'args':'', 'mode':'list','show_site':$.TLA.show_site, 'output':'jsonp'}, function(r){
				sess.setItem("itemhash", JSON.stringify(r));
			});
			$.when(
				$.ajax({url:'../php_libs/items.php', async:true, type:'GET', dataType:'json', 
						data:{'act':'itemtype', 'category_id':'', 'output':'jsonp'}})
			).then(function(r){
				$.items.itemTag = r;
				var tag = [];	// タグIDの配列
				var posId = {};	// タグIDをキーにした対応するプリント位置IDのハッシュ
				for(var ppId in $.items.itemTag[1]){
					var tagId = $.items.itemTag[1][ppId]['tag'];
					tag[tag.length] = tagId;
					posId[tagId] = ppId;
				}
				$.getJSON($.TLA.api+'?callback=?', {'act':'item', 'categoryid':1, 'mode':tag, 'show_site':$.TLA.site, 'output':'jsonp'}, function(r){
					jQuery.each(r, function(key, val){
						$.items.hash[val.id] = [val.code, val.name, val.cost, val.cost_color, val.item_row, posId[val.tag_id]];
					});
				});

				// シルエットの選択イベント
				$('#boxwrap').on('change', 'input[name="body_type"]', function(){
					$.showPrintPosition($(this).val());
				});

				// プリント位置画像のロールオーバーとクリックイベント
				$.init_position();
			}).fail(function(jqXHR, textStatus, errorThrown){
				alert('Error: '+textStatus);
			});
		},
		init_position: function(){
		/*
		*	絵型のマウスクリックイベント設定
		*/
			$('#pos_wrap div').each( function(){
				$(this).children('img:not(:nth-child(1))').each(function(index) {
					var postfix = '_on';
					var img = $(this);
					var id = img.parent().attr('class').split('_')[1];
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
					).click( function(){
						if(img.is('.cur')) return;
						var cur = $(this).siblings('img.cur');
						cur.attr('src', cur.attr('src').replace(/_on.png$/, '.png')).removeClass('cur');
						img.attr('src', src_on);
						img.addClass('cur');
						$('.posname_'+id).text(img.attr('alt'));
					});

					if(index==0){
						img.not('.cur').attr('src', src_on).addClass('cur');
						$('.posname_'+id).text(img.attr('alt'));
					}
				});
			});

			// 色数指定で計算開始
			$('select', '#pos_wrap').change( function(){
				$.calcPrice();
			});

			// 見積一覧をクリア
			$.init_result();
		},
		init_result: function() {
		/*
		 * 見積り一覧を初期化
		 */
			$('#result_wrap02 .ttl ins').html(0);
			$('#result_wrap02 table tbody').html('');
			$('.more, .rankingmore', '#result_wrap02').hide();
		},
		items:{
		/*
		*	指定カテゴリのアイテム情報
		*	ppid:	絵型IDが商品DBの指定と違うアイテム
		*/
			'hash':{},
			'itemTag':[]
		},
		clearItems: function(){
		/*
		*	指定カテゴリのアイテム情報を初期化
		*/
			$.items.hash = {};
		},
		getStorage: function(key){
		/*
		*	sessionStorageのデータを取得
		*	@key		取得するデータのキー(itemhash)を指定、未指定（falseとなる0,"",null,undefined,false）で全てのデータ
		* 	@第二引数		アイテムコード
		*	return		{i_coloe_code, i_caption}
		*/
			var sess = sessionStorage;
			var store = {};
			if(!key){
				for(var key in sess){
					store[key] = JSON.parse(sess.getItem(key));
				}
			}else{
				store[key] = JSON.parse(sess.getItem(key));
				if(arguments.length>1){
					store = store[key][arguments[1]];
				}
			}
			return store;
		},
		changeCategory: function(my){
		/*
		*	商品カテゴリーの変更
		*/
			var categoryId = my.options[my.selectedIndex].value;
			// t-shirts の場合にアイテムカラー指定
			if(categoryId==1){
				$('#color_wrap').show();
			}else{
				$('#color_wrap input[name=color]').val(['0']);
				$('#color_wrap').hide();
			}
			$.clearItems();
			var tag = [];	// タグIDの配列
			var posId = {};	// タグIDをキーにした対応するプリント位置IDのハッシュ
			for(var ppId in $.items.itemTag[categoryId]){
				var tagId = $.items.itemTag[categoryId][ppId]['tag'];
				tag[tag.length] = tagId;
				posId[tagId] = ppId;
			}
			$.items.hash = {};
			$.getJSON($.TLA.api+'?callback=?', {'act':'item', 'categoryid':categoryId, 'mode':tag,　'show_site':$.TLA.show_site, 'output':'jsonp'}, function(r){
				jQuery.each(r, function(key, val){
					$.items.hash[val.id] = [val.code, val.name, val.cost, val.cost_color, val.item_row, posId[val.tag_id]];
				});
				$.setBody(categoryId);
			});
		},
		setBody: function(id){
		/*
		*	シルエット選択の書換
		*/
			$('#boxwrap').children().remove();
			$.ajax({url:'../php_libs/pageinfo.php', async:false, type:'POST', dataType:'html', 
				data:{'act':'body','category_id':id}, success: function(r){
					r = r.slice(4);
					$('#boxwrap').html(r);
					$('#boxwrap img').imagesLoaded(function(){
						/*$('#boxwrap').masonry('reload');*/
					});

					// シルエットの選択イベント
					$('.check_body', '#boxwrap').change( function(){
						var posid = $(this).val();
						$.showPrintPosition(posid);
					});

					// 絵型のイベント設定
					var posid = $('div:first', '#boxwrap').find('.check_body').val();
					$.showPrintPosition(posid);
				}
			});
		},
		showPrintPosition: function(id){
		/*
		*	プリント位置画像（絵型）とインク色数指定の生成
		*	@id		プリントポジションID
		*/
			$.ajax({url:'../php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
					data:{'act':'position','itemid':id,'id_type':'pos'}, success: function(r){
						if(r.substring(0,4) == "null") {
							r = r.substring(4,r.length );
						}
						$('#pos_wrap #pos_wrap_step3').html(r);
						$.init_position();
					}
				   });
		},
		calcPrice: function(){
		/*
		*	見積計算
		*	アイテムコード、枚数、インク色数、プリント位置の配列　[itemcode, amount, ink, pos][...]
		*/
			var vol = $('#order_amount').val();
			var pos_count = 0;
			var itemid = [];
			var amount = [];
			var pos = [];
			var ink = [];
			var size = [];
			var option = [];
			var optionValue = 0;
			var ppID = $('#boxwrap .check_body:checked').val();	// 絵型ID
			var category_key = $("#category_selector option:selected").attr("rel");

			// Tシャツの場合のみアイテムカラー（白か白以外）を指定
			if(category_key=='t-shirts'){
				optionValue = $('#color_wrap input[name=color]:checked').val();
			}

			// デザインの数
			$('#pos_wrap select').each( function(){
				var pos_name = $(this).parent().prev().text();
				var ink_count = $(this).val();
				if(ink_count==0) return true;		// continue
				for(var itemId in $.items.hash){
					if($.items.hash[itemId][5]!=ppID) continue;
					itemid.push(itemId);
					amount.push(vol);
					pos.push(pos_name);
					ink.push(ink_count);
					size.push(0);
					option.push(optionValue);
				}
				pos_count++;
			});

			$.init_result();

			if(pos_count==0){
				return;
			}

			var args = {'sheetsize':'1', 'act':'printfeelist','show_site':$.TLA.show_site, 'output':'jsonp', 'itemid':itemid, 'amount':amount, 'pos':pos, 'ink':ink, 'size':size, 'option':option};
			$.getJSON($.TLA.api+'?callback=?', args, function(r){
				// 見積り額と表示順を設定
				var costIndex = 2;	// 白色
				if(optionValue!=0) costIndex = 3;	// 白色以外
				jQuery.each(r, function(key, val){
					if (val.printfee==0) return true;
					r[key]['row'] = $.items.hash[val.itemid][4]-0;
					r[key]['base'] = ($.items.hash[val.itemid][costIndex]-0)*vol + (val.printfee-0);
				});
				var r2 = r.slice(0);

				// 安い順
				r.sort( function(a,b){
					if (a.base < b.base) return -1;
					if (a.base > b.base) return 1;
					if (a.row < b.row) return -1;
					if (a.row > b.row) return 1;
					return 0;
				});

				// 人気順
				r2.sort( function(a,b){
					if (a.row < b.row) return -1;
					if (a.row > b.row) return 1;
					return 0;
				});

				var TR = ["", ""];
				var idx = 0;
				var num = 0;
				jQuery.each(r, function(key, val){
					var itemid = val.itemid;
					var itemcode = $.items.hash[itemid][0];
					var itemname = $.items.hash[itemid][1];
					var base = val.base-0;
					var tax = Math.floor( base * (val.tax/100) );
					var result = Math.floor( base * (1+val.tax/100) );
					var perone = Math.ceil(result/vol);
					var itemHash = $.getStorage("itemhash", itemcode);
					//var tc = $.getStorage("thumbcolor", itemcode);
					num++;
					if(num==4) idx++;
					TR[idx] +=  '<tr>';
					TR[idx] +=  '<td class="number">';
					if(idx==0){
						TR[idx] += '<img src="../img/ranking_icon_0'+num+'.png" width="30" height="20" alt="'+num+'位"><br>';
					}
					TR[idx] += num+'位</td>';
					TR[idx] += '<td>';
					TR[idx] += '<p class="ranking_icon">';
					if(idx==0){
						TR[idx] += '<img src="../img/ranking_icon_0'+num+'.png" width="30" height="20" alt="'+num+'位"><br>';
					}
					TR[idx] += num+'位</p>';
					TR[idx] +=  '<p class="name">'+itemname+'</p>';
					TR[idx] +=  '<img src="'+_IMG_PSS+'items/list/'+category_key+'/'+itemcode+'/'+itemcode+'_'+itemHash['i_color_code']+'.jpg" width="100" height="100" alt="image" class="result_img">';
					TR[idx] +=  '<p class="coment">'+itemHash['i_caption']+'</p>';
					TR[idx] +=  '<p class="arrow">1枚あたり</p>';
					TR[idx] +=  '<p class="per">￥<span>'+$.addFigure(perone)+'</span>&#65374;</p>';
					TR[idx] +=  '<p class="total">合計￥ <span>'+$.addFigure(result)+'</span>&#65374;</p>';
					//							TR[idx] +=  '<p class="detail"><a href="/items/'+category_key+'/'+itemcode+'.html">詳細を見る</a></p>';
					TR[idx] +=  '<p class="detail"><a href="/items/'+category_key+'/item.html?id='+itemcode+'">詳細を見る</a></p>';
					TR[idx] +=  '<p class="apply"><a href="/order/?item_id='+itemid+'&update=1">お申し込みへ</a></p>';
					TR[idx] +=  '</td>';

					itemid = r2[key].itemid;
					itemcode = $.items.hash[itemid][0];
					itemname = $.items.hash[itemid][1];
					base = r2[key].base-0;
					tax = Math.floor( base * (r2[key].tax/100) );
					result = Math.floor( base * (1+r2[key].tax/100) );
					perone = Math.ceil(result/vol);
					itemHash = $.getStorage("itemhash", itemcode);
					//tc = $.getStorage("thumbcolor", itemcode);
					TR[idx] +=  '<td>';
					TR[idx] += '<p class="ranking_icon">';
					if(idx==0){
						TR[idx] += '<img src="../img/ranking_icon_0'+num+'.png" width="30" height="20" alt="'+num+'位"><br>';
					}
					TR[idx] += num+'位</p>';
					TR[idx] +=  '<p class="name">'+itemname+'</p>';
					TR[idx] +=  '<img src="'+_IMG_PSS+'items/list/'+category_key+'/'+itemcode+'/'+itemcode+'_'+itemHash['i_color_code']+'.jpg" width="100" height="100" alt="image" class="result_img">';
					TR[idx] +=  '<p class="coment">'+itemHash['i_caption']+'</p>';
					TR[idx] +=  '<p class="arrow">1枚あたり</p>';
					TR[idx] +=  '<p class="per">￥<span>'+$.addFigure(perone)+'</span>&#65374;</p>';
					TR[idx] +=  '<p class="total">合計￥ <span>'+$.addFigure(result)+'</span>&#65374;</p>';
					//							TR[idx] +=  '<p class="detail"><a href="/items/'+category_key+'/'+itemcode+'.html">詳細を見る</a></p>';
					TR[idx] +=  '<p class="detail"><a href="/items/'+category_key+'/item.html?id='+itemcode+'">詳細を見る</a></p>';
					TR[idx] +=  '<p class="apply"><a href="/order/?item_id='+itemid+'&update=1">お申し込みへ</a></p>';
					TR[idx] +=  '</td>';
					TR[idx] +=  '</tr>';
				});

				$('#result_wrap02 .ttl ins').html(num);
				$('#result_wrap02 .rankingmore:eq(0) table tbody').html(TR[0]);
				$('#result_wrap02 .rankingmore:eq(1) table tbody').html(TR[1]);
				$('.rankingmore:eq(0), .more', '#result_wrap02').show();
			});
		},
		gotodetail: function(my){
		/*
		*	アイテム詳細ページへ遷移
		*/
			var category_key = $('#category_selector option:selected').attr('rel');
			var path = 'items/'+category_key+'/'+$(my).attr('rel')+'.html';
			location.href = 'http://www.takahama428.com/'+path;

		}
	});


	/* 計算開始 */
	$('#order_amount, #sort_selector, #color_wrap input').change( function(){
		$.calcPrice();
	});


	/* さらに表示 */
	$('#result_wrap02 .more').on('click', function() {
		$(this).hide();
		$(this).next('.rankingmore').slideDown('fast');
	});

	/* initialize */
	$.init();

});
