<?php
/*------------------------------------------------------------

	File_name    : maler.php
	Description  : takahama428 web site send mail class
	Charset      : euc-jp
	Created      : 2011.03.26
	Log			 : 2012.08.12 アンケート送信
				   2013.02.01 見積りの問い合わせ
				   2013.08.09 お問合せ項目のチェックを追加
				   2014.01.23 ミニTキャンペーンのお申し込みを追加
				   2014.02.04 都道府県を別にする
				   2014.08.05 出張打ち合わせフォームに対応
				   2015.02.18 当日特急プランのお問合せ
				   2015.04.01 オリジナルタオルお問い合わせ
				   2015.05.13 デザインコンシェルジュ
 				   2015.06.19 大口注文お問い合わせ
				   2016.01.28 お悩み、解決フォーム
				   2016.11.08 アンケート項目変更
				   2017.07.05 サーバー移行に伴い文字コード設定を更新
				   
-------------------------------------------------------------- */

require_once 'conndb.php';

class Mailer{
/*
 *	$info
 *		['title']			info, request, estimate, express, orange,(test for DEBUG), ...
 *		['customername']
 *		['company']
 *		['email']
 *		['tel']
 *		['subject']
 *		['message']
 *		['zipcode']
 *		['addr0']
 *		['addr1']
 *		['addr2']
 *		['sample']
 *		['deliveryday']
 *		['category']
 *		['amount']
 *		['amount1']
 *		['amount2']
 *		['printinfo']
 *		['deriinfo']
 *		['iteminfo']
 *
 *
 *	$_FILES
 *		['attachfile'][]	複数対応のため配列
 *		
 */
	private $info = array();
	
	public function __construct($info=null){
		$this->info = $info;
	}
	
	public function send(){
		try{
			$titles = array(
				'info'=>'お問い合わせ',
				'request'=>'資料請求', 
				'estimate'=>'お見積問合せ',
				'express'=>'お急ぎの製作お問合せ',
				'test'=>'テスト',
				'minit'=>'ユニフォームミニTお申し込み',
				'illusttemplate'=>'イラレ入稿テンプレート',
				'visit'=>'出張打ち合わせ',
				'expresstoday'=>'当日特急プラン',
				'towel'=>'オリジナルタオルお問い合わせ',
				'designconsierge'=>'デザインコンシェルジュ',
				'bigorder'=>'大口注文お問い合わせ',
				'quickcontact'=>'お悩み解決フォーム',
				'orange'=>'俺んじ君ワークショップお申し込み',
			);
			// 添付ファイルがある場合の処理
			$attach = array();
			$result = true;
			$t=0;
			$attach_count = count($_FILES['attachfile']['tmp_name']);
			for($i=0; $i<$attach_count; $i++) {
				if( is_uploaded_file($_FILES['attachfile']['tmp_name'][$i]) ){
					$result = false;
					if ($_FILES['attachfile']['error'][$i] == UPLOAD_ERR_OK){
				  	
				    	$tmp_path = $_FILES['attachfile']['tmp_name'][$i];
				    	$filename = $_FILES['attachfile']['name'][$i];
						$filetype = $_FILES['attachfile']['type'][$i];
						$filesize = $_FILES['attachfile']['size'][$i];
						$files_len += $filesize;
						
						if($files_len > _MAXIMUM_SIZE){
							$result_msg = '添付ファイルサイズは20MBまでにして下さい。';
						}else{
							$uploadfile = file_get_contents($tmp_path);
							$img_encode64 = chunk_split(base64_encode($uploadfile));
							
					      	$result = true;
						}
						    
				    }else{
				     	$result_msg = '添付ファイルのアップロード中にエラーです。添付ファイルの指定をやり直してください。';
				    }
				    
				    if($result){
				    	$attach[$t]['file'] = $img_encode64;
						$attach[$t]['name'] = $filename;
						$attach[$t]['type'] = $filetype;
						$attach[$t]['size'] = $filesize;
						$t++;
				    }else{
				    	break;
				    }
				}
			}
			
			if($result){
				$mail_info = "";
				if($this->info['title']=='minit' || $this->info['title']=='towel' || $this->info['title']=='orange'){
					$mail_info .= "弊社で内容を確認後、お見積りのメールをお送りいたします。\n";
					$mail_info .= "ご確認のほどよろしくお願いいたします。\n\n\n";
				}
				$mail_info .= "【　".$titles[$this->info['title']]."　】\n";
				$mail_info .= "■お名前：　".$this->info['customername']." 様\n";
				if(isset($this->info['company'])){
					$mail_info .= "■会社名：　".$this->info['company']." 様\n";
				}
				if(isset($this->info['ruby'])){
					$mail_info .= "■フリガナ：　".$this->info['ruby']." 様\n";
				}else if(isset($this->info['customerruby'])){
					$mail_info .= "■フリガナ：　".$this->info['customerruby']." 様\n";
				}
				$mail_info .= "■E-Mail：　".$this->info['email']."\n";
				$mail_info .= "■TEL：　".$this->info['tel']."\n\n";
				
				if($this->info['title']=='request' || $this->info['title']=='express' || $this->info['title']=='expresstoday' || $this->info['title']=='test' || $this->info['title']=='towel' || $this->info['title']=='orange'){
					$mail_info .= "■お届け先ご住所：\n";
					$mail_info .= "〒".$this->info['zipcode']."\n";
					$mail_info .= $this->info['addr0']."\n";
					$mail_info .= $this->info['addr1']."\n";
					$mail_info .= $this->info['addr2']."\n";
					
					if($this->info['title']=='request'){
						if(!empty($this->info['requestplan'])){
							$mail_info .= "\n■請求プラン：　\n".implode("\n", $this->info['requestplan']);
							$mail_info .= "\n";
						}
						
						// 2014-08-20 旧資料請求ページ
						if(empty($this->info['sample'])){
							$mail_info .= "\n■商品サンプル：　なし\n";
						}else{
							$mail_info .= "\n■商品サンプル：　希望します\n";
						}
					}
					$mail_info .= "■----------------------------------------\n\n";
				}
				
				if($this->info['title']=='bigorder' || $this->info['title']=='quickcontact' || $this->info['title']=='expresstoday'){
					if(empty($this->info['deliveryday'])) $this->info['deliveryday'] = 'なし';
					$mail_info .= "\n■ご希望納期：　 ".$this->info['deliveryday']."\n";
				}
				if($this->info['title']=='bigorder' || $this->info['title']=='quickcontact'){
					$categories = array(""=>"指定なし",
										"t-shirts"=>"Ｔシャツ",
                                        "polo-shirts"=>"ポロシャツ",
                                        "sweat"=>"スウェット・パーカー",
                                        "long-shirts"=>"ロングＴシャツ",
                                        "sportswear"=>"スポーツウェア",
                                        "ladys"=>"レディース",
                                        "outer"=>"アウター",
                                        "overall"=>"つなぎ",
                                        "baby"=>"ベビー",
                                        "cap"=>"キャップ・バンダナ",
                                        "tote-bag"=>"バッグ",
                                        "apron"=>"エプロン",
                                        "towel"=>"タオル",
                                        "workwear"=>"ワークウェア",
                                        "goods"=>"プレゼント・グッズ");
					$mail_info .= "■商品カテゴリー：　 ".$categories[$this->info['category']]."\n";
					$mail_info .= "■枚数：　 ".$this->info['amount']." 枚\n";
					
					$mail_info .= "■お届け先：　 ";
					if($this->info['title']=='quickcontact' || $this->info['title']=='info'){
						if($this->info['place']==1){
							$mail_info .= "翌日配達の地域\n";
						}else{
							$mail_info .= "翌々日配達の地域\n";
						}
				
					}else{
						if(empty($this->info['pref'])){
							$mail_info .= "指定なし\n";
						}else{
							$mail_info .= $this->info['pref']."\n";
						}
					}
				}
	

				if($this->info['title']=='express'){
					if(empty($this->info['deliveryday'])) $this->info['deliveryday'] = 'なし';
					$mail_info .= "\n■ご希望納期：　 ".$this->info['deliveryday']."\n";
					
					$categories = array(""=>"指定なし",
										"t-shirts"=>"Ｔシャツ",
                                        "polo-shirts"=>"ポロシャツ",
                                        "sweat"=>"スウェット・パーカー",
                                        "long-shirts"=>"ロングＴシャツ",
                                        "sportswear"=>"スポーツウェア",
                                        "ladys"=>"レディース",
                                        "outer"=>"アウター",
                                        "overall"=>"つなぎ",
                                        "baby"=>"ベビー",
                                        "cap"=>"キャップ・バンダナ",
                                        "tote-bag"=>"バッグ",
                                        "apron"=>"エプロン",
                                        "towel"=>"タオル",
                                        "workwear"=>"ワークウェア",
                                        "goods"=>"プレゼント・グッズ");
					$mail_info .= "■商品カテゴリー：　 ".$categories[$this->info['category']]."\n";
					$mail_info .= "■枚数：　 ".$this->info['amount']." 枚\n";
				

				}
				if($this->info['title']=='express' || $this->info['title']=='quickcontact'){
					$mail_info .= "■プリント情報：\n";
					$mail_info .= $this->info['printinfo']."\n\n";
				}else if($this->info['title']=='minit'){
					$mail_info .= "■弊社ご利用について：\n";
					$mail_info .= $this->info['repeater']."\n\n";
				}

				if($this->info['title']=='quickcontact'){
					$mail_info .= "■アイテム要望：\n";
					$mail_info .= $this->info['iteminfo']."\n\n";
				}else if($this->info['title']=='minit'){
					$mail_info .= "■弊社ご利用について：\n";
					$mail_info .= $this->info['repeater']."\n\n";
				}

				if($this->info['title']=='quickcontact' || $this->info['title']=='info'){
					$mail_info .= "\n■ご希望納期：　 ".$this->info['deliveryday']."\n";
					$mail_info .= "■ご要望：\n";
					$mail_info .= $this->info['deriinfo']."\n\n";
				}else if($this->info['title']=='minit'){
					$mail_info .= "■弊社ご利用について：\n";
					$mail_info .= $this->info['repeater']."\n\n";
				}
				if($this->info['title']=='orange'){
					$mail_info .= "■参加人数：大人　　".$this->info['amount1']."人\n";
					$mail_info .= "■参加人数：小人　 ".$this->info['amount2']."人\n";
					$mail_info .= "■----------------------------------------\n\n";
				}

				
				if($this->info['title']=='bigorder'){
					$mail_info .= "■ご利用用途：　　".implode(', ', $this->info['youto'])."\n";
					$mail_info .= "■制作枚数：　 ".$this->info['vol']."\n";
					$mail_info .= "■----------------------------------------\n\n";
				}
				
				if(!empty($this->info['subtitle'])){
					$subtext = "　[ ".implode(', ', $this->info['subtitle'])." ]";
				}
				
				if(isset($this->info['subject'])){
					$mail_info .= "\n■件　名：　 ".$this->info['subject'].$subtext."\n\n";
				}
				
				if($this->info['title']=='estimate' || $this->info['title']=='test'){
					for($i=0; $i<count($this->info['itemname']); $i++){
						$mail_info .= "■商品：　 ".$this->info['itemname'][$i]."\n";
						$mail_info .= "■サイズ：　 ".$this->info['itemsize'][$i]."\n";
						$mail_info .= "■枚数：　 ".$this->info['amount'][$i]." 枚\n";
						$mail_info .= "■プリント位置と色数：\n";
						$mail_info .= str_replace("|", "\n", $this->info['printinfo'][$i])."\n";
						$mail_info .= "--------------------\n\n";
					}
					$mail_info .= "■商品代：　 ".$this->info['itemsum']."\n";
					$mail_info .= "■プリント代：　 ".$this->info['printfee']."\n";
					$mail_info .= "■合計：　 ".$this->info['total']."\n";
					$mail_info .= "■----------------------------------------\n\n";
				}
				
				if($this->info['title']=='visit'){
					$mail_info .= "■----------------------------------------\n\n";
					$mail_info .= "■ご利用用途：　　".$this->info['purpose']."\n";
					$mail_info .= "■お届け先：　　".$this->info['delivery']."\n";
					$mail_info .= "■ご希望アイテム：　　".$this->info['category']."\n";
					$mail_info .= "■デザイン：　　".$this->info['design']."\n";
					$mail_info .= "■プリント箇所・色数：\n";
					$mail_info .= $this->info['inks']."\n\n";
					$mail_info .= "■----------------------------------------\n\n";
					$mail_info .= "■ご検討枚数：　　".$this->info['amount']." 枚\n";
					$mail_info .= "■ご予算（1枚あたり）：　　".$this->info['budget']." 円\n";
					$mail_info .= "■打合せ場所（駅名）：　　".$this->info['place']."\n";
					$mail_info .= "■希望時間：　　".$this->info['meetingtime']."\n";
				}
				
				if($this->info['title']=='expresstoday'){
					$amount = 0;
					if(!empty($this->info['S_001'])){
						$amount += $this->info['S_001'];
						$mail_info .= "■商品カラー：　 ホワイト\n";
						$mail_info .= "■サイズ：　 S\n";
						$mail_info .= "■枚数：　 ".$this->info['S_001']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['M_001'])){
						$amount += $this->info['M_001'];
						$mail_info .= "■商品カラー：　 ホワイト\n";
						$mail_info .= "■サイズ：　 M\n";
						$mail_info .= "■枚数：　 ".$this->info['M_001']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['L_001'])){
						$amount += $this->info['L_001'];
						$mail_info .= "■商品カラー：　 ホワイト\n";
						$mail_info .= "■サイズ：　 L\n";
						$mail_info .= "■枚数：　 ".$this->info['L_001']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['XL_001'])){
						$amount += $this->info['XL_001'];
						$mail_info .= "■商品カラー：　 ホワイト\n";
						$mail_info .= "■サイズ：　 XL\n";
						$mail_info .= "■枚数：　 ".$this->info['XL_001']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					
					if(!empty($this->info['S_005'])){
						$amount += $this->info['S_005'];
						$mail_info .= "■商品カラー：　 ブラック\n";
						$mail_info .= "■サイズ：　 S\n";
						$mail_info .= "■枚数：　 ".$this->info['S_005']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['M_005'])){
						$amount += $this->info['M_005'];
						$mail_info .= "■商品カラー：　 ブラック\n";
						$mail_info .= "■サイズ：　 M\n";
						$mail_info .= "■枚数：　 ".$this->info['M_005']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['L_005'])){
						$amount += $this->info['L_005'];
						$mail_info .= "■商品カラー：　 ブラック\n";
						$mail_info .= "■サイズ：　 L\n";
						$mail_info .= "■枚数：　 ".$this->info['L_005']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['XL_005'])){
						$amount += $this->info['XL_005'];
						$mail_info .= "■商品カラー：　 ブラック\n";
						$mail_info .= "■サイズ：　 XL\n";
						$mail_info .= "■枚数：　 ".$this->info['XL_005']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					
					if(!empty($this->info['Free_001'])){
						$amount += $this->info['Free_001'];
						$mail_info .= "■商品カラー：　 ホワイト\n";
						$mail_info .= "■サイズ：　 Free\n";
						$mail_info .= "■枚数：　 ".$this->info['Free_001']." 枚\n";
						$mail_info .= "--------------------\n\n";
					}
					
					if(!empty($this->info['noprint'])){
						$mail_info .= "■プリントなしで購入する：　  はい\n";
						$mail_info .= "--------------------\n\n";
					}
					$mail_info .= "■合計枚数：　 ".$amount." 枚\n";
					$mail_info .= "■----------------------------------------\n\n";
					
					$mail_info .= "■プリント位置と色数 \n";
					for($i=0; $i<count($this->info['printpos']); $i++){
						$pinfo = explode("_", $this->info['printpos'][$i]);
						$mail_info .= $pinfo[0].":　".$pinfo[1]."色\n";
					}
					$mail_info .= "■----------------------------------------\n\n";
				}
				
				if($this->info['title']=='towel'){
					$mail_info .= "■タオルの種類：　 ".$this->info['kinds']."\n";
					$mail_info .= "■版のサイズ：　 ".$this->info['size']."\n";
					$mail_info .= "■インク色数：　 ".$this->info['color']." 枚\n";
					$mail_info .= "■枚数：　 ".$this->info['amount']." 枚\n";
					$mail_info .= "■----------------------------------------\n\n";
				}
				
				if(isset($this->info['message'])){
					if($this->info['title']=='illusttemplate'){
						$mail_info .= "■デザインのご要望：\n";
					}else if($this->info['title']=='visit'){
						$mail_info .= "■ご相談内容：\n";
					}else{
						$mail_info .= "■お問合せ内容（メッセージ）：\n";
					}
					$mail_info .= $this->info['message']."\n\n";
					$mail_info .= "■----------------------------------------\n\n";
				}
				
				if($this->info['title']=='designconsierge'){
					$mail_info .= "\n■使用目的：\n".$this->info['purpose']."\n\n";
					$mail_info .= "■----------------------------------------\n\n";
					
					$mail_info .= "\n■デザインイメージ：\n".$this->info['design']."\n\n";
					$mail_info .= "■----------------------------------------\n\n";
					
					$mail_info .= "■商品カテゴリー：　 ".$this->info['category']."\n";
					
					$mail_info .= "■枚数：　 ".$this->info['amount']." 枚\n";
					
					$mail_info .= "\n■プリント箇所：\n".$this->info['print']."\n\n";
					$mail_info .= "■----------------------------------------\n\n";
				}
				
				$mail_info .= "■添付ファイル：\n";
				$attach_count = count($attach);
				for($i=0; $i<$attach_count; $i++){
					$mail_info .= "　".$attach[$i]['name']."\n";
				}
				if($attach_count==0){
					$mail_info .= "　なし\n";
				}
				$mail_info .= "\n■----------------------------------------\n\n";
				
				// 資料請求で住所が無い場合は中止
				if($this->info['title']=='request' && empty($this->info['addr0'])){
					return false;
				}
				
				if($result && $this->info['title']=='request'){
					if ($this->info['subject']!='資料請求') {
						throw new Exception();
					}
					$addr = htmlspecialchars(mb_convert_encoding($this->info['addr0'], "utf-8"), ENT_QUOTES, "utf-8");
					$addr .= htmlspecialchars(mb_convert_encoding($this->info['addr1'], "utf-8"), ENT_QUOTES, "utf-8");
					if(!empty($this->info['addr2'])){
						$addr = $addr.' '.htmlspecialchars(mb_convert_encoding($this->info['addr2'], "utf-8"), ENT_QUOTES, "utf-8");
					}
					$args = array(
						"requester"=>htmlspecialchars(mb_convert_encoding($this->info['customername'], "utf-8"), ENT_QUOTES, "utf-8"),
						"subject"=>htmlspecialchars(mb_convert_encoding($this->info['subject'], "utf-8"), ENT_QUOTES, "utf-8"),
						"message"=>htmlspecialchars(mb_convert_encoding($this->info['message'], "utf-8"), ENT_QUOTES, "utf-8"),
						"reqmail"=>$this->info['email'],
						"reqzip"=>$this->info['zipcode'],
						"reqaddr"=>$addr,
						"site_id"=>1
					);
					$conn = new ConnDB();
					$conn->requestmail($args);
				}
				
				$result = $this->send_mail($mail_info, $this->info['customername'], $this->info['email'], $attach, $this->info['title']);
			
			}
			
			return $result;
			
		}catch (Exception $e) {
			return false;
		}
	}
	
	
	/**
	*	マイページからの追加注文
	*/
	public function send_repeat(){
		try{
			// 添付ファイルがある場合の処理
			$attach = array();
			$result = true;
			$t=0;
			$attach_count = count($_FILES['attachfile']['tmp_name']);
			for($i=0; $i<$attach_count; $i++) {
				if( is_uploaded_file($_FILES['attachfile']['tmp_name'][$i]) ){
					$result = false;
					if ($_FILES['attachfile']['error'][$i] == UPLOAD_ERR_OK){
				  	
				    	$tmp_path = $_FILES['attachfile']['tmp_name'][$i];
				    	$filename = $_FILES['attachfile']['name'][$i];
						$filetype = $_FILES['attachfile']['type'][$i];
						$filesize = $_FILES['attachfile']['size'][$i];
						$files_len += $filesize;
						
						if($files_len > _MAXIMUM_SIZE){
							$result_msg = '添付ファイルサイズは20MBまでにして下さい。';
						}else{
							$uploadfile = file_get_contents($tmp_path);
							$img_encode64 = chunk_split(base64_encode($uploadfile));
							
					      	$result = true;
						}
						    
				    }else{
				     	$result_msg = '添付ファイルのアップロード中にエラーです。添付ファイルの指定をやり直してください。';
				    }
				    
				    if($result){
				    	$attach[$t]['file'] = $img_encode64;
						$attach[$t]['name'] = $filename;
						$attach[$t]['type'] = $filetype;
						$attach[$t]['size'] = $filesize;
						$t++;
				    }else{
				    	break;
				    }
				}
			}
			
			if($result){
				$mail_info = "";
				$mail_info .= "【　追加注文のお申し込み　】\n\n";
				$mail_info .= "■元注文No.：　".$this->info['orders_id']."\n";
				$mail_info .= "■----------------------------------------\n\n";
				
				$mail_info .= "■お名前：　".$this->info['customername']." 様\n";
				$mail_info .= "■E-Mail：　".$this->info['email']."\n";
				$mail_info .= "■TEL：　".$this->info['tel']."\n\n";
				$mail_info .= "■お届け先：　";
				if($this->info['addr0']!='' || $this->info['deli']=='1'){
					$mail_info .= "\n〒".$this->info['zipcode']."\n";
					$mail_info .= $this->info['addr0']."\n";
					$mail_info .= $this->info['addr1']."\n";
					$mail_info .= $this->info['addr2']."\n";
				}else{
					$mail_info .= "登録住所\n";
				}
				$mail_info .= "\n■ご希望納期：　 ".$this->info['deliveryday']."\n";
				$mail_info .= "■お問合せ内容：\n";
				$mail_info .= $this->info['message']."\n\n";
				$mail_info .= "■----------------------------------------\n\n";
				
				$tot_amount = 0;
				for($i=0; $i<count($this->info['itemname']); $i++){
					$mail_info .= "■アイテム：　".$this->info['itemname'][$i]."\n";
					$mail_info .= "■カラー：　".$this->info['color'][$i]."\n";
					$mail_info .= "■サイズ：　".$this->info['itemsize'][$i]."\n";
					$mail_info .= "■枚数：　".$this->info['amount'][$i]."\n";
					$mail_info .= "------------\n\n";
					$tot_amount += $this->info['amount'][$i];
				}
				$mail_info .= "■合計枚数：　".$tot_amount."\n";
				$mail_info .= "\n■----------------------------------------\n\n";
				
				$mail_info .= "■添付ファイル：\n";
				$attach_count = count($attach);
				for($i=0; $i<$attach_count; $i++){
					$mail_info .= "　".$attach[$i]['name']."\n";
				}
				if($attach_count==0){
					$mail_info .= "　なし\n";
				}
				$mail_info .= "\n■----------------------------------------\n\n";
				
				$result = $this->send_mail($mail_info, $this->info['customername'], $this->info['email'], $attach, $this->info['title']);
			}
			
			return $result;
			
		}catch (Exception $e) {
			return false;
		}
	}
	
	
	/**
	*	メール送信
	*	@mail_text		送信する本文
	*	@name			お客様の名前
	*	@to				返信先のメールアドレス
	*	@attach			添付ファイル情報
	*	@mailuser		送信を受け付けるアドレスのユーザー部 default: info
	*	
	*	define('_INFO_EMAIL', 'info@takahama428.com');
	*	define('_REQUEST_EMAIL', 'request@takahama428.com');
	*	define('_ESTIMATE_EMAIL', 'estimate@takahama428.com');
	*	define('_ORDER_EMAIL', 'order@takahama428.com');
	*
	*	返り値			true:送信成功 , false:送信失敗
	*/
	protected function send_mail($mail_text, $name, $to, $attach, $mailuser='info'){
		mb_language("japanese");
		mb_internal_encoding("EUC-JP");
		
		switch($mailuser){
		case 'request':	$receiver = _INFO_EMAIL;
						$subtitle = '資料請求';
						break;
		case 'estimate':$receiver = _ESTIMATE_EMAIL;
						$subtitle = 'お見積りのお問い合わせ';
						break;
		case 'express':	$receiver = _ORDER_EMAIL;
						$subtitle = 'お急ぎの製作お問合せ';
						break;
		case 'minit':	$receiver = _ORDER_EMAIL;
						$subtitle = 'ユニフォームミニTお申し込み';
						break;
		case 'illusttemplate': 
						$receiver = _INFO_EMAIL;
						$subtitle = 'イラレ入稿テンプレート';
						break;
		case 'repeat':	$receiver = _INFO_EMAIL;
						$subtitle = '追加注文のお申し込み';
						break;
		case 'visit':	$receiver = _INFO_EMAIL;
						$subtitle = '出張打ち合わせ予約・申込み';
						break;
		case 'expresstoday':
						$receiver = _ORDER_EMAIL;
						$subtitle = '当日特急プランお問い合わせ';
						break;
		case 'towel':	$receiver = _ORDER_EMAIL;
						$subtitle = 'オリジナルタオルお問い合わせ';
						break;
		case 'designconsierge':	
						$receiver = _INFO_EMAIL;
						$subtitle = 'デザインコンシェルジュ';
						break;
		case 'towel':	$receiver = _INFO_EMAIL;
						$subtitle = '大口注文お問い合わせ';
						break;
		case 'quickcontact':	$receiver = _INFO_EMAIL;
						$subtitle = 'お悩み解決フォーム';
						break;
		case 'test':	$receiver = "test@takahama428.com";	// debug
						$subtitle = 'テスト送信';
						break;
		case 'orange':$receiver = _INFO_EMAIL;
						$subtitle = '俺んじ君ワークショップお申し込み';
						break;
		default :		$receiver = _INFO_EMAIL;
						$subtitle = 'お問い合わせ';
						break;	

		}
		
		$sendto = $receiver;
		$suffix = "【オリジナルTシャツ屋　takahama428】　"; // 件名の後ろに付加するテキスト
		$subject = $subtitle.$suffix;						// 件名
		$msg = "";											// 送信文
		$boundary = md5(uniqid(rand())); 					// バウンダリー文字（メールメッセージと添付ファイルの境界とする文字列を設定）
		
		$fromname = "タカハマ428";
		$from = mb_encode_mimeheader($fromname,"JIS")."<".$sendto.">";
		$header = "From: $from\n";
		$header .= "Reply-To: $from\n";
		$header .= "X-Mailer: PHP/".phpversion()."\n";
		$header .= "MIME-version: 1.0\n";
		
		if(!empty($attach)){ 		// 添付ファイルがあり
			$header .= "Content-Type: multipart/mixed;\n";
			$header .= "\tboundary=\"$boundary\"\n";
			$msg .= "This is a multi-part message in MIME format.\n\n";
			$msg .= "--$boundary\n";
			$msg .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$msg .= "Content-Transfer-Encoding: 7bit\n\n";
		}else{												// 添付ファイルなし
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
		}
		
		$msg .= mb_convert_encoding($mail_text,"JIS","EUC-JP");	// ここで注文情報をエンコードして設定
		
		if(!empty($attach)){		// 添付ファイル情報
			for($i=0; $i<count($attach); $i++){
				$msg .= "\n\n--$boundary\n";
				$msg .= "Content-Type: " . $attach[$i]['type'] . ";\n";
				$msg .= "\tname=\"".$attach[$i]['name']."\"\n";
				$msg .= "Content-Transfer-Encoding: base64\n";
				$msg .= "Content-Disposition: attachment;\n";
				$msg .= "\tfilename=\"".$attach[$i]['name']."\"\n\n";
				$msg .= $attach[$i]['file']."\n";
			}
			$msg .= "--$boundary--";
		}
		
		// 件名のマルチバイトをエンコード
		$subject  = mb_encode_mimeheader($subject,"JIS");
		
		// メール送信
		if(mail($sendto, $subject, $msg, $header)){
			// 自動返信メール
			$sendto = $to;
			
			if($mailuser!="designconsierge"){
				$title = $subtitle."ありがとうございます";
			}else{
				$title = $subtitle."のご利用ありがとうございます";
			}
			$subject = mb_encode_mimeheader($title,"JIS");
			$from = $receiver;
			$fromname = "タカハマライフアート";
			$from = mb_encode_mimeheader($fromname,"JIS")."<".$from.">";
			
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg = $name."　様\n";
			$msg .= "このたびは、タカハマライフアートをご利用いただき誠にありがとうございます。\n";
			if($mailuser!="designconsierge"){
				$msg .= "以下の内容で".$subtitle."を受付いたしました。\n";
			}
			$msg .= "\n";
			$msg .= $mail_text;
			
			
			// 休業の告知文を挿入
			$msg .= mb_convert_encoding(_NOTICE_HOLIDAY,"JIS","utf-8");
			
			// 臨時の告知文を挿入
			$msg .= mb_convert_encoding(_EXTRA_NOTICE,"JIS","utf-8");
			
			$msg .= "\n※ご不明な点やお気づきのことがございましたら、ご遠慮なくお問い合わせください。\n";
			$msg .= "■営業時間　10:00 - 18:00　　■定休日：　土日祝\n\n";
			$msg .= "━ タカハマライフアート ━━━━━━━━━━━━━━━━━━━━━━━\n\n";
			$msg .= "　Phone：　　"._OFFICE_TEL."\n";
			$msg .= "　E-Mail：　　"._INFO_EMAIL."\n";
			$msg .= "　Web site：　"._DOMAIN."/\n";
			$msg .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
			
			$msg = mb_convert_encoding($msg,"JIS","EUC-JP");
			
			$res = mail($sendto, $subject, $msg, $header);
			
			return $res;	// 成功
			
		}else{
			return false;	// 失敗
		}
	}
	
	
	
	
	/**
	*	アンケートの送信
	*/
	public function send_enquete(){
		try{
			// 択一選択の回答
			$a1 = array("","とても分りにくかった","分りにくかった","普通","分りやすかった","とても分りやすかった");
			$a3 = array("全く不安は無かった","不安な部分があった");
			$a5 = array("","とても悪かった","悪かった","普通","良かった","とても良かった");
			$a6 = array("","全くイメージ通りではなかった","イメージしていたより悪かった","普通","イメージ通り良かった","イメージ以上に良かった");
			$a7 = array("","とても悪かった","悪かった","普通","良かった","とても良かった");
			$a14 = array("","その他","2回目以降の購入","セミナー講演会","雑誌、新聞記事、広告","知り合いの紹介","インターネット検索");
			$number = 'K'.str_pad($this->info['number'], 6, '0', STR_PAD_LEFT);
			$zipcode = str_replace('-', ''. mb_convert_kana($this->info['zipcode'], 'a'));
			
			// メール本文
			$mail_info = "【　お客様アンケート　】\n\n";
			$mail_info .= "■顧客ID：　K".$number."\n";
			/*
			*	2013-12-31 廃止
			*
			$mail_info .= "■お名前：　".$this->info['customername']." 様\n";
			$mail_info .= "■ご住所：　〒".$this->info['zipcode']." ".$this->info['addr']."\n";
			*/
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "■Q1：　今回、タカハマライフアートをお選びいただいた理由をお聞かせ下さい。\n";
			$mail_info .= "■A1：　".$this->info['a12']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "■Q2：　タカハマライフアートのホームページはわかりやすかったでしょうか？\n";
			$mail_info .= "■A2：　".$a1[$this->info['a1']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "■Q3：　ホームページで、わかりやすかった点、わかりにくかった点について、具体的に教えて下さい。\n";
			$mail_info .= "■A3：　".$this->info['a2']."\n";
			$mail_info .= "----------------------------------------\n\n";
			/*
			$mail_info .= "■Q3：　ご注文後、商品到着まで、何か不安を感じられましたでしょうか？\n";
			$mail_info .= "■A3：　".$a3[$this->info['a3']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "■Q4：　どういう不安があったかをお答え下さい。\n";
			$mail_info .= "■A4：　".$this->info['a4']."\n";
			$mail_info .= "----------------------------------------\n\n";
			*/
			$mail_info .= "■Q4：　ご注文いただいた際の弊社の対応はいかがでしたでしょうか？\n";
			$mail_info .= "■A4：　".$a5[$this->info['a5']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "■Q5：　仕上がりました商品は、お客様のイメージ通りでしたでしょうか？\n";
			$mail_info .= "■A5：　".$a6[$this->info['a6']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "■Q6：　商品が到着した際の梱包状態はいかがでしたでしょうか？\n";
			$mail_info .= "■A6：　".$a7[$this->info['a7']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "■Q7：　実際に商品を着用・使用してみての、アイテムに関する感想をお願いします。\n";
			$mail_info .= "■A7：　".$this->info['a10']."\n";
			$mail_info .= "----------------------------------------\n\n";
			/*
			$mail_info .= "■Q8：　デザイン、色、サイズ、素材など、「もっとこんな商品（アイテム）があればよいのに！」というご希望があればお聞かせ下さい。\n";
			$mail_info .= "■A8：　".$this->info['a11']."\n";
			$mail_info .= "----------------------------------------\n\n";
			*/
			$mail_info .= "■Q8：　ご使用の用途を教えてください。(音楽イベント、文化祭など)\n";
			$mail_info .= "■A8：　".$this->info['a13']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "■Q9：　「もっとこんなサービス・商品があれば良いのに！」というご要望があればお聞かせ下さい。\n";
			$mail_info .= "■A9：　".$this->info['a8']."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "■Q10：　弊社を知ったきっかけを教えてください。\n";
			$mail_info .= "■A10：　".$a14[$this->info['a14']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "■Q11：　その他、注文してみての感想・お気づきの点などがありましたらお聞かせ下さい。\n";
			$mail_info .= "■A11：　".$this->info['a9']."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "Date: ".date('Y-m-d H:i:s')."\n";
						
			// 送信処理
			mb_language("japanese");
			mb_internal_encoding("EUC-JP");
			$sendto = _ORDER_EMAIL;
						
			$subject = "お客様アンケート";						// 件名
			$msg = "";											// 送信文
			$boundary = md5(uniqid(rand())); 					// バウンダリー文字（メールメッセージと添付ファイルの境界とする文字列を設定）
			
			$fromname = "タカハマ428";
			$from = mb_encode_mimeheader($fromname,"JIS")."<".$sendto.">";
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ここで本文をエンコードして設定
			
			// 件名のマルチバイトをエンコード
			$subject = mb_encode_mimeheader($subject,"JIS");
			
			// メール送信
			$result = true;
			if(mail($sendto, $subject, $msg, $header)){
				// DB登録
				$this->info['customername'] = '';	//mb_convert_encoding($this->info['customername'], 'utf-8','euc-jp');
				$this->info['zipcode'] = '';		//str_replace('-', '', mb_convert_kana($this->info['zipcode'],'a','euc-jp'));
				$this->info['addr'] ='';			//mb_convert_encoding($this->info['addr'], 'utf-8','euc-jp');
				$this->info['a2'] = mb_convert_encoding($this->info['a2'], 'utf-8','euc-jp');
				//$this->info['a4'] = mb_convert_encoding($this->info['a4'], 'utf-8','euc-jp');
				$this->info['a8'] = mb_convert_encoding($this->info['a8'], 'utf-8','euc-jp');
				$this->info['a9'] = mb_convert_encoding($this->info['a9'], 'utf-8','euc-jp');
				$this->info['a10'] = mb_convert_encoding($this->info['a10'], 'utf-8','euc-jp');
				$this->info['a11'] = mb_convert_encoding($this->info['a11'], 'utf-8','euc-jp');
				$this->info['a12'] = mb_convert_encoding($this->info['a12'], 'utf-8','euc-jp');
				$this->info['a13'] = mb_convert_encoding($this->info['a13'], 'utf-8','euc-jp');
				$conn = new ConnDB();
				$conn->setEnquete($this->info);
			}else{
				$result = false;
			}
			
			return $result;
			
		}catch (Exception $e) {
			return false;
		}
	}
	
	
	
	/**
	*	CC、BCCを付けてメール送信  charset UTF-8
	*	@mail_info		メール本文
	*	@subject		件名
	*	@sendto			送信先のメールアドレスの配列
	*	@formname		送信者の名前
	*	@fromaddr		送信者のメールアドレス（Reply-To に設定）
	*	@bcc			BCC のメールアドレス default ""
	*	@cc				CC のメールアドレス default ""
	*	@attach			添付ファイル情報の配列 default ""
	*	
	*	@return		送信成功: ['success']　　エラー: アドレスを配列で返す。
	*/
	public function send_multi($mail_info, $subject, $sendto, $fromname, $formaddr, $bcc="", $cc="", $attach=""){
		mb_language("japanese");
		$autoReply = false;					// 返信メールの有無（trueで返信する）
		$msg = "";							// 送信文
		$boundary = md5(uniqid(rand())); 	// バウンダリー文字（メールメッセージと添付ファイルの境界とする文字列を設定）
		$from = mb_encode_mimeheader($fromname,"JIS")."<"._ESTIMATE_EMAIL.">";
		$replay = mb_encode_mimeheader($fromname,"JIS")."<".$formaddr.">";
		$header = "From: $from\n";
		$header .= "Reply-To: $replay\n";
		if(!empty($bcc)){
			$header .= "Bcc: ".$bcc."\n";
		}
		if(!empty($cc)){
			$header .= "Cc: ".$cc."\n";
		}
		$header .= "X-Mailer: PHP/".phpversion()."\n";
		$header .= "MIME-version: 1.0\n";

		if(!empty($attach)){ 		// 添付ファイルがあり
			$header .= "Content-Type: multipart/mixed;\n";
			$header .= "\tboundary=\"$boundary\"\n";
			$msg .= "This is a multi-part message in MIME format.\n\n";
			$msg .= "--$boundary\n";
			$msg .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$msg .= "Content-Transfer-Encoding: 7bit\n\n";
		}else{												// 添付ファイルなし
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
		}

		
		$footer = "\n※ご不明な点やお気づきのことがございましたら、ご遠慮なくお問い合わせください。\n";
		$footer .= "■営業時間　10:00 - 18:00　　■定休日：　土日祝\n\n";
		$footer .= "━ タカハマライフアート ━━━━━━━━━━━━━━━━━━━━━━━\n\n";
		$footer .= "　Phone：　　"._OFFICE_TEL."\n";
		$footer .= "　E-Mail：　　"._INFO_EMAIL."\n";
		$footer .= "　Web site：　"._DOMAIN."/\n\n";
		$footer .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
		
		$mail_info .= mb_convert_encoding($footer,'JIS','euc-jp');
		$msg .= mb_convert_encoding($mail_info,"JIS","euc-jp");	// ここで本文をエンコードして設定

		if(!empty($attach)){		// 添付ファイル情報
			for($i=0; $i<count($attach); $i++){
				$msg .= "\n\n--$boundary\n";
				$msg .= "Content-Type: " . $attach[$i]['type'] . ";\n";
				$msg .= "\tname=\"".$attach[$i]['name']."\"\n";
				$msg .= "Content-Transfer-Encoding: base64\n";
				$msg .= "Content-Disposition: attachment;\n";
				$msg .= "\tfilename=\"".$attach[$i]['name']."\"\n\n";
				$msg .= $attach[$i]['file']."\n";
			}
			$msg .= "--$boundary--";
		}

		// 件名のマルチバイトをエンコード
		$subject  = mb_encode_mimeheader($subject,"JIS");

		// メール送信
		$res = array();
		$sendto[] = $formaddr;	// 本人への返信用
		for($i=0; $i<count($sendto); $i++){
			if(strpos($sendto[$i], "@")===false){
				$res[] = $sendto[$i];
				continue;
			}
			list($localname, $domain) = explode("@", $sendto[$i]);
			if(!checkdnsrr($domain, 'MX')){
				$res[] = $sendto[$i];
			}else{
				if(!mail($sendto[$i], $subject, $msg, $header)){
					$res[] = $sendto[$i];	// 失敗したアドレス
				}
			}
		}

		if(empty($res)) $res[] = 'success';
		return $res;
	}
}
?>
