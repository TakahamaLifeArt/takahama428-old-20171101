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
*	�᡼�������⥸�塼��
*
*	���ѥ���ɡ�{newpass, email}
*	�֥���ơ�{subject, message, [attachfile]}
*	�桼������Ͽ��{uname, email}
*	���åץ��ɤΤ��Τ餻��{customername, email, uploadfile[]}
*/
	private $info = array();
	
	public function __construct($info=null){
		$this->info = $info;
	}
	
	/**
	*	���ѥ���ɤ�����
	*	@newpass
	*	@email
	*/
	public function send_pass(){
		try{
			// �᡼����ʸ
			$mail_info = "���Τ��Ӥϡ������ϥޥ饤�ե����Ȥ����Ѥ����������ˤ��꤬�Ȥ��������ޤ���\n\n";
			
			$mail_info .= "�ѥ���ɤΤ���礻��ĺ���ޤ����Τǡ���Ϣ�������ޤ���\n\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "�������ѥ���ɡ�".$this->info['newpass']."\n\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "���������ѥ���ɤ�ȯ�Ԥˤ�ꡢ����ޤǤ����Ѥ��������Ƥ����ѥ���ɤϻȤ��ʤ��ʤ�ޤ�����\n\n";
			
			$mail_info .= "�������ѥ���ɤϲ��Τ�Τˤʤ�ޤ��Τǡ��Τ��ۤ�\n";
			$mail_info .= "�إ�������ȥڡ����٤�ꡢ�Ф��䤹���ѥ���ɤ��ѹ�����뤳�Ȥ򤪤����ᤷ�ޤ���\n\n";
			
			$mail_info .= "\n�������������䤪���Ť��Τ��Ȥ��������ޤ����顢����θ�ʤ����䤤��碌����������\n";
			$mail_info .= "���ĶȻ��֡�9:30 - 18:00���������������������\n\n";
			$mail_info .= "�� �����ϥޥ饤�ե����� ����������������������������������������������\n\n";
			$mail_info .= "��Phone������"._OFFICE_TEL."\n";
			$mail_info .= "��E-Mail������"._INFO_EMAIL."\n";
			$mail_info .= "��Web site����"._DOMAIN."/\n";
			$mail_info .= "������������������������������������������������������������������\n";
			
			// ��������
			mb_language("japanese");
			$sendto = $this->info['email'];
			$title = "�ѥ���ɤΤ�Ϣ��";
			$subject = mb_encode_mimeheader(mb_convert_encoding($title,"JIS","EUC-JP"));
			$fromname = "�����ϥޥ饤�ե�����";
			$from = mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","EUC-JP"))."<"._INFO_EMAIL.">";
			
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ��������ʸ�򥨥󥳡���
			
			// �᡼������
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
	*	�֥�����Ƶ���������
	*	@subject
	*	@message
	*	@attachfile[]
	*/
	public function send_blogdata(){
		try{
			// ź�եե����뤬������ν���
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
			
			// �᡼����ʸ
			$mail_info = "�����֥ͥ����\n\n";
			
			$mail_info .= "������No. :��".$this->info['orders_id']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "���ܵ�̾ :��".$this->info['customername']."\n";
			$mail_info .= "��E-Mail :��".$this->info['email']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "��������\n";
			$mail_info .= $this->info['message']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "��ź�եե�����\n";
			$attach_count = count($attach);
			if($attach_count==0){
				$mail_info .= "���ʤ�\n";
			}else{
				for($i=0; $i<$attach_count; $i++){
					$mail_info .= "��".$attach[$i]['name']."\n";
				}
			}
			$mail_info .= "----------------------------------------\n\n";
			
			// ��������
			mb_language("japanese");
			$sendto = _INFO_EMAIL;
			$title = "���Ҥ��ޥ֥����";
			$subject = mb_encode_mimeheader(mb_convert_encoding($title,"JIS","EUC-JP"));
			$fromname = "�����ϥޥ饤�ե�����";
			$from = mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","EUC-JP"))."<"._INFO_EMAIL.">";
			$msg = "";
			$boundary = md5(uniqid(rand()));
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			if(!empty($attach)){ 		// ź�եե����뤬����
				$header .= "Content-Type: multipart/mixed;\n";
				$header .= "\tboundary=\"$boundary\"\n";
				$msg .= "This is a multi-part message in MIME format.\n\n";
				$msg .= "--$boundary\n";
				$msg .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
				$msg .= "Content-Transfer-Encoding: 7bit\n\n";
			}else{												// ź�եե�����ʤ�
				$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
				$header .= "Content-Transfer-Encoding: 7bit\n";
			}
			
			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ��������ʸ�򥨥󥳡���
			
			if($attach_count>0){		// ź�եե��������
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
			
			// �᡼������
			$result = true;
			if(mail($sendto, $subject, $msg, $header)){
				// ��ư�ֿ��᡼��
				$sendto = $this->info['email'];
				$title = "���Ҥ��ޥ֥��ؤ���Ƥ��꤬�Ȥ��������ޤ�";
				$subject = mb_encode_mimeheader(mb_convert_encoding($title,"JIS","EUC-JP"));
				$fromname = "�����ϥޥ饤�ե�����";
				$from = mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","EUC-JP"))."<"._INFO_EMAIL.">";
				
				$header = "From: $from\n";
				$header .= "Reply-To: $from\n";
				$header .= "X-Mailer: PHP/".phpversion()."\n";
				$header .= "MIME-version: 1.0\n";
				$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
				$header .= "Content-Transfer-Encoding: 7bit\n";
				
				$msg = $this->info['customername']."����\n";
				$msg .= "�����äˤʤäƤ���ޤ��������ϥޥ饤�ե����ȤǤ������ޤ���\n\n";
				
				$msg .= "�����֥ͥ��ؤΤ���ơ����ˤ��꤬�Ȥ��������ޤ�����\n";
				$msg .= "�������������Ƥϡ����Ҥǳ�ǧ�塢�����֥��˥��åפ����Ƥ��������ޤ���\n\n";
				
				$msg .= "�֤����ͤ�����\n";
				$msg .= "http://www.takahama428.com/blog/thanks_blog/\n\n";
				
				$msg .= "�����ͤΤ�����ʹ����Τ����䤿���β����δ�ӤǤ���\n\n";
				
				$msg .= "�ޤ���������ź��������뤳�Ȥ��������ޤ����顢\n";
				$msg .= "�����ڤˤ����������������ޤ���\n\n";
				
				$msg .= "�����åհ�Ʊ������ꤪ�Ԥ����Ƥ���ޤ���\n\n";
				
				
				// �ٶȤι���ʸ������
				$msg .= mb_convert_encoding(_NOTICE_HOLIDAY,"euc-jp","utf-8");
				
				
				$msg .= "\n�������������䤪���Ť��Τ��Ȥ��������ޤ����顢����θ�ʤ����䤤��碌����������\n";
				$msg .= "���ĶȻ��֡�9:30 - 18:00���������������������\n\n";
				$msg .= "�� �����ϥޥ饤�ե����� ����������������������������������������������\n\n";
				$msg .= "��Phone������"._OFFICE_TEL."\n";
				$msg .= "��E-Mail������"._INFO_EMAIL."\n";
				$msg .= "��Web site����"._DOMAIN."/\n";
				$msg .= "������������������������������������������������������������������\n";
				
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
	*	�桼������Ͽ�Τ��Τ餻
	*	QUO�����ɤΥץ쥼���
	*/
	public function send_registerd(){
		try{
			// �᡼����ʸ
			$mail_info = "�ڡ���������Ͽ����\n\n";
			$mail_info .= "����̾������".mb_convert_encoding($this->info['uname'],"EUC-JP","UTF-8")." ��\n";
			$mail_info .= "��E-Mail����".$this->info['email']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "��Ͽ��: ".date('Y-m-d H:i:s')."\n\n";
			
			
			// ��������
			$sendto = 'takahamaushida@gmail.com';
						
			$subject = "��������Ͽ�Τ��Τ餻";	// ��̾
			$msg = "";											// ����ʸ
			$boundary = md5(uniqid(rand())); 					// �Х�����꡼ʸ���ʥ᡼���å�������ź�եե�����ζ����Ȥ���ʸ����������
			
			$fromname = "�����ϥ�428";
			$from = mb_encode_mimeheader($fromname)."<"._INFO_EMAIL.">";
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ��������ʸ����򥨥󥳡��ɤ�������
			
			// ��̾�Υޥ���Х��Ȥ򥨥󥳡���
			$subject  = mb_encode_mimeheader($subject);
			
			// �᡼������
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
	* �ǥ�����ǡ����Υ��åץ��ɤ����Τ���
	* /user/uploader.php
	* @return {boolean} true:������false:����
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
				
			// �᡼����ʸ
			$mail_info = "�ڡ��ǥ�����ե�����Υ��åץ��ɡ���\n\n";
			$mail_info .= "����̾������".$this->info['customername']." ��\n";
			$mail_info .= "��E-Mail����".$this->info['email']."\n";
			$mail_info .= "----------------------------------------\n\n";

			$mail_info .= "���åץ��ɥե�����: \n";
			for ($i=0; $i<count($this->info['uploadfile']); $i++) {
				$mail_info .= ($i+1).",\n";
				$mail_info .= $this->info['uploadfile'][$i]._QUERY_STRING."\n\n";
			}
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "����å�������\n";
			$mail_info .= $this->info['message']."\n\n";
			
			// ��������
			$sendto = _INFO_EMAIL;

			$subject = "�ǥ����󥢥åץ���";	// ��̾
			$msg = "";							// ����ʸ
			$boundary = md5(uniqid(rand())); 	// �Х�����꡼ʸ���ʥ᡼���å�������ź�եե�����ζ����Ȥ���ʸ����������

			$fromname = "�����ϥ�428";
			$from = mb_encode_mimeheader($fromname, "JIS")."<"._INFO_EMAIL.">";
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";

			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ��������ʸ����򥨥󥳡��ɤ�������

			// ��̾�Υޥ���Х��Ȥ򥨥󥳡���
			$subject  = mb_encode_mimeheader($subject, "JIS");

			// �᡼������
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
