<?php
/*------------------------------------------------------------

	File_name	: ordermail.php
	Description	: takahama428 web site send order mail class
	Hash		: Session data
					$_SESSION['orders']['items'];		商品
					$_SESSION['orders']['attach'];		添付ファイル
					$_SESSION['orders']['customer'];	ユーザー
					$_SESSION['orders']['options'];		オプション
					$_SESSION['orders']['sum'];			合計値（商品代、プリント代、枚数）
	Charset		: utf-8
	Log			: 2011.03.26 created
				  2012.03.14 プリント情報の本文生成を更新
				  2014.02.04 都道府県を分ける
				  2014.05.12 支払方法にカード決済を追加
				  2014.08.13 特急料金の有無を追加
				  2017-05-25 プリント代計算の仕様変更によるプリント情報の更新
	
-------------------------------------------------------------- */
require_once dirname(__FILE__).'/../php_libs/http.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/php_libs/conndb.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/config.php';

class Ordermail extends Conndb{

	public function send(){
		try{
			$items = $_SESSION['orders']['items'];
			$attach = $_SESSION['orders']['attach'];
			$user = $_SESSION['orders']['customer'];
			$opts = $_SESSION['orders']['options'];
			$sum = $_SESSION['orders']['sum'];
						
			$order_info = "☆━━━━━━━━【　お申し込み内容　】━━━━━━━━☆\n\n";
			
			$order_info .= "┏━━━━━━━━┓\n";
			$order_info .= "◆　　ご希望納期\n";
			$order_info .= "┗━━━━━━━━┛\n";
			if(empty($opts['deliveryday'])){
				$order_info .= "◇　納期指定なし\n";
			}else{
				$order_info .= "◇　納期　：　".$opts['deliveryday']."\n";
			}
			
			
			if(!empty($opts['expressInfo'])){
				$order_info .= "◇　".$opts['expressInfo']."：　特急料金あり\n";
			}
			
			$delitime = array(
				'なし',
				'午前中', 
				'12:00-14:00',
				'14:00-16:00',
				'16:00-18:00',
				'18:00-20:00',
				'20:00-21:00'
			);
			$order_info .= "◇　配達時間指定　：　".$delitime[$opts['deliverytime']]."\n\n";
			$order_info .= "━━━━━━━━━━━━━━━━━━━━━\n\n";
			
			$order_info .= "┏━━━━━━━┓\n";
			$order_info .= "◆　　商品情報\n";
			$order_info .= "┗━━━━━━━┛\n";
			
/*
*	['items'][category_id]['category_key']
*			  			  ['category_name']
*		   	  			  ['item'][id]['code']
*					   			  	  ['name']
*							      	  ['color'][code]['name']
*											 		 ['size'][sizeid]['sizename']
*											  						 ['amount']
*																	 ['cost']
*/
			$attach_info = array();
			foreach($items as $catid=>$v1){
				foreach($v1['item'] as $itemid=>$v2){
					$item_name = $v2['code']." ".$v2['name'];
					$order_info .= "◆アイテム：　".$item_name."\n";
					$posid = $v2['posid'];
					foreach($v2['color'] as $colorcode=>$v3){
						$color_name = $v3['name'];
						$order_info .= "◇カラー：　".$colorcode." ".$color_name."\n";
						$order_info .= "◇サイズと枚数\n";
						$subtotal = 0;
						foreach($v3['size'] as $sizeid=>$v4){
							if(empty($v4['amount'])) continue;
							$order_info .= $v4['sizename']."　：　".$v4['amount']."枚\n";
						}
						$order_info .= "--------------------\n";
					}
					$order_info .= "\n\n";
/*
*	プリント位置と添付ファイル
*	['items'][category_id]['item'][id]['name']
*									  ['posid']
*									  ['design'][base][0]['posname']
*												   		 ['ink']
*														 ['attachname']
*/
					$attach_info[$posid]['design'] = $v2['design'];
					$attach_info[$posid]['item'][] = $item_name;
					
				}
			}
			$order_info .= "◆枚数合計：　".number_format($sum['amount'])." 枚\n";
			$order_info .= "━━━━━━━━━━━━━━━━━━━━━\n\n\n";
			

/*
*	$attach_info
*	[posid]['item'][アイテム名, ...]
*		   ['design'][base][0]['posname']
*					   		  ['ink']
*							  ['attachname']
*							  ['img']['file']
*									 ['name']
*									 ['type']
*/
			$order_info .= "┏━━━━━━━━━┓\n";
			$order_info .= "◆　　プリント情報\n";
			$order_info .= "┗━━━━━━━━━┛\n";
			$sizeName = array('大', '中', '小');
			$printName = array('silk'=>'シルク','digit'=>'デジタル転写','inkjet'=>'インクジェット');
			foreach($attach_info as $posid=>$a1){
				$order_item = "◇アイテム：　".implode('、', $a1['item'])."\n";
				$printinfo = '';
				foreach($a1['design'] as $base=>$a2){
					
					$tmp = "";
					for($i=0; $i<count($a2); $i++){
						if($a2[$i]['ink']==0 && empty($a2[$i]['attachname'])) continue;
						if($a2[$i]['ink']>=9) $ink = "フルカラー\n";
						else $ink = $a2[$i]['ink']."色\n";
						$tmp = $a2[$i]['posname']."　".$ink;
						// $printinfo .= "◇プリント方法：　".$printName[$a2[$i]['printing']]."\n";
						$printinfo .= "◇プリント位置：　".$base."\n";
						$printinfo .= "◇デザインサイズ：　".$sizeName[$a2[$i]['areasize']]."\n";
						$printinfo .= "◇デザインの色数：　".$tmp."\n";
					}
				}
				if($printinfo!=""){
					$order_info .= $order_item.$printinfo;
					$order_info .= "------------------------------------------\n\n";
				}else{
					$order_info .= $order_item."プリントなし\n";
					$order_info .= "------------------------------------------\n\n";
				}
			}
			
			if(empty($opts['pack'])){
				$order_info .= "◇たたみ・袋詰め：　希望しない\n";
			}else if($opts['pack']==1){
				$order_info .= "◇たたみ・袋詰め：　希望する\n";
			}else{
				$order_info .= "◇たたみ・袋詰め：　袋のみ\n";
			}
			
			//$order_info .= "◇デザインの入稿方法：　".$opts['ms']."\n\n";
			//$order_info .= "◇プリントカラー：　\n".$opts['note_printcolor']."\n\n";
			//$order_info .= "◇文字入力の確認：　\n".$opts['note_write']."\n\n";
			$order_info .= "━━━━━━━━━━━━━━━━━━━━━\n\n\n";

			$order_info .= "┏━━━━━━━━━┓\n";
			$order_info .= "◆　　添付ファイル\n";
			$order_info .= "┗━━━━━━━━━┛\n";
			if(empty($attach)){
				$order_info .= "添付なし\n";
			}else{
				for($a=0; $a<count($attach); $a++){
					$order_info .= "◇ファイル名：　".mb_convert_encoding($attach[$a]['img']['name'], 'utf-8')."\n";
				}
			}
			$order_info .= "━━━━━━━━━━━━━━━━━━━━━\n\n\n";
			
			
			$order_info .= "┏━━━━━┓\n";
			$order_info .= "◆　　割引\n";
			$order_info .= "┗━━━━━┛\n";
			
			// 学割
			if(!empty($opts['student'])){
				switch($opts['student']){
					case '3':	$discountname[] = "学割";
								break;
					case '5':	$discountname[] = "2クラス割";
								break;
					case '7':	$discountname[] = "3クラス割";
								break;
				}
			}
			
			// ブログ割
			if(!empty($opts['blog'])){
				$discountname[] = "ブログ割";
			}
			
			// イラレ割
			if(!empty($opts['illust'])){
				$discountname[] = "イラレ割";
			}
			
			// 紹介割
			if(!empty($opts['intro'])){
				$discountname[] = "紹介割";
			}
			
			if(empty($discountname)){
				$order_info .= "◇割引：　なし\n";
			}else{
				$order_info .= "◇割引：　".implode(', ', $discountname)."\n";
			}
			
			$order_info .= "━━━━━━━━━━━━━━━━━━━━━\n\n\n";
			
			
			$order_info .= "┏━━━━━━━━┓\n";
			$order_info .= "◆　　お客様情報\n";
			$order_info .= "┗━━━━━━━━┛\n";
			$order_info .= "◇お名前：　".$user['customername']."　様\n";
			$order_info .= "◇フリガナ：　".$user['customerruby']."　様\n";
			$order_info .= "◇ご住所：　〒".$user['zipcode']."\n";
			$order_info .= "　　　　　　　　".$user['addr0']."\n";
			$order_info .= "　　　　　　　　".$user['addr1']."\n";
			$order_info .= "　　　　　　　　".$user['addr2']."\n";
			$order_info .= "◇TEL：　".$user['tel']."\n";
			$order_info .= "◇E-Mail：　".$user['email']."\n";
			$order_info .= "------------------------------------------\n\n";
			
			$order_info .= "◇弊社ご利用について：　";
			if($user['repeater']==1){
				$order_info .= "初めてのご利用\n\n";
			}else if($user['repeater']==2){
				$order_info .= "以前にも注文したことがある\n\n";
			}else{
				$order_info .= "-\n\n";
			}
			
			/*
			$attr = array('','法人','学生','個人');
			$order_info .= "◇お客様について：　".$attr[$opts['attr']]."\n";
			
			$purpose = array('', 
				'文化祭・体育祭（クラス・サークル・企業）',
				'スポーツ・ダンスユニフォーム（部活・サークルなど）',
				'スタッフユニフォーム・制服',
				'販売・販促品',
				'プレゼント・記念品',
				'個人用'
			);
			$tmp = array();
			if(!empty($opts['purpose'])){
				for($i=0; $i<count($opts['purpose']); $i++){
					if($opts['purpose'][$i]==1){
						$tmp[] = $purpose[$i];
					}
				}
			}
			$tmp[] = $opts['purpose_text'];
			$order_info .= "◇ご使用用途：　".implode(', ',$tmp)."\n";
			
			$media = array('', 
				'Yahoo検索','Google検索','その他検索エンジン',
				'Tシャツ、ファッション関連サイト','バナー広告',
				'カタログ・チラシ等','友人・知人の紹介'
			);
			$tmp = array();
			if(!empty($opts['media'])){
				for($i=0; $i<count($opts['media']); $i++){
					if($opts['media'][$i]==1){
						$tmp[] = $media[$i];
					}
				}
			}
			$tmp[] = $opts['media_text'];
			$order_info .= "◇何でお知りになったか：　".implode(', ',$tmp)."\n";
			*/
			
			if(empty($opts['publish'])){
				$order_info .= "◇デザイン掲載：　掲載可\n\n";
			}else{
				$order_info .= "◇デザイン掲載：　掲載不可\n\n";
			}
			$order_info .= "◇デザインについてのご要望など：\n";
			if(empty($user['note_design'])){
				$order_info .= "なし\n";
			}else{
				$order_info .= $user['note_design']."\n";
			}
			$order_info .= "------------------------------------------\n\n";
			
			$order_info .= "◇刺繍をご希望の場合：\n";
			if(empty($user['note_printmethod'])){
				$order_info .= "なし\n";
			}else{
				$order_info .= $user['note_printmethod']."\n";
			}
			$order_info .= "------------------------------------------\n\n";
			
			$order_info .= "◇プリントするデザインの色：\n";
			$order_info .= $user['note_printcolor']."\n";
			$order_info .= "------------------------------------------\n\n";
			
			$payment = array("銀行振込","代金引換","現金でお支払い（工場でお受取）","カード決済","コンビニ決済");
			$order_info .= "◇お支払方法：　".$payment[$opts['payment']]."\n\n";
			
			$order_info .= "◇ご要望・ご質問など：\n";
			if(empty($user['comment'])){
				$order_info .= "なし\n\n";
			}else{
				$order_info .= $user['comment']."\n\n";
			}
			$order_info .= "━━━━━━━━━━━━━━━━━━━━━\n\n";

			/*
			$order_info .= "┏━━━━━━━┓\n";
			$order_info .= "◆　　お届け先\n";
			$order_info .= "┗━━━━━━━┛\n";
			if(!empty($user['deli'])){
				$order_info .= "◇宛名：　".$user['organization']."　様\n";
				$order_info .= "◇ご住所：　〒".$user['delizipcode']."\n";
				$order_info .= "　　　　　　　　　".$user['deliaddr1']." ".$info['deliaddr2']."\n";
			}else{
				$order_info .= "（上記ご連絡先と同じ場所にお届けする）\n";
			}
			$order_info .= "━━━━━━━━━━━━━━━━━━━━━\n\n";
			*/
			
			/* 2013-11-25 廃止
			if(empty($user['payment'])){
				$order_info .= "┏━━━━━━━┓\n";
				$order_info .= "◆　　お振込先\n";
				$order_info .= "┗━━━━━━━┛\n";
				$order_info .= "振込口座：　三菱東京ＵＦＪ銀行\n";
				$order_info .= "新小岩支店744　普通 3716333\n";
				$order_info .= "口座名義：　ユ）タカハマライフアート\n";
				$order_info .= "━━━━━━━━━━━━━━━━━━━━━\n";
				$order_info .= "※お振込み手数料は、お客様のご負担とさせて頂いております。\n\n";
			}
			*/
			
			// send mail
			$res = $this->send_mail($order_info, $user['customername'], $user['email'], $attach);
			if (!$res) {
				throw new Exception();
			}
			
			// db
			$res = $this->insertOrderToDB();
			
			return $res;
			
		}catch (Exception $e) {
			return false;
		}
	}

	
	/**
	*	メール送信
	*	@mail_text		顧客情報と注文内容
	*	@name			お客様の名前
	*	@to				返信先のメールアドレス
	*	@attach			添付ファイル情報
	*	返り値			true:送信成功 , false:送信失敗
	*/
	protected function send_mail($mail_text, $name, $to, $attach){
		mb_language("japanese");
		mb_internal_encoding("UTF-8");
		$sendto = _ORDER_EMAIL;						// 送信先
//		$sendto = _TEST_EMAIL;						// 送信先（TEST）
		$suffix = "【takahama428】"; 				// 件名の後ろに付加するテキスト
		$subject = "お申し込み".$suffix;			// 件名
		$msg = "";									// 送信文
		$boundary = md5(uniqid(rand())); 			// バウンダリー文字（メールメッセージと添付ファイルの境界とする文字列を設定）
		
		$fromname = "タカハマ428";
		$from = mb_encode_mimeheader(mb_convert_encoding($fromname, "JIS", "UTF-8"))."<".$sendto.">";
		
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
		
		// ここで本文をエンコードして設定
		$msg .= mb_convert_encoding("お名前：　".$name."　様\n".$mail_text,"JIS","UTF-8");
		
		if(!empty($attach)){		// 添付ファイル情報
			for($i=0; $i<count($attach); $i++){

				$msg_chunk_split = chunk_split($attach[$i]['img']['file']);
				$msg .= "\n\n--$boundary\n";
				$msg .= "Content-Type: " . $attach[$i]['img']['type'] . ";\n";
				$msg .= "\tname=\"".$attach[$i]['img']['name']."\"\n";
				$msg .= "Content-Transfer-Encoding: base64\n";
				$msg .= "Content-Disposition: attachment;\n";
				$msg .= "\tfilename=\"".$attach[$i]['img']['name']."\"\n\n";
				$msg .= $msg_chunk_split."\n";
			}
			$msg .= "--$boundary--";
		}
		
		// 件名のマルチバイトをエンコード
		$subject = mb_encode_mimeheader(mb_convert_encoding($subject, "JIS", "UTF-8"));
				
		// メール送信
		if(mail($sendto, $subject, $msg, $header)){
			
			// 自動返信メール
			$sendto = $to;
			$subject = 'お申し込みありがとうございます';
			$subject = mb_encode_mimeheader(mb_convert_encoding($subject,"JIS","UTF-8"));
			$from = _INFO_EMAIL;
			$fromname = "タカハマライフアート";
			$from = mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","UTF-8"))."<".$from.">";
			
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg = $name."　様\n";
			$msg .= "このたびは、タカハマライフアートをご利用いただき誠にありがとうございます。\n";
			$msg .= "お申し込みを承りました。\n";
			$msg .= "このメールはお申し込みいただいたお客様へ、内容確認の自動返信となっております。\n\n";
			
			$msg .= "《現時点ではご注文は確定しておりません》\n\n";
			$msg .= "お申し込みいただいた内容でのお見積メールを改めてお送りいたしますので、お見積メール到着をお待ち下さい。\n";
			$msg .= "お見積メールは営業時間内で順次お送りしておりますが、お急ぎの場合、また、なかなか届かない場合には、\n";
			$msg .= "お手数ですが、フリーダイヤル"._TOLL_FREE."までご連絡ください。\n";
			$msg .= "（営業時間：平日10：00～18：00 ※お急ぎの場合でも営業時間内での対応となります。予めご了承下さい。）\n\n\n";
			
			$msg .= "《お支払いにつきまして》\n\n";
			$msg .= "最終打ち合わせが終了し、ご注文が確定いたしましたら、ご注文内容の「確認メール」をお送りいたします。\n";
			$msg .= "間違いが無いかご確認の上、確認メールに記載の方法でお支払いください。\n\n";
			$msg .= "引き続き、どうぞよろしくお願いいたします。\n\n";
			
			$msg .= $mail_text;
			
			// 臨時の告知文を挿入
			//$msg .= _EXTRA_NOTICE;

			$msg .= _NOTICE_HOLIDAY;
			$msg .= "\n";
			$msg .= _EXTRA_NOTICE;
			$msg .= "\n";

			$msg .= "\n※ご不明な点やお気づきのことがございましたら、ご遠慮なくお問い合わせください。\n";
			$msg .= "┏━━━━━━━━━━━━━━━━━━━\n";
			$msg .= "┃タカハマライフアート\n";
			$msg .= "┃　Phone：　　"._OFFICE_TEL."\n";
			$msg .= "┃　E-Mail：　　"._INFO_EMAIL."\n";
			$msg .= "┃　Web site：　"._DOMAIN."/\n";
			$msg .= "┗━━━━━━━━━━━━━━━━━━━\n";
			
			$msg = mb_convert_encoding($msg,"JIS","UTF-8");
			
			$res = mail($sendto, $subject, $msg, $header);
			
			return $res;	// 成功
		
		}else{
			return false;	// 失敗
		}
		
	}

	public function insertOrderToDB(){
		$httpObj = new HTTP(_ORDER_INFO);
		$items = $_SESSION['orders']['items'];
		$attach = $_SESSION['orders']['attach'];
		$user = $_SESSION['orders']['customer'];
		$opts = $_SESSION['orders']['options'];
		$sum = $_SESSION['orders']['sum'];		

		// 顧客情報
		$customer_id = "";
		//新規顧客の場合
		if(empty($user['member'])){
			$field1 = array("number","cstprefix","customer_id","customerruby","companyruby","customername","company","tel","mobile","fax","email","password","mobmail","bill","cutofday","cyclebilling","paymentday","remittancecharge","zipcode","addr0","addr1","addr2","addr3","addr4","reg_site");
			$data1 = array("","k","",$user['customerruby'],"",$user['customername'],"",$user['tel'],"","",$user['email'],$user['pass'],"","1","20","1","31","1",$user['zipcode'],$user['addr0'],$user['addr1'],$user['addr2'],"","",_SITE);
		} else {
			//ログインした顧客の場合
			$customer_id = $user['member'];
			//$field1 = "";
			$field1 = array("customer_id","customerruby","customername","tel","email","zipcode","addr0","addr1","addr2","reg_site");
			$data1 = array($customer_id,$user['customerruby'],$user['customername'],$user['tel'],$user['email'],$user['zipcode'],$user['addr0'],$user['addr1'],$user['addr2'],_SITE);
		}

		// お届け先情報
		$field2 = array("customer_id", "delivery_customer");
		$data2 = array($customer_id, $user['delivery_customer']);

		// 受注情報

		$discount = "";
		// ブログ割
		if(empty($opts['blog'])){
			$discount = "blog0";
		}else{
			$discount = "blog1";
		}
		$discount .= ",";
		// イラレ割
		if(empty($opts['illust'])){
			$discount .= "illust0";
		}else{
			$discount .= "illust1";
		}

		// 学割
		switch($opts['student']){
			case '3':	$discount1 = "student";
						break;
			case '5':	$discount1 = "team2";
						break;
			case '7':	$discount1 = "team3";
						break;
			default: 	$discount1 = "";
		}

		// 紹介割
		if(!empty($opts['intro'])){
			$discount2 = "introduce";
		}
		// 支払方法
		$payment = array("wiretransfer","cod","cash","credit","conbi");

		// 消費税
		$tax = parent::getSalesTax();
		$tax /= 100;

		// 見積
		$basefee = $sum["itemprice"] + $sum["printprice"] + $sum["optionfee"];
		$salestax = floor(basefee*$tax);
		$total = floor(basefee*(1+$tax));
		$credit = 0;
		if($sum["payment"]==3){
			$credit = ceil($total*_CREDIT_RATE);
			$total += $credit;
		}
		$perone = ceil($total/$sum['amount']);
		
		// コメント欄
		$comment[] = $user['note_design'];
		$comment[] = $user['note_printcolor'];
		$comment[] = $user['note_printmethod'];
		$comment[] = $user['comment'];
		$strComment = implode("\n", $comment);	
		
		$field3 = array(
		"id","reception","destination","order_comment","paymentdate",
		"exchink_count","exchthread_count","deliverytime","manuscriptdate","invoicenote","billnote",
		"contact_number",
		"additionalname","extradiscountname","boxnumber","handover","factory",
		"destcount","ordertype","schedule1","schedule2","schedule3","schedule4",

		"arrival","carriage","check_amount","noprint","design","manuscript",
		"discount1","discount2","reduction","reductionname","freeshipping","payment",

		"phase","budget","deliver","purpose","designcharge","job","free_printfee",
		"free_discount","additionalfee","extradiscount","rakuhan","completionimage",
		"staffdiscount","maintitle","customer_id","estimated","order_amount",

		"purpose_text","reuse","applyto","repeater",
		"package_no",
		"package_nopack",
		"pack_nopack_volume",
		"package_yes",
		"pack_yes_volume",
		"discount","media","bill",

		"productfee","printfee","silkprintfee","colorprintfee","digitprintfee",
		"inkjetprintfee","cuttingprintfee","embroideryprintfee","exchinkfee","additionalfee","packfee",

		"expressfee","discountfee","reductionfee","carriagefee","extracarryfee",
		"designfee","codfee","basefee","salestax","creditfee", "conbifee","repeatdesign","allrepeat");

		$data3 = array
		("","0","0",$strComment,"",
		"0","0",$opts['deliverytime'],"","","",
		"",
		"","","0","0","0",
		"1","general","","","",$opts['deliveryday'],

		"0","normal",$sum['amount'],$opts['noprint'],"","",
		$discount1,$discount2,"0","","0",$payment[$opts['payment']],

		"accept","0","2","","0","その他","0",
		"0","0","","0","0",
		"0","",$customer_id,$total,$sum['amount'],

		"",$user['repeater'],"0","0",
		empty($opts['pack'])? 1: 0,
		$opts['pack']!=2? 0: 1,
		$opts['pack']!=2? 0: $sum['amount'],
		$opts['pack']!=1? 0: 1,
		$opts['pack']!=1? 0: $sum['amount'],
		$discount,"","",

		$sum["itemprice"],$sum["printprice"],"0","0","0",
		"0","0","0","0","0",$sum["pack"],

		$sum["expressfee"],$sum["discount"],"0",$sum["carriage"],"0",
		"0",$sum["codfee"],$basefee,$salestax,$credit, $sum["conbifee"],"0","0");

		$field4 = array("master_id","choice","plateis","size_id","amount","item_cost","item_printfee","item_printone","item_id","item_name","stock_number","maker","size_name","item_color","price");
		$field5 = array();
		$field6 = array("category_id","printposition_id","subprice");
		$field7 = array("areaid", "print_id", "area_name", "area_path", "origin", "ink_count", "print_type","areasize_from", "areasize_to", "areasize_id", "print_option", "jumbo_plate", "design_plate","design_type","design_size", "repeat_check", "silkmethod");
		$field8 = array("areaid", "area_id", "selective_key", "selective_name");

		//注文商品
		$data4 = array();
		$data5 = array();
		//商品カテゴリーごとのプリント情報
		//data6
		$orderprint = array();
		//data7
		$orderarea = array();
		//data8
		$orderselectivearea = array();


		$attach_info = array();
		$idx6 = 0;
		$idx7 = 0;
		$idx8 = 0;
		foreach($items as $catid=>$v1){
			foreach($v1['item'] as $itemid=>$v2){
				$posid = $v2['posid'];
				$orderprintTemp =$catid."|".$posid."|0";
				array_push($orderprint , $orderprintTemp);

				foreach($v2['color'] as $colorcode=>$v3){
					foreach($v3['size'] as $sizeid=>$v4){
						if(empty($v4['amount'])) continue;
						$tempData4 = $v3['master_id']."|1|1|".$sizeid."|".$v4['amount']."|".$v4['cost']."|0|0|||||||" ;
						array_push($data4, $tempData4);
					}
			  	}
//				$origin[$catid][$posid] = array();
				foreach($v2['design'] as $base=>$a2){
//					$origin[$catid][$posid][$base] = array("silk"=>1, "digit"=>1);
					for($i=0; $i<count($a2); $i++){
						if($a2[$i]['ink']==0 && $opts['noprint']==0) continue;
						$printCode = $a2[$i]['printing'];
						$sizeFrom = $printCode!='silk'? 0: 35;
						$sizeTo = $printCode!='silk'? 0: 27;
						$ink = 0;
						if ($printCode=='silk') {
							$ink = $a2[$i]['ink']==9 ? "4" : $a2[$i]['ink'];
						}
						$tempData7 = "0|".$idx6."|". $a2[$i]['areakey']."|".$a2[$i]['categorytype']."/".$a2[$i]['itemtype']."|1|".$ink."|".$printCode."|".$sizeFrom."|".$sizeTo."|".$a2[$i]['areasize']."|0|0|1|".(empty($opts['illust'])? "": "イラレ")."||0|1";
						array_push($orderarea , $tempData7);
						if($a2[$i]['ink']>0){
							$data8[$idx8]['area_id'] = $idx7;
							$data8[$idx8]['selective_key'] = $a2[$i]['poskey'];
							$data8[$idx8]['selective_name'] = $a2[$i]['posname'];
							$idx8++;
							$tempData8 ="0|".$idx7."|".$a2[$i]['poskey']."|".$a2[$i]['posname'];
							array_push($orderselectivearea , $tempData8);
						}
						$idx7++;
						if($opts['noprint']==1){
							break 2;
						}
					}
				}
				$idx6++;
			}
		}
		$field9 = array("inkid", "area_id", "ink_name", "ink_code", "ink_position");
		$orderink = array();
	  	$field10= array("exchid","ink_id","exchink_name","exchink_code","exchink_volume");
	  	$exchink = array();
	  	$field12 = array();
	  	$data12 = array();

		//添付ファイル
		for($i=0;$i<count($attach);$i++){
			$file[$i] = $attach[$i]['img']['file'];
			$filename[$i] = $attach[$i]['img']['name'];
		}

		//管理システムにpost
		$res = $httpObj->request('POST', array('act'=>'insert', 'mode'=>'order', 'field1'=>$field1, 'data1'=>$data1, 'field2'=>$field2, 'data2'=>$data2,
				'field3'=>$field3, 'data3'=>$data3, 'field4'=>$field4, 'data4'=>$data4, 'field5'=>$field5, 'data5'=>$data5,
				'field6'=>$field6, 'data6'=>$orderprint, 'field7'=>$field7, 'data7'=>$orderarea,
				'field8'=>$field8, 'data8'=>$orderselectivearea, 'field9'=>$field9, 'data9'=>$orderink,
				'field10'=>$field10, 'data10'=>$exchink, 'field12'=>$field12, 'data12'=>$data12, 'file'=>$file, 'name'=>$filename,'site'=>_SITE));
		return $res;
	}
}
?>
