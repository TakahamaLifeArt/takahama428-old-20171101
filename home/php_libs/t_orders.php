<?php
/*
*	session for order
*	charset utf-8
*
*	['sum']['itemprice']
*		   ['printprice']
*		   ['amount']
*		   ['discount']
*		   ['carriage']
*		   ['codfee']
*		   ['pack']
*		   ['discountname][ , ...]
*
*	['items'][category_id]['category_key']
*			  			  ['category_name']
*		   	  			  ['item'][id]['code']
*					   			  	  ['name']
*							      	  ['color'][code]['master_id']
*													 ['name']
*											 		 ['size'][sizeid]['sizename']
*											  						 ['amount']
*																	 ['cost']
*
*									  ['posid']
*									  ['makerid']
*									  ['design'][base][0]['posname']
*														 ['poskey']
*												   		 ['ink']
*												   		 ['categorytype']
*												   		 ['itemtype']
*														 ['areakey']
*														 ['areasize']
*														 ['printing']
*
*	['attach'][0]['img']['file']
*						['name']
*						['type']
*				 ['pos']['id']
*						['base']
*						['posname']
*
*	['customer']['member']			顧客ID
*				['customername']
*				['customerruby']
*				['email']
*				['tel']
*				['zipcode']
*				['addr0']
*				['addr1']
*				['addr2']
*				['deli_id']			納品先ID
*				['delizipcode']
*				['deliaddr0']
*				['deliaddr1']
*				['deliaddr2']
*				['comment']
*				['note_design']		デザインの備考
*				['note_printcolor'] インク色指定
*				['note_printmethod'] 刺繍を希望
*				['repeater']		リピーター　1:初めて、2:以前に注文あり
*
*	['options']['pack']				0, 1, 2
*			   ['packfee']			袋詰め代
*			   ['blog']				0, 3
*			   ['student']			0, 3, 5, 7
*			   ['illust']			0, 1
*			   ['intro']			0, 3
*			   ['payment']			0:bank, 1:cod, 2:cash, 3:credit, 4:conbi
*			   ['publish']			0:掲載可, 1:掲載不可
*			   ['nodeliday']		納期指定 0:あり, 1:なし
*			   ['deliveryday']		YYYY-MM-DD
*			   ['deliverytime']		配達時間
*			   ['expressfee']		特急料金
*			   ['expressInfo']		特急の種類
*			   ['expressError']		製作日数不足のメッセージ
*			   ['noprint']			0:プリントあり(default)		1:プリントなし
*/
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/session_my_handler.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/jd/japaneseDate.php';
require_once dirname(__FILE__).'/items.php';
require_once dirname(__FILE__).'/conndb.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/order/ordermail.php';


class Orders Extends Conndb {

	private $_TAX = 0;

	public function __construct(){
		parent::__construct();
		$this->_TAX = parent::getSalesTax();
	}
	
	
	/*
	*	消費税率を返す
	*/
	public function salestax(){
		return $this->_TAX;
	}
	
	
	/*
	*	セッションの初期化	
	*	@mode			初期化するセッションのキー、default:null 全て
	*/
	public function init($mode=null){
		if(is_null){
			$_SESSION['orders']['sum'] = array();
			$_SESSION['orders']['items'] = array();
			$_SESSION['orders']['attach'] = array();
			$_SESSION['orders']['customer'] = array();
			$_SESSION['orders']['options'] = array();
		}else if($mode=='items' || $mode=='customer' || $mode=='options' || $mode=='attach' || $mode=='sum'){
			$_SESSION['orders'][$mode] = array();
		}
	}
	

	/*
	*	ログイン済みのお客さん情報自動登録
	*
	*/
	public function userAuto($me){
/*
		foreach($me as $key=>$val){
			$_SESSION['orders']['customer'][$key] = $val;
		}
*	['customer']['member']			顧客ID
*				['customername']
*				['customerruby']
*				['email']
*				['tel']
*				['zipcode']
*				['addr0']
*				['addr1']
*				['addr2']
*				['deli_id']			納品先ID
*				['delizipcode']
*				['deliaddr0']
*				['deliaddr1']
*				['deliaddr2']
*				['comment']
*				['note_design']		デザインの備考
*				['note_printcolor'] インク色指定
*				['note_printmethod'] 刺繍を希望
*				['repeater']		リピーター　1:初めて、2:以前に注文あり

*/
		$_SESSION['orders']['customer']['member'] = $me['id'];
		$_SESSION['orders']['customer']['customername'] = $me['customername'];
		$_SESSION['orders']['customer']['customerruby'] = $me['customerruby'];
		$_SESSION['orders']['customer']['email'] = $me['email'];
		$_SESSION['orders']['customer']['tel'] = $me['tel'];
		$_SESSION['orders']['customer']['zipcode'] = $me['zipcode'];
		$_SESSION['orders']['customer']['addr0'] = $me['addr0'];
		$_SESSION['orders']['customer']['addr1'] = $me['addr1'];
		$_SESSION['orders']['customer']['addr2'] = $me['addr2'];
	}

	/*
	*	セッションの更新
	*	@mode		items, estimation, customer, attach
	*/
	public function update($mode){
		$isExist = false;
		if($mode=='items'){
		/*
		*	商品情報の更新
		*	@categoryid
		*	@categorykey
		*	@categoryname
		*	@itemid
		*	@itemcode
		*	@itemname
		*	@colorcode			array
		*	@colorname			array
		*	@posid
		*	@noprint
		*	@makerid
		*	@sizename			array
		*	@amount				array
		*	@cost				array
		*
		*	['items'][category_id]['category_key']
		*		  				  ['category_name']
		*	   	  				  ['item'][id]['code']
		*				   				  	  ['name']
		*							      	  ['color'][code]['master_id']
		*													 ['name']
		*											 		 ['size'][sizeid]['sizename']
		*											  						 ['amount']
		*																	 ['cost']
		*									  ['posid']
		*									  ['makerid']
		*
		*	return		 {'sum':商品代, 'amount':合計枚数, 'printprice':プリント代合計, 'total':見積合計,
		*				  'options': {'discount':割引, 'carriage':送料, 'codfee':代引手数料, pack:袋詰代},
		*				  'category': [{category_id,category_key,category_name,item_id,item_code,item_name,color_code,color_name,cost,amount},{} ...] }
		*/
			$catid = $_REQUEST['categoryid'];
			$itemid = $_REQUEST['itemid'];
			
			if(isset($_SESSION['orders']['items'][$catid])){
				$isExist = 'category';
				if(isset($_SESSION['orders']['items'][$catid]['item'][$itemid])){
					$isExist = 'item';
				}
			}
			
			if(!$isExist){
				$_SESSION['orders']['items'][$catid]['category_key'] = $_REQUEST['categorykey'];
				$_SESSION['orders']['items'][$catid]['category_name'] = $_REQUEST['categoryname'];
				
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['code'] = $_REQUEST['itemcode'];
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['name'] = $_REQUEST['itemname'];
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['posid'] = $_REQUEST['posid'];
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['makerid'] = $_REQUEST['makerid'];
			}else if($isExist=='category'){
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['code'] = $_REQUEST['itemcode'];
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['name'] = $_REQUEST['itemname'];
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['posid'] = $_REQUEST['posid'];
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['makerid'] = $_REQUEST['makerid'];
			}
			
			//$_SESSION['orders']['items'][$catid]['item'][$itemid]['noprint'] = $_REQUEST['noprint'];
			//$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['name'] = $_REQUEST['colorname'];
			//$item_sum = 0;
			$amount = 0;
			$tmp = $_SESSION['orders']['items'][$catid]['item'][$itemid]['color'];
			$cnt= count($_REQUEST['sizeid']);
			for($i=0; $i<$cnt; $i++){
				$sizeid = $_REQUEST['sizeid'][$i];
				if($colorcode!=$_REQUEST['colorcode'][$i]){
					if($colorcode!=""){
						if($amount==0){
							unset($_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]);
							if(count($_SESSION['orders']['items'][$catid]['item'][$itemid]['color'])==0){
								unset($_SESSION['orders']['items'][$catid]['item'][$itemid]);
							}
						}
					}
					$colorcode = $_REQUEST['colorcode'][$i];
					$amount = 0;
					unset($tmp[$colorcode]);
				}
				
				if($_REQUEST['noprint']==1){	// プリントなしで10％UPし1円単位を切り上げる
					$cost = round($_REQUEST['cost'][$i]*1.1+4, -1);
				}else{
					$cost = $_REQUEST['cost'][$i];
				}
				
				if(!isset($_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['master_id'])){
					$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['master_id'] = $_REQUEST['master_id'][$i];
				}
				if(!isset($_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['name'])){
					$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['name'] = $_REQUEST['colorname'][$i];
				}
				
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['size'][$sizeid]['sizename'] = $_REQUEST['sizename'][$i];
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['size'][$sizeid]['amount'] = $_REQUEST['amount'][$i];
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['size'][$sizeid]['cost'] = $cost;
				$amount += $_REQUEST['amount'][$i];
				//$item_sum += $_REQUEST['cost'][$i]*$_REQUEST['amount'][$i];
			}
			
			// Step2で削除されたカラーのアイテム情報を消去
			if(!empty($tmp)){
				foreach($tmp as $key=>$val){
					unset($_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$key]);
				}
			}
			
			$data = $this->reqDetails();
			
			return $data;
		}
		
		if($mode=='amount'){
		/*
		*	枚数の更新
		*/
			$catid = $_REQUEST['categoryid'];
			$itemid = $_REQUEST['itemid'];
			$colorcode = $_REQUEST['colorcode'];
			$sizeid = $_REQUEST['sizeid'];
			
			$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['size'][$sizeid]['amount'] = $_REQUEST['amount'];
			
			$data = $this->reqDetails();
			
			return $data;
		}
		
		if($mode=='design'){
		/*
		*	プリント位置のデザイン情報の更新、base（前、後、横）に対して１箇所
		*	@base			array
		*	@posid
		*	@poskey			array
		*	@posname		array
		*	@ink			array
		*	@itemtype		array
		*	@areakey		array
		*	@areasize		array
		*	@printing		array
		*
		*	['items'][category_id]['item'][id]['posid']
		*									  ['design'][base][0]['posname']
		*														 ['poskey']
		*												   		 ['ink']
		*												   		 ['categorytype']
		*												   		 ['itemtype']
		*														 ['areakey']
		*														 ['areasize']
		*														 ['printing']
		*
		*	return			{'itemprice':商品代, 'amount':合計枚数, 'printprice':プリント代合計, 'total':見積合計,
		*					 'options': {'discount':割引, 'carriage':送料, 'codfee':代引手数料, pack:袋詰代}}
		*/
			$base = $_REQUEST['base'];
			$posid = $_REQUEST['posid'];
			
			foreach($_SESSION['orders']['items'] as $catid=>$v1){
				foreach($v1['item'] as $itemid=>$v2){
					if($v2['posid']!=$posid) continue;
					$a=0;
					for($i=0; $i<count($base); $i++){
//						if($cur_base!=$base[$i]){
//							$cur_base = $base[$i];
//							if(isset($v2['design'][$cur_base])) $_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$cur_base] = array();
//							$a=0;
//						}
						$_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$base[$i]] = array();
//						if ($_REQUEST['ink'][$i]!=0) {
							$tmp = array('poskey'=>$_REQUEST['poskey'][$i],
										 'posname'=>$_REQUEST['posname'][$i],
										 'ink'=>$_REQUEST['ink'][$i],
										 'categorytype'=>$_REQUEST['categorytype'][$i],
										 'itemtype'=>$_REQUEST['itemtype'][$i],
										 'areakey'=>$_REQUEST['areakey'][$i],
										 'areasize'=>$_REQUEST['areasize'][$i],
										);
							$_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$base[$i]][$a] = $tmp;
//						}
					}
				}
			}
			
			//}
			
			/*
			*	オプションの未設定項目を初期化
			*	['options']['pack']				0, 1, 2
			*			   ['packfee']			袋詰め代
			*			   ['blog']				0, 3
			*			   ['student']			0, 3, 5, 7
			*			   ['illust']			0, 1
			*			   ['intro']			0, 3
			*			   ['payment']			0:bank, 1:cod, 2:cash, 3:credit, 4:conbi
			*			   ['publish']			0:掲載可, 1:掲載不可
			*			   ['nodeliday']		納期指定 0:あり, 1:なし
			*			   ['deliveryday']		YYYY-MM-DD
			*			   ['deliverytime']		配達時間
			*			   ['expressfee']		特急料金
			*			   ['expressInfo']		特急の種類
			*			   ['expressError']		製作日数不足のメッセージ
			*			   ['noprint']			0:プリントあり(default)		1:プリントなし
			*/
			$hash = array('pack'=>0,
						  'packfee'=>0,
						  'blog'=>0,
						  'student'=>0,
						  'illust'=>0,
						  'intro'=>0,
						  'payment'=>0,
						  'publish'=>0,
						  'nodeliday'=>0,
						  'deliveryday'=>'',
						  'deliverytime'=>0,
						  'expressfee'=>0,
						  'expressInfo'=>'',
						  'expressError'=>'',
						  );
			foreach($hash as $key=>$val){
				if(!isset($_SESSION['orders']['options'][$key])) $_SESSION['orders']['options'][$key] = $val;
			}
			
			// デザインの備考
			if(isset($_REQUEST['note_design'])){
				$_SESSION['orders']['customer']['note_design'] = $_REQUEST['note_design'];
			}
			
			$data = $this->reqEstimation();
			
			return $data;
		}
		
		if($mode=='options'){
		/*
		*	割引、袋詰、支払方法、希望納期、入稿方法の指定とプリントカラーの備考欄を更新
		*	@key
		*	@val
		*
		*	['options']['pack']				0, 1, 2
		*			   ['packfee']			袋詰め代
		*			   ['blog']				0, 3
		*			   ['student']			0, 3, 5, 7
		*			   ['illust']			0, 1
		*			   ['intro']			0, 3
		*			   ['payment']			0:bank, 1:cod, 2:cash, 3:credit, 4:conbi
		*			   ['publish']			0:掲載可, 1:掲載不可
		*			   ['nodeliday']		納期指定 0:あり, 1:なし
		*			   ['deliveryday']		YYYY-MM-DD
		*			   ['deliverytime']		配達時間
		*			   ['expressfee']		特急料金
		*			   ['expressInfo']		特急の種類
		*			   ['expressError']		製作日数不足のメッセージ
		*			   ['noprint']			0:プリントあり(default)		1:プリントなし
		*
		*	return		{'discount':割引, 'carriage':送料, 'codfee':代引手数料, pack:袋詰代, discountname:[...]}
		*/
			
			$_SESSION['orders']['options'][$_REQUEST['key']] = $_REQUEST['val'];
			
			$data['options'] = $this->reqOptionfee();
			return $data;
		}
		
		if($mode=='customer'){
		/*
		*	ユーザー情報を更新
		*
		*	['customer']['member']			顧客ID
		*				['customername']
		*				['customerruby']
		*				['email']
		*				['tel']
		*				['zipcode']
		*				['addr0']
		*				['addr1']
		*				['addr2']
		*				['deli_id']			納品先ID
		*				['delizipcode']
		*				['deliaddr0']
		*				['deliaddr1']
		*				['deliaddr2']
		*				['comment']
		*				['note_design']		デザインの備考
		*				['note_printcolor'] インク色指定
		*				['note_printmethod'] 刺繍を希望
		*				['repeater']		弊社ご利用につて
		*/
		
			foreach($_REQUEST as $key=>$val){
				if($key=='act' || $key=='mode') continue;
				$_SESSION['orders']['customer'][$key] = $val;
			}
			
			return;
		}
		
		if($mode=='attach'){
		/*
		*	添付ファイルの更新
		*
		*	['attach'][0]['img']['file']
		*						['name']
		*						['type']
		*				 ['pos']['id']
		*						['base']
		*						['posname']
		*/
			
			// formからのPOSTデータのエンコード
			/*
			$pos = array(
					'id'=>$_REQUEST['posid'],
					'base'=>mb_convert_encoding($_REQUEST['base'], 'utf-8', 'euc-jp'),
					'posname'=>mb_convert_encoding($_REQUEST['posname'], 'utf-8', 'euc-jp')
					);
			*/
			$_SESSION['orders']['attach'] = array();;
			$error_msg = "";
			$attach_count = count($_FILES['attach']['tmp_name']);
			for($i=0; $i<$attach_count; $i++) {
				$result = null;
				$attach['img']['file'] = null;
				$attach['img']['name'] = null;
				$attach['img']['type'] = null;
				if( is_uploaded_file($_FILES['attach']['tmp_name'][$i]) ){
					if ($_FILES['attach']['error'][$i] == UPLOAD_ERR_OK){
						$tmp_path = $_FILES['attach']['tmp_name'][$i];
						$filename = $_FILES['attach']['name'][$i];
						$filetype = $_FILES['attach']['type'][$i];
						$files_len += $_FILES['attach']['size'][$i];
						
						if($files_len > _MAXIMUM_SIZE){
							$error_msg = '添付ファイルサイズは100MBまでにして下さい。';
							$result = 'ERR';
						}else{
						/*
							$extension = substr(strrchr($filename, '.'), 0);
		    				$save_name = md5(session_id()).md5(basename($filename)).time();
				    		$save_path = $upload_dir.$save_name.$extension;
						    move_uploaded_file($temp_path, $save_path);
						    
						    $position_id = $_POST['position_id'];
						    $data['position_id'] = $position_id;
						    $data['filename'] = $filename;
						    $data['save_path'] = $web_dir.$save_name.$extension;
						    $data['filetype'] = $filetype;
					    */
					    
							$uploadfile = file_get_contents($tmp_path);
							$img_encode64 = chunk_split(base64_encode($uploadfile));
							
					      	$result = 'OK';
						}
						    
				    }else{ 
				     	$error_msg = '添付ファイルのアップロード中にエラーです。添付ファイルの指定をやり直してください。';
				     	$result = 'ERR';
				    }
				    
				    if($result=='OK'){						// アップロード成功
			    		$attach['img']['file'] = $img_encode64;
						$attach['img']['name'] = $filename;
						$attach['img']['type'] = $filetype;
						
						
						$_SESSION['orders']['attach'][] = $attach;
						
						/*
						$isExist = false;
						for($j=0; $j<count($_SESSION['orders']['attach']); $j++){
							if( $_SESSION['orders']['attach'][$j]['pos']['id']==$pos['id'] || 
								$_SESSION['orders']['attach'][$j]['pos']['base']==$pos['base'] || 
								$_SESSION['orders']['attach'][$j]['pos']['posname']==$pos['posname']
							){
								$_SESSION['orders']['attach'][$j]['img'] = $attach;
								$isExist = true;
								break;
							}
						}
						if(!$isExist){
							$_SESSION['orders']['attach'][$j]['pos']['id'] = $pos['id'];
							$_SESSION['orders']['attach'][$j]['pos']['base'] = $pos['base'];
							$_SESSION['orders']['attach'][$j]['pos']['posname'] = $pos['posname'];
							$_SESSION['orders']['attach'][$j]['img'] = $attach;
						}
						*/
						
						/*	
						*	添付ファイル名を代入
						*	['items'][category_id]['item'][id]['posid']
						*									  ['design'][base][0]['posname']
						*												   		 ['ink']
						*												   		 ['attachname']
						*
						*/
						/*
						foreach($_SESSION['orders']['items'] as $catid=>$v1){
							foreach($v1['item'] as $itemid=>$v2){
								if($v2['posid']!=$pos['id']) continue;
								foreach($v2['design'] as $base=>$v3){
									if($base!=$pos['base']) continue;
									for($k=0; $k<count($v3); $k++){
										if($v3[$k]['posname']==$pos['posname']){
											$_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$base][$k]['attachname'] = $attach['name'];
										}
									}
								}
							}
						}
						*/
						
					}
					
				}
			}
			
			// this code is outputted to IFRAME (embedded frame)
			echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp" /><title>-</title></head><body>';
			echo '<script language="JavaScript" type="text/javascript">'."\n";
			//echo 'var parWin = window.parent;';
			
			if ($result == 'ERR'){
				if(empty($error_msg)) $error_msg = '添付ファイルのエラーです。';
				$error_msg = mb_convert_encoding($error_msg, 'euc-jp', 'utf-8');
			  	echo 'alert("'.$error_msg.'");';
			}
	
			echo "\n".'</script></body></html>';
			exit(); // do not go futher 
		
		}
	}
	
	
	/*
	*	セッションの削除
	*	@mode		items
	*/
	public function remove($mode){
		if($mode=='items'){
		/*
		*	商品情報の削除
		*	@categoryid
		*	@itemid
		*	@colorcode
		*	@sizeid
		*
		*	['items'][category_id]['item'][id]['color'][code]['master_id']
		*													 ['name']
		*											 		 ['size'][sizeid]['sizename']
		*											  						 ['amount']
		*																	 ['cost']
		*
		*	return		 {'itemprice':商品代, 'amount':合計枚数, 'printprice':プリント代合計, 'total':見積合計,
		*				  'options': {'discount':割引, 'carriage':送料, 'codfee':代引手数料, pack:袋詰代},
		*				  'category': [{category_id,category_key,category_name,item_id,item_code,item_name,color_code,color_name,cost,amount},{} ...] }
		*/
			$catid = $_REQUEST['categoryid'];
			$itemid = $_REQUEST['itemid'];
			$colorcode = $_REQUEST['colorcode'];
			$sizeid = $_REQUEST['sizeid'];
			
			if(empty($catid)){
			// 全てのアイテム情報を削除
				$_SESSION['orders']['items'] = array();
			}else{
				$isExist = false;
				$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['size'][$sizeid]['amount'] = 0;
				foreach($_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]['size'] as $sizeid=>$val){
					if($val['amount']>0){
						$isExist = true;
						break;
					}
				}
				
				if(!$isExist){
					$_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode] = array();
					unset($_SESSION['orders']['items'][$catid]['item'][$itemid]['color'][$colorcode]);
					if(count($_SESSION['orders']['items'][$catid]['item'][$itemid]['color'])==0){
						$_SESSION['orders']['items'][$catid]['item'][$itemid] = array();
						unset($_SESSION['orders']['items'][$catid]['item'][$itemid]);
					}
				}
			}
			
			$data = $this->reqDetails();
			
			return $data;
		}
		
		if($mode=='attach'){
		/*
		*	添付ファイルの削除
		*	@posid
		*	@base
		*	@posname
		*
		*	['attach'][0]['pos']['id']
		*						['base']
		*						['posname']
		*				 ['img']['file']
		*						['name']
		*						['type']
		*
		*	['items'][category_id]['item'][id]['posid']
		*									  ['design'][base][0]['posname']
		*														 ['poskey']
		*												   		 ['ink']
		*												   		 ['categorytype']
		*												   		 ['itemtype']
		*														 ['areakey']
		*														 ['areasize']
		*														 ['printing']
		*/
		
		
			$_SESSION['orders']['attach'] = array();
			
		/*
			$pos = array(
					'id'=>$_REQUEST['posid'],
					'base'=>$_REQUEST['base'],
					'posname'=>$_REQUEST['posname']
					);
			
			foreach($_SESSION['orders']['items'] as $catid=>$v1){
				foreach($v1['item'] as $itemid=>$v2){
					if($v2['posid']!=$pos['id']) continue;
					foreach($v2['design'] as $base=>$v3){
						if($base!=$pos['base']) continue;
						for($k=0; $k<count($v3); $k++){
							if($v3[$k]['posname']==$pos['posname']){
								$_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$base][$k]['attachname'] = '';
							}
						}
					}
				}
			}
			
			for($i=0; $i<count($_SESSION['attach']); $i++){
				if( $_SESSION['orders']['attach'][$i]['pos']['id']==$pos['id'] || 
					$_SESSION['orders']['attach'][$i]['pos']['base']==$pos['base'] || 
					$_SESSION['orders']['attach'][$i]['pos']['posname']==$pos['posname']
				){
					array_splice($_SESSION['orders']['attach'],$i,1);	
					break;	
				}
			}
		*/
		
		}
	}
	
	
	/*
	*	見積ボックスに表示する明細を返す
	*
	*	['items'][category_id]['category_key']
	*		  				  ['category_name']
	*	   	  				  ['item'][id]['code']
	*				   				  	  ['name']
	*							      	  ['color'][code]['master_id']
	*													 ['name']
	*											 		 ['size'][sizeid]['sizename']
	*											  						 ['amount']
	*																	 ['cost']
	*									  ['posid']
	*									  ['design'][base][0]['posname']
	*														 ['poskey']
	*												   		 ['ink']
	*												   		 ['categorytype']
	*												   		 ['itemtype']
	*														 ['areakey']
	*														 ['areasize']
	*														 ['printing']
	*
	*	return		 {'itemprice':商品代, 'amount':合計枚数, 'printprice':プリント代合計, 'total':見積合計,
	*				  'options': {'discount':割引, 'carriage':送料, 'codfee':代引手数料, pack:袋詰代},
	*				  'category': [{category_id,category_key,category_name,item_id,item_code,item_name,posid,color_code,color_name,cost,amount},{} ...] }
	*/
	public function reqDetails(){
		//$subcat = Items::getSubCategory();
		$data = $this->reqEstimation();
		$list = array();
		if(count($_SESSION['orders']['items'])==0){
			$data['category'] = array();
			return $data;
		}
		foreach($_SESSION['orders']['items'] as $catid=>$val){
			$cat_key = $val['category_key'];
			$cat_name = $val['category_name'];
			foreach($val['item'] as $itemid=>$val2){
				foreach($val2['color'] as $colorcode=>$val3){
					$tmp = array();
					foreach($val3['size'] as $sizeid=>$val4){
						$tmp['cost'] += $val4['cost']*$val4['amount'];
						$tmp['amount'] += $val4['amount'];
					}
					/*
					if($catid==1){
						if($subcat['long-shirts'][$val2['code']]){
							$cat_key = 'long-shirts';
							$cat_name = 'ロングTシャツ';
						}else if($subcat['baby'][$val2['code']]){
							$cat_key = 'baby';
							$cat_name = 'ベビー';
						}else{
							$cat_key = 't-shirts';
							$cat_name = 'Tシャツ';
						}
					}
					*/
					$list[] = array('categoryid'=>$catid,
									'categorykey'=>$cat_key,
									'categoryname'=>$cat_name,
									'master_id'=>$val3['master_id'],
									'itemid'=>$itemid,
									'itemcode'=>$val2['code'],
									'itemname'=>$val2['name'],
									'posid'=>$val2['posid'],
									'colorcode'=>$colorcode,
									'colorname'=>$val3['name'],
									'size'=>$val3['size'],
									'cost'=>$tmp['cost'],
									'amount'=>$tmp['amount']
									);
				}
			}
		}
		$data['category'] = $list;
		
		return $data;
	}
	
	
	/*
	*	見積ボックスの明細を返す
	*
	*	return			{'itemprice':商品代, 'amount':合計枚数, 'printprice':プリント代合計, 'total':見積合計, 'design':[[インク色数, プリント位置名],[]],
	*					 'options': {'discount':割引, 'carriage':送料, 'codfee':代引手数料, pack:袋詰代, discountname:[...]}}
	*/
	public function reqEstimation(){
		$data = $this->reqItemfee();
		$print = $this->reqPrintfee();
		$_SESSION['orders']['sum']['itemprice'] = $data['itemprice'];
		$_SESSION['orders']['sum']['amount'] = $data['amount'];
		$_SESSION['orders']['sum']['printprice'] = $print['printprice'];
		$data['printprice'] = $print['printprice'];
		$data['printing'] = $print['printing'];
		$data['design'] = $print['design'];
		$data['detail'] = $print['detail'];
		$data['options'] = $this->reqOptionfee();
		$data['total'] = $data['itemprice'] + $data['printprice'] + $data['options']['optionfee'];
		
		$data['inkjet'] = $print['inkjet'];
		return $data;
	}
	
	
	/*
	*	商品代と枚数の合計を返す
	*
	*	return		{'itemprice':価格, 'amount':枚数}
	*/
	public function reqItemfee(){
		$data = array('itemprice'=>0, 'amount'=>0);
		if(count($_SESSION['orders']['items'])==0) return $data;
		foreach($_SESSION['orders']['items'] as $catid=>$val){
			foreach($val['item'] as $itemid=>$val2){
				foreach($val2['color'] as $colorcode=>$val3){
					foreach($val3['size'] as $sizeid=>$val4){
						$data['itemprice'] += $val4['cost']*$val4['amount'];
						$data['amount'] += $val4['amount'];
					}
				}
			}
		}
		
		return $data;
	}
	
	
	/*
	*	プリント代合計を返す
	*	['items'][category_id]['category_name']
	*						  ['item'][id]['posid']
	*									  ['design'][base][0]['posname']
	*														 ['poskey']
	*												   		 ['ink']
	*												   		 ['categorytype']
	*												   		 ['itemtype']
	*														 ['areakey']
	*														 ['areasize']
	*														 ['printing']
	*	@sheetsize		転写のデザインサイズ　default: 1
	*
	*	return			{'printprice':プリント代, 'design':['インク色数','プリント位置名']}
	*/	
	public function reqPrintfee($sheetsize='1'){
		$args = array();
		if($_SESSION['orders']['options']['noprint']==0 && !empty($_SESSION['orders']['items'])){
			foreach($_SESSION['orders']['items'] as $catid=>$val){
				foreach($val['item'] as $itemid=>$val2){

					$design = array();
					foreach($val2['design'] as $base=>$val3){
						for($i=0; $i<count($val3); $i++){
							if(!empty($val3[$i]['ink'])){
//								if($val3[$i]['ink']==9) $ink = '4 色以上';
//								else $ink = $val3[$i]['ink'].' 色';

								$design[] = array($val3[$i]['ink'], $val3[$i]['posname'], $val3[$i]['areasize']);
							}
						}
					}

					$option = array();
					$volume = 0;
					foreach($val2['color'] as $colorcode=>$val4){
						$idx = $val4['name']!='ホワイト'? 1: 0;
						if(! array_key_exists($idx, $option)) $option[$idx] = 0;
						foreach($val4['size'] as $sizeid=>$val5){
							$option[$idx] += $val5['amount'];
							$volume += $val5['amount'];
						}
					}

					for($i=0; $i<count($design); $i++){
						$args[] = array('itemid'=>$itemid, 'amount'=>$volume, 'ink'=>$design[$i][0], 'pos'=>$design[$i][1], 'size'=>$design[$i][2], 'option'=>$option);
					}

				}
			}
		}
		
		if(empty($args)){
			$data = array('printprice'=>0, 'design'=>$args);
		}else{
			$res = parent::printfee($args);
//			$data = array('printprice'=>$res['printfee'], 'design'=>$args, 'printing'=>$res['printing'], 'detail'=>$res['detail']);
			$data = array('printprice'=>$res['printfee'], 'design'=>$args);
			
			// 箇所毎のプリント方法を設定
			$items = $_SESSION['orders']['items'];
			foreach ($items as $catid => $v1) {
				foreach ($v1['item'] as $itemid => $v2) {
					foreach ($v2['design'] as $base => $a2) {
						for ($i=0; $i<count($a2); $i++) {
							$key = $a2[$i]['posname'].'-'.$a2[$i]['areasize'].'-'.$a2[$i]['ink'];
							$_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$base][$i]['printing'] = key($res['printing'][$key]);
						}
					}
					unset($a2);
				}
			}
		}
		return $data;
	}
	
	
	/*
	*	オプション代を返す
	*
	*	p1  商品代＋プリント代＋インク色替代
	*	p2  割引金額								対象：p1
	*	p3  値引金額
	*	p4  特急料金（２日仕上げと翌日仕上げ）		対象：p1+p2+p7+p9+p10
	*	p5  送料									対象：p1+p2+p3+p7+p9+p10+p11
	*	p6  特別送料（超速便、タイム便）
	*	p7  デザイン代
	* 	p8  代引き手数料
	*	p9  袋詰め代
	*	p10 袋代
	*	p11 追加料金
	*
	*	return			{'discount':割引, 'carriage':送料, 'codfee':代引手数料, pack:袋詰代, discountname:[...], プリントの有無
	*					 'itemprice',商品代, 'printprice',プリント代, 'amount':枚数, 'expressfee':特急料金}
	*/
	public function reqOptionfee(){
		$p1 = $_SESSION['orders']['sum']['itemprice'] + $_SESSION['orders']['sum']['printprice'];
		$opt =& $_SESSION['orders']['options'];
		$discountname = array();;
		$discount_ratio = 0;
		
		// 2013-10-17 30000円の条件廃止
		//if($p1>30000){
			$discount_ratio = $opt['student'];
			switch($opt['student']){
			case '3':	$discountname[] = '学割';
						break;
			case '5':	$discountname[] = '2クラス割';
						break;
			case '7':	$discountname[] = '3クラス割';
						break;
			}
			
			if(!empty($opt['intro'])){
				$discount_ratio += $opt['intro'];
				$discountname[] = '紹介割';
			}
		//}
		
		if(!empty($opt['blog'])){
			$discount_ratio += $opt['blog'];
			$discountname[] = 'ブログ割';
		}
		
		$options['discount'] = -1 * ceil(($p1 * $discount_ratio)/100);
		if(!empty($opt['illust'])){
			$options['discount'] -= 1000;
			$discountname[] = 'イラレ割';
		}
		
		$options['discountname'] = $discountname;
		
		$options['expressfee'] = 0;
		$options['expressError'] = '';
		$options['expressInfo'] = '';
		if(!empty($opt['deliveryday'])){
//			$d = explode('-', $opt['deliveryday']);
			$d = preg_split('/[\/-]/', $opt['deliveryday']);
			$time_stamp = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
			$workday = $this->getWorkDay($time_stamp);
			if($opt['pack']==1 && $_SESSION['orders']['sum']['amount']>9){
				$isPack = true;
			} else {
				$isPack = false;
			}

			if ($workday<1 || ($workday==1 && $isPack)) {
				$options['expressError'] = '製作日数が足りません！';
			} else if($workday==1) {
				$options['expressError'] = '<a href="/sameday/">当日仕上げの専用ページ</a>をご利用ください。';
			} else if($workday==2) {
				if ($isPack) $opt['pack'] = 0;
			} else if($isPack) {
				$workday--;
			}

			$express = 0;
			switch($workday){
				case 1:	$express = 10;
					$options['expressInfo'] = '当日仕上げ';
					break;
				case 2:	$express = 5;
					$options['expressInfo'] = '翌日仕上げ';
					break;
				case 3:	$express = 3;
					$options['expressInfo'] = '２日仕上げ';
					break;
			}
		}
		
		//$options['packfee'] = empty($opt['pack'])? 0: $_SESSION['orders']['sum']['amount']*_PACK_FEE;
		if(empty($opt['pack'])){
			$options['packfee'] = 0;
		}else if($opt['pack'] == 1){
			$options['packfee'] = $_SESSION['orders']['sum']['amount']*_PACK_FEE;
		}else{
			$options['packfee'] = $_SESSION['orders']['sum']['amount']*_NO_PACK_FEE;
		}
		$opt['packfee'] = $options['packfee'];
		$p2 = $p1 + $options['discount']+$options['packfee'];
		
		$options['expressfee'] = ceil(($p2*$express)/10);
		$_SESSION['orders']['options']['expressInfo'] = $options['expressInfo'];
		$_SESSION['orders']['options']['expressfee'] = $options['expressfee'];
		
		$options['carriage'] = ($p2<30000 && $p2>0)? 700: 0;
		$options['codfee'] = ($opt['payment']=='1')? 800: 0;
		$options['conbifee'] = ($opt['payment']=='4')? 800: 0;
		
		$options['optionfee'] = $options['discount'] + $options['carriage'] + $options['codfee'] + $options['conbifee'] + $options['packfee'] + $options['expressfee'];
		
		$options['payment'] = $opt['payment'];
		$options['deliveryday'] = $opt['deliveryday'];
		$options['publish'] = $opt['publish'];
		$options['noprint'] = $opt['noprint'];
		
		$delitime = array('', '午前中', '12:00-14:00', '14:00-16:00', '16:00-18:00', '18:00-20:00', '20:00-21:00');
		$options['deliverytime'] = $delitime[$opt['deliverytime']];
		
		foreach($options as $key=>$val){
			$_SESSION['orders']['sum'][$key] = $val;
		}
			
		return $_SESSION['orders']['sum'];
	}
	
	
	/*
	*	アイテムIDとカラーを指定してセッションの商品情報を返す
	*	['items'][category_id]['category_key']
	*		  				  ['category_name']
	*	   	  				  ['item'][id]['code']
	*				   				  	  ['name']
	*							      	  ['color'][code]['master_id']
	*													 ['name']
	*											 		 ['size'][sizeid]['sizename']
	*											  						 ['amount']
	*																	 ['cost']
	*									  ['posid']
	*									  ['design'][base][0]['posname']
	*														 ['poskey']
	*												   		 ['ink']
	*												   		 ['categorytype']
	*												   		 ['itemtype']
	*														 ['areakey']
	*														 ['areasize']
	*														 ['printing']
	*
	*	@itemid			アイテムID
	*	@colorcode		カラーコード　（指定なしは当該アイテムで指定されている全てのカラーを取得する）
	*
	*	return			{'cateogryid','categorykey','categoryname','itemie','itemcode','itemname','posid','colorcode',vol:{size_id:枚数}}
	*/
	public function reqPage($itemid, $colorcode){
		$data = array();
		if(count($_SESSION['orders']['items'])==0) return $data;
		if($colorcode==""){
			foreach($_SESSION['orders']['items'] as $catid=>$val){
				if(isset($val['item'][$itemid])){
					foreach($val['item'][$itemid]['color'] as $colorcode=>$hash){
						$tmp = array();
						$tmp['categoryid'] = $catid;
						$tmp['categorykey'] = $val['category_key'];
						$tmp['categoryname'] = $val['category_name'];
						$tmp['master_id'] = $hash['master_id'];
						$tmp['itemid'] = $itemid;
						$tmp['itemcode'] = $val['item'][$itemid]['code'];
						$tmp['itemname'] = $val['item'][$itemid]['name'];
						$tmp['posid'] = $val['item'][$itemid]['posid'];
						$tmp['colorcode'] = $colorcode;
						foreach($hash['size'] as $sizeid=>$val2){
							$tmp['vol'][$sizeid] = $val2['amount'];
						}
						
						$data[] = $tmp;
					}
					
					break;
				}
			}
		}else{
			foreach($_SESSION['orders']['items'] as $catid=>$val){
				if(isset($val['item'][$itemid]['color'][$colorcode])){
					$tmp = array();
					$tmp['categoryid'] = $catid;
					$tmp['categorykey'] = $val['category_key'];
					$tmp['categoryname'] = $val['category_name'];
					$tmp['master_id'] = $val['item'][$itemid]['color'][$colorcode]['master_id'];
					$tmp['itemid'] = $itemid;
					$tmp['itemcode'] = $val['item'][$itemid]['code'];
					$tmp['itemname'] = $val['item'][$itemid]['name'];
					$tmp['posid'] = $val['item'][$itemid]['posid'];
					$tmp['colorcode'] = $colorcode;
					foreach($val['item'][$itemid]['color'][$colorcode]['size'] as $sizeid=>$val2){
						$tmp['vol'][$sizeid] = $val2['amount'];
					}
					$data[] = $tmp;
					
					break;
				}
			}
		}
		
		return $data;
	}
	
	
	/*
	*	注文商品のプリント位置指定情報を返す
	*	['items'][category_id]['category_name']
	*						  ['item'][id]['posid']
	*									  ['design'][base][0]['posname']
	*														 ['poskey']
	*												   		 ['ink']
	*												   		 ['categorytype']
	*												   		 ['itemtype']
	*														 ['areakey']
	*														 ['areasize']
	*														 ['printing']
	*
	*	['attach'][0]['pos']['id']
	*						['base']
	*						['posname']
	*				 ['img']['file']
	*						['name']
	*						['type']
	*
	*	@item_id			アイテムID
	*	@category_id		カテゴリーID
	*
	*	return			{posid:{'img':[{'filename','base_name','posid','ppdata'},{},{}], 
	*							'design':{base:[{'posname','poskey','ink','categorytype','itemtype','areakey','areasize','printing'},{...}]},
	*							'categoryname':catebory_name,
	*							  } }
	*/
	public function reqOrderposition($item_id, $category_id){
		$files = array();
		if (!isset($_SESSION['orders']['items'][$category_id])) {
			$tmp = parent::positionFor($item_id);
			$posId = $tmp[0]['posid'];
			$files[$posId]['img'] = $tmp;
		} elseif (!isset($_SESSION['orders']['items'][$category_id]['item'][$item_id])) {
			$tmp = parent::positionFor($item_id);
			$posId = $tmp[0]['posid'];
			$files[$posId]['img'] = $tmp;
			foreach($_SESSION['orders']['items'][$category_id]['item'] as $itemId=>$val){
				if($val['posid'] != $posId) continue;
				$files[$posId]['design'] = $_SESSION['orders']['items'][$category_id]['item'][$itemId]['design'];
				$files[$posId]['categoryname'] = $_SESSION['orders']['items'][$category_id]['category_name'];
				break;
			}
		} else {
			foreach($_SESSION['orders']['items'] as $catid=>$val){
				$catname = $val['category_name'];
				foreach($val['item'] as $itemid=>$val2){
					if($itemid!=$item_id) continue;
					$posId = $val2['posid'];
					if(!isset($files[$posId])){
						$files[$posId]['img'] = parent::positionFor($itemid);
					}
					for($i=0; $i<count($files[$posId]['img']); $i++){
						$base = $files[$posId]['img'][$i]['base_name'];
						if(!isset($val2['design'][$base])){
							if(isset($design[$posId][$base])){
								$_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$base] = $design[$posId][$base];
							}else{
								$_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$base][0] = array('posname'=>'',
																												  'ink'=>0,
																												  'attachname'=>'',
																												   'poskey'=>'',
																												   'categorytype'=>'',
																												   'itemtype'=>'',
																												   'areakey'=>'',
																												   'areasize'=>'',
																												   'printing'=>''
																												 );
							}
						}else{
							$design[$posId][$base] = $val2['design'][$base];
						}
					}
					
					$files[$posId]['design'] = $_SESSION['orders']['items'][$catid]['item'][$itemid]['design'];
					$files[$posId]['categoryname'] = $catname;
				}
			}
		}
		
		return $files;
	}	
	
	/*
	*	注文確認に表示する商品明細を返す
	*
	*	['items'][category_id]['category_key']
	*		  				  ['category_name']
	*	   	  				  ['item'][id]['code']
	*				   				  	  ['name']
	*							      	  ['color'][code]['master_id']
	*													 ['name']
	*											 		 ['size'][sizeid]['sizename']
	*											  						 ['amount']
	*																	 ['cost']
	*									  ['posid']
	*									  ['makerid']
	*									  ['design'][base][0]['posname']
	*														 ['poskey']
	*												   		 ['ink']
	*												   		 ['categorytype']
	*												   		 ['itemtype']
	*														 ['areakey']
	*														 ['areasize']
	*														 ['printing']
	*	
	*	return		 { 'items':[{'itemid','name','color','sizeid','size','cost','amount','subtotal','pos':[...]},{}, ...],
	*				   'option':{'discount':割引, 'carriage':送料, 'codfee':代引手数料, pack:袋詰代, discountname:[...]},
	*				   'user':{SESSION情報} }
	*/
	public function reqConfirmation(){
		if(empty($_SESSION['orders']['items'])) return '';
		$list = array();
		$design = array();
		$k = 0;
		foreach($_SESSION['orders']['items'] as $catid=>$val){
			foreach($val['item'] as $itemid=>$val2){
				
				foreach($val2['design'] as $base=>$val5){
					for($i=0; $i<count($val5); $i++){
						if($val5[$i]['ink']==0){
							$ink = 0;
						}else if($val5[$i]['ink']==9){
							$ink = '4 色以上';
						}else{
							$ink = $val5[$i]['ink'].' 色';
						}
						$design[$k]['itemname'] = $val2['name'];
						$design[$k]['posname'] = $val5[$i]['posname'];
						$design[$k]['ink'] = $ink;
						$design[$k]['areasize'] = $val5[$i]['areasize'];
						$design[$k]['printing'] = $val5[$i]['printing'];
						$k++;
					}
				}
				
				foreach($val2['color'] as $colorcode=>$val3){
					$tmpSize= array();
					foreach($val3['size'] as $sizeid=>$val4){
						if(empty($val4['amount'])) continue;
						$val3['size'][$sizeid]['subtotal'] = $val4['cost']*$val4['amount'];
						$tmpSize[] = $val3['size'][$sizeid];
					}
					$list[] = array('categorykey'=>$val['category_key'],
									'master_id'=>$val3['master_id'],
									'id'=>$itemid,
									'makerid'=>$val2['makerid'],
									'code'=>$val2['code'],
									'name'=>$val2['name'],
									'color'=>$val3['name'],
									'colorcode'=>$colorcode,
									'sizeid'=>$sizeid,
									'size'=>$tmpSize
									);
				}
				
			}
		}
		
		//$option = $this->reqOptionfee();
		$option = $_SESSION['orders']['sum'];
		$option['pack'] = $_SESSION['orders']['options']['pack'];
		$option['attach'] = array();
		for($i=0; $i<count($_SESSION['orders']['attach']); $i++){
			$option['attach'][] = mb_convert_encoding($_SESSION['orders']['attach'][$i]['img']['name'], 'utf-8');
		}
		
		//user
		$user = $_SESSION['orders']['customer'];
		$user['comment'] = nl2br($user['comment']);
		$user['note_design'] = nl2br($user['note_design']);
		$user['note_printcolor'] = nl2br($user['note_printcolor']);
		$user['note_printmethod'] = nl2br($user['note_printmethod']);
		
		$data = array('items'=>$list, 'design'=>$design, 'option'=>$option, 'user'=>$user);
		return $data;
	}
	
	
	/*
	*	今注文確定日からお届け日を計算
	*	３営業日＋発送日（営業日）＋配達日数（土日含む）
	*
	*	@baseSec		注文確定日の秒数 default: null　(今日)
	*	@transport		配送日数　default: 1　（北海道、九州、本州離島、島根隠岐郡は配送に2日）
	*	@count_days		作業日　default: 4　（発送日を含む）
	*	@mode			null: default,  simple: 袋詰の有無を考慮しない
	*
	*	return			お届日付情報のハッシュ	{'year','month','day','weekname'}
	*/
	public function getDelidate($baseSec=null, $transport=1, $count_days=4, $mode=null){
	
		$sum = $this->reqItemfee();								// 注文商品情報　['amount':合計枚数]
		$isPack = $_SESSION['orders']['options']['pack'];
		$jd = new japaneseDate();
		$one_day = 86400;										// 一日の秒数
		
		if(empty($baseSec)){
			$time_stamp = time()+39600;							// 13:00からは翌日扱いのため11時間の秒数分を足す
			$year  = date("Y", $time_stamp);
			$month = date("m", $time_stamp);
			$day   = date("d", $time_stamp);
			$baseSec = mktime(0, 0, 0, $month, $day, $year);	// 注文確定日の00:00のtimestampを取得
		}
		$workday=0;												// 作業に要する日数をカウント
		if(is_null($mode)){
			if($isPack=="1" && $sum['amount']>=10){				// 袋詰めありで且つ10枚以上のときは作業日数がプラス1日
				$count_days++;
			}
		}
		$_from_holiday = strtotime(_FROM_HOLIDAY);				// お休み開始日
		$_to_holiday	= strtotime(_TO_HOLIDAY);				// お休み最終日
		while($workday<$count_days){
			$fin = $jd->makeDateArray($baseSec);
			if( (($fin['Weekday']>0 && $fin['Weekday']<6) && $fin['Holiday']==0) && ($baseSec<$_from_holiday || $_to_holiday<$baseSec) ){
				$workday++;
			}
			$baseSec += $one_day;
		}
		
		// 配送日数は曜日に関係しないため、2日かかる地域の場合に1日分を足す
		if($transport==2){
			$baseSec += $one_day;
		}
		$fin = $jd->makeDateArray($baseSec);
		
		$weekday = $jd->viewWeekday($fin['Weekday']);
		$fin['weekname'] = $weekday;
		
		return $fin;
	}
	
	
	/*
	*	作業に要する営業日数をカウントして返す
	*
	*	@baseSec	起算日（UNIXタイムスタンプの秒数）
	*	@one_day	一日の秒数（86400）
	*	@cnt		営業日として数える日数（通常は当日含めて３営業日）
	*
	*	return		休みではない日付を返す（japaneseDataオブジェクト）
	*/
	function getDeliveryDay($baseSec, $one_day, $cnt){
		global $_from_holiday, $_to_holiday;
		$jd = new japaneseDate();
		$workday=0;
		while($workday<=$cnt){
			
			$fin = $jd->makeDateArray($baseSec);
			if( (($fin['Weekday']>0 && $fin['Weekday']<6) && $fin['Holiday']==0) && ($baseSec<$_from_holiday || $_to_holiday<$baseSec) ){
				$workday++;
			}
			$baseSec += $one_day;
		}
		
		return $fin;
	}
	
	
	/*
	*	今日注文確定とし希望納期までの営業日数を返す
	*
	*	@targetSec	納期（UNIXタイムスタンプの秒数）
	*
	*	return		営業日数を返す（japaneseDataオブジェクト）
	*/
	function getWorkDay($targetSec){
		$_from_holiday = strtotime(_FROM_HOLIDAY);				// お休み開始日
		$_to_holiday	= strtotime(_TO_HOLIDAY);				// お休み最終日
		$jd = new japaneseDate();
		$workday=0;
		$one_day=86400;
		
		// 作業開始日の00:00のtimestampを取得
		$time_stamp = time()+39600;				// 13:00からは翌日扱いのため11時間の秒数分を足す
		$year  = date("Y", $time_stamp);
		$month = date("m", $time_stamp);
		$day   = date("d", $time_stamp);
		$baseSec = mktime(0, 0, 0, $month, $day, $year);
		
		while($baseSec<$targetSec){
			$fin = $jd->makeDateArray($baseSec);
			if( (($fin['Weekday']>0 && $fin['Weekday']<6) && $fin['Holiday']==0) && ($baseSec<$_from_holiday || $_to_holiday<$baseSec) ){
				$workday++;
			}
			$baseSec += $one_day;
		}
		
		return $workday;
	}
	
	
	/*
	*	カテゴリー情報を取得
	*
	*	@itemid			アイテムID
	*
	*	return			[アイテム情報, カテゴリ一覧]
	*/
	public function getCategoryData($itemid){
		$itemattr = parent::itemAttr($itemid);
		$categories = parent::categoryList();
		
		return array($itemattr, $categories);
	}
}

$regist = $_SESSION['orders'];

if(isset($_REQUEST['act'])){
	$orders = new Orders();
	$isJSON = true;
	switch($_REQUEST['act']){
	case 'update':
		$dat = $orders->update($_REQUEST['mode']);
		
		break;
		
	case 'remove':
		$dat = $orders->remove($_REQUEST['mode']);
		
		break;
		
	case 'page':
		$dat = $orders->reqPage($_REQUEST['itemid'], $_REQUEST['colorcode']);
		
		break;
		
	case 'details':
		$dat = $orders->reqDetails();
		
		break;
		
	case 'confirm':
		$dat = $orders->reqConfirmation();
		
		break;
		
	case 'register':
		$dat = $_SESSION;
		
		break;
		
	case 'orderposition':
	/*
	*	response		{posid:{'img':[{'filename','base_name','posid','ppdata'},{},{}], 
	*							'design':{base:[{'posname','poskey','ink','categorytype','itemtype','areakey','areasize','printing'},{...}]},
	*							'categoryname':catebory_name,
	*							  } }
	*
	*	return			絵型のタグ | プリントなしのチェック（1:なし）
	*/
		$files = $orders->reqOrderposition($_REQUEST['itemid'], $_REQUEST['catid']);
		foreach($files as $posid=>$v){
			$ppData = $v['img'][0]['ppdata'];
			for($i=0; $i<count($v['img']); $i++){
				$base_name = $v['img'][$i]['base_name'];
				$posname_key = basename($v['img'][$i]['filename'], '.txt');
				$imgfile = file_get_contents($v['img'][$i]['filename']);
				$f = preg_replace('/.\/img/', _IMG_PSS, $imgfile);
				$res .= '<div class="ppid_'.$posid.'">';
				
				$tmp = explode('/>', $f);
				$tmp = explode(' ', $tmp[1]);
				$first_posname = preg_replace('/(alt=|")/','', $tmp[1]);

				$res .= '<div class="posimg">'.$f.'</div>';
				$res .= '<div class="inkbox">';
				$res .= '<table><caption>'.$base_name.'</caption>';
				$res .= '<thead><tr><th>プリント位置</th><th>デザインの大きさ</th><th>デザインの色数</th></tr></thead>';
				$res .= '<tfoot><tr><td>'.$ppData['category'].'</td><td>'.$ppData['item'].'</td><td>'.$posname_key.'</td></tr></tfoot>';
				$res .= '<tbody>';
				
				
				if(!empty($v['design'][$base_name])){
					$cnt = count($v['design'][$base_name]);
					for($j=0; $j<$cnt; $j++){
						$posname = $v['design'][$base_name][$j]['posname'];
						if(empty($posname)){
							$posname = $first_posname;
							foreach($_SESSION['orders']['items'] as $catid=>$val){
								foreach($val['item'] as $itemid=>$val2){
									if($posid==$val2['posid']){
										$_SESSION['orders']['items'][$catid]['item'][$itemid]['design'][$base_name][$j]['posname'] = $posname;
									}
								}
							}
						}else{
							//$attachname = $v['design'][$base_name][$j]['attachname'];
						}
						$ink = $v['design'][$base_name][$j]['ink'];
						$size = $v['design'][$base_name][$j]['areasize'];
						$res .= '<tr>';
						$res .= '<th>'.$posname.'</th>';
						$res .= '<td>';
						if ($posid!=46) {
							$res .= '<select class="areasize">';
							$select = '<option value="0">大</option><option value="1">中</option><option value="2">小</option>';
							$res .= str_replace('value="'.$size.'"', 'value="'.$size.'" selected="selected"', $select);
							$res .= '</select>';
						}
						$res .= '</td>';
						$res .= '<td>';
						if($posid!=46){
							$res .= '<select class="ink">';
							$select = '<option value="0">選択してください</option>';
							$select .= '<option value="1">1色</option><option value="2">2色</option><option value="3">3色</option>';
							$select .= '<option value="9">4色以上</option>';
							$res .= str_replace('value="'.$ink.'"', 'value="'.$ink.'" selected="selected"', $select);
							$res .= '</select>';
						}else{
							$res .= '<p class="note"><span>※</span>プリントなしの商品です。</p>';
						}
						$res .= '</td>';
						/*
						$res .= '<td>';
						$res .= '<form name="uploaderform" action="/php_libs/orders.php" target="upload_iframe" method="post" enctype="multipart/form-data">';
						$res .= '<input type="hidden" name="act" value="update" />';
						$res .= '<input type="hidden" name="mode" value="attach" />';
						$res .= '<input type="hidden" name="posid" value="'.$posid.'" />';
						$res .= '<input type="hidden" name="base" value="'.$base_name.'" />';
						$res .= '<input type="hidden" name="posname" value="'.$posname.'" />';
						$res .= '<input type="hidden" name="attachname[]" value="'.$attachname.'" />';
						$res .= '<input type="file" name="attach[]" class="attach" onchange="this.form.submit()" /><img alt="取消" src="/common/img/delete.png" class="del_attach" />';
						$res .= '</form>';
						$res .= '</td>';
						*/
						
						$res .= '</tr>';
					}
				}else{
					$res .= '<tr>';
					$res .= '<th>'.$first_posname.'</th>';
					$res .= '<td>';
					if ($posid!=46) {
						$res .= '<select class="areasize">';
						$res .= '<option value="0" selected="selected">大</option><option value="1">中</option><option value="2">小</option>';
						$res .= '</select>';
					}
					$res .= '</td>';
					$res .= '<td>';
					if($posid!=46){
						$res .= '<select class="ink">';
						$res .= '<option value="0" selected="selected">選択してください</option>';
						$res .= '<option value="1">1色</option><option value="2">2色</option><option value="3">3色</option>';
						$res .= '<option value="9">4色以上</option>';
						$res .= '</select>';
					}else{
						$res .= '<p class="note"><span>※</span>プリントなしの商品です。</p>';
					}
					$res .= '</td>';
					
					/*
					$res .= '<td>';
					$res .= '<form name="uploaderform" action="/php_libs/orders.php" target="upload_iframe" method="post" enctype="multipart/form-data">';
					$res .= '<input type="hidden" name="act" value="update" />';
					$res .= '<input type="hidden" name="mode" value="attach" />';
					$res .= '<input type="hidden" name="posid" value="'.$posid.'" />';
					$res .= '<input type="hidden" name="base" value="'.$base_name.'" />';
					$res .= '<input type="hidden" name="posname" value="" />';
					$res .= '<input type="hidden" name="attachname[]" value="" />';
					$res .= '<input type="file" name="attach[]" class="attach" onchange="this.form.submit()" /><img alt="取消" src="/common/img/delete.png" class="del_attach" />';
					$res .= '</form>';
					$res .= '</td>';
					*/
					
					$res .= '</tr>';
					
				}
				$res .= '</tbody></table>';
				$res .= '</div>';
				$res .= '</div>';
			}
		}
		
		//$res .= '|'.$_SESSION['orders']['items'][$_REQUEST['catid']]['item'][$_REQUEST['itemid']]['noprint'];
		$res .= '|'.$_SESSION['orders']['options']['noprint'];
		$res .= '|'.$_SESSION['orders']['customer']['note_printmethod'];
		$res = mb_convert_encoding($res, 'euc-jp', 'utf-8');
		$isJSON = false;
		break;

	case 'deliverydays':
	/* 
	*	お届日を4パターン一括で計算する
	*	@base		注文確定日の秒数
	*	@transport	配送日数　default:1
	*	@mode		null以外の場合、袋詰の有無による作業日数の加算をおこなわない
	*
	*	return		[通常納期, 2日仕上げ, 翌日仕上げ, 当日仕上げ, 注文確定日(通常締め), 注文確定日(当日締め)]
	*/
			$jd = new japaneseDate();
			$one_day = 86400;
			$_from_holiday = strtotime(_FROM_HOLIDAY);				// お休み開始日
			$_to_holiday	= strtotime(_TO_HOLIDAY);				// お休み最終
			
			// 現在の日付
			$time_stamp = time();
			$year  = date("Y", $time_stamp);
			$month = date("m", $time_stamp);
			$day   = date("d", $time_stamp);
			
			// 注文確定予定日
			$post_year  = date("Y", $_REQUEST['base']);
			$post_month = date("m", $_REQUEST['base']);
			$post_day   = date("d", $_REQUEST['base']);
			
			// 当日の場合に計算開始日の00:00のtimestampを取得
			if($year==$post_year && $month==$post_month && $day==$post_day){
				$time_stamp = time()+46800;	// 当日仕上げの〆は11:00のため13時間の秒数分を足す
				$year  = date("Y", $time_stamp);
				$month = date("m", $time_stamp);
				$day   = date("d", $time_stamp);
				$time_stamp = mktime(0, 0, 0, $month, $day, $year);
				// 休日の場合に翌営業日にする
				$fin = $jd->makeDateArray($time_stamp);
				while( (($fin['Weekday']==0 || $fin['Weekday']==6) || $fin['Holiday']!=0) || ($time_stamp>=$_from_holiday && $_to_holiday>=$time_stamp) ){
					$time_stamp += $one_day;
					$fin = $jd->makeDateArray($time_stamp);
				}
				$baseSec[] = $time_stamp;
				
				$time_stamp = time()+39600;	// 通常納期、2日仕上げ、翌日仕上げは〆時間（13：00）からは翌日扱いのため11時間の秒数分を足す
				$year  = date("Y", $time_stamp);
				$month = date("m", $time_stamp);
				$day   = date("d", $time_stamp);
				$time_stamp   = mktime(0, 0, 0, $month, $day, $year);
				// 休日の場合に翌営業日にする
				$fin = $jd->makeDateArray($time_stamp);
				while( (($fin['Weekday']==0 || $fin['Weekday']==6) || $fin['Holiday']!=0) || ($time_stamp>=$_from_holiday && $_to_holiday>=$time_stamp) ){
					$time_stamp += $one_day;
					$fin = $jd->makeDateArray($time_stamp);
				}
				$baseSec[] = $time_stamp;
				$baseSec[] = $time_stamp;
				$baseSec[] = $time_stamp;
			}else{
				// 休日の場合に翌営業日にする
				$time_stamp = $_REQUEST['base'];
				$fin = $jd->makeDateArray($time_stamp);
				while( (($fin['Weekday']==0 || $fin['Weekday']==6) || $fin['Holiday']!=0) || ($time_stamp>=$_from_holiday && $_to_holiday>=$time_stamp) ){
					$time_stamp += $one_day;
					$fin = $jd->makeDateArray($time_stamp);
				}
				$baseSec = array($time_stamp, $time_stamp, $time_stamp, $time_stamp);
			}
			
			$transport = 1;
			if(isset($_REQUEST['transport'])){
				if($_REQUEST['transport']==2) $transport = 2;
			}
			
			$mode = null;
			if(isset($_REQUEST['mode'])) $mode = $_REQUEST['mode'];	// null以外の場合、袋詰の有無による作業日数の加算をおこなわない
			
			// 納期計算
			for($cnt=4,$i=3; $cnt>0; $cnt--,$i--){
				$fin = $orders->getDelidate($baseSec[$i], $transport, $cnt, $mode);
				$dat[] = $fin['Year'].'/'.$fin['Month'].'/'.$fin['Day'];
			}
			
			// 注文確定日を返す
			if(count($dat)>0){
				// 通常締め時間
				$year  = date("Y", $baseSec[3]);
				$month = date("m", $baseSec[3]);
				$day   = date("d", $baseSec[3]);
				$dat[] = $year.'/'.$month.'/'.$day;
				// 当日締め時間
				$year  = date("Y", $baseSec[0]);
				$month = date("m", $baseSec[0]);
				$day   = date("d", $baseSec[0]);
				$dat[] = $year.'/'.$month.'/'.$day;
			}
			
		break;
		
	case 'firmorderdays':
	/* 
	*	お届日から注文確定日を4パターン一括で計算する
	*	@base		お届け日の秒数
	*	@transport	配送日数　default:1
	*
	*	return		[通常納期, 2日仕上げ, 翌日仕上げ, 当日仕上げ]
	*				※製作日数が足りない場合は除外
	*/
		$jd = new japaneseDate();
		$one_day = -86400;
		$_from_holiday = strtotime(_FROM_HOLIDAY);				// お休み開始日
		$_to_holiday	= strtotime(_TO_HOLIDAY);				// お休み最終日
		$dat = array();
		
		// 現在のタイムスタンプを取得
		$time_stamp = time()+(60*60*13);	// 当日仕上げは11:00で〆て翌日扱い
		$year  = date("Y", $time_stamp);
		$month = date("m", $time_stamp);
		$day   = date("d", $time_stamp);
		$today[] = mktime(0, 0, 0, $month, $day, $year);
		
		$time_stamp = time()+(60*60*11);	// 午後(13:00)の場合は翌日扱い
		$year  = date("Y", $time_stamp);
		$month = date("m", $time_stamp);
		$day   = date("d", $time_stamp);
		$res = mktime(0, 0, 0, $month, $day, $year);
		$today[] = $res;
		$today[] = $res;
		$today[] = $res;
		
		// お届け日から発送日（平日）を逆算
		$transport = 1;
		if(isset($_REQUEST['transport'])){
			if($_REQUEST['transport']==2) $transport = 2;
		}
		for($cnt=3; $cnt>=0; $cnt--){
			$baseSec = $_POST['base'] + ($one_day*$transport);
			$fin = $jd->makeDateArray($baseSec);
			while( (($fin['Weekday']==0 || $fin['Weekday']==6) || $fin['Holiday']!=0) || ($baseSec>=$_from_holiday && $_to_holiday>=$baseSec) ){
				$baseSec += $one_day;
				$fin = $jd->makeDateArray($baseSec);
			}
			
			// 発送日から注文確定日を逆算
			$fin = $orders->getDeliveryDay($baseSec, $one_day, $cnt);
			$baseSec = mktime(0, 0, 0, $fin['Month'], $fin['Day'], $fin['Year']);
			
			// 注文確定日が現在よりも前になる場合は除外する
			if($baseSec<$today[$cnt]){
				//$dat[] = '';
				continue;
			}
			$dat[] = $fin['Year'].'/'.$fin['Month'].'/'.$fin['Day'];
		}
		
		break;
		
	case 'checkemail':
	/* 
	*	メールアドレスの登録状況の確認
	*	@param {aray} args		[メールアドレス]
	*
	*	return		登録済み：[顧客情報]、　未登録：[]
	*/
		$dat = $orders->checkExistEmail($_REQUEST['args']);
		
		break;
	}
	
	
	if($isJSON){
		$isJSON = false;
		$json = new Services_JSON();
		$res = $json->encode($dat);
		header("Content-Type: text/javascript; charset=utf-8");
		
		/*
		* $res = $json->encode($dat, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		*/
		
		/*
		*	JSONP
		*	$res = $_REQUEST['callback'].'('.$json->encode($dat).')';
		*/
	}
	
	echo $res;
}
