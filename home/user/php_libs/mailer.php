<?php
/*------------------------------------------------------------

	File_name    : maler.php
	Description  : resend password
	Charset      : euc-jp
	Log	    	 : 2011.07.01	created
				   
-------------------------------------------------------------- */

require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/config.php';

class Mailer{
/*
*	メール送信モジュール
*
*	仮パスワード：{newpass, email}
*	ブログ投稿：{subject, message, [attachfile]}
*	ユーザー登録：{uname, email}
*	アップロードのお知らせ：{customername, email, uploadfile[]}
*/
	private $info = array();
	
	public function __construct($info=null){
		$this->info = $info;
	}
	
	/**
	*	仮パスワードの送信
	*	@newpass
	*	@email
	*/
	public function send_pass(){
		try{
			// メール本文
			$mail_info = "このたびは、タカハマライフアートをご利用いただき誠にありがとうございます。\n\n";
			
			$mail_info .= "パスワードのお問合せを頂きましたので、ご連絡いたします。\n\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "新しいパスワード：".$this->info['newpass']."\n\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "※新しいパスワードの発行により、これまでご利用いただいていたパスワードは使えなくなりました。\n\n";
			
			$mail_info .= "※新いパスワードは仮のものになりますので、のちほど\n";
			$mail_info .= "『アカウントページ』より、覚えやすいパスワードに変更されることをおすすめします。\n\n";
			
			$mail_info .= "\n※ご不明な点やお気づきのことがございましたら、ご遠慮なくお問い合わせください。\n";
			$mail_info .= "■営業時間　9:30 - 18:00　　■定休日：　土日祝\n\n";
			$mail_info .= "━ タカハマライフアート ━━━━━━━━━━━━━━━━━━━━━━━\n\n";
			$mail_info .= "　Phone：　　"._OFFICE_TEL."\n";
			$mail_info .= "　E-Mail：　　"._INFO_EMAIL."\n";
			$mail_info .= "　Web site：　"._DOMAIN."/\n";
			$mail_info .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
			
			// 送信処理
			mb_language("japanese");
			$sendto = $this->info['email'];
			$title = "パスワードのご連絡";
			$subject = mb_encode_mimeheader(mb_convert_encoding($title,"JIS","EUC-JP"));
			$fromname = "タカハマライフアート";
			$from = mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","EUC-JP"))."<"._INFO_EMAIL.">";
			
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ここで本文をエンコード
			
			// メール送信
			$result = true;
			if(!mail($sendto, $subject, $msg, $header)){
				$result = false;
			}
			
			return $result;
			
		}catch (Exception $e) {
			return false;
		}
	}
	
	
	/**
	*	ブログの投稿記事を送信
	*	@subject
	*	@message
	*	@attachfile[]
	*/
	public function send_blogdata(){
		try{
			// 添付ファイルがある場合の処理
			$attach = array();
			$result = true;
			$t=0;
			$attach_count = count($_FILES['attachfile']['tmp_name']);
			for($i=0; $i<$attach_count; $i++) {
				$result = false;
				if( is_uploaded_file($_FILES['attachfile']['tmp_name'][$i]) ){
					if ($_FILES['attachfile']['error'][$i] == UPLOAD_ERR_OK){
						$filesize = $_FILES['attachfile']['size'][$i];
						$files_len += $filesize;
						
						if($files_len <= _MAXIMUM_SIZE){
							$uploadfile = file_get_contents($_FILES['attachfile']['tmp_name'][$i]);
							$attach[$t]['file'] = chunk_split(base64_encode($uploadfile));
							$attach[$t]['name'] = $_FILES['attachfile']['name'][$i];
							$attach[$t]['type'] = $_FILES['attachfile']['type'][$i];
							$attach[$t]['size'] = $filesize;
							$t++;
							$result = true;
						}
				    }
				}
				if(!$result) break;
			}
			if(!$result) return false;
			
			// メール本文
			$mail_info = "お客様ブログ投稿\n\n";
			
			$mail_info .= "■受注No. :　".$this->info['orders_id']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "■顧客名 :　".$this->info['customername']."\n";
			$mail_info .= "■E-Mail :　".$this->info['email']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "■コメント\n";
			$mail_info .= $this->info['message']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "■添付ファイル\n";
			$attach_count = count($attach);
			if($attach_count==0){
				$mail_info .= "　なし\n";
			}else{
				for($i=0; $i<$attach_count; $i++){
					$mail_info .= "　".$attach[$i]['name']."\n";
				}
			}
			$mail_info .= "----------------------------------------\n\n";
			
			// 送信処理
			mb_language("japanese");
			$sendto = _INFO_EMAIL;
			$title = "お客さまブログ投稿";
			$subject = mb_encode_mimeheader(mb_convert_encoding($title,"JIS","EUC-JP"));
			$fromname = "タカハマライフアート";
			$from = mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","EUC-JP"))."<"._INFO_EMAIL.">";
			$msg = "";
			$boundary = md5(uniqid(rand()));
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
			
			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ここで本文をエンコード
			
			if($attach_count>0){		// 添付ファイル情報
				for($i=0; $i<$attach_count; $i++){
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
			
			// メール送信
			$result = true;
			if(mail($sendto, $subject, $msg, $header)){
				// 自動返信メール
				$sendto = $this->info['email'];
				$title = "お客さまブログへの投稿ありがとうございます";
				$subject = mb_encode_mimeheader(mb_convert_encoding($title,"JIS","EUC-JP"));
				$fromname = "タカハマライフアート";
				$from = mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","EUC-JP"))."<"._INFO_EMAIL.">";
				
				$header = "From: $from\n";
				$header .= "Reply-To: $from\n";
				$header .= "X-Mailer: PHP/".phpversion()."\n";
				$header .= "MIME-version: 1.0\n";
				$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
				$header .= "Content-Transfer-Encoding: 7bit\n";
				
				$msg = $this->info['customername']."　様\n";
				$msg .= "お世話になっております。タカハマライフアートでございます。\n\n";
				
				$msg .= "お客様ブログへのご投稿、誠にありがとうございました。\n";
				$msg .= "いただいた原稿は、弊社で確認後、下記ブログにアップさせていただきます。\n\n";
				
				$msg .= "「お客様の声」\n";
				$msg .= "http://www.takahama428.com/blog/thanks_blog/\n\n";
				
				$msg .= "お客様のお声が聞けるのが、私たちの何よりの喜びです。\n\n";
				
				$msg .= "また何かお力添えが出来ることがございましたら、\n";
				$msg .= "お気軽にお声かけくださいませ。\n\n";
				
				$msg .= "スタッフ一同、心よりお待ちしております。\n\n";
				
				
				// 休業の告知文を挿入
				$msg .= mb_convert_encoding(_NOTICE_HOLIDAY,"euc-jp","utf-8");
				
				
				$msg .= "\n※ご不明な点やお気づきのことがございましたら、ご遠慮なくお問い合わせください。\n";
				$msg .= "■営業時間　9:30 - 18:00　　■定休日：　土日祝\n\n";
				$msg .= "━ タカハマライフアート ━━━━━━━━━━━━━━━━━━━━━━━\n\n";
				$msg .= "　Phone：　　"._OFFICE_TEL."\n";
				$msg .= "　E-Mail：　　"._INFO_EMAIL."\n";
				$msg .= "　Web site：　"._DOMAIN."/\n";
				$msg .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
				
				$msg = mb_convert_encoding($msg,"JIS","EUC-JP");
				
				$result = mail($sendto, $subject, $msg, $header);
			}else{
				$result = false;
			}
			
			return $result;
			
		}catch (Exception $e) {
			return false;
		}
	}

	/**
	*	ユーザー登録のお知らせ
	*	QUOカードのプレゼント
	*/
	public function send_registerd(){
		try{
			// メール本文
			$mail_info = "【　お客様登録　】\n\n";
			$mail_info .= "■お名前：　".mb_convert_encoding($this->info['uname'],"EUC-JP","UTF-8")." 様\n";
			$mail_info .= "■E-Mail：　".$this->info['email']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "登録日: ".date('Y-m-d H:i:s')."\n\n";
			
			
			// 送信処理
			$sendto = 'takahamaushida@gmail.com';
						
			$subject = "お客様登録のお知らせ";	// 件名
			$msg = "";											// 送信文
			$boundary = md5(uniqid(rand())); 					// バウンダリー文字（メールメッセージと添付ファイルの境界とする文字列を設定）
			
			$fromname = "タカハマ428";
			$from = mb_encode_mimeheader($fromname)."<"._INFO_EMAIL.">";
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ここで注文情報をエンコードして設定
			
			// 件名のマルチバイトをエンコード
			$subject  = mb_encode_mimeheader($subject);
			
			// メール送信
			if(mail($sendto, $subject, $msg, $header)){
				$result = true;
			}else{
				$result = false;
			}
			
			return $result;
			
		}catch (Exception $e) {
			return false;
		}
	}


	/**
	* デザインデータのアップロードを通知する
	* /user/uploader.php
	* @return {boolean} true:成功　false:失敗
	*/
	public function send_upload_info(){
		try{
			define("_QUERY_STRING", "?auth=admin");
			mb_language("japanese");
			mb_internal_encoding("EUC-JP");
			
			if (empty($this->info['customername']) || empty($this->info['email'])) {
				throw new Exception();
			}
			
			mb_convert_variables('euc-jp', 'utf-8', $this->info);
				
			// メール本文
			$mail_info = "【　デザインファイルのアップロード　】\n\n";
			$mail_info .= "■お名前：　".$this->info['customername']." 様\n";
			$mail_info .= "■E-Mail：　".$this->info['email']."\n";
			$mail_info .= "----------------------------------------\n\n";

			$mail_info .= "アップロードファイル: \n";
			for ($i=0; $i<count($this->info['uploadfile']); $i++) {
				$mail_info .= ($i+1).",\n";
				$mail_info .= $this->info['uploadfile'][$i]._QUERY_STRING."\n\n";
			}
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "■メッセージ：\n";
			$mail_info .= $this->info['message']."\n\n";
			
			// 送信処理
			$sendto = _INFO_EMAIL;

			$subject = "デザインアップロード";	// 件名
			$msg = "";							// 送信文
			$boundary = md5(uniqid(rand())); 	// バウンダリー文字（メールメッセージと添付ファイルの境界とする文字列を設定）

			$fromname = "タカハマ428";
			$from = mb_encode_mimeheader($fromname, "JIS")."<"._INFO_EMAIL.">";
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";

			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ここで注文情報をエンコードして設定

			// 件名のマルチバイトをエンコード
			$subject  = mb_encode_mimeheader($subject, "JIS");

			// メール送信
			if(mail($sendto, $subject, $msg, $header)){
				$result = true;
			}else{
				$result = false;
			}

			return $result;

		}catch (Exception $e) {
			return false;
		}
	}
}
?>
