<?php
/*------------------------------------------------------------

	File_name    : maler.php
	Description  : takahama428 web site send mail class
	Charset      : euc-jp
	Created      : 2011.03.26
	Log			 : 2012.08.12 ���󥱡�������
				   2013.02.01 ���Ѥ���䤤��碌
				   2013.08.09 ����礻���ܤΥ����å����ɲ�
				   2014.01.23 �ߥ�T�����ڡ���Τ��������ߤ��ɲ�
				   2014.02.04 ��ƻ�ܸ����̤ˤ���
				   2014.08.05 ��ĥ�Ǥ���碌�ե�������б�
				   2015.02.18 �����õޥץ��Τ���礻
				   2015.04.01 ���ꥸ�ʥ륿���뤪�䤤��碌
				   2015.05.13 �ǥ����󥳥󥷥��른��
 				   2015.06.19 �����ʸ���䤤��碌
				   2016.01.28 ��Ǻ�ߡ����ե�����
				   2016.11.08 ���󥱡��ȹ����ѹ�
				   2017.07.05 �����С��ܹԤ�ȼ��ʸ������������򹹿�
				   
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
 *		['attachfile'][]	ʣ���б��Τ�������
 *		
 */
	private $info = array();
	
	public function __construct($info=null){
		$this->info = $info;
	}
	
	public function send(){
		try{
			$titles = array(
				'info'=>'���䤤��碌',
				'request'=>'��������', 
				'estimate'=>'��������礻',
				'express'=>'���ޤ��������礻',
				'test'=>'�ƥ���',
				'minit'=>'��˥ե�����ߥ�T����������',
				'illusttemplate'=>'��������ƥƥ�ץ졼��',
				'visit'=>'��ĥ�Ǥ���碌',
				'expresstoday'=>'�����õޥץ��',
				'towel'=>'���ꥸ�ʥ륿���뤪�䤤��碌',
				'designconsierge'=>'�ǥ����󥳥󥷥��른��',
				'bigorder'=>'�����ʸ���䤤��碌',
				'quickcontact'=>'��Ǻ�߲��ե�����',
				'orange'=>'���󤸷��������åפ���������',
			);
			// ź�եե����뤬������ν���
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
							$result_msg = 'ź�եե����륵������20MB�ޤǤˤ��Ʋ�������';
						}else{
							$uploadfile = file_get_contents($tmp_path);
							$img_encode64 = chunk_split(base64_encode($uploadfile));
							
					      	$result = true;
						}
						    
				    }else{
				     	$result_msg = 'ź�եե�����Υ��åץ�����˥��顼�Ǥ���ź�եե�����λ������ľ���Ƥ���������';
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
					$mail_info .= "���Ҥ����Ƥ��ǧ�塢�����Ѥ�Υ᡼������ꤤ�����ޤ���\n";
					$mail_info .= "����ǧ�Τۤɤ�������ꤤ�������ޤ���\n\n\n";
				}
				$mail_info .= "�ڡ�".$titles[$this->info['title']]."����\n";
				$mail_info .= "����̾������".$this->info['customername']." ��\n";
				if(isset($this->info['company'])){
					$mail_info .= "�����̾����".$this->info['company']." ��\n";
				}
				if(isset($this->info['ruby'])){
					$mail_info .= "���եꥬ�ʡ���".$this->info['ruby']." ��\n";
				}else if(isset($this->info['customerruby'])){
					$mail_info .= "���եꥬ�ʡ���".$this->info['customerruby']." ��\n";
				}
				$mail_info .= "��E-Mail����".$this->info['email']."\n";
				$mail_info .= "��TEL����".$this->info['tel']."\n\n";
				
				if($this->info['title']=='request' || $this->info['title']=='express' || $this->info['title']=='expresstoday' || $this->info['title']=='test' || $this->info['title']=='towel' || $this->info['title']=='orange'){
					$mail_info .= "�����Ϥ��褴���ꡧ\n";
					$mail_info .= "��".$this->info['zipcode']."\n";
					$mail_info .= $this->info['addr0']."\n";
					$mail_info .= $this->info['addr1']."\n";
					$mail_info .= $this->info['addr2']."\n";
					
					if($this->info['title']=='request'){
						if(!empty($this->info['requestplan'])){
							$mail_info .= "\n������ץ�󡧡�\n".implode("\n", $this->info['requestplan']);
							$mail_info .= "\n";
						}
						
						// 2014-08-20 ���������ڡ���
						if(empty($this->info['sample'])){
							$mail_info .= "\n�����ʥ���ץ롧���ʤ�\n";
						}else{
							$mail_info .= "\n�����ʥ���ץ롧����˾���ޤ�\n";
						}
					}
					$mail_info .= "��----------------------------------------\n\n";
				}
				
				if($this->info['title']=='bigorder' || $this->info['title']=='quickcontact' || $this->info['title']=='expresstoday'){
					if(empty($this->info['deliveryday'])) $this->info['deliveryday'] = '�ʤ�';
					$mail_info .= "\n������˾Ǽ������ ".$this->info['deliveryday']."\n";
				}
				if($this->info['title']=='bigorder' || $this->info['title']=='quickcontact'){
					$categories = array(""=>"����ʤ�",
										"t-shirts"=>"�ԥ����",
                                        "polo-shirts"=>"�ݥ����",
                                        "sweat"=>"�������åȡ��ѡ�����",
                                        "long-shirts"=>"��󥰣ԥ����",
                                        "sportswear"=>"���ݡ��ĥ�����",
                                        "ladys"=>"��ǥ�����",
                                        "outer"=>"��������",
                                        "overall"=>"�Ĥʤ�",
                                        "baby"=>"�٥ӡ�",
                                        "cap"=>"����åס��Х����",
                                        "tote-bag"=>"�Хå�",
                                        "apron"=>"���ץ��",
                                        "towel"=>"������",
                                        "workwear"=>"���������",
                                        "goods"=>"�ץ쥼��ȡ����å�");
					$mail_info .= "�����ʥ��ƥ��꡼���� ".$categories[$this->info['category']]."\n";
					$mail_info .= "��������� ".$this->info['amount']." ��\n";
					
					$mail_info .= "�����Ϥ��衧�� ";
					if($this->info['title']=='quickcontact' || $this->info['title']=='info'){
						if($this->info['place']==1){
							$mail_info .= "������ã���ϰ�\n";
						}else{
							$mail_info .= "�⡹����ã���ϰ�\n";
						}
				
					}else{
						if(empty($this->info['pref'])){
							$mail_info .= "����ʤ�\n";
						}else{
							$mail_info .= $this->info['pref']."\n";
						}
					}
				}
	

				if($this->info['title']=='express'){
					if(empty($this->info['deliveryday'])) $this->info['deliveryday'] = '�ʤ�';
					$mail_info .= "\n������˾Ǽ������ ".$this->info['deliveryday']."\n";
					
					$categories = array(""=>"����ʤ�",
										"t-shirts"=>"�ԥ����",
                                        "polo-shirts"=>"�ݥ����",
                                        "sweat"=>"�������åȡ��ѡ�����",
                                        "long-shirts"=>"��󥰣ԥ����",
                                        "sportswear"=>"���ݡ��ĥ�����",
                                        "ladys"=>"��ǥ�����",
                                        "outer"=>"��������",
                                        "overall"=>"�Ĥʤ�",
                                        "baby"=>"�٥ӡ�",
                                        "cap"=>"����åס��Х����",
                                        "tote-bag"=>"�Хå�",
                                        "apron"=>"���ץ��",
                                        "towel"=>"������",
                                        "workwear"=>"���������",
                                        "goods"=>"�ץ쥼��ȡ����å�");
					$mail_info .= "�����ʥ��ƥ��꡼���� ".$categories[$this->info['category']]."\n";
					$mail_info .= "��������� ".$this->info['amount']." ��\n";
				

				}
				if($this->info['title']=='express' || $this->info['title']=='quickcontact'){
					$mail_info .= "���ץ��Ⱦ���\n";
					$mail_info .= $this->info['printinfo']."\n\n";
				}else if($this->info['title']=='minit'){
					$mail_info .= "�����Ҥ����ѤˤĤ��ơ�\n";
					$mail_info .= $this->info['repeater']."\n\n";
				}

				if($this->info['title']=='quickcontact'){
					$mail_info .= "�������ƥ���˾��\n";
					$mail_info .= $this->info['iteminfo']."\n\n";
				}else if($this->info['title']=='minit'){
					$mail_info .= "�����Ҥ����ѤˤĤ��ơ�\n";
					$mail_info .= $this->info['repeater']."\n\n";
				}

				if($this->info['title']=='quickcontact' || $this->info['title']=='info'){
					$mail_info .= "\n������˾Ǽ������ ".$this->info['deliveryday']."\n";
					$mail_info .= "������˾��\n";
					$mail_info .= $this->info['deriinfo']."\n\n";
				}else if($this->info['title']=='minit'){
					$mail_info .= "�����Ҥ����ѤˤĤ��ơ�\n";
					$mail_info .= $this->info['repeater']."\n\n";
				}
				if($this->info['title']=='orange'){
					$mail_info .= "�����ÿͿ�����͡���".$this->info['amount1']."��\n";
					$mail_info .= "�����ÿͿ������͡� ".$this->info['amount2']."��\n";
					$mail_info .= "��----------------------------------------\n\n";
				}

				
				if($this->info['title']=='bigorder'){
					$mail_info .= "�����������ӡ�����".implode(', ', $this->info['youto'])."\n";
					$mail_info .= "������������� ".$this->info['vol']."\n";
					$mail_info .= "��----------------------------------------\n\n";
				}
				
				if(!empty($this->info['subtitle'])){
					$subtext = "��[ ".implode(', ', $this->info['subtitle'])." ]";
				}
				
				if(isset($this->info['subject'])){
					$mail_info .= "\n���̾���� ".$this->info['subject'].$subtext."\n\n";
				}
				
				if($this->info['title']=='estimate' || $this->info['title']=='test'){
					for($i=0; $i<count($this->info['itemname']); $i++){
						$mail_info .= "�����ʡ��� ".$this->info['itemname'][$i]."\n";
						$mail_info .= "������������ ".$this->info['itemsize'][$i]."\n";
						$mail_info .= "��������� ".$this->info['amount'][$i]." ��\n";
						$mail_info .= "���ץ��Ȱ��֤ȿ�����\n";
						$mail_info .= str_replace("|", "\n", $this->info['printinfo'][$i])."\n";
						$mail_info .= "--------------------\n\n";
					}
					$mail_info .= "�������塧�� ".$this->info['itemsum']."\n";
					$mail_info .= "���ץ����塧�� ".$this->info['printfee']."\n";
					$mail_info .= "����ס��� ".$this->info['total']."\n";
					$mail_info .= "��----------------------------------------\n\n";
				}
				
				if($this->info['title']=='visit'){
					$mail_info .= "��----------------------------------------\n\n";
					$mail_info .= "�����������ӡ�����".$this->info['purpose']."\n";
					$mail_info .= "�����Ϥ��衧����".$this->info['delivery']."\n";
					$mail_info .= "������˾�����ƥࡧ����".$this->info['category']."\n";
					$mail_info .= "���ǥ����󡧡���".$this->info['design']."\n";
					$mail_info .= "���ץ��Ȳսꡦ������\n";
					$mail_info .= $this->info['inks']."\n\n";
					$mail_info .= "��----------------------------------------\n\n";
					$mail_info .= "������Ƥ���������".$this->info['amount']." ��\n";
					$mail_info .= "����ͽ����1�礢����ˡ�����".$this->info['budget']." ��\n";
					$mail_info .= "���ǹ礻���ʱ�̾�ˡ�����".$this->info['place']."\n";
					$mail_info .= "����˾���֡�����".$this->info['meetingtime']."\n";
				}
				
				if($this->info['title']=='expresstoday'){
					$amount = 0;
					if(!empty($this->info['S_001'])){
						$amount += $this->info['S_001'];
						$mail_info .= "�����ʥ��顼���� �ۥ磻��\n";
						$mail_info .= "������������ S\n";
						$mail_info .= "��������� ".$this->info['S_001']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['M_001'])){
						$amount += $this->info['M_001'];
						$mail_info .= "�����ʥ��顼���� �ۥ磻��\n";
						$mail_info .= "������������ M\n";
						$mail_info .= "��������� ".$this->info['M_001']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['L_001'])){
						$amount += $this->info['L_001'];
						$mail_info .= "�����ʥ��顼���� �ۥ磻��\n";
						$mail_info .= "������������ L\n";
						$mail_info .= "��������� ".$this->info['L_001']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['XL_001'])){
						$amount += $this->info['XL_001'];
						$mail_info .= "�����ʥ��顼���� �ۥ磻��\n";
						$mail_info .= "������������ XL\n";
						$mail_info .= "��������� ".$this->info['XL_001']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					
					if(!empty($this->info['S_005'])){
						$amount += $this->info['S_005'];
						$mail_info .= "�����ʥ��顼���� �֥�å�\n";
						$mail_info .= "������������ S\n";
						$mail_info .= "��������� ".$this->info['S_005']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['M_005'])){
						$amount += $this->info['M_005'];
						$mail_info .= "�����ʥ��顼���� �֥�å�\n";
						$mail_info .= "������������ M\n";
						$mail_info .= "��������� ".$this->info['M_005']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['L_005'])){
						$amount += $this->info['L_005'];
						$mail_info .= "�����ʥ��顼���� �֥�å�\n";
						$mail_info .= "������������ L\n";
						$mail_info .= "��������� ".$this->info['L_005']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					if(!empty($this->info['XL_005'])){
						$amount += $this->info['XL_005'];
						$mail_info .= "�����ʥ��顼���� �֥�å�\n";
						$mail_info .= "������������ XL\n";
						$mail_info .= "��������� ".$this->info['XL_005']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					
					if(!empty($this->info['Free_001'])){
						$amount += $this->info['Free_001'];
						$mail_info .= "�����ʥ��顼���� �ۥ磻��\n";
						$mail_info .= "������������ Free\n";
						$mail_info .= "��������� ".$this->info['Free_001']." ��\n";
						$mail_info .= "--------------------\n\n";
					}
					
					if(!empty($this->info['noprint'])){
						$mail_info .= "���ץ��Ȥʤ��ǹ������롧��  �Ϥ�\n";
						$mail_info .= "--------------------\n\n";
					}
					$mail_info .= "������������ ".$amount." ��\n";
					$mail_info .= "��----------------------------------------\n\n";
					
					$mail_info .= "���ץ��Ȱ��֤ȿ��� \n";
					for($i=0; $i<count($this->info['printpos']); $i++){
						$pinfo = explode("_", $this->info['printpos'][$i]);
						$mail_info .= $pinfo[0].":��".$pinfo[1]."��\n";
					}
					$mail_info .= "��----------------------------------------\n\n";
				}
				
				if($this->info['title']=='towel'){
					$mail_info .= "��������μ��ࡧ�� ".$this->info['kinds']."\n";
					$mail_info .= "���ǤΥ��������� ".$this->info['size']."\n";
					$mail_info .= "�����󥯿������� ".$this->info['color']." ��\n";
					$mail_info .= "��������� ".$this->info['amount']." ��\n";
					$mail_info .= "��----------------------------------------\n\n";
				}
				
				if(isset($this->info['message'])){
					if($this->info['title']=='illusttemplate'){
						$mail_info .= "���ǥ�����Τ���˾��\n";
					}else if($this->info['title']=='visit'){
						$mail_info .= "�����������ơ�\n";
					}else{
						$mail_info .= "������礻���ơʥ�å������ˡ�\n";
					}
					$mail_info .= $this->info['message']."\n\n";
					$mail_info .= "��----------------------------------------\n\n";
				}
				
				if($this->info['title']=='designconsierge'){
					$mail_info .= "\n��������Ū��\n".$this->info['purpose']."\n\n";
					$mail_info .= "��----------------------------------------\n\n";
					
					$mail_info .= "\n���ǥ����󥤥᡼����\n".$this->info['design']."\n\n";
					$mail_info .= "��----------------------------------------\n\n";
					
					$mail_info .= "�����ʥ��ƥ��꡼���� ".$this->info['category']."\n";
					
					$mail_info .= "��������� ".$this->info['amount']." ��\n";
					
					$mail_info .= "\n���ץ��Ȳսꡧ\n".$this->info['print']."\n\n";
					$mail_info .= "��----------------------------------------\n\n";
				}
				
				$mail_info .= "��ź�եե����롧\n";
				$attach_count = count($attach);
				for($i=0; $i<$attach_count; $i++){
					$mail_info .= "��".$attach[$i]['name']."\n";
				}
				if($attach_count==0){
					$mail_info .= "���ʤ�\n";
				}
				$mail_info .= "\n��----------------------------------------\n\n";
				
				// ��������ǽ��̵꤬���������
				if($this->info['title']=='request' && empty($this->info['addr0'])){
					return false;
				}
				
				if($result && $this->info['title']=='request'){
					if ($this->info['subject']!='��������') {
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
	*	�ޥ��ڡ���������ɲ���ʸ
	*/
	public function send_repeat(){
		try{
			// ź�եե����뤬������ν���
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
							$result_msg = 'ź�եե����륵������20MB�ޤǤˤ��Ʋ�������';
						}else{
							$uploadfile = file_get_contents($tmp_path);
							$img_encode64 = chunk_split(base64_encode($uploadfile));
							
					      	$result = true;
						}
						    
				    }else{
				     	$result_msg = 'ź�եե�����Υ��åץ�����˥��顼�Ǥ���ź�եե�����λ������ľ���Ƥ���������';
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
				$mail_info .= "�ڡ��ɲ���ʸ�Τ��������ߡ���\n\n";
				$mail_info .= "������ʸNo.����".$this->info['orders_id']."\n";
				$mail_info .= "��----------------------------------------\n\n";
				
				$mail_info .= "����̾������".$this->info['customername']." ��\n";
				$mail_info .= "��E-Mail����".$this->info['email']."\n";
				$mail_info .= "��TEL����".$this->info['tel']."\n\n";
				$mail_info .= "�����Ϥ��衧��";
				if($this->info['addr0']!='' || $this->info['deli']=='1'){
					$mail_info .= "\n��".$this->info['zipcode']."\n";
					$mail_info .= $this->info['addr0']."\n";
					$mail_info .= $this->info['addr1']."\n";
					$mail_info .= $this->info['addr2']."\n";
				}else{
					$mail_info .= "��Ͽ����\n";
				}
				$mail_info .= "\n������˾Ǽ������ ".$this->info['deliveryday']."\n";
				$mail_info .= "������礻���ơ�\n";
				$mail_info .= $this->info['message']."\n\n";
				$mail_info .= "��----------------------------------------\n\n";
				
				$tot_amount = 0;
				for($i=0; $i<count($this->info['itemname']); $i++){
					$mail_info .= "�������ƥࡧ��".$this->info['itemname'][$i]."\n";
					$mail_info .= "�����顼����".$this->info['color'][$i]."\n";
					$mail_info .= "������������".$this->info['itemsize'][$i]."\n";
					$mail_info .= "���������".$this->info['amount'][$i]."\n";
					$mail_info .= "------------\n\n";
					$tot_amount += $this->info['amount'][$i];
				}
				$mail_info .= "������������".$tot_amount."\n";
				$mail_info .= "\n��----------------------------------------\n\n";
				
				$mail_info .= "��ź�եե����롧\n";
				$attach_count = count($attach);
				for($i=0; $i<$attach_count; $i++){
					$mail_info .= "��".$attach[$i]['name']."\n";
				}
				if($attach_count==0){
					$mail_info .= "���ʤ�\n";
				}
				$mail_info .= "\n��----------------------------------------\n\n";
				
				$result = $this->send_mail($mail_info, $this->info['customername'], $this->info['email'], $attach, $this->info['title']);
			}
			
			return $result;
			
		}catch (Exception $e) {
			return false;
		}
	}
	
	
	/**
	*	�᡼������
	*	@mail_text		����������ʸ
	*	@name			�����ͤ�̾��
	*	@to				�ֿ���Υ᡼�륢�ɥ쥹
	*	@attach			ź�եե��������
	*	@mailuser		����������դ��륢�ɥ쥹�Υ桼������ default: info
	*	
	*	define('_INFO_EMAIL', 'info@takahama428.com');
	*	define('_REQUEST_EMAIL', 'request@takahama428.com');
	*	define('_ESTIMATE_EMAIL', 'estimate@takahama428.com');
	*	define('_ORDER_EMAIL', 'order@takahama428.com');
	*
	*	�֤���			true:�������� , false:��������
	*/
	protected function send_mail($mail_text, $name, $to, $attach, $mailuser='info'){
		mb_language("japanese");
		mb_internal_encoding("EUC-JP");
		
		switch($mailuser){
		case 'request':	$receiver = _INFO_EMAIL;
						$subtitle = '��������';
						break;
		case 'estimate':$receiver = _ESTIMATE_EMAIL;
						$subtitle = '�����Ѥ�Τ��䤤��碌';
						break;
		case 'express':	$receiver = _ORDER_EMAIL;
						$subtitle = '���ޤ��������礻';
						break;
		case 'minit':	$receiver = _ORDER_EMAIL;
						$subtitle = '��˥ե�����ߥ�T����������';
						break;
		case 'illusttemplate': 
						$receiver = _INFO_EMAIL;
						$subtitle = '��������ƥƥ�ץ졼��';
						break;
		case 'repeat':	$receiver = _INFO_EMAIL;
						$subtitle = '�ɲ���ʸ�Τ���������';
						break;
		case 'visit':	$receiver = _INFO_EMAIL;
						$subtitle = '��ĥ�Ǥ���碌ͽ�󡦿�����';
						break;
		case 'expresstoday':
						$receiver = _ORDER_EMAIL;
						$subtitle = '�����õޥץ���䤤��碌';
						break;
		case 'towel':	$receiver = _ORDER_EMAIL;
						$subtitle = '���ꥸ�ʥ륿���뤪�䤤��碌';
						break;
		case 'designconsierge':	
						$receiver = _INFO_EMAIL;
						$subtitle = '�ǥ����󥳥󥷥��른��';
						break;
		case 'towel':	$receiver = _INFO_EMAIL;
						$subtitle = '�����ʸ���䤤��碌';
						break;
		case 'quickcontact':	$receiver = _INFO_EMAIL;
						$subtitle = '��Ǻ�߲��ե�����';
						break;
		case 'test':	$receiver = "test@takahama428.com";	// debug
						$subtitle = '�ƥ�������';
						break;
		case 'orange':$receiver = _INFO_EMAIL;
						$subtitle = '���󤸷��������åפ���������';
						break;
		default :		$receiver = _INFO_EMAIL;
						$subtitle = '���䤤��碌';
						break;	

		}
		
		$sendto = $receiver;
		$suffix = "�ڥ��ꥸ�ʥ�T����Ĳ���takahama428�ۡ�"; // ��̾�θ����ղä���ƥ�����
		$subject = $subtitle.$suffix;						// ��̾
		$msg = "";											// ����ʸ
		$boundary = md5(uniqid(rand())); 					// �Х�����꡼ʸ���ʥ᡼���å�������ź�եե�����ζ����Ȥ���ʸ����������
		
		$fromname = "�����ϥ�428";
		$from = mb_encode_mimeheader($fromname,"JIS")."<".$sendto.">";
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
		
		$msg .= mb_convert_encoding($mail_text,"JIS","EUC-JP");	// ��������ʸ����򥨥󥳡��ɤ�������
		
		if(!empty($attach)){		// ź�եե��������
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
		
		// ��̾�Υޥ���Х��Ȥ򥨥󥳡���
		$subject  = mb_encode_mimeheader($subject,"JIS");
		
		// �᡼������
		if(mail($sendto, $subject, $msg, $header)){
			// ��ư�ֿ��᡼��
			$sendto = $to;
			
			if($mailuser!="designconsierge"){
				$title = $subtitle."���꤬�Ȥ��������ޤ�";
			}else{
				$title = $subtitle."�Τ����Ѥ��꤬�Ȥ��������ޤ�";
			}
			$subject = mb_encode_mimeheader($title,"JIS");
			$from = $receiver;
			$fromname = "�����ϥޥ饤�ե�����";
			$from = mb_encode_mimeheader($fromname,"JIS")."<".$from.">";
			
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg = $name."����\n";
			$msg .= "���Τ��Ӥϡ������ϥޥ饤�ե����Ȥ����Ѥ����������ˤ��꤬�Ȥ��������ޤ���\n";
			if($mailuser!="designconsierge"){
				$msg .= "�ʲ������Ƥ�".$subtitle."����դ������ޤ�����\n";
			}
			$msg .= "\n";
			$msg .= $mail_text;
			
			
			// �ٶȤι���ʸ������
			$msg .= mb_convert_encoding(_NOTICE_HOLIDAY,"JIS","utf-8");
			
			// �׻��ι���ʸ������
			$msg .= mb_convert_encoding(_EXTRA_NOTICE,"JIS","utf-8");
			
			$msg .= "\n�������������䤪���Ť��Τ��Ȥ��������ޤ����顢����θ�ʤ����䤤��碌����������\n";
			$msg .= "���ĶȻ��֡�10:00 - 18:00���������������������\n\n";
			$msg .= "�� �����ϥޥ饤�ե����� ����������������������������������������������\n\n";
			$msg .= "��Phone������"._OFFICE_TEL."\n";
			$msg .= "��E-Mail������"._INFO_EMAIL."\n";
			$msg .= "��Web site����"._DOMAIN."/\n";
			$msg .= "������������������������������������������������������������������\n";
			
			$msg = mb_convert_encoding($msg,"JIS","EUC-JP");
			
			$res = mail($sendto, $subject, $msg, $header);
			
			return $res;	// ����
			
		}else{
			return false;	// ����
		}
	}
	
	
	
	
	/**
	*	���󥱡��Ȥ�����
	*/
	public function send_enquete(){
		try{
			// �������β���
			$a1 = array("","�ȤƤ�ʬ��ˤ����ä�","ʬ��ˤ����ä�","����","ʬ��䤹���ä�","�ȤƤ�ʬ��䤹���ä�");
			$a3 = array("�����԰¤�̵���ä�","�԰¤���ʬ�����ä�");
			$a5 = array("","�ȤƤⰭ���ä�","�����ä�","����","�ɤ��ä�","�ȤƤ��ɤ��ä�");
			$a6 = array("","�������᡼���̤�ǤϤʤ��ä�","���᡼�����Ƥ�����갭���ä�","����","���᡼���̤��ɤ��ä�","���᡼���ʾ���ɤ��ä�");
			$a7 = array("","�ȤƤⰭ���ä�","�����ä�","����","�ɤ��ä�","�ȤƤ��ɤ��ä�");
			$a14 = array("","����¾","2���ܰʹߤι���","���ߥʡ��ֱ��","�����ʹ����������","�Τ�礤�ξҲ�","���󥿡��ͥåȸ���");
			$number = 'K'.str_pad($this->info['number'], 6, '0', STR_PAD_LEFT);
			$zipcode = str_replace('-', ''. mb_convert_kana($this->info['zipcode'], 'a'));
			
			// �᡼����ʸ
			$mail_info = "�ڡ������ͥ��󥱡��ȡ���\n\n";
			$mail_info .= "���ܵ�ID����K".$number."\n";
			/*
			*	2013-12-31 �ѻ�
			*
			$mail_info .= "����̾������".$this->info['customername']." ��\n";
			$mail_info .= "�������ꡧ����".$this->info['zipcode']." ".$this->info['addr']."\n";
			*/
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "��Q1�������󡢥����ϥޥ饤�ե����Ȥ����Ӥ�����������ͳ��ʹ������������\n";
			$mail_info .= "��A1����".$this->info['a12']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "��Q2���������ϥޥ饤�ե����ȤΥۡ���ڡ����Ϥ狼��䤹���ä��Ǥ��礦����\n";
			$mail_info .= "��A2����".$a1[$this->info['a1']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "��Q3�����ۡ���ڡ����ǡ��狼��䤹���ä������狼��ˤ����ä����ˤĤ��ơ�����Ū�˶����Ʋ�������\n";
			$mail_info .= "��A3����".$this->info['a2']."\n";
			$mail_info .= "----------------------------------------\n\n";
			/*
			$mail_info .= "��Q3��������ʸ�塢��������ޤǡ������԰¤򴶤����ޤ����Ǥ��礦����\n";
			$mail_info .= "��A3����".$a3[$this->info['a3']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "��Q4�����ɤ������԰¤����ä�����������������\n";
			$mail_info .= "��A4����".$this->info['a4']."\n";
			$mail_info .= "----------------------------------------\n\n";
			*/
			$mail_info .= "��Q4��������ʸ�����������ݤ����Ҥ��б��Ϥ������Ǥ����Ǥ��礦����\n";
			$mail_info .= "��A4����".$a5[$this->info['a5']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "��Q5�����ž夬��ޤ������ʤϡ������ͤΥ��᡼���̤�Ǥ����Ǥ��礦����\n";
			$mail_info .= "��A5����".$a6[$this->info['a6']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "��Q6�������ʤ����夷���ݤκ�����֤Ϥ������Ǥ����Ǥ��礦����\n";
			$mail_info .= "��A6����".$a7[$this->info['a7']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "��Q7�����ºݤ˾��ʤ����ѡ����Ѥ��ƤߤƤΡ������ƥ�˴ؤ��봶�ۤ򤪴ꤤ���ޤ���\n";
			$mail_info .= "��A7����".$this->info['a10']."\n";
			$mail_info .= "----------------------------------------\n\n";
			/*
			$mail_info .= "��Q8�����ǥ����󡢿������������Ǻ�ʤɡ��֤�äȤ���ʾ��ʡʥ����ƥ�ˤ�����Ф褤�Τˡ��פȤ�������˾������Ф�ʹ������������\n";
			$mail_info .= "��A8����".$this->info['a11']."\n";
			$mail_info .= "----------------------------------------\n\n";
			*/
			$mail_info .= "��Q8���������Ѥ����Ӥ򶵤��Ƥ���������(���ڥ��٥�ȡ�ʸ���פʤ�)\n";
			$mail_info .= "��A8����".$this->info['a13']."\n";
			$mail_info .= "----------------------------------------\n\n";
			
			$mail_info .= "��Q9�����֤�äȤ���ʥ����ӥ������ʤ�������ɤ��Τˡ��פȤ�������˾������Ф�ʹ������������\n";
			$mail_info .= "��A9����".$this->info['a8']."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "��Q10�������Ҥ��Τä����ä����򶵤��Ƥ���������\n";
			$mail_info .= "��A10����".$a14[$this->info['a14']]."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "��Q11��������¾����ʸ���ƤߤƤδ��ۡ������Ť������ʤɤ�����ޤ����餪ʹ������������\n";
			$mail_info .= "��A11����".$this->info['a9']."\n";
			$mail_info .= "----------------------------------------\n\n";
			$mail_info .= "Date: ".date('Y-m-d H:i:s')."\n";
						
			// ��������
			mb_language("japanese");
			mb_internal_encoding("EUC-JP");
			$sendto = _ORDER_EMAIL;
						
			$subject = "�����ͥ��󥱡���";						// ��̾
			$msg = "";											// ����ʸ
			$boundary = md5(uniqid(rand())); 					// �Х�����꡼ʸ���ʥ᡼���å�������ź�եե�����ζ����Ȥ���ʸ����������
			
			$fromname = "�����ϥ�428";
			$from = mb_encode_mimeheader($fromname,"JIS")."<".$sendto.">";
			$header = "From: $from\n";
			$header .= "Reply-To: $from\n";
			$header .= "X-Mailer: PHP/".phpversion()."\n";
			$header .= "MIME-version: 1.0\n";
			$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
			
			$msg .= mb_convert_encoding($mail_info,"JIS","EUC-JP");	// ��������ʸ�򥨥󥳡��ɤ�������
			
			// ��̾�Υޥ���Х��Ȥ򥨥󥳡���
			$subject = mb_encode_mimeheader($subject,"JIS");
			
			// �᡼������
			$result = true;
			if(mail($sendto, $subject, $msg, $header)){
				// DB��Ͽ
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
	*	CC��BCC���դ��ƥ᡼������  charset UTF-8
	*	@mail_info		�᡼����ʸ
	*	@subject		��̾
	*	@sendto			������Υ᡼�륢�ɥ쥹������
	*	@formname		�����Ԥ�̾��
	*	@fromaddr		�����ԤΥ᡼�륢�ɥ쥹��Reply-To �������
	*	@bcc			BCC �Υ᡼�륢�ɥ쥹 default ""
	*	@cc				CC �Υ᡼�륢�ɥ쥹 default ""
	*	@attach			ź�եե������������� default ""
	*	
	*	@return		��������: ['success']�������顼: ���ɥ쥹��������֤���
	*/
	public function send_multi($mail_info, $subject, $sendto, $fromname, $formaddr, $bcc="", $cc="", $attach=""){
		mb_language("japanese");
		$autoReply = false;					// �ֿ��᡼���̵ͭ��true���ֿ������
		$msg = "";							// ����ʸ
		$boundary = md5(uniqid(rand())); 	// �Х�����꡼ʸ���ʥ᡼���å�������ź�եե�����ζ����Ȥ���ʸ����������
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

		
		$footer = "\n�������������䤪���Ť��Τ��Ȥ��������ޤ����顢����θ�ʤ����䤤��碌����������\n";
		$footer .= "���ĶȻ��֡�10:00 - 18:00���������������������\n\n";
		$footer .= "�� �����ϥޥ饤�ե����� ����������������������������������������������\n\n";
		$footer .= "��Phone������"._OFFICE_TEL."\n";
		$footer .= "��E-Mail������"._INFO_EMAIL."\n";
		$footer .= "��Web site����"._DOMAIN."/\n\n";
		$footer .= "������������������������������������������������������������������\n";
		
		$mail_info .= mb_convert_encoding($footer,'JIS','euc-jp');
		$msg .= mb_convert_encoding($mail_info,"JIS","euc-jp");	// ��������ʸ�򥨥󥳡��ɤ�������

		if(!empty($attach)){		// ź�եե��������
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

		// ��̾�Υޥ���Х��Ȥ򥨥󥳡���
		$subject  = mb_encode_mimeheader($subject,"JIS");

		// �᡼������
		$res = array();
		$sendto[] = $formaddr;	// �ܿͤؤ��ֿ���
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
					$res[] = $sendto[$i];	// ���Ԥ������ɥ쥹
				}
			}
		}

		if(empty($res)) $res[] = 'success';
		return $res;
	}
}
?>
