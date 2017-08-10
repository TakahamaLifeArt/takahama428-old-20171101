<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/session_my_handler.php';
require_once dirname(__FILE__).'/mailer.php';
require_once dirname(__FILE__).'/conndb.php';

function h($s) {
	return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}

/*
*	ログイン状態のチェック
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
*	指定先へリダイレクト
*/
function jump($s) {
	header("Location: ".$s);
	exit;
}

/*
*	CSRF対策
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
*	メールアドレス（addr_spec）チェック
*/
function isValidEmailFormat($email, $supportPeculiarFormat = true){
    $wsp              = '[\x20\x09]'; // 半角空白と水平タブ
    $vchar            = '[\x21-\x7e]'; // ASCIIコードの ! から ~ まで
    $quoted_pair      = "\\\\(?:{$vchar}|{$wsp})"; // \ を前につけた quoted-pair 形式なら \ と " が使用できる
    $qtext            = '[\x21\x23-\x5b\x5d-\x7e]'; // $vchar から \ と " を抜いたもの。\x22 は " , \x5c は \
    $qcontent         = "(?:{$qtext}|{$quoted_pair})"; // quoted-string 形式の条件分岐
    $quoted_string    = "\"{$qcontent}+\""; // " で 囲まれた quoted-string 形式。
    $atext            = '[a-zA-Z0-9!#$%&\'*+\-\/\=?^_`{|}~]'; // 通常、メールアドレスに使用出来る文字
    $dot_atom         = "{$atext}+(?:[.]{$atext}+)*"; // ドットが連続しない RFC 準拠形式をループ展開で構築
    $local_part       = "(?:{$dot_atom}|{$quoted_string})"; // local-part は dot-atom 形式 または quoted-string 形式のどちらか
    // ドメイン部分の判定強化
    $alnum            = '[a-zA-Z0-9]'; // domain は先頭英数字
    $sub_domain       = "{$alnum}+(?:-{$alnum}+)*"; // hyphenated alnum をループ展開で構築
    $domain           = "(?:{$sub_domain})+(?:[.](?:{$sub_domain})+)+"; // ハイフンとドットが連続しないように $sub_domain をループ展開
    $addr_spec        = "{$local_part}[@]{$domain}"; // 合成
    // 昔の携帯電話メールアドレス用
    $dot_atom_loose   = "{$atext}+(?:[.]|{$atext})*"; // 連続したドットと @ の直前のドットを許容する
    $local_part_loose = $dot_atom_loose; // 昔の携帯電話メールアドレスで quoted-string 形式なんてあるわけない。たぶん。
    $addr_spec_loose  = "{$local_part_loose}[@]{$domain}"; // 合成
    // 昔の携帯電話メールアドレスの形式をサポートするかで使う正規表現を変える
    if($supportPeculiarFormat){
        $regexp = $addr_spec_loose;
    }else{
        $regexp = $addr_spec;
    }
    // \A は常に文字列の先頭にマッチする。\z は常に文字列の末尾にマッチする。
    if(preg_match("/\A{$regexp}\z/", $email)){
        return true;
    }else{
        return false;
    }
}


/*
*	ユーザー登録
*	@args	POSTデータ
*
*	return	error text
*/
function user_registration($args){
	$conndb = new Conndb(_API_U);
	
	foreach($args as $key=>&$val){
		//if($key=='uicon') continue;
		$val = mb_convert_encoding($val, "utf-8", "euc-jp");
	}
	
	// アイコンデータ用ハッシュを初期化
		/*
			$args['uicon'] = '';
			$args['iname'] = '';
			$args['mime'] = '';
		*/
	

	$err = array();
	if(empty($args['uname'])){
		$err['uname'] = 'ユーザーネームを入力して下さい。';
	}

/*
	else if($conndb->checkExistName(array($args['uname'],$args['userid']))){
		$err['uname'] = 'ユーザーネームは使用されています。';
	}
*/

	if(empty($args['email'])){
		$err['email'] = 'メールアドレスを入力して下さい。';
	}else if(!isValidEmailFormat($args['email'])){
		$err['email'] = 'メールアドレスが正しくありません。';
	}else if($conndb->checkExistEmail(array($args['email'],$args['reg_site']))){
		$err['email'] = 'メールアドレスは登録されています。';
	}

	// エラーチェック
	/*
	$err = array();
	if(empty($args['uname'])){
		$err['uname'] = 'ユーザーネームを入力して下さい。';
	}
	/*
	else if($conndb->checkExistName(array($args['uname'],$args['userid']))){
		$err['uname'] = 'ユーザーネームは使用されています。';
	}
	*/
	/*
	if(empty($args['email'])){
		$err['email'] = 'メールアドレスを入力して下さい。';
	}else if(!isValidEmailFormat($args['email'])){
		$err['email'] = 'メールアドレスが正しくありません。';
	}else if($conndb->checkExistEmail(array($args['email'],$args['userid']))){
		$err['email'] = 'メールアドレスは登録されています。';
	}
	*/
	
	/* Ver 5.2
	if(!filter_var($args['email'], FILTER_VALIDATE_EMAIL)){
		$err['email'] = 'メールアドレスが正しくありません。';
	}
	*/
	
	if(!isset($args['profile'])){
		if(empty($args['pass'])){
			$err['pass'] = 'パスワードを入力して下さい。';
		}else if(!preg_match("/^[a-zA-Z0-9]+$/", $args['pass'])){
			$err['pass'] = '使用できる文字は半角英数のみです。';
		}else if(strlen($args['pass'])<8 || strlen($args['pass'])>32){
			$err['pass'] = '8文字以上32文字以内で指定してください。';
		}else if($args['pass']!=$args['passconf']){
			$err['passconf'] = 'パスワードの確認が合っていません。';
		}
	}
	
	// アイコンのアップロードチェック
/*
	if($_FILES['uicon']['error']==UPLOAD_ERR_OK && isset($_FILES['uicon'])){
		
		$size = filesize($_FILES['uicon']['tmp_name']);
		if(!$size || $size>_MAXIMUM_SIZE){
			$err['uicon'] = 'ファイルサイズが大きすぎます。10MBまでにして下さい。';
		}
		
		if(empty($err['uicon'])){
			// アップロードされたファイルの拡張子を取得
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
					$err['uicon'] = '使用できる画像タイプは、GIF、PNG、JPEG だけです。';
			}
		}
		
		if(empty($err['uicon'])){
			// 元画像のファイル名
			$imageFileName = sha1(time().mt_rand()).$ext;
			
			// 元画像を保存
			$imageFilePath = _USER_ICON.$imageFileName;
			$rs = move_uploaded_file($_FILES['uicon']['tmp_name'], $imageFilePath);
			if(!$rs){
				$err['uicon'] = 'アイコンのアップロードでエラーが発生しました。';
			}
		}
		
		if(empty($err['uicon'])){
			// 縮小画像の作成、保存
			$width = $imagesize[0];
			$height = $imagesize[1];
			
			if($width>_ICON_WIDTH || $height>_ICON_WIDTH){
				// 元ファイルを画像タイプによって作る
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
				
				// 元画像を縮小した時の高さ
				$iconheight = round($height * _ICON_WIDTH / $width);
				
				// アイコン用のイメージをメモリーに配置
				$iconImage = imagecreatetruecolor(_ICON_WIDTH, _ICON_WIDTH);
				$drawcolor = imagecolorallocatealpha($iconImage, 255, 255, 255, 0);	// 不透明の白
				imagefill($iconImage, 0, 0, $drawcolor);
				
				// 塗りつぶす色に透過を指定して作成
				//$transcolor = imagecolorallocatealpha($iconImage, 255, 255, 255, 127);	// 透明
				//imagefill($iconImage, 0, 0, $transcolor);
				
				// 縮小画像作成
				imagecopyresampled($iconImage, $srcImage, 0,0,0,0,_ICON_WIDTH,$iconheight,$width,$height);
				
				// 画像作成時の透過処理を設定
				imagealphablending($iconImage,false);
				imagesavealpha($iconImage,true);
				
				// アイコンを上書
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
		/* DBに登録 */
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

				// ユーザー登録のお知らせ
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
*	ユーザー更新
*	@args	POSTデータ
*
*	return	error text
*/
function update_user($args){
	$conndb = new Conndb(_API_U);
	
	foreach($args as $key=>&$val){
		$val = mb_convert_encoding($val, "utf-8", "euc-jp");
	}
	
	// エラーチェック
	$err = array();
	if(empty($args['uname'])){
		$err['uname'] = 'ユーザーネームを入力して下さい。';
	}
	
	if(empty($err)){
		/* DBに更新 */
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
*	パスワード変更
*	@args	['userid','pass']
*
*	return	error text
*/
function update_pass($args){
	// trim
	foreach($args as $key=>&$val){
		$val = trim(mb_convert_kana($val,"s", "utf-8"));
	}
	
	// エラーチェック
	if(empty($args['pass'])){
		$err['pass'] = 'パスワードを入力して下さい。';
	}else if(!preg_match("/^[a-zA-Z0-9]+$/", $args['pass'])){
		$err['pass'] = '使用できる文字は半角英数のみです。';
	}else if(strlen($args['pass'])<8 || strlen($args['pass'])>32){
		$err['pass'] = '8文字以上32文字以内で指定してください。';
	}else if($args['pass']!=$args['passconf']){
		$err['passconf'] = 'パスワードの確認が合っていません。';
	}
	
	
	if(empty($err)){
		$conndb = new Conndb(_API_U);
		$res = $conndb->updatePass($args);
		if(!$res) $err['pass'] = '通信エラー';
	}
	
	return $err;
}

/*
*	お客様住所・電話変更
*	@args	['userid','zipcode','addr0','addr1','addr2']
*
*	return	error text
*/
function update_addr($args){
	// trim
	foreach($args as $key=>&$val){
		$val = trim(mb_convert_encoding($val,"utf-8","euc-jp"));
	}
	// エラーチェック
	if(empty($args['zipcode'])){
		$err['zipcode'] = '郵便番号を入力してください。';
	}else if(empty($args['addr0'])){
		$err['addr0'] = '都道府県を入力してください。';
	}else if(empty($args['addr1'])){
		$err['addr1'] = '市/区を入力してください。';
	}else if(empty($args['addr2'])){
		$err['addr2'] = 'アドレスを入力してください。';
	}else if(!preg_match("/^[0-9]{3}[-]?[0-9]{0,4}$/", $args['zipcode'])){
		$err['zipcode'] = '郵便番号をチェックしてください。';
	}else if(empty($args['tel'])){
		$err['tel'] = '電話番号を入力してください。';
	}
/*else if(!preg_match("(0\d{1,4}-|\(0\d{1,4}\) ?)?\d{1,4}-\d{4}", $args['tel'])){
		$err['tel'] = '電話番号をチェックしてください。';
	}
*/
	
	if(empty($err)){
		$conndb = new Conndb(_API_U);
		$res = $conndb->updateAddr($args);
		if(!$res) $err['addr'] = '通信エラー';
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
*	お届け先変更
*	@args	['userid','zipcode','addr0','addr1','addr2']
*
*	return	error text
*/

function update_deli($args, $delId){
	// trim
	foreach($args as $key=>&$val){
		$val = trim(mb_convert_encoding($val,"utf-8","euc-jp"));
	}
	// エラーチェック
	if(empty($args['organization'])){
		$err[$delId.'_organization'] = 'お届き先を入力してください。';
	}else if(empty($args['delizipcode'])){
		$err[$delId.'_delizipcode'] = '郵便番号を入力してください。';
	}else if(empty($args['deliaddr0'])){
		$err[$delId.'_deliaddr0'] = '都道府県を入力してください。';
	}else if(empty($args['deliaddr1'])){
		$err[$delId.'_deliaddr1'] = '住所1を入力してください。';
	}else if(empty($args['deliaddr2'])){
		$err[$delId.'_deliaddr2'] = '住所2を入力してください。';
	}else if(!preg_match("/^[0-9]{3}[-]?[0-9]{0,4}$/", $args['delizipcode'])){
		$err[$delId.'_delizipcode'] = '郵便番号をチェックしてください。';
	}else if(empty($args['delitel'])){
		$err[$delId.'_delitel'] = '電話番号を入力してください。';
	}

/*else if(!preg_match("(0\d{1,4}-|\(0\d{1,4}\) ?)?\d{1,4}-\d{4}", $args['tel'])){
		$err['tel'] = '電話番号をチェックしてください。';
	}
*/
	
	if(empty($err)){
		$conndb = new Conndb(_API_U);
		$res = $conndb->updateDeli($args);
		if(!$res){
			$err[$delId.'_deliaddr'] = '通信エラー';
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
//$menu = '<div class="menu"><a href="/user/menu.php">メニュー</a></div>';
$menu = '';
$menu .= '<div class="menu" id="menu1"><a href="/user/designed.php">イメージ画像</a></div>';
$menu .= '<div class="menu" id="menu1"><a href="/user/credit.php">お支払状況</a></div>';
$menu .= '<div class="menu" id="menu2"><a href="/user/progress.php">製作の状況</a></div>';
$menu .= '<div class="menu" id="menu2"><a href="/user/history.php">ご注文履歴</a></div>';
$menu .= '<div class="menu" id="menu2"><a href="/user/account.php">アカウント</a></div>';

$url_path = parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH);
$menu = preg_replace('/<a href="'.preg_quote($url_path, '/').'">(.+?)<\/a>/', '<span>$1</span>', $menu);
?>