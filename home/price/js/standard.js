/**
*	Takahama Life Art
*	料金の目安
*	charset euc-jp
*/

$(function(){
	
	jQuery.extend({
		prop: {iteminfo:[
			{'itemcode':'', 'path':''},
			{'itemcode':'085-cvt', 'path':'t-shirts/085-cvt/085-cvt_021.jpg'},
			{'itemcode':'185-nsz', 'path':'sweat/185-nsz/185-nsz_003.jpg'},
			{'itemcode':'100-vp', 'path':'polo-shirts/100-vp/100-vp_034.jpg'},
			{'itemcode':'p-1910', 'path':'sportswear/p-1910/p-1910_00.jpg'},
			{'itemcode':'160-wcn', 'path':'ladys/160-wcn/160-wcn_011.jpg'},
			{'itemcode':'051-et', 'path':'outer/051-et/051-et_132.jpg'},
			{'itemcode':'700-evm', 'path':'cap/700-evm/700-evm_147.jpg'},
			{'itemcode':'538-cmt', 'path':'towel/538-cmt/538-cmt_034.jpg'},
			{'itemcode':'761-ent', 'path':'tote-bag/761-ent/761-ent_001.jpg'},
			{'itemcode':'018-cap', 'path':'apron/018-cap/018-cap_005.jpg'},
			{'itemcode':'802-ols', 'path':'workwear/802-ols/802-ols_001.jpg'},
			{'itemcode':'215-eh', 'path':'goods/215-eh/215-eh_015.jpg'},
			{'itemcode':'101-lvc', 'path':'long-shirts/101-lvc/101-lvc_032.jpg'},
			{'itemcode':'5145', 'path':'baby/5145/5145_576.jpg'},
			{'itemcode':'', 'path':''},
			{'itemcode':'9000', 'path':'overall/9000/9000_112.jpg'}
			]
		},
		changeCategory: function(my){
		/*
		*	商品カテゴリーの変更
		*/
			var categoryid = my.options[my.selectedIndex].value;
			$('#item_image img').attr('src', _IMG_PSS+'items/'+$.prop.iteminfo[categoryid]['path']);
			$.calc($.prop.iteminfo[categoryid]['itemcode']);
		},
		calc: function(itemcode){
		/*
		 *	目安の計算
		 */
			var amount = $('#amount_selector').val();
			var prm1 = [itemcode, itemcode];
			var prm2 = [amount, amount];
			var prm3 = ['1','3'];
			$.ajax({url:'../php_libs/pageinfo.php', async:false, data:'post', dataType:'json', 
				data:{'act':'eachprice','itemcode':prm1, 'amount':prm2, 'ink':prm3}, success: function(r){
					$('#std_01').text($.addFigure(r[0]['perone']));
					$('#std_02').text($.addFigure(r[1]['perone']));
				}
			});
		}
	});
	
	/*
	*	枚数の変更
	*/
	$('#amount_selector').change( function(){
		var categoryid = $('#category_selector').val();
		$.calc($.prop.iteminfo[categoryid]['itemcode']);
	});
	
});
