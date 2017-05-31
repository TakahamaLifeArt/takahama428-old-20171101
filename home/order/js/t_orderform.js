/*
*	Takahama Life Art
*	��ʸ�ե�����
*	charset euc-jp
*	log
*	2017-05-25	�ץ�����׻��λ����ѹ�
*/

$(function(){
	
	/********************************
	*	�ĥ�������˹�碌�����Ǥ��ư
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
	*	�ڡ�����Υ������˳�ǧ��å�����
	*
	window.addEventListener('beforeunload', function(event) {
		return event.returnValue = '�����Ȥ����Ƥϸ�ǳ�ǧ���뤳�Ȥ�����ޤ���;
    }, false);
   	
   	*/
   	
	/********************************
	*	���إܥ���
	*/
	$('.step_next').click(function(e){
		/*
		if($(this).is('.disable_arrow')){
			$.msgbox('<p style="font-size:18px;color:#e1630e;"><img alt="" src="./img/alert.png" />��������ܤ򤴳�ǧ����������</p>');
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
				$.msgbox('�����ƥ५�顼�λ��꤬��ʣ���Ƥ��ޤ�������ǧ������������');
				return;
			}
			
			if(totAmount==0){
				$.msgbox('�����ƥ���������ꤷ�Ƥ���������');
				return;
			}
			
			if(!$.showPrintPosition()){
				$.msgbox('�����ƥ��������ǧ���Ƥ���������');
			}
			
		}else if($(this).is('.goto_cart')){
		// Step4��
			if($.chkInks()){
				var r1 = $.updateItem();
				var r2 = $.updatePosition();
				r2['category'] = r1['category'];
				$.setCart(r2);
			}
		}else if($(this).is('.goto_user')){
		// Step5��
			if($('#nodeliday').prop('checked')){
				$.next(4);
			}else{
				if($('#deliveryday').val()==""){
					$.msgbox('����˾Ǽ������ꤷ�Ƥ���������');
				}else{
					$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'details'}, 
						success: function(r){
							if(r.options.expressError!=""){
								$.msgbox(r.options.expressError+"\n����˾Ǽ���򤴳�ǧ����������");
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
		// Step6��
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
	*	�Ϥ�������
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
	*	���ܥ���
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
	*	�����Ȥ򸫤�
	*/
	$('.viewcart').click( function(){
		$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'details'}, 
			success: function(r){
				if(r.design.length==0 && r.options.noprint==0){
					$.msgbox('�ץ��Ȥ���ǥ�����ο�������ꤷ�Ƥ���������');
				}else{
					$.setCart(r);
				}
			}
		});
	});
	
	
	/********************************
	*	�������󥹤Υݥåץ��å�
	*/
//	$('.pop_size', '#step2').on('click', function(){
	$('#step2').on('click', '.pop_size', function(){
		var msg = '<h3>���������ܰ�</h3><hr>';
		msg += '<p class="toc"><img alt="������" src="./img/measuretable.jpg"></p>';
		msg += '<p class="note toc"><span>��</span>��ˡ��cm�ˤϤ����ޤǤ��ܰ¤Ǥ����᡼�����侦�ʤˤ�äư㤤���������ޤ���</p>';
		$.msgbox(msg);
	});
	
	$('#pop_express').click( function(){
		var msg = '<h3>�õ�����ˤĤ���</h3><hr>';
		msg += '<p>�̾�Ǽ���Ǥϴ֤˹��ʤ��Ȥ������ޤ��Τ����ͤˤϡ��õ�����Ȥ���2���ž夲�������ž夲�������ž夲���������ޤ������줾�����ӳ������⤬������ޤ���</p>';
		msg += '<dl class="list"><dt>2���ž夲</dt><dd>�̾������1.3��</dd><dt>�����ž夲</dt><dd>�̾������1.5��</dd><dt>�����ž夲</dt><dd>�̾������2��</dd></ul>';
		$.msgbox(msg);
	});
	
	$('#pop_pack').click( function(){
		var msg = '<h3>�޵�</h3><hr>';
		msg += '<p><img width="100%" alt="�޵ͤ���" src="./img/img_packing.jpg"><img width="100%" alt="�޵ͤʤ�" src="./img/img_nopacking.jpg"></p>';
		msg += '<p class="note">�޵ͤ���ʺ��ˤ��޵ͤʤ��ʱ��ˤλ��ͼ̿��Ǥ���</p>';
		$.msgbox(msg, 750);
	});
	
	$('#pop_payment').click( function(){
		var msg = '<h3 class="syousai">��Կ���</h3><hr>';
		msg += '<p>�����θ��¤ˤ���������������</p>';
		msg += '<p>����˾��Ǽ�������2�����ޤǤˤ������򤪴ꤤ�פ��ޤ����������ˤ������ǧ���Ǥ��ʤ��ΤǤ���դ��������ˤ�����������ϡ������ͤΤ���ô�Ȥ����Ƥ��������Ƥ���ޤ���</p>';
		msg += '<dl class="list">';
		msg += '<dt>���̾</dt>';
		msg += '<dd>��ɩ����գƣʶ��</dd>';
		msg += '<dt>��Ź̾</dt>';
		msg += '<dd>�������Ź��744</dd>';
		msg += '<dt>���¼���</dt>';
		msg += '<dd>����</dd>';
		msg += '<dt>�����ֹ�</dt>';
		msg += '<dd>3716333</dd>';
		msg += '<dt>����̾��</dt>';
		msg += '<dd>��˥����ϥޥ饤�ե�����</dd>';
		msg += '</dl>';

		msg += '<hr><br><h3 class="syousai">������</h3><hr>';
		msg += '�������������1��ˤĤ�&yen;800����ȴ�ˤ�����ޤ���';
		msg += '����ʧ����ۡʾ�����+������������������ܾ����ǡˤ������ȼԤˤ���ʧ������������';
		msg += '�����ͤΤ��Թ�Ǥ���ʧ�������ʣ���ˤʤä���硢1��ˤĤ�&yen;800����ȴ�ˤ��ɲä����Ƥ��������ޤ���';

		msg += '<hr><br><h3 class="syousai">�����ɷ��</h3><hr>';
		msg += '�Ƽ說�쥸�åȥ����ɤ������Ѥ��������ޤ���';
		msg += '����˾��Ǽ�������2�����ޤǤ˥����ɷ�Ѽ�³���򤪴ꤤ�פ��ޤ���';
		msg += '�������ˤ������ǧ���Ǥ��ʤ��ΤǤ���դ��������˥����ɷ�ѥ����ƥ���������5%�ˤϡ������ͤΤ���ô�Ȥ����Ƥ��������Ƥ���ޤ���';
		msg += '���ҤΡ֥ޥ��ڡ����ס�֤���ʧ�������ס�֥����ɷ�ѤΤ��������Ϥ����餫��פˤƷ�Ѥ���ǽ�Ǥ���';
		msg += '<center><p><img width="60%" alt="�����ɼ���" src="./img/card.png"></p></center>';
		$.msgbox(msg);
	});
	
	
	
	
	/*---------- STEP 1 ----------*/
	
	/********************************
	*	���ƥ��꡼�ѹ��ǥ����ƥ����ɽ���򹹿�
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
				
				// ������
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
					
					// �͵�����
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
										'<p class="price_s" style="white-space: nowrap;"><p style="display:inline-block;">TAKAHAMA����</p><span id="price_cost" style="white-space: nowrap;"><span>'+v['minprice']+'</span>�ߏ��</span></p>'+
									'</li>'+
								'</ul>'+
								'<p class="tor"><a href="../items/'+folder+'/item.html?id='+code+'">�����ƥ�ξܺ٤�</a></p>'+
							'</li>';
					i++;
				}
				ls += '</ul>';
				$('#h3_itemlist span').text('��'+i+'�����ƥ��');
				
				if(tmp.length>0){
				// �͵����ʤ�������
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
										'<p class="price" style="white-space: nowrap;"><p style="display:inline-block;">TAKAHAMA����</p><span id="price_cost" style="white-space: nowrap;"><span>'+v['minprice']+'</span>�ߏ��</span></p>'+
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
	*	�����ƥ�λ����Step2��
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
		// ���ʥå���
			$.showPop(func);
		}else{
			func();
		}
	});
	
	
	/*---------- STEP 2 ----------*/
	
	/********************************
	*	�����ƥ५�顼���ѹ�
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
	*	�̤Υ����ƥ५�顼���ɲ�
	*/
	$('#add_item_color').click( function(){
		var target = $(this).parent('.btn_line');
		var clone = $('.pane:first', '#step2').clone();
		
		// ���ꥫ�顼������
		clone.find('.color_thumb li.nowimg').removeClass('nowimg');
		var my = clone.find('.color_thumb li:first');
		my.addClass('nowimg');
		var imgname = $('img', my).attr('src').replace('_s.jpg', '.jpg');
		var colorcode = $('img', my).attr('alt');
		var code = $.itemparam.itemcode+'_'+colorcode;
		var colorname = $('img', my).attr('title');
		clone.find('.item_image img').attr({'src':imgname, 'alt':code});
		clone.find('.notes_color').html(colorname);
		
		// �ѥ�᡼����������
		$.itemparam.colorname = colorname;
		$.itemparam.colorcode = colorcode;
		
		// �������ơ��֥������
		$.showSizeform($.itemparam.itemid, colorcode, [], clone);
		
		// ����ܥ�����ɲ�
		clone.prepend('<p class="btn_line"><ins class="del_item_color"><img src="/common/img/delete.png" alt="���">���</ins></p>');
		
		clone.insertBefore(target);
	});
	
	
	/********************************
	*	�����ƥ५�顼����
	*/
//	$('.del_item_color').on('click', function(){
	$('#step2').on('click', '.del_item_color', function(){
		$(this).closest('.pane').slideUp('normal', function(){$(this).remove();});
	});
	
	
	/********************************
	*	����Υƥ����ȥܥå�����ɽ�����ꥢ��̤���ѡ�
	*/
	$('.clear_amount').click( function(e){
		$('.size_table tbody tr:not(".heading") td input').each( function(){
			$(this).val('0');
		});
		$('.cur_amount').text('0');
	});
	
	
	/********************************
	*	����ι��ɽ��
	*/
//	$('.size_table tbody tr:not(".heading") td[class*="size_"] input').on('change', function(){
	$('#step2').on('change', '.size_table tbody tr:not(".heading") td[class*="size_"] input', function(){
		$.check_NaN(this);
		var amount = 0;
		$(this).closest('.size_table').find('tbody tr:not(".heading") td[class*="size_"] input').each( function(){
			if($(this).val()-0!=0) amount+=($(this).val()-0);
		});
		$(this).closest('.size_table').next('.btmline').find('.cur_amount').text($.addFigure(amount));
		
		// ������
		var tot = 0;
		$('.cur_amount').each( function(){
			tot += $(this).text().replace(/,/g, '')-0;
		});
		$('#tot_amount').text($.addFigure(tot));
	});
	
	
	/*---------- STEP 3 ----------
		STEP4�ؤ����ܤǥץ��Ȱ��֤ȥ��󥯿���������
	*/
	
	/********************************
	*	�ץ��Ȥʤ��ǹ���
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
					$(this).val("0").prev("span").text("���򤷤Ƥ�������");
				});
			}
			$('#pos_wrap').hide();
			func();
		}else{
			$.confbox.show("�ץ��Ȥʤ��ǥ����Ȥ����줿���ʤ����ƺ������ޤ�����������Ǥ�����", 
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
	*	�ɽ����˾�ξ��Υƥ����ȥ��ꥢ
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
	*	�����Ȥ�������ѹ�
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
	*	����ʤɤΥ����å�
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
	*	Ǽ���λ���ʤ�
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
					// Ǽ������ʤ��ξ���Ǽ������
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
	*	���Ϥ����ֻ���
	*/
	$('#deliverytime').change( function(){
		var key = $(this).attr('name');
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'options', 'key':key, 'val':val};
		$.ajax({url:'/php_libs/t_orders.php', type:'post', dataType:'json', async:true, data:postData});
	});
	
	
	/********************************
	*	ź�եե�������ɲ�
	*/
	$('.add_attach', '#uploaderform').click( function(){
		if($('input[type=file]', '#uploaderform').length>3){
			$.msgbox('���٤�ź�դǤ���ե������4�ĤޤǤǤ���');
			return;
		}
		$(this).parent().before('<p><input type="file" onChange="this.form.submit()" name="attach[]" size="19" title="�ǥ�����ե��������ꤷ�Ƥ�������" /><span class="del_attach"><img src="/common/img/delete.png" alt="���">���</span></p>');
	});
	
	
	/********************************
	*	ź�եե�����μ�á���Ʊ����
	*/
	$('#uploaderform').on('click', '.del_attach', function(){
		if($('input[type=file]', '#uploaderform').length==1){
			$(this).parent().before('<p><input type="file" onChange="this.form.submit()" name="attach[]" size="19" title="�ǥ�����ե��������ꤷ�Ƥ�������" /><span class="del_attach"><img src="/common/img/delete.png" alt="���">���</span></p>');
		}
		$(this).parent().remove();
		document.forms.uploaderform.submit();
	});
	
	
	/********************************
	*	�ǥ���������ͤȥ��󥯿�����Υƥ����ȥ��ꥢ
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
	*	�ե�������ǥ��󥿡��ˤ�������
	*/
	$('#loginform_wrapper form').on("keypress", "input", function(e){
		var code=(e.charCode) ? e.charCode : ((e.which) ? e.which : e.keyCode);
		if(code == 13 || code == 3) $("#login_button").click();
	});
	
	
	/********************************
	*	������ܥ����ɽ��
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
	*	������ܥ���
	*/
	$("#login_button").click( function(){
//alert("login_button");
		var f = document.forms.loginform;
		var email = f.email.value.trim();
		var pass = f.pass.value.trim();
		if(email==""){
			alert('�᡼�륢�ɥ쥹�����Ϥ��Ƥ���������');
			return;
		}
		if(pass==""){
			alert('�ѥ���ɤ����Ϥ��Ƥ���������');
			return;
		}
		
		var f=document.forms.loginform;
		var fd = new FormData(f);
		$.login(fd);
	});
	
	/********************************
	*	��Ͽ�ѤߤΤ��Ϥ��������
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
	*	���Ƥ����Ϥ����;�������
	*/
	$("#show_userinfo").click( function(){
		$.setCustomer(true);
	});
	
	/********************************
	*	���Ϥ��轻�����Ϥ�ɽ������ɽ��������
	*/
	$('#deli').change( function(){
		if($(this).is(':checked')){
			$('#deli_list').fadeIn();
		}else{
			$('#deli_list').fadeOut();
		}
	});
	
	/********************************
	*	�ǥ�����ǺܤΥ����å�
	*/
	$(':radio[name="publish"]', '#step5').change( function(){
		var key = $(this).attr('name');
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'options', 'key':key, 'val':val};
		$.ajax({url:'/php_libs/t_orders.php', type:'post', dataType:'json', async:true, data:postData});
	});
	
	
	/********************************
	*	�᡼�륢�ɥ쥹����Ͽ��ǧ
	*/
//	$('#email').change( function(){
//		var val = $(this).val();
//		$.checkEmail(val);
//	});
	
	/********************************
	*	������ܥ���
	*/
	$('#member_login').on('click', function(){
		var email = $('#login_input_email').val().trim();
		var pass = $('#login_input_pass').val().trim();
		if(!$.check_email(email)){
			return;
		}
		var required = [];
		if(email=='') required.push('<li>�᡼�륢�ɥ쥹</li>');
		if(pass=='') required.push('<li>�ѥ����</li>');
		var required_list = '<ul class="msg">'+required.toString().replace(/,/g,'')+'</ul>';
		if(required.length>0){
			$.msgbox("ɬ�ܹ��ܤ����Ϥ򤴳�ǧ����������<br />"+required_list);
			return;
		}

		//���å����˥桼��������¸
		//���̤��ɽ��
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
	*	��ǧ�����å�����ʸ�ܥ����ͭ��
	*/
	$('#agree').change( function(){
		if($(this).prop('checked')){
			$('#sendorder').removeClass('disable_button');
		}else{
			$('#sendorder').addClass('disable_button');
		}
	});
	
	
	/********************************
	*	��ʸ�ܥ���
	*/
	$('#sendorder').click( function(){
		if($(this).is('.disable_button')){
			$.msgbox('��ջ���򤴳�ǧ�ξ塢�ڳ�ǧ���ޤ����ۥ����å��򥯥�å����Ƥ���������');
		}else{
			var func = function(){
				document.forms.orderform.submit();
/*
				var args = $("#conf_email").text();
				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'checkemail', 'args':args}, 
					success: function(r){
						if(r.length!=0){
//							var msg = '<h1>��Ͽ�ѤߤΥ᡼�륢�ɥ쥹�Ǥ���</h1>';
//							msg += '<p>E-mail����'+args+'</p>';
//							msg += '<p>���Ǥˤ���Ͽ����Ƥ���桼��������Ǥ��������ߤ���դ������ޤ���<br>';
//							msg += '�ޥ��ڡ����ǥ����󤷤Ƥ���������</p>';
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
	*	ʸ���Ǥ��γ�ǧ
	*
	$('#note_write').change( function(){
		var val = $(this).val();
		var postData = {'act':'update', 'mode':'options', 'key':'note_write', 'val':val};
		$.ajax({url:'/php_libs/orders.php', type:'post', dataType:'text', async:true, data:postData});
	});
	*/
	
	
	/*---------- Estimation box ----------*/
	
	/********************************
	*	������٤�ɽ������ɽ��
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
	*	���ѥܥå�������ѹ�
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
					discountname = '��'+r.discountname.toString()+'��';
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
	*	���Ѥ�����
	*
	$('.del_cart').on('click', function(e){
		var tr = $(e.target).closest('tr');
		var args = tr.attr('class').split('_').slice(1);
		var msg = tr.children('td:eq(0)').text()+'<br />���顼��'+tr.children('td:eq(1)').text();
		msg += ' �� '+tr.children('td:eq(2)').text()+'���äޤ���<br />������Ǥ�����'
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
	*	���ѥܥå������龦�ʤ���ꤷ��ɽ��
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
		*	�����ȤΥ����ƥ�ǡ���
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
			// �����Ȥ򸫤�BOX�Υܥ��������
				$.itemparam.categoryid = 1;
				$.itemparam.categorykey = 't-shirts';
				$.itemparam.categoryname = 'T�����';
				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'details'}, 
					success: function(r){
						if(r.design.length==0 && r.options.noprint==0){
							$.msgbox('�ץ��Ȥ���ǥ�����ο�������ꤷ�Ƥ���������');
						}else{
							$.setCart(r);
						}
					}
				});
			}else if(_UPDATED==3){
			// ���ʾܺ٤ȥ������̤θ��Ѥ��׻��Ѥ߾��֤Ǥ�����
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
				$.itemparam.categoryname = 'T�����';
				
				$.itemparam.itemid = 4;
				$.itemparam.itemcode = '085-cvt';
				$.itemparam.itemname = '�إӡ��������ȣԥ����';
			}
			
			$.itemparam.colorname = '�ۥ磻��';
			$.itemparam.colorcode = '001';
			

			$('.datepicker', '#step4').datepicker({
				beforeShowDay: function(date){
					var weeks = date.getDay();
					var texts = "";
					if(weeks == 0) texts = "����";
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
			
			// ��ʸ����ܥ���Υ����å�
			$('#agree').prop('checked', false);
			$('#sendorder').addClass('disable_button');
		},
		showPop: function(func){
		/*
		*	�᡼�����֥��ʥå����פξ��˺߸˳�ǧ��ɬ�פʻݥݥåץ��å�
		*
		*	@�������		�ʤ���default�������ꡧ��ǧ�����å�����Ѥ���
		*/
			var args = 1;
			var chk = '';
			if(arguments.length==2){
				chk = '<p style="margin-top:1em;font-size:125%;"><label><input type="checkbox" id="agree_stock" value="1" onChange="$.checkAgreeStock(this);"> ��ǧ���ޤ���</label>';
				chk += '<ins class="fontred" style="font-size:80%;"> �ʥ����å����Ƥ���������</ins></p>';
				args = 0;
			}
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
		*	���ʥå����κ߸˳�ǧ��å������γ�ǧ�����å��ǥݥåץ��åפΣϣ˥ܥ����ͭ����̵�������ؤ���
		*/
			if($(my).is(':checked')){
				$('input.confirm_ok').prop('disabled', false);
			}else{
				$('input.confirm_ok').prop('disabled', true);
			}
		},
		setCustomer: function(args){
		/*
		*	@args	true: ���Ƥ���������	false: Step4��������
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
					// �����󤷤ʤ���硢�̾�ɽ��
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
						// �����󤷤���硢����ꥹ�Ȥ����
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
						$('#delivery_customer').append($('<option>').attr({ value: '-1' }).text('��������'));
						if(typeof me.delivery != 'undefined'){
							for(var i=0; i<me.delivery.length; i++){
								$('#delivery_customer').append($('<option>').attr({ value: me.delivery[i].id }).text('��������' + (i+1)));
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
				$.msgbox("�᡼�륢�ɥ쥹��"+email+"����Ͽ�ѤߤǤ���<br>��������̤��餪�ꤤ�������ޤ���<br><a href='/user/resend_pass.php' target='_brank'>�ѥ���ɤ�˺�줿���Ϥ������</a>");
				$("#show_loginwrap").click();
				
			}).fail(function(xhr, status, error){
				alert("Error: "+error+"<br>xhr: "+xhr);
			});
		},
		getPage: function(itemid, colorcode){
		/*
		*	�����ƥ५�顼�ȥ��������Ȥ���������ꤷ�ƥ��å���������֤�
		*
		*	return		[{'cateogryid','categorykey','categoryname','itemie','itemcode','itemname','posid','colorcode',vol:{size_id:���}}]
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
		*	�����ȥڡ����Υ����ƥ���������
		*	@r		reqDetail
		*/
			$('.itemsum, .printfee, .totamount, .total, .perone, .base, .tax, .credit', '#estimation_wrap').text('0');
			var dat = '';
			if(r.category.length==0){
				$('#estimation_wrap tbody').html('<tr><td colspan="7"></td><td class="last"></td></tr>');
				$.updateOptions(r);
				$.msgbox('�����Ȥ˾��ʤϤ���ޤ���<hr><br>�����Ȥξ��ʤϽФ����켫ͳ�Ǥ��������ڤˤ����Ѥ�򤴳�ǧ����������');
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
						dat += '<td><span class="btn_sub" onclick="$.editItem(\''+c.categorykey+'\','+c.itemid+');">�ѹ�</span>'+thumb+'<p>'+c.itemname+'<p/>���顼����'+c.colorname+'</td>';
						/*dat += '<td></td>';*/
						dat += '<td class="ac">'+hash.sizename+'</td>';
						dat += '<td class="ar">'+hash.cost+'</td>';
						dat += '<td class="ac"><input type="number" value="'+hash.amount+'" min="1" step="1" class="args_'+c.categoryid+'_'+c.itemid+'_'+c.colorcode+'_'+sizeid+'" /> ��</td>';
						dat += '<td class="ar"><p>'+$.addFigure(sub)+'</p><span class="btn_sub del_cart" onclick="$.deleteitem(this,'+c.categoryid+','+c.itemid+',\''+c.colorcode+'\','+sizeid+');">���</span></td></tr>';
						/*dat += '<td class="ac"></td>';*/
					}
				}
				
				$('#estimation_wrap tbody').html(dat);
				
				/* ���Ѥ��ۤι��� */
				$.updateEstimation(r);
				$.next(3);
			}
		},
		editItem: function(catkey, itemid){
		/*
		*	�����Ȥ�[�ѹ�]�ܥ����Step2������
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
		*	���ʤλ����Step2 �Υ�������
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
			
			/* �����Ǽ���륿����
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
		*	ʣ���Υ��顼�����򤵤�Ƥ������Step2�Υ�������
		*/
			
			var target = $('.btn_line', '#step2');
			
			for(var i=1; i<$.temp.colorcode.length; i++){
				var clone = $('.pane:first', '#step2').clone();
				// ���ꥫ�顼������
				var colorcode = $.temp.colorcode[i];
				clone.find('.color_thumb li.nowimg').removeClass('nowimg');
				var my = clone.find('.color_thumb li img[alt="'+colorcode+'"]').parent('li');
				my.addClass('nowimg');
				var imgname = $('img', my).attr('src').replace('_s.jpg', '.jpg');
				var code = $.itemparam.itemcode+'_'+colorcode;
				var colorname = $('img', my).attr('title');
				clone.find('.item_image img').attr({'src':imgname, 'alt':code});
				clone.find('.notes_color').html(colorname);
				
				// �ѥ�᡼����������
				$.itemparam.colorname = colorname;
				$.itemparam.colorcode = colorcode;
				
				// �������ơ��֥������
				$.showSizeform($.itemparam.itemid, colorcode, $.temp.volume[i], clone);
				
				// ����ܥ�����ɲ�
				clone.prepend('<p class="btn_line"><ins class="del_item_color"><img src="/common/img/delete.png" alt="���">���</ins></p>');
				
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
		*	���������Ȥ�������ϥե�����
		*	@itemid			�����ƥ�ID
		*	@colorcode		�����ƥ५�顼������
		*	@volume			������ID�򥭡��ˤ�������Υϥå���
		*	@target			�������ơ��֥�ο����Ǥ�jQuery���֥������ȡ�.pane��
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
						size_body = '<th>'+$.addFigure(val['cost'])+' ��</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'"><input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" onblur="$.blurNumber(this);"/></td>';
					}else if(cost != val['cost'] || (val['id']>(++pre_sizeid) && val['id']>10)){	// ñ�����㤦���ޤ��ϡ�������160�ʲ��������������Ϣ³���Ƥ��ʤ�
						size_table += '<tr class="heading">'+size_head+'</tr>';
						size_table += '<tr>'+size_body+'<td>��</td></tr>';
						
						pre_sizeid = val['id'];
						cost = val['cost'];
						size_head = '<th></th><th>'+val['name']+'</th>';
						size_body = '<th>'+$.addFigure(val['cost'])+' ��</th><td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'"><input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" onblur="$.blurNumber(this);"/></td>';
					}else{
						pre_sizeid = val['id'];
						size_head += '<th>'+val['name']+'</th>';
						size_body += '<td class="size_'+val['id']+'_'+val['name']+'_'+val['cost']+'"><input id="size_'+val['id']+'" type="number" value="'+amount+'" min="0" max="999" class="forNum" onfocus="$.focusNumber(this);" /></td>';
					}
		        });
		        size_table += '<tr class="heading">'+size_head+'</tr>';
				size_table += '<tr>'+size_body+'<td>��</td></tr>';
				$('.sizeprice .size_table tbody', pane).html(size_table);
				$('.sizeprice .size_table caption', pane).html(r[0]["master_id"]);
				pane.find('.cur_amount').text($.addFigure(sum));
				
				// ������
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
		*	�ץ��Ȱ��ֲ����ʳ����ˤȥ��󥯿�������Υ�������
		*	@��������ͭ����ϰ�ư���ʤ�
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
						$('#pos_wrap').html('<h3>'+$.itemparam.categoryname+'<span>���ƥ��꡼</span></h3>'+val[0]);
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
		*	�ץ��Ȱ��ֲ����Υ��륪���С��ȥ���å����٥��
		*	ʣ������ġ�����å��ǻ��������
		*/
			if($('#pos_wrap').children('div').attr('class').split('_')[1]==46) return;		// �ץ��Ȥʤ�����
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
							tr += '<option value="0" selected="selected">��</option><option value="1">��</option><option value="2">��</option>';
							tr += '</select></td>';
							tr += '<td><select class="ink"><option value="0" selected="selected">���򤷤Ƥ�������</option>';
							tr += '<option value="1">1��</option><option value="2">2��</option><option value="3">3��</option>';
							tr += '<option value="9">4���ʾ�</option></select></td>';
							
							/*
							tr += '<td>';
							tr += '<form name="uploaderform" action="/php_libs/orders.php" target="upload_iframe" method="post" enctype="multipart/form-data">';
							tr += '<input type="hidden" name="act" value="update" />';
							tr += '<input type="hidden" name="mode" value="attach" />';
							tr += '<input type="hidden" name="posid" value="'+posid+'" />';
							tr += '<input type="hidden" name="base" value="'+base+'" />';
							tr += '<input type="hidden" name="posname" value="'+posname+'" />';
							tr += '<input type="hidden" name="attachname[]" value="" />';
							tr += '<input type="file" name="attach[]" class="attach" onchange="this.form.submit()" /><img alt="���" src="/common/img/delete.png" class="del_attach" />';
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
		*	���󥯻���γ�ǧ
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
				isInks = true;	// �ץ��Ȥʤ����ʤϥ��󥯻����ʤ�
			}
			
			if(!isInks){
				$.msgbox('�ץ��Ȥ���ǥ�����ο�������ꤷ�Ƥ���������');
			};
			
			return isInks;
		},
		updateItem: function(){
		/*
		*	�����Ȥ˾��ʤ��ɲá�����ι���������������פ�0��ξ��Ͻ�������ߤ��ƥ�å�������Ф�
		*	@������������Ϻ��
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
				
				// �����������񤹤�
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
					$.msgbox('�����ƥ���������ꤷ�Ƥ���������');
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
		*	�ץ��Ȱ��֤ȥ��󥯿���
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
		*	���Ѥ����٥ơ��֥�ǥ����ƥ�κ��
		*/
			var args = [catid, itemid, colorcode, sizeid];
			var tr = $(my).closest('tr');
			var msg = tr.children('td:eq(1)').html()+'<br />��������'+tr.children('td:eq(2)').text()+' �������ޤ���������Ǥ�����';
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
			var sizeName = ['��', '��', '��'];
			var printName = {'silk':'���륯', 'digit':'ž��', 'inkjet':'���󥯥����å�'};
			var print_size = '';
			var print_pos = '';
			var ink_count = '';
			for(var i=0; i<r.design.length; i++){
//				var sect = r.design[i]['pos']+'-'+r.design[i]['size']+'-'+r.design[i]['ink'];
//				for (var printCode in r.printing[sect]) {
//					var fee = r.printing[sect][printCode];
//					ink_count += '<p>'+printName[printCode]+':'+fee+'��</p>';
//				}
				var ink = r.design[i]['ink']==9? '4���ʾ�': r.design[i]['ink']+'��';
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
			
			// �������
			var spec = '';
			if(r.options.discount!=0){
				spec = r.options['discountname'].toString();
			}
			$('.discountname', '#estimation_wrap').text(spec);
			
			// ����������­
			if(r.options.expressError!=""){
				$.msgbox(r.options.expressError+"\n����˾Ǽ���򤴳�ǧ����������");
				$('#express_notice ins').text("����˾Ǽ���򤴳�ǧ����������").closest('p').show();
				$('#deliveryday').val("");
			}else{
			// �õ�����μ���
				spec = '';
				if(r.options.expressfee!=0){
					spec = r.options['expressInfo'];
					$('#express_notice ins').text('�õ����⤬������ޤ�����'+spec+'��').closest('p').show();
					
					// �����ž夲�ξ����޵ͤ�������ԲĤˤ���
					if (spec=='�����ž夲') {
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
			
			// �����Ѥ�BOX
			$.digits( $('tr.total td span', '#floatingbox'), total);
			$('tr.total td span', '#showcart_box').text($.addFigure(total));
			$('tr:first td span', '#floatingbox, #showcart_box').text(r.options.amount);
			$('tr:last td span', '#floatingbox').text($.addFigure(perone));
		},
		updateUser: function(){
		/*
		*	�桼�����������Ͽ
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
		*	�桼���������ɬ�ܹ��ܤ����ϳ�ǧ����ʸ���Ƥ�ɽ��
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
			
			// �����桼�����ξ�硢�桼����¸�ߥ����å�
			var isExistUser = false;
			if ($('#pass').is(':visible')) {
//				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'checkemail', 'email':$("#email").val(), 'reg_site':'1'},
				$.ajax({url:'/php_libs/t_orders.php', type:'get', dataType:'json', async:false, data:{'act':'checkemail', 'args':[email]}, 
					success: function(r){
						if(r.length!=0){
							var msg = '<h1>��Ͽ�ѤߤΥ᡼�륢�ɥ쥹�Ǥ���</h1>';
							msg += '<p>E-mail����'+email+'</p>';
							msg += '<p>�����󤷤Ƥ���������</p>';
							msg += '<p><a href="/user/resend_pass.php" target="_brank">�ѥ���ɤκ�ȯ�ԤϤ������</a></p>';
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
			if($('#customername').val().trim()=='') required.push('<li>��̾��</li>');
			if(email=='') required.push('<li>�᡼�륢�ɥ쥹</li>');
			if($('#pass').is(':visible') && $('#pass').val().trim()=='') required.push('<li>�ѥ����</li>');
			if($('#tel').val().trim()=='') required.push('<li>�������ֹ�</li>');
			if($('#addr1').val().trim()=='') required.push('<li>������</li>');
			if(!$(':radio[name=repeater]:checked').val()) required.push('<li>���Ҥ����ѤˤĤ���</li>');
			if($('#deli').is(':checked')){
				if($('#organization').val().trim()=='') required.push('<li>��̾</li>');
				if($('#delitel').val().trim()=='') required.push('<li>���Ϥ���Τ������ֹ�</li>');
				if($('#deliaddr1').val().trim()=='') required.push('<li>���Ϥ��褴����</li>');
			}
			var required_list = '<ul class="msg">'+required.toString().replace(/,/g,'')+'</ul>';
			if(required.length>0){
				$.msgbox("ɬ�ܹ��ܤ����Ϥ򤴳�ǧ����������<br />"+required_list);
				return;
			}
			
			// �桼�����������Ͽ
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
						tr += '<td>'+items[i]['name']+'<br/ >'+thumb+'<span>���顼�� '+items[i]['color']+'</span></td>';
						
						var size = '';
						var cost = '';
						var vol = '';
						var sub = '';
						for(var j=0; j<items[i]['size'].length; j++){
							size += '<p>'+items[i]['size'][j]['sizename']+'</p>';
							cost += '<p>'+$.addFigure(items[i]['size'][j]['cost'])+'</p>';
							vol += '<p>'+items[i]['size'][j]['amount']+'<span>��</span></p>';
							sub += '<p>'+$.addFigure(items[i]['size'][j]['subtotal'])+'<span>��</span></p>';
							amount += items[i]['size'][j]['amount']-0;
							item_price += items[i]['size'][j]['subtotal']-0;
						}
						tr += '<td class="ac">'+size+'</td>';
						tr += '<td class="ar">'+cost+'</td>';
						tr += '<td class="ar">'+vol+'</td>';
						tr += '<td class="ar">'+sub+'</td>';
						tr += '</tr>';
						
						// �᡼�����֥��ʥå����פ�̵ͭ���ǧ
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
					tr += '<tr><td colspan="3" class="ac">����</td><td class="ar">'+$.addFigure(amount)+'<span>��</span></td><td class="ar">'+$.addFigure(item_price)+'<span>��</span></td></tr>';
					tr += '<tr><td colspan="4">�ץ�����</td><td class="ar">'+$.addFigure(option.printprice)+'<span>��</span></td></tr>';
					
					var optionfee = 0;
					var optname = {'discount':'���', 'carriage':'����', 'codfee':'��������', 'conbifee': '����ӥ˼����', 'packfee':'�޵���', 'expressfee':'�õ�����'};
					var pack = '��˾���ʤ�';
					if(r.option.pack==1){
						pack = '��˾����';
					}else if(r.option.pack==2){
						pack = '�ޤΤ�';
					}
					for(var m in option){
						var spec = '';
						if(m=='discount'){
							if(option[m]!=0) spec = '��'+option['discountname'].toString()+'��';
							else spec = '';
							tr += '<tr><td colspan="4">'+optname[m]+'<span id="spec_discount">'+spec+'</span></td><td class="ar fontred">��<ins id="conf_discount">'+$.addFigure(option[m])+'<ins><span>��</span></td>';
						}else if(m=='carriage'){
							tr += '<tr><td colspan="4">'+optname[m]+'<span>��30,000�߰ʾ��<ins class="fontred">����̵��</ins>��</span></td><td class="ar"><ins id="conf_carriage">'+option[m]+'</ind><span>��</span></td>';
						}else if(m=='codfee' && option[m]!=0){
							tr += '<tr><td colspan="4">'+optname[m]+'</td><td class="ar">'+option[m]+'<span>��</span></td>';
						}else if(m=='conbifee' && option[m]!=0){
							tr += '<tr><td colspan="4">'+optname[m]+'</td><td class="ar">'+option[m]+'<span>��</span></td>';
						}else if(m=='packfee'){
							tr += '<tr><td colspan="4">'+optname[m]+'<span>��'+pack+'��</span></td><td class="ar"><ins id="conf_pack">'+$.addFigure(option[m])+'</ind><span>��</span></td>';
						}else if(m=='expressfee'&& option['expressInfo']!=''){
							tr += '<tr><td colspan="4">'+optname[m]+'<span>��'+option['expressInfo']+'��</span></td><td class="ar"><ins id="conf_expressfee">'+$.addFigure(option[m])+'</ind><span>��</span></td>';
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
						attachfiles = '�ʤ�';
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
								$('#conf_'+u).html('���ƤΤ�����');
							}else if(r.user[u]==2){
								$('#conf_'+u).html('�����ˤ���ʸ�������Ȥ�����');
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
						$('#conf_publish').text('�Ǻܲ�');
					}else{
						$('#conf_publish').text('�Ǻ��Բ�');
					}
					
					$('#conf_deliveryday').text(r.option.deliveryday);
					$('#conf_deliverytime').text(r.option.deliverytime);
					
					var payment = ['��Կ���', '������', '�������Ǹ���ʧ��', '�����ɷ��', '����ӥ˷��'];
					$('#conf_payment').text(payment[r.option.payment]);
					
					
					// �ץ��Ⱦ���
					var tbody = '';
					var curitemname = '';
					var inks = 0;
					var sizeName = ['��', '��', '��'];
//					var printMethod = {'silk':'���륯', 'digit':'�ǥ�����ž��','inkjet':'���󥯥����å�'};
					for(var i=0; i<r.design.length; i++){
						if(curitemname!=r.design[i]['itemname']){
							if(curitemname!='' && inks==0){
								tbody = '<tr><th>'+curitemname+'</th><td colspan="3">�ץ��Ȥʤ�</td></tr>';
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
					
					if(inks==0) tbody = '<tr><th>'+curitemname+'</th><td colspan="3">�ץ��Ȥʤ�</td></tr>';
					$('#conf_print tbody').html(tbody);
				}
			});
			
			if(isError){
				$.msgbox("�̿����顼��ȯ�����Ƥ��ޤ���<br>������Ǥ���Step1����äƤ���������");
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
					if(ink_txt.indexOf('����')!=-1){
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
				$('#conf_print').html('<p>�ץ��Ȥʤ�</p>');
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
		*	��ۤ�������Ļ벽
		*	@target	jQuery ���֥�������
		*	@args		�ѹ���ζ��
		*/
			var price = $target.text().replace(/,/g,"") - 0;	// ���ζ��
			var plus = Math.abs(args - price);					// ����
			if(plus==0) return;
			var limit = 10;										// �񴹤�����
			var inc = 0;										// ����������
			var strUA = navigator.userAgent.toLowerCase();		// �֥饦�����̤���٤Υ桼������������Ȥ����
			if(strUA.indexOf("msie") != -1){					// �񴹤�������Ĵ������
				limit = 10;
				inc = 100;
			}else if(strUA.indexOf("firefox") != -1){
				limit = 25;
				inc = 40;
			}else{
				limit = 100;
				inc = 10;
			}
			if(plus<=1000){										// ���ۤ�¿�ɤ��¹Ի��֤˱ƶ����ʤ��褦��Ĵ��
				limit = Math.floor(plus / inc);
			}else{
				inc = Math.floor(plus / limit);
			}
			if(args-price<0){inc = -1*inc;}					// ��ۤ�������
			var str = 0;										// ����ڤ�Ѥߤ�ɽ�����
			var cnt = 0;										// ���󥯥���Ȳ��
			var intervalID = setInterval( function(){			// 1�ߥ��ä��Ȥ˼¹�
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
