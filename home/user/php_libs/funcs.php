<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/session_my_handler.php';
require_once dirname(__FILE__).'/mailer.php';
require_once dirname(__FILE__).'/conndb.php';

function h($s) {
	return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}

/*
*	��������֤Υ����å�
*/
function checkLogin() {
//	if( empty($_SESSION['me']) || empty($_SESSION['me']['tla_customer_id']) ){
if( empty($_SESSION['me']) ){
		$res = false;
	}else{
		$res = $_SESSION['me'];
	}
	return $res;
}

/*
*	������إ�����쥯��
*/
function jump($s) {
	header("Location: ".$s);
	exit;
}

/*
*	CSRF�к�
*/
function setToken() {
	$token = sha1(uniqid(mt_rand(), true));
	$_SESSION['token'] = $token;
}

function chkToken() {
	if($_SESSION['token']!=$_POST['token']) {
		jump(_DOMAIN);
		exit;
	}
}

/*
*	�᡼�륢�ɥ쥹��addr_spec�˥����å�
*/
function isValidEmailFormat($email, $supportPeculiarFormat = true){
    $wsp              = '[\x20\x09]'; // Ⱦ�Ѷ���ȿ�ʿ����
    $vchar            = '[\x21-\x7e]'; // ASCII�����ɤ� ! ���� ~ �ޤ�
    $quoted_pair      = "\\\\(?:{$vchar}|{$wsp})"; // \ �����ˤĤ��� quoted-pair �����ʤ� \ �� " �����ѤǤ���
    $qtext            = '[\x21\x23-\x5b\x5d-\x7e]'; // $vchar ���� \ �� " ��ȴ������Ρ�\x22 �� " , \x5c �� \
    $qcontent         = "(?:{$qtext}|{$quoted_pair})"; // quoted-string �����ξ��ʬ��
    $quoted_string    = "\"{$qcontent}+\""; // " �� �Ϥޤ줿 quoted-string ������
    $atext            = '[a-zA-Z0-9!#$%&\'*+\-\/\=?^_`{|}~]'; // �̾�᡼�륢�ɥ쥹�˻��ѽ����ʸ��
    $dot_atom         = "{$atext}+(?:[.]{$atext}+)*"; // �ɥåȤ�Ϣ³���ʤ� RFC ��������롼��Ÿ���ǹ���
    $local_part       = "(?:{$dot_atom}|{$quoted_string})"; // local-part �� dot-atom ���� �ޤ��� quoted-string �����Τɤ��餫
    // �ɥᥤ����ʬ��Ƚ�궯��
    $alnum            = '[a-zA-Z0-9]'; // domain ����Ƭ�ѿ���
    $sub_domain       = "{$alnum}+(?:-{$alnum}+)*"; // hyphenated alnum ��롼��Ÿ���ǹ���
    $domain           = "(?:{$sub_domain})+(?:[.](?:{$sub_domain})+)+"; // �ϥ��ե�ȥɥåȤ�Ϣ³���ʤ��褦�� $sub_domain ��롼��Ÿ��
    $addr_spec        = "{$local_part}[@]{$domain}"; // ����
    // �Τη������å᡼�륢�ɥ쥹��
    $dot_atom_loose   = "{$atext}+(?:[.]|{$atext})*"; // Ϣ³�����ɥåȤ� @ ��ľ���ΥɥåȤ���Ƥ���
    $local_part_loose = $dot_atom_loose; // �Τη������å᡼�륢�ɥ쥹�� quoted-string �����ʤ�Ƥ���櫓�ʤ������֤�
    $addr_spec_loose  = "{$local_part_loose}[@]{$domain}"; // ����
    // �Τη������å᡼�륢�ɥ쥹�η����򥵥ݡ��Ȥ��뤫�ǻȤ�����ɽ�����Ѥ���
    if($supportPeculiarFormat){
        $regexp = $addr_spec_loose;
    }else{
        $regexp = $addr_spec;
    }
    // \A �Ͼ��ʸ�������Ƭ�˥ޥå����롣\z �Ͼ��ʸ����������˥ޥå����롣
    if(preg_match("/\A{$regexp}\z/", $email)){
        return true;
    }else{
        return false;
    }
}


/*
*	�桼������Ͽ
*	@args	POST�ǡ���
*
*	return	error text
*/
function user_registration($args){
	$conndb = new Conndb(_API_U);
	
	foreach($args as $key=>&$val){
		//if($key=='uicon') continue;
		$val = mb_convert_encoding($val, "utf-8", "euc-jp");
	}
	
	// ��������ǡ����ѥϥå��������
		/*
			$args['uicon'] = '';
			$args['iname'] = '';
			$args['mime'] = '';
		*/
	

	$err = array();
	if(empty($args['uname'])){
		$err['uname'] = '�桼�����͡�������Ϥ��Ʋ�������';
	}

/*
	else if($conndb->checkExistName(array($args['uname'],$args['userid']))){
		$err['uname'] = '�桼�����͡���ϻ��Ѥ���Ƥ��ޤ���';
	}
*/

	if(empty($args['email'])){
		$err['email'] = '�᡼�륢�ɥ쥹�����Ϥ��Ʋ�������';
	}else if(!isValidEmailFormat($args['email'])){
		$err['email'] = '�᡼�륢�ɥ쥹������������ޤ���';
	}else if($conndb->checkExistEmail(array($args['email'],$args['reg_site']))){
		$err['email'] = '�᡼�륢�ɥ쥹����Ͽ����Ƥ��ޤ���';
	}

	// ���顼�����å�
	/*
	$err = array();
	if(empty($args['uname'])){
		$err['uname'] = '�桼�����͡�������Ϥ��Ʋ�������';
	}
	/*
	else if($conndb->checkExistName(array($args['uname'],$args['userid']))){
		$err['uname'] = '�桼�����͡���ϻ��Ѥ���Ƥ��ޤ���';
	}
	*/
	/*
	if(empty($args['email'])){
		$err['email'] = '�᡼�륢�ɥ쥹�����Ϥ��Ʋ�������';
	}else if(!isValidEmailFormat($args['email'])){
		$err['email'] = '�᡼�륢�ɥ쥹������������ޤ���';
	}else if($conndb->checkExistEmail(array($args['email'],$args['userid']))){
		$err['email'] = '�᡼�륢�ɥ쥹����Ͽ����Ƥ��ޤ���';
	}
	*/
	
	/* Ver 5.2
	if(!filter_var($args['email'], FILTER_VALIDATE_EMAIL)){
		$err['email'] = '�᡼�륢�ɥ쥹������������ޤ���';
	}
	*/
	
	if(!isset($args['profile'])){
		if(empty($args['pass'])){
			$err['pass'] = '�ѥ���ɤ����Ϥ��Ʋ�������';
		}else if(!preg_match("/^[a-zA-Z0-9]+$/", $args['pass'])){
			$err['pass'] = '���ѤǤ���ʸ����Ⱦ�ѱѿ��ΤߤǤ���';
		}else if(strlen($args['pass'])<8 || strlen($args['pass'])>32){
			$err['pass'] = '8ʸ���ʾ�32ʸ������ǻ��ꤷ�Ƥ���������';
		}else if($args['pass']!=$args['passconf']){
			$err['passconf'] = '�ѥ���ɤγ�ǧ����äƤ��ޤ���';
		}
	}
	
	// ��������Υ��åץ��ɥ����å�
/*
	if($_FILES['uicon']['error']==UPLOAD_ERR_OK && isset($_FILES['uicon'])){
		
		$size = filesize($_FILES['uicon']['tmp_name']);
		if(!$size || $size>_MAXIMUM_SIZE){
			$err['uicon'] = '�ե����륵�������礭�����ޤ���10MB�ޤǤˤ��Ʋ�������';
		}
		
		if(empty($err['uicon'])){
			// ���åץ��ɤ��줿�ե�����γ�ĥ�Ҥ����
			$imagesize = getimagesize($_FILES['uicon']['tmp_name']);
			switch($imagesize['mime']){
				case 'image/gif':
					$ext = '.gif';
					break;
				case 'image/png':
					$ext = '.png';
					break;
				case 'image/jpeg':
					$ext = '.jpg';
					break;
				default:
					$err['uicon'] = '���ѤǤ�����������פϡ�GIF��PNG��JPEG �����Ǥ���';
			}
		}
		
		if(empty($err['uicon'])){
			// �������Υե�����̾
			$imageFileName = sha1(time().mt_rand()).$ext;
			
			// ����������¸
			$imageFilePath = _USER_ICON.$imageFileName;
			$rs = move_uploaded_file($_FILES['uicon']['tmp_name'], $imageFilePath);
			if(!$rs){
				$err['uicon'] = '��������Υ��åץ��ɤǥ��顼��ȯ�����ޤ�����';
			}
		}
		
		if(empty($err['uicon'])){
			// �̾������κ�������¸
			$width = $imagesize[0];
			$height = $imagesize[1];
			
			if($width>_ICON_WIDTH || $height>_ICON_WIDTH){
				// ���ե��������������פˤ�äƺ��
				switch($imagesize['mime']){
					case 'image/gif':
						$srcImage = imagecreatefromgif($imageFilePath);
						break;
					case 'image/png':
						$srcImage = imagecreatefrompng($imageFilePath);
						break;
					case 'image/jpeg':
						$srcImage = imagecreatefromjpeg($imageFilePath);
						break;
				}
				
				// ��������̾��������ι⤵
				$iconheight = round($height * _ICON_WIDTH / $width);
				
				// ���������ѤΥ��᡼������꡼������
				$iconImage = imagecreatetruecolor(_ICON_WIDTH, _ICON_WIDTH);
				$drawcolor = imagecolorallocatealpha($iconImage, 255, 255, 255, 0);	// ��Ʃ������
				imagefill($iconImage, 0, 0, $drawcolor);
				
				// �ɤ�Ĥ֤�����Ʃ�����ꤷ�ƺ���
				//$transcolor = imagecolorallocatealpha($iconImage, 255, 255, 255, 127);	// Ʃ��
				//imagefill($iconImage, 0, 0, $transcolor);
				
				// �̾���������
				imagecopyresampled($iconImage, $srcImage, 0,0,0,0,_ICON_WIDTH,$iconheight,$width,$height);
				
				// ������������Ʃ�����������
				imagealphablending($iconImage,false);
				imagesavealpha($iconImage,true);
				
				// �����������
				switch($imagesize['mime']){
					case 'image/gif':
						imagegif($iconImage, _USER_ICON.$imageFileName);
						break;
					case 'image/png':
						imagepng($iconImage, _USER_ICON.$imageFileName);
						break;
					case 'image/jpeg':
						imagejpeg($iconImage, _USER_ICON.$imageFileName);
						break;
				}
			}
			
			$args['uicon'] = base64_encode(file_get_contents(_USER_ICON.$imageFileName));
			$args['iname'] = $imageFileName;
			$args['mime'] = $imagesize['mime'];
			
			unlink(_USER_ICON.$imageFileName);
		}
	}
*/
	
	
	if(empty($err)){
		/* DB����Ͽ */
		if(isset($args['profile'], $args['userid'])){
			$res = $conndb->updateUser($args);
		}else{
			$args['reg_site'] = _SITE;
			$res = $conndb->setUser($args);
		}
		
		if($res){
			if(isset($args['profile'], $args['userid'])){
				$u = $conndb->getUserList($args['userid']);
				$_SESSION['me'] = array_merge($_SESSION['me'],array(
					'id'=>$u[0]['id'],
				  'customername'=>$u[0]['customername'],
				  'email'=>$u[0]['email'],
					'customerruby'=>$u[0]['customerruby']
				  //'number'=>$u[0]['number']
				));
			}else{

				// �桼������Ͽ�Τ��Τ餻
				$mailer = new Mailer($args);
				$isSend = $mailer->send_registerd();
				
//				$u = $conndb->getUser(array('email'=>$args['email'], 'pass'=>$args['pass']));
//				$_SESSION['me'] = $u;
				

				$u = $conndb->getUserList($res);
				$_SESSION['me'] = $u;
				$_SESSION['me'] = array_merge($_SESSION['me'],array(
					'id'=>$u[0]['id'],
				  'customername'=>$u[0]['customername'],
				  'email'=>$u[0]['email'],
					'customerruby'=>$u[0]['customerruby'],
					'new'=>true
				));
				jump('./account.php');
			}
		}else{
			return $res;
		}
	}
	
	return $err;
}


/*
*	�桼��������
*	@args	POST�ǡ���
*
*	return	error text
*/
function update_user($args){
	$conndb = new Conndb(_API_U);
	
	foreach($args as $key=>&$val){
		$val = mb_convert_encoding($val, "utf-8", "euc-jp");
	}
	
	// ���顼�����å�
	$err = array();
	if(empty($args['uname'])){
		$err['uname'] = '�桼�����͡�������Ϥ��Ʋ�������';
	}
	
	if(empty($err)){
		/* DB�˹��� */
		if(isset($args['profile'], $args['userid'])){
			$res = $conndb->updateUser($args);
		}
		if($res){
			if(isset($args['profile'], $args['userid'])){
				$u = $conndb->getUserList($args['userid']);
				$_SESSION['me'] = array_merge($_SESSION['me'],array(
					'id'=>$u[0]['id'],
				  'customername'=>$u[0]['customername'],
				  'email'=>$u[0]['email'],
					'customerruby'=>$u[0]['customerruby']
				  //'number'=>$u[0]['number']
				));
			}
		}else{
			return $res;
		}
	}
	
	return $err;
}


/*
*	�ѥ�����ѹ�
*	@args	['userid','pass']
*
*	return	error text
*/
function update_pass($args){
	// trim
	foreach($args as $key=>&$val){
		$val = trim(mb_convert_kana($val,"s", "utf-8"));
	}
	
	// ���顼�����å�
	if(empty($args['pass'])){
		$err['pass'] = '�ѥ���ɤ����Ϥ��Ʋ�������';
	}else if(!preg_match("/^[a-zA-Z0-9]+$/", $args['pass'])){
		$err['pass'] = '���ѤǤ���ʸ����Ⱦ�ѱѿ��ΤߤǤ���';
	}else if(strlen($args['pass'])<8 || strlen($args['pass'])>32){
		$err['pass'] = '8ʸ���ʾ�32ʸ������ǻ��ꤷ�Ƥ���������';
	}else if($args['pass']!=$args['passconf']){
		$err['passconf'] = '�ѥ���ɤγ�ǧ����äƤ��ޤ���';
	}
	
	
	if(empty($err)){
		$conndb = new Conndb(_API_U);
		$res = $conndb->updatePass($args);
		if(!$res) $err['pass'] = '�̿����顼';
	}
	
	return $err;
}

/*
*	�����ͽ��ꡦ�����ѹ�
*	@args	['userid','zipcode','addr0','addr1','addr2']
*
*	return	error text
*/
function update_addr($args){
	// trim
	foreach($args as $key=>&$val){
		$val = trim(mb_convert_encoding($val,"utf-8","euc-jp"));
	}
	// ���顼�����å�
	if(empty($args['zipcode'])){
		$err['zipcode'] = '͹���ֹ�����Ϥ��Ƥ���������';
	}else if(empty($args['addr0'])){
		$err['addr0'] = '��ƻ�ܸ������Ϥ��Ƥ���������';
	}else if(empty($args['addr1'])){
		$err['addr1'] = '��/������Ϥ��Ƥ���������';
	}else if(empty($args['addr2'])){
		$err['addr2'] = '���ɥ쥹�����Ϥ��Ƥ���������';
	}else if(!preg_match("/^[0-9]{3}[-]?[0-9]{0,4}$/", $args['zipcode'])){
		$err['zipcode'] = '͹���ֹ������å����Ƥ���������';
	}else if(empty($args['tel'])){
		$err['tel'] = '�����ֹ�����Ϥ��Ƥ���������';
	}
/*else if(!preg_match("(0\d{1,4}-|\(0\d{1,4}\) ?)?\d{1,4}-\d{4}", $args['tel'])){
		$err['tel'] = '�����ֹ������å����Ƥ���������';
	}
*/
	
	if(empty($err)){
		$conndb = new Conndb(_API_U);
		$res = $conndb->updateAddr($args);
		if(!$res) $err['addr'] = '�̿����顼';
		$u = $conndb->getUserList($args['userid']);
		$_SESSION['me'] = array_merge($_SESSION['me'],array(
			'zipcode'=>$u[0]['zipcode'],
		  'addr0'=>$u[0]['addr0'],
		  'addr1'=>$u[0]['addr1'],
			'addr2'=>$u[0]['addr2'],
		  'tel'=>$u[0]['tel']
		));
		
	}

	
	return $err;
}

/*
*	���Ϥ����ѹ�
*	@args	['userid','zipcode','addr0','addr1','addr2']
*
*	return	error text
*/

function update_deli($args, $delId){
	// trim
	foreach($args as $key=>&$val){
		$val = trim(mb_convert_encoding($val,"utf-8","euc-jp"));
	}
	// ���顼�����å�
	if(empty($args['organization'])){
		$err[$delId.'_organization'] = '���Ϥ�������Ϥ��Ƥ���������';
	}else if(empty($args['delizipcode'])){
		$err[$delId.'_delizipcode'] = '͹���ֹ�����Ϥ��Ƥ���������';
	}else if(empty($args['deliaddr0'])){
		$err[$delId.'_deliaddr0'] = '��ƻ�ܸ������Ϥ��Ƥ���������';
	}else if(empty($args['deliaddr1'])){
		$err[$delId.'_deliaddr1'] = '����1�����Ϥ��Ƥ���������';
	}else if(empty($args['deliaddr2'])){
		$err[$delId.'_deliaddr2'] = '����2�����Ϥ��Ƥ���������';
	}else if(!preg_match("/^[0-9]{3}[-]?[0-9]{0,4}$/", $args['delizipcode'])){
		$err[$delId.'_delizipcode'] = '͹���ֹ������å����Ƥ���������';
	}else if(empty($args['delitel'])){
		$err[$delId.'_delitel'] = '�����ֹ�����Ϥ��Ƥ���������';
	}

/*else if(!preg_match("(0\d{1,4}-|\(0\d{1,4}\) ?)?\d{1,4}-\d{4}", $args['tel'])){
		$err['tel'] = '�����ֹ������å����Ƥ���������';
	}
*/
	
	if(empty($err)){
		$conndb = new Conndb(_API_U);
		$res = $conndb->updateDeli($args);
		if(!$res){
			$err[$delId.'_deliaddr'] = '�̿����顼';
		}else{
			$u = $conndb->getUserList($args['userid']);
			$_SESSION['me']['delivery'] = array_merge($_SESSION['me'],array(
				'organization'=>$u[0]['organization'],
				'delizipcode'=>$u[0]['delizipcode'],
			  'deliaddr0'=>$u[0]['deliaddr0'],
			  'deliaddr1'=>$u[0]['deliaddr1'],
				'deliaddr2'=>$u[0]['deliaddr2'],
				'deliaddr3'=>$u[0]['deliaddr3'],
				'deliaddr4'=>$u[0]['deliaddr4'],
			  'delitel'=>$u[0]['delitel']
			));
		}
	}
	
	return $err;
}
/*
*	menu
*/
//$menu = '<div class="menu"><a href="/user/menu.php">��˥塼</a></div>';
$menu = '';
$menu .= '<div class="menu" id="menu1"><a href="/user/designed.php">���᡼������</a></div>';
$menu .= '<div class="menu" id="menu1"><a href="/user/credit.php">����ʧ����</a></div>';
$menu .= '<div class="menu" id="menu2"><a href="/user/progress.php">����ξ���</a></div>';
$menu .= '<div class="menu" id="menu2"><a href="/user/history.php">����ʸ����</a></div>';
$menu .= '<div class="menu" id="menu2"><a href="/user/account.php">���������</a></div>';

$url_path = parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH);
$menu = preg_replace('/<a href="'.preg_quote($url_path, '/').'">(.+?)<\/a>/', '<span>$1</span>', $menu);
?>