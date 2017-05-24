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
			/*
			$.ajax({
				url:'../php_libs/itemFeatures.php', async:true, type:'GET', dataType:'json', 
				data:{'act':'itemhash','mode':'feature'}
			}).success(function(r){
				sess.setItem("itemhash", JSON.stringify(r));
			});
			$.ajax({
				url:'../php_libs/itemFeatures.php', async:true, type:'GET', dataType:'json', 
				data:{'act':'thumbcolor','mode':'feature'}
			}).success(function(r){
				sess.setItem("thumbcolor", JSON.stringify(r));
			})
			*/;
			$.getJSON($.TLA.api+'?callback=?', {'act':'itemdetail', 'args':'', 'mode':'list','show_site':$.TLA.show_site, 'output':'jsonp'}, function(r){
				sess.setItem("itemhash", JSON.stringify(r));
			});
			$.getJSON($.TLA.api+'?callback=?', {'act':'item','show_site':$.TLA.show_site, 'categoryid':1, 'output':'jsonp'}, function(r){
				jQuery.each(r, function(key, val){
					var posid = val.posid;
					if(typeof $.items.ppid[val.id]!='undefined'){
						posid = $.items.ppid[val.id];
					}
					$.items.itemid.push(val.id);
					$.items.hash[val.id] = [val.code, val.name, val.cost, val.cost_color, val.item_row, posid];
				});
			});
			
			/********************************
			*	絵型（シルエット）の整列
			*/
			$('#boxwrap img').imagesLoaded(function(){
/*
				$('#boxwrap').masonry({
					itemSelector: '.box',
					isAnimated : true,
					columnWidth: 237
				});
*/
			});
			
			// シルエットの選択イベント
			$('.check_body', '#boxwrap').change( function(){
				var posid = $(this).val();
				$.showPrintPosition(posid);
			});
			
			
			// プリント位置画像のロールオーバーとクリックイベント
			$.init_position();
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
			'itemid':[],
			'ppid':{
					1:1,
					2:1,
					4:1,
					5:1,
					6:1,
					106:1,
					150:1,
					193:1,
					196:1,
					215:1,
					245:1,
					249:1,
					277:1,
					278:1,
					279:1,
					284:1,
					323:1,
					324:1,
					364:1,
					365:1,
					405:1,
					407:3,
					537:3,
					541:3,
					542:3,
					543:3,
					544:3,
					545:3,
					546:3,
					547:3,
					548:3,
					488:1,
					489:1,
					490:1,
					491:1,
					492:1,
					493:1,
					494:3,
					516:1,
					517:1,
					536:1,
					81:27,
					82:27,
					576:34,
					577:34,
					578:34,
					579:34,
					580:34,
					76:25,
					161:25,
					162:25,
					339:25,
					342:25,
					343:25,
					614:32,
					156:16,
					205:16,
					206:16,
					207:16,
					258:16,
					259:16,
					265:3,
					268:3,
					273:16,
					274:16,
					381:3,
					571:16,
					582:3,
					583:3,
					584:3,
					585:3,
					586:3,
					587:3,
					588:3,
					589:3,
					590:3,
					591:3,
					592:3,
					593:3,
					594:3,
					595:3,
					596:3,
					597:3,
					598:3,
					599:3,
					600:3,
					601:3,
					602:3,
					603:16,
					604:16,
					605:16,
					622:3,
					431:53,
					432:53,
					461:53,
					471:53,
					472:53,
					479:8,
					480:9,
					482:8,
					483:9,
					485:8,
					586:55,
					587:54,
					588:55,
					589:54,
					590:55,
					591:54,
					592:55,
					593:54,
					594:54,
					595:55,
					596:54,
					597:55,
					598:54,
					599:55,
					600:54,
					601:55,
					602:54,
					603:16,
					604:16,
					605:16,
					167:29,
					168:29,
					169:29,
					170:29,
					362:29,
					363:29,
					615:29,
					616:29,
					383:49,
					384:49,
					385:49,
					386:50,
					387:49,
					388:49,
					389:49,
					390:49,
					115:8,
					117:8,
					122:10,
					123:7,
					124:7,
					125:10,
					126:7,
					127:10,
					172:7,
					173:10,
					174:8,
					175:8,
					180:10,
					181:8,
					224:10,
					228:7,
					230:8,
					231:8,
					251:8,
					368:7,
					369:10,
					370:8,
					376:7,
					410:10,
					519:10,
					520:7,
					549:6,
					550:6,
					551:6,
					552:6,
					553:6,
					554:6,
					555:6,
					557:6,
					558:8,
					560:6,
					563:6,
					564:6,
					566:6,
					569:7,
					570:10,
					521:7,
					522:10,
					524:7,
					526:7,
					529:7,
					530:10,
					532:7,
					533:10,
					112:30,
					113:30,
					114:30,
					212:30,
					255:30,
					411:30,
					512:30,
					58:18,
					59:18,
					66:18,
					73:18,
					158:18,
					159:23,
					160:18,
					262:18,
					275:23,
					297:18,
					329:23,
					556:18,
					559:18,
					561:18,
					562:18,
					565:18,
					567:18,
					568:18,
					281:31,
					282:31,
					283:31,
					289:31,
					621:31,
					606:3,
					607:41,
					608:41,
					609:41,
					610:3,
					611:41,
					290:2,
					291:2,
					293:2,
					294:2,
					391:4,
					392:4,
					505:4,
					572:2,
					573:4,
					574:2,
					575:2,
					581:2,
					612:2,
					613:2,
					503:2,
					504:2,
					506:4,
					507:2,
					508:2,
					509:2,
					404:44,
					617:40,
					618:40,
					619:40,
					620:40,
					306:32,
					307:32,
					308:32,
					310:32
				}
		},
		clearItems: function(){
		/*
		*	指定カテゴリのアイテム情報を初期化
		*/
			$.items.hash = {};
			$.items.itemid = [];
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
			var categoryid = my.options[my.selectedIndex].value;
			// t-shirts の場合にアイテムカラー指定
			if(categoryid==1){
				$('#color_wrap').show();
			}else{
				$('#color_wrap input[name=color]').val(['0']);
				$('#color_wrap').hide();
			}
			$.clearItems();
			$.getJSON($.TLA.api+'?callback=?', {'act':'item', 'categoryid':categoryid,'show_site':$.TLA.show_site, 'output':'jsonp'}, function(r){
				jQuery.each(r, function(key, val){
					var posid = val.posid;
					if(typeof $.items.ppid[val.id]!='undefined'){
						posid = $.items.ppid[val.id];
					}
					$.items.itemid.push(val.id);
					$.items.hash[val.id] = [val.code, val.name, val.cost, val.cost_color, val.item_row, posid];
				});
				$.setBody(categoryid);
				
				/*
				var option = '';
	        	jQuery.each(r, function(key, val){
	        		option += '<option value="'+val.id+'"';
	        		if(key==0){
	        			option += ' selected="selected"';
	        		}
					option += '>['+val.code+'] '+val.name+'</option>';
					$('#item_selector').html(option);
				});
				$.changeItem();
				*/
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
		changeItem: function(){
		/*
		*	商品の変更
		*
			var itemid = $('#item_selector').val();
			$.ajax({url:'../php_libs/pageinfo.php', async:false, type:'POST', dataType:'text', 
				data:{'act':'price','itemid':itemid}, success: function(r){
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
			
			$.showPrintPosition(itemid);
		*/
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
		printparam:{
		/*
		*	プリント代計算で使用するパラメーター
		*
			'itemid':[],
			'itemname':[],
			'sizeid':[],
			'size':[],
			'cost':[],
			'amount':[],
			'pos':[],
			'ink':[]
		*/
		},
		addOrder: function(){
		/*
		*	見積明細に追加
		*	同じ商品が見積もりテーブルにある場合は、同じサイズは上書する
		*
			// var category_name = $('#category_selector option:selected').text();
			// var category_id = $('#category_selector').val();
			var item_name = $('#item_selector option:selected').text();
			var item_id = $("#item_selector").val();
			var size_id = [];
			var size = [];
			var cost = [];
			var posi = [];
			var inks = [];
			var volm = [];
			var a = 0;
			var isPrint = true;
			
			$('#price_wrap table tbody tr').each( function(){
				var v = ($(this).find('input.forNum').val()-0);
				if(v==0) return true;
				size_id[a] = $(this).children('th').attr('class').split('_')[1];
				size[a] = $(this).children('th').text();
				cost[a] = $(this).children('td:first').text().split('円')[0].replace(/,/g, '');
				volm[a] = v;
				a++
			});
			
			if(a==0){
				$.msgbox('枚数をご指定ください。')
				return;
			}
			
			a = 0;
			if($('#pos_wrap table select').length==0){
				isPrint = false;
				posi[0] = 'プリントなし';
				inks[0] = 0;
			}
			$('#pos_wrap table select').each( function(){
				var ink = $(this).val()-0;
				var index = $(this).attr('class').split('_')[1];
				var posname = $('.posname_'+index).text();
				if(ink>0){
					posi[a] = posname;
					inks[a] = ink;
					a++;
				}
			});
			
			if(a==0 && isPrint){
				$.msgbox('プリント位置をご指定ください。')
				return;
			}
			
			var isExist = false;
			for(var i=0; i<$.printparam.itemid.length; i++){
				if($.printparam.itemid[i]==item_id) isExist = true;	
			}
			if(isExist){	// 同じアイテムがある場合
				var sizelen = $.printparam.size.length;
				var poslen = $.printparam.pos.length;
				for(var s=0; s<size.length; s++){
					for(var t=0; t<sizelen; t++){
						// 同じサイズがある場合に当該サイズのハッシュを削除
						if($.printparam.size[t]==size[s] && $.printparam.itemid[t]==item_id){
							for(var k in $.printparam){
								$.printparam[k].splice(t,1);
							}
							t--;
						}
					}
					
					// 再登録
					for(var u=0; u<posi.length; u++){
						$.printparam.itemid.push(item_id);
						$.printparam.itemname.push(item_name);
						$.printparam.sizeid.push(size_id[s]);
						$.printparam.size.push(size[s]);
						$.printparam.cost.push(cost[s]);
						$.printparam.amount.push(volm[s]);
						$.printparam.pos.push(posi[u]);
						$.printparam.ink.push(inks[u]);
					}
				}
				
			}else{	// 新規
				for(var x=0; x<size.length; x++){
					for(var y=0; y<posi.length; y++){
						$.printparam.itemid.push(item_id);
						$.printparam.itemname.push(item_name);
						$.printparam.sizeid.push(size_id[x]);
						$.printparam.size.push(size[x]);
						$.printparam.cost.push(cost[x]);
						$.printparam.amount.push(volm[x]);
						$.printparam.pos.push(posi[y]);
						$.printparam.ink.push(inks[y]);
					}
				}
			}
			
			// ハッシュをアイテムとサイズの昇順で並び替え
			var tmp = [];
			for(var h=0; h<$.printparam.itemid.length; h++){
				tmp[h] =  { 'itemid':$.printparam.itemid[h],
							'itemname':$.printparam.itemname[h],
							'sizeid':$.printparam.sizeid[h],
							'size':$.printparam.size[h],
							'amount':$.printparam.amount[h],
							'cost':$.printparam.cost[h],
							'pos':$.printparam.pos[h],
							'ink':$.printparam.ink[h]
							}
			}
			tmp.sort(function(a,b){ return a.itemid-b.itemid || a.sizeid-b.sizeid });
			$.printparam = {'itemid':[],
							'itemname':[],
							'sizeid':[],
							'size':[],
							'cost':[],
							'amount':[],
							'pos':[],
							'ink':[]
							};
			for(var g=0; g<tmp.length; g++){
				$.printparam.itemid[g] = tmp[g]['itemid'];
				$.printparam.itemname[g] = tmp[g]['itemname'];
				$.printparam.sizeid[g] = tmp[g]['sizeid'];
				$.printparam.size[g] = tmp[g]['size'];
				$.printparam.cost[g] = tmp[g]['cost'];
				$.printparam.amount[g] = tmp[g]['amount'];
				$.printparam.pos[g] = tmp[g]['pos'];
				$.printparam.ink[g] = tmp[g]['ink'];
			}
			
			$.calcPrice();
		*/
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
			var ppID = $('.check_body:checked', '#boxwrap').val();	// 絵型ID
			var category_key = $("#category_selector option:selected").attr("rel");
			
			// Tシャツの場合のみアイテムカラー（白か白以外）を指定
			if(category_key=='t-shirts'){
				optionValue = $('#color_wrap input[name=color]:checked').val();
			}
			
			// デザインの数
//			$('#pos_wrap table tbody tr:last td').each( function(){
			$('#pos_wrap select').each( function(){
				var pos_name = $(this).parent().prev().text();
				var ink_count = $(this).val();
				if(ink_count==0) return true;		// continue
				for(var i=0; i<$.items.itemid.length; i++){
					if($.items.hash[$.items.itemid[i]][5]!=ppID) continue;	// 指定されたシルエットの絵型に対応したアイテムのみ
					itemid.push($.items.itemid[i]);
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
