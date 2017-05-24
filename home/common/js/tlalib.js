/*
*	タカハマライフアート
*	TLA Library
*	charset euc-jp
*
*	depends: 	jQuery.js
*				phonedata.js
*				modalbox/css/jquery.modalbox.css
*				modalbox/jquery.modalbox.js
*/
		
/**************************************************************
*		外部ソースの読込
***************************************************************/
$.ajaxSetup({scriptCharset:'utf-8'});
$.getScript('/common/js/phonedata.js');


	$(function(){
	
/***************************************************************************************************************************
*
*	common module
*
****************************************************************************************************************************/
		
		/********************************
		*	入力エリアに透かし文字で説明を表示
		*/
 		$.updnWatermark.attachAll();
 		
 		
 		/********************************
		*	縦スクロールに合わせて要素を移動
		*/
		if($('#oisogi').length>0){
			$(window).scroll(function () {
				var box = $('#oisogi');
				var parent_contents = $('#container');
				var padding = parseInt(box.css("padding-top").substring(0,box.css("padding-top").indexOf("px")));
				var above = 0;  	// 0 is the above element height.
				var initTop = 330;	// top
				var boxYloc = padding;
				var bottomPos = parent_contents.height() - (box.height() + boxYloc); //get the maximum scrollTop value
				var offset = $(document).scrollTop();
				offset = offset>bottomPos? bottomPos: offset+initTop;
				box.animate({top:offset+"px"},{duration:500,queue:false});
			});
		}
		
 		
 		/********************************
		*	スムーススクロール
		*/
 		$('a[href*=#]').on('click', function() {
	        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
	            var $target = $(this.hash);
	            $target = $target.length && $target || $('[name=' + this.hash.slice(1) +']');
	            if ($target.length) {
	                var targetOffset = $target.offset().top;
	       //         $($.browser.opera ? document.compatMode == 'BackCompat' ? 'body' : 'html' :'html,body')
	       //         .animate({scrollTop: targetOffset}, 1000, 'easeOutExpo');
									$('html,body').animate({scrollTop: targetOffset}, 1000, 'easeOutExpo');
	                return false;
	            }
	        }
	    });
	    
	    
		/********************************
		*	trim
		*/
		String.prototype.trim = function(){return this.replace(/^[\s　]+|[\s　]+$/g, '');};
		
		
		/********************************
		*	move cursor and trim text in forms
		*/
		$('form input:not([class^=for])').on('keypress', function(e){
			var my = (e.target || window.event.srcElement);
			var code=(e.charCode) ? e.charCode : ((e.which) ? e.which : e.keyCode);
			if(code == 13 || code == 3){
				var self = $(this);
				self.val(self.val().trim());
				$(this).moveCursor(my);
			}
		}).on('focusout', function(e){
			$(this).val($(this).val().trim());
		});
		
		
		/********************************
		*	restriction of input and move cursor in forms
		*/
		jQuery.fn.extend({
			restrictKey: function(e, mode){
			/*
			*	入力制御
			*	@mode	num: 	数値
			*			price:	金額
			*			date:	日付 
			*/
				var my = (e.target || window.event.srcElement);
				var code=(e.charCode) ? e.charCode : ((e.which) ? e.which : e.keyCode);
				switch(mode){
				case 'num':
					if (   !e.ctrlKey 				// Ctrl+?
				        && !e.altKey 				// Alt+?
				        && code != 0 				// ?
				        && code != 8 				// BACKSPACE
				        && code != 9 				// TAB
				        && code != 13 				// Enter
				        && code != 37 && code != 39 // ←→
				        && (code < 48 || code > 57)) // 0-9
				    	e.preventDefault();

				    if(code == 13 || code == 3) $(this).moveCursor(my);
			    	break;
				case 'price':
					if (   !e.ctrlKey 				// Ctrl+?
				        && !e.altKey 				// Alt+?
				        && code != 0 				// ?
				        && code != 8 				// BACKSPACE
				        && code != 9 				// TAB
				        && code != 13 				// Enter
				        && code != 37 && code != 39 // ←→
				        && code != 45				// -
				        && code != 46				// .
				        && (code < 48 || code > 57)) // 0-9
				    	e.preventDefault();

				    if(code == 13 || code == 3) $(this).moveCursor(my);
			    	break;
			    case 'date':
					if (   !e.ctrlKey 				// Ctrl+?
				        && !e.altKey 				// Alt+?
				        && code != 0 				// ?
				        && code != 8 				// BACKSPACE
				        && code != 9 				// TAB
				        && code != 13 				// Enter
				        && code != 37 && code != 39 // ←→
				        && code != 45				// -
				        && (code < 47 || code > 57)) // 0-9 /
				    	e.preventDefault();

				    if(code == 13 || code == 3) $(this).moveCursor(my);
			    	break;
			    }

			    return this;
			},
			moveCursor: function(my){
			/*
			*	form 内のテキストフィールドをエンターキーで移動する
			*/
				if(!my.form){
					$(my).focusout();
					return this;	// form要素でなければ何もしない
				}
				var first = -1;		// form内の最初のtext（readonlyは除く）のインデックス
				var isMove = false;	// カーソル移動が出来たかどうかのチェック
				var elem = my.form.elements;
			    for(var i=0; i<elem.length; i++){
			    	if( first==-1 && elem[i].type=="text" && !$(elem[i]).attr('readonly') && elem[i].style.display!='none' ) first = i;
			    	if( elem[i]==my ){
		    			while(i<elem.length-1){
		    				i++;
			    			if( elem[i].type=="text" && !$(elem[i]).attr('readonly') && elem[i].style.display!='none' ){
			    				elem[i].focus();
			    				isMove = true;
			    				break;
			    			}
			    		}
			    		if( !isMove && first!=-1 ) elem[first].focus();
			    		break;
			    	}
			    }
			    return this;
			}
		});

		
		/********************************
		*	入力制御関連のclass属性
		*/
		
		// 0と自然数　0から9 のみ入力、桁区切りなし、不正値は"0"
		$('.forNum').on('keypress', function(e){
			$(this).restrictKey(e, 'num');
		}).on('focusout', function(e){
			$.check_NaN(this);
		});
		
		// 0と自然数　0から9 のみ入力、桁区切りなし、不正値は""
		$('.forBlank').on('keypress', function(e){
			$(this).restrictKey(e, 'num');
		}).on('focusout', function(e){
			$.check_NaN(this,"");
		});
		
		// 0から9 . - のみ入力、桁区切りなし、不正値は"0"
		$('.forReal').on('keypress', function(e){
			$(this).restrictKey(e, 'price');
	    }).on('focusout', function(e){
	    	$.check_Real(this);
		});
		
		// 金額　0から9 . - のみ入力、桁区切りあり、フォーカスでカンマなしに変換、不正値は"0"
		$('.forPrice').on('keypress', function(e){
			$(this).restrictKey(e, 'price');
		}).on('focusin', function(){
	    	var c = this.value;
	      	this.value = c.replace(/,/g, '');
	      	var self = this;
	      	$(self).select();
	    }).on('focusout', function(e){
	    	var c = this.value;
			this.value = $.addFigure(c);
		});
		
		// 日付　0から9 / - のみ入力し、不正値は""
		$('.forDate').on('keypress',function(e){
			$(this).restrictKey(e,'date');
	    }).on('focusout', function(e){
	    	$.check_date(e, this);
	    });
	    
	    // zipcode mask
		$('.forZip').keypress( function(e) {
			$(this).restrictKey(e,'num');
	    }).on('focusin', function(){
	    	$.restrict_num(8, this);
	    }).on('focusout', function(e){
	    	this.maxLength = 8;
	    	this.value = $.zip_mask(this.value);
	    });

		// tel and fax mask 
		$('.forPhone').keypress( function(e) {
			$(this).restrictKey(e,'num');
	    }).on('focusin', function(){
	    	$.restrict_num(13, this);
	    }).on('focusout', function(e){
	    	var res = $.phone_mask(this.value); 
	    	this.maxLength = res.l;
	    	this.value = res.c;
	    });
		
		/* フォームの文字数制限
		*	文字数は半角でmaxlengthの数
		*/
		$('.restrict').focusout( function(){
			var val = $(this).val();
			var maxlen = $(this).attr('maxlength');
			var res = $.restrictInput(val, maxlen);
			if(val!=res[0]){
				$.msgbox("入力できる文字数は、\n全角"+maxlen/2+"文字、半角"+maxlen+"文字までです。");
				$(this).val(res[0]);
			}
		});
		
		
		
		/********************************
		*	グローバルナビのドロップダウン
		*	428HP 拡張
		*/
		$("#gnavi > ul > li").mouseover( function(){
			var c = $('ul li', this).length;
			var h = 0;
			if($('ul', this).is(".items")){
				h = $('ul li:eq(1)', this).outerHeight(true) * (c-1);
				h /= 5;
				h += $('ul li:eq(0)', this).outerHeight(true);
			}else{
				h = $('ul li', this).outerHeight(true) * c;
			}
			$('ul', this).animate({height:h+'px'}, {duration: 'fast', queue:false});
		});
		$("#gnavi > ul > li").mouseout( function(){
			$('ul', this).animate({height:'0px'}, {duration: 'fast', queue:false});
		});
		
		
		
		/********************************
		*	サイドナビのアコーディオン
		*	428HP 拡張
		*/
		$('.toggler', '#toggle').click( function(){
			if($(this).next().is(':visible')){
				$(this).removeClass('cur');
			}else{
				$(this).addClass('cur');
			}
			$('.toggler', '#toggle').not(this).removeClass('cur').next().hide('700');
			$(this).next().slideToggle('700');
		});
		
	});
	
	
/***************************************************************************************************************************
*
*	extended the jQuery object 
*
****************************************************************************************************************************/

	jQuery.extend({
		strLength: function (args){
		/*
		*	半角英数とそれ以外の判断
		*	@args	対象文字列
		*
		*	return	全角:2、半角:1 とした文字量
		*/
			var len = 0;
			for(s=0; s<args.length; s++){
				if(args[s].match(/[ｱ-ﾝｧｨｩｪｫｬｭｮﾟﾞ]/)){
					len++;
				}else{
					var strSrc = escape(args[s]);
					for(var i=0; i<strSrc.length; i++, len++){
						if(strSrc.charAt(i) == "%"){
							if(strSrc.charAt(++i) == "u"){
								i += 3;
								len++;
							}
							i++;
						}
					}
				}
			}
			return len;
		},
		restrictInput: function(args, maxlen){
		/*
		*	全半角の区別なく指定文字数以内で丸める
		*	@args	対象文字列
		*	@maxlen	半角での最大文字数
		*
		*	return	[文字列, 文字数]
		*/
			var i = 0;
			var str = '';
			var len = 0;
			var res = 0;
			var isOver = 0;
			
			if(maxlen==0) return '';
			
			for(i=0; i<args.length; i++){
				str = args.slice(i,i+1);
				res = $.strLength(str);
				/*
				if(str.match(/[ｱ-ﾝｧｨｩｪｫｬｭｮﾟﾞ]/)){
					res = 1;	// 半角カナ
				}else{
					res = $.strLength(str);
				}
				*/
				if(res+len>maxlen && isOver==0){
					isOver = i;
				}
				len += res;
			}
			if(isOver==0) isOver = i;
			str = args.slice(0,isOver);
			return [str, len];
		},
		addFigure:function(args){
		/*
		*	金額の桁区切り
		*	@arg		対象の値
		*
		*	@return		桁区切りした文字列
		*/
			var str = new String(args);
			str = str.replace(/[０-９]/g, function(m){
	    				var a = "０１２３４５６７８９";
		    			var r = a.indexOf(m);
		    			return r==-1? m: r;
		    		});
		    str -= 0;
	    	var num = new String(str);
	    	if( num.match(/^[-]?\d+(\.\d+)?/) ){
	    		while(num != (num = num.replace(/^(-?\d+)(\d{3})/, "$1,$2")));
	    	}else{
	    		num = "0";
	    	}
	    	return num;
		},
		check_NaN:function(my){
		/*
		*	自然数かどうか
		*	@my			Object
		*
		*	@return		自然数でない場合に0を返す、第二引数があれば、自然数以外のときの返り値として使用
		*/
			var err = arguments.length>1? arguments[1]: 0;
			var str = my.value.trim().replace(/[０-９]/g, function(m){
	    				var a = "０１２３４５６７８９";
		    			var r = a.indexOf(m);
		    			return r==-1? m: r;
		    		});
		    my.value = (str.match(/^\d+$/))? str-0: err;
		    return my.value;
		},
		check_Real: function(my){
		/*
		*	実数かどうか（整数、小数点）
		*	@my			Object
		*
		*	@return		不正値は0
		*/
			var str = my.value.trim().replace(/[０-９]/g, function(m){
				var a = "０１２３４５６７８９";
				var r = a.indexOf(m);
				return r==-1? m: r;
			});
			my.value = (str.match(/^-?[0-9]+([\.]{1}[0-9]+)?$/))? str-0: 0;
			return my.value;
		},
		check_date: function(e, my){
		/*
		*	日付の妥当性を確認、yyyy-mm-ddをオブジェクトの値に代入、日付ではない時はブランク（空文字）を代入
		*	@e		event
		*	@my		Object
		*/
    		var val = my.value;
		 	var date = new Date();
			var res = new Array();
			var yy, mm, dd;
			if(val.match(/^(\d{4})-([01]?\d{1})-([0123]?\d{1})$/)){
				res = val.split('-');
				yy = res[0]-0;
				mm = res[1]-0;
				dd = res[2]-0;
			}else if(val.match(/^([01]?\d{1})-([0123]?\d{1})$/)){
				res = val.split('-');
				yy = date.getFullYear();
				mm = res[0]-0;
				dd = res[1]-0;
			}
			date = new Date(yy, mm-1, dd);
			if(yy==date.getFullYear() && mm-1==date.getMonth() && dd==date.getDate()){
				mm = (""+mm).length==1? "0"+mm: mm;
				dd = (""+dd).length==1? "0"+dd: dd;
				my.value = yy+'-'+mm+'-'+dd;
			}else{
				my.value = "";
			}
			var evt = e? e: event;
			evt.preventDefault();
	    },
	    restrict_num:function(n, my) {
	    /*
	    *	テキストフィールドの入力文字数を制限する、当該オブジェクトを選択状態にする
	    *	@n		入力可能な文字数
	    *	@my		オブジェクト
	    */
      		var c = my.value;
	      	c = c.replace(/[^\d]/g, '');
		    my.maxLength = n;
		    my.value = c;
		    var self = my;
		    $(self).select();
	    },
	    zip_mask:function(args) {
	    /*
	    *	郵便番号を「-」で区切る
	    *	@args		郵便番号
	    *
	    *	@return		「-」で区切った郵便番号を返す
	    */
	    	var c = args.replace(/[０-９]/g, function(m){
	    				var a = "０１２３４５６７８９";
		    			var r = a.indexOf(m);
		    			return r==-1? m: r;
		    		});
	      	c = c.replace(/[^\d]/g, '');
	      	if(c.length >= 3) c = c.substr(0, 3) + '-' + c.substr(3);
	      	
	      	return c;
		},
		phone_mask:function(args){
		/*
		*	電話番号を「-」で区切る
		*	@args		電話番号
		*
		*	@return		[c:電話番号, l:桁数]　
		*/
			var l = 12;
			var c = args.replace(/[０-９]/g, function(m){
	    				var a = "０１２３４５６７８９";
		    			var r = a.indexOf(m);
		    			return r==-1? m: r;
		    		});
	      	c = c.replace(/[^\d]/g, '');
      		if($.check_phone_separate(c, 5)){
      			c = c.substr(0, 5) + '-' + c.substr(5, 1) + '-' + c.substr(6, 4);
      		}
      		else if($.check_phone_separate(c, 4)){
      			c = c.substr(0, 4) + '-' + c.substr(4, 2) + '-' + c.substr(6, 4);
      		}
	      	else{
	      		var tel1 = c.substr(0, 3);
	      		if(tel1.match(/^0[5789]0$/)){
	      			c = c.substr(0, 3) + '-' + c.substr(3, 4) + '-' + c.substr(7, 4);
	      			l = 13;
	      		}
	      		else if($.check_phone_separate(c, 3)){
	      			c = c.substr(0, 3) + '-' + c.substr(3, 3) + '-' + c.substr(6, 4);
	      		}
	      		else if($.check_phone_separate(c, 2)){
		      		c = c.substr(0, 2) + '-' + c.substr(2, 4) + '-' + c.substr(6, 4);
		      	}
		    }
		    
		    return {'c':c,'l':l};
		},
		check_phone_separate:function(c, count){
		/*
		*	該当する市外局番の有無を確認する
		*	@c			チェックする市外局番
		*	@count		市外局番の桁数
		*
		*	@return		該当する市外局番がある場合: true  無ければ: false
		*/
      		var tel1 = c.substr(0, count);
      		var flg = false;
      		var phone = '';
      		switch(count){
      			case 5:phone = phone5;
      						break;
      			case 4:phone = phone4;
      						break;
      			case 3:phone = phone3;
      						break;
      			case 2:phone = phone2;
      						break;
      			default:return flg;
      		}

      		for(var i=0; i<phone.length; i++){
	      		if(phone[i]==tel1){
	      			flg = true;
	      			break;
	      		}
	      	}

	      	return flg;
		},
		check_email: function(email){
		/*
		*	メールアドレスの妥当性チェック
		*	@email		メールアドレス
		*
		*	@return		OK: true	NG: false
		*/
	   
			if(email.trim()=="" || !email.match(/@/)){
				$.msgbox('メールアドレスではありません。');
				return false;
			}

			var res = false;
			
			/*	RFC2822 addr_spec 準拠パターン							*/
			/*	atom       = {[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+};		*/
			/*  dot_atom   = {$atom(?:\.$atom)*};						*/
			/*  quoted     = {"(?:\\[^\r\n]|[^\\"])*"};					*/
			/*  local      = {(?:$dot_atom|$quoted)};					*/
			/*  domain_lit = {\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\]};		*/
			/*  domain     = {(?:$dot_atom|$domain_lit)};				*/
			/*  addr_spec  = {$local\@$domain};							*/
			$.ajax({
				url:'/php_libs/checkDNS.php', async:false, type:'POST', dataType:'text', data:{'email': email}, 
				success:function(r){
					if(r){
						if( email.match(/^(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+))*)|(?:\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\])))$/)){
							//$.msgbox('OK!\n確認メールを送信してください。');
							res = true;
						}else{
							$.msgbox('メールアドレスを確認してください。');
						}
					}else{
						$.msgbox('@マークより後を確認してください。');
					}
				}
			});
			
			return res;
			
		},
		scrollto: function(target){
		/*
		*	指定位置にスクロール
		*	@target		jQuery オブジェクト
		*	第二引数	コールバック関数
		*/
			var fnc = null;
			if(arguments.length>1 && typeof arguments[1]=="function") fnc = arguments[1];	// 第二引数があれば、コールバック関数として使用 
			var targetOffset = target.offset().top;
//			$($.browser.opera ? document.compatMode == 'BackCompat' ? 'body' : 'html' :'html,body')
			$('html,body').animate({scrollTop: targetOffset}, 500, 'easeQuart', fnc);
        },
	    getDelimiter: function(r){
	    /*
	    *	データベースからの抽出結果の文字列をデータに区切るコードを取得
	    *	@r		抽出結果の文字列
	    */
			var delimiter = r.slice(-30);			// 区切り文字に使用しているコードを取得
			var res = [];
 			res['fld'] = delimiter.slice(0,10);		// フィールドの区切り
 			res['dat'] = delimiter.slice(10,20);	// キーと値の区切り
 			res['rec'] = delimiter.slice(-10);		// レコードの区切り
 			$.delimiter.rec = res['rec'];
 			$.delimiter.fld = res['fld'];
 			$.delimiter.dat = res['dat'];
 			return res;
		},
		delimiter: {
		/*
		*	getDelimiter で抽出した区切り文字列を保持する
		*	rec:	レコードの区切り
		*	fld:	フィールドの区切り
		*	dat:	データの区切り
		*/
			'rec':"",
			'fld':"",
			'dat':""
		},
		msgbox: function(msg){
		/*
		*	メッセージボックス
		*	@msg		表示するメッセージ文
		*	@arguments	タイトルを指定、指定なしの場合は「メッセージ」
		*/
			var title = arguments.length==2? arguments[1]: 'メッセージ';
			$('#msgbox').off('show.bs.modal').on('show.bs.modal', {'message': msg, 'title':title}, function (e) {
				$('.modal-footer').hide();
				$('#msgbox .modal-title').html(e.data.title);
				$('#msgbox .modal-body p').html(e.data.message);
			});
			$('#msgbox').modal('show');
    	},
		confbox: {
		/*
		*	確認ボックス
		*	@msg		表示するメッセージ文
		*	@fn			callback ボタンが押された後の処理　OK:true, Cancel:false
		*/
			show: function(msg, fn){
				$.confbox.result.data = false;
				$('#msgbox').off('show.bs.modal').on('show.bs.modal', {'message': msg}, function (e) {
					$('.modal-footer').show();
					$('#msgbox .modal-body p').html(e.data.message);
					$(this).one('click', '.is-ok', function(){
						$.confbox.result.data = true;
					});
					$(this).one('click', '.is-cancel', function(){
						$.confbox.result.data = false;
					});
				});
				$('#msgbox').off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
					fn();
				});
				$('#msgbox').modal('show');
			},
			result: {
				'data':false
			}
		},
		TLA: {
			'api':'http://takahamalifeart.com/v1/api',
			'show_site':'1'
		}
	});
	
	
/***************************************************
*		スムーススクロール
*/

	jQuery.extend( jQuery.easing,
	{
		def: 'easeOutQuad',
		swing: function (x, t, b, c, d) {
			//alert(jQuery.easing.default);
			return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
		},
		easeInQuad: function (x, t, b, c, d) {
			return c*(t/=d)*t + b;
		},
		easeOutQuad: function (x, t, b, c, d) {
			return -c *(t/=d)*(t-2) + b;
		},
		easeInExpo: function (x, t, b, c, d) {
			return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
		},
		easeOutExpo: function (x, t, b, c, d) {
			return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
		},
		easeInOutExpo: function (x, t, b, c, d) {
			if (t==0) return b;
			if (t==d) return b+c;
			if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
			return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
		},
		easeInSine: function (x, t, b, c, d) {
			return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
		},
		easeOutSine: function (x, t, b, c, d) {
			return c * Math.sin(t/d * (Math.PI/2)) + b;
		},
		easeQuart: function (x, t, b, c, d) {
			return -c * ((t=t/d-1)*t*t*t - 1) + b;
		}
	});

	
/***************************************************
*		全ての画像の読込みを完了してから処理を実行させる
*/
$.fn.imagesLoaded = function(callback){
	var elems = this.filter('img'),
    	len = elems.length,
    	blank = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
      
	elems.bind('load.imgloaded',function(){
		if(--len <= 0 && this.src !== blank){
			elems.unbind('load.imgloaded');
			callback.call(elems,this);
		}
	}).each(function(){
     	// cached images don't fire load sometimes, so we reset src.
		if (this.complete || this.complete === undefined){
			var src = this.src;
        	// webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
        	// data uri bypasses webkit log warning (thx doug jones)
			this.src = blank;
			this.src = src;
		}
	});

	return this;
};


/***************************************************
*		入力エリアの透かし文字
*/
(function($) {
    $.fn.updnWatermark = function(options) {
        options = $.extend({}, $.fn.updnWatermark.defaults, options);
        return this.each(function() {
            var $input = $(this);
			// Checks to see if watermark already applied.
            var $watermark = $input.data("updnWatermark");
            // Only create watermark if title attribute exists
            if (!$watermark && this.title) {
            	$watermark = this.title;
            	$input.data("updnWatermark", $watermark);

            }
			// Hook up blur/focus handlers to show/hide watermark.
            if ($watermark) {
                $input
                    .focus(function(ev) {
                    	var c = this.value.trim();
                    	if(c==$watermark){
                    		$input.val("").css('color','#333');
                    	}
                    })
                    .blur(function(ev) {
                        if (!$(this).val()) {
                            $input.val($watermark).css('color','#999');
                        }else{
                        	$input.css('color','#333');
                        }
                    });
                // Sets initial watermark state.
                if (!$input.val()) {
                    $input.val($watermark).css('color','#999');
                }
            }
        });
    };
    $.fn.updnWatermark.defaults = {
        cssClass: "updnWatermark"
    };
    $.updnWatermark = {
        attachAll: function(options) {
			$("input:text[title!=''],input:password[title!=''],textarea[title!='']").updnWatermark(options);
        }
    };
})(jQuery);