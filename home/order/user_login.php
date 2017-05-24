<?php

// file encode: utf-8

require_once $_SERVER['DOCUMENT_ROOT'].'/user/php_libs/funcs.php';

// ログイン処理
if (isset($_REQUEST['login'])) {
	$args = array($_REQUEST['email']);
	$conndb = new Conndb(_API_U);

	// エラーチェック
	if (empty($_REQUEST['email'])) {
		$err = 'メールアドレスを入力して下さい。';
	} else if (!$conndb->checkExistEmail($args)) {
		$err = 'このメールアドレスは登録されていません。';
	} else if (empty($_REQUEST['pass'])) {
		$err = 'パスワードを入力して下さい。';
	} else {
		$args = array('email'=>$_REQUEST['email'], 'pass'=>$_REQUEST['pass']);
		$me = $conndb->getUser($args);
		if (!$me) {
			$err = "メールアドレス（".$_REQUEST['email']."）かパスワードが認識できません。ご確認下さい。";
		}
	}
	
	if (empty($err)) {
		$_SESSION['me'] = $me;
		//注文画面でログインした場合、注文情報にもセッションを設定する必要がある
		$_SESSION['orders']['customer']['member'] = $me['id'];
		$_SESSION['orders']['customer']['customername'] = $me['customername'];
		$_SESSION['orders']['customer']['customerruby'] = $me['customerruby'];
		$_SESSION['orders']['customer']['email'] = $me['email'];
		$_SESSION['orders']['customer']['tel'] = $me['tel'];
		$_SESSION['orders']['customer']['zipcode'] = $me['zipcode'];
		$_SESSION['orders']['customer']['addr0'] = $me['addr0'];
		$_SESSION['orders']['customer']['addr1'] = $me['addr1'];
		$_SESSION['orders']['customer']['addr2'] = $me['addr2'];
		$json = new Services_JSON();
		$res = $json->encode($me);
		header("Content-Type: text/javascript; charset=utf-8");
		echo $res;
	} else {
		$json = new Services_JSON();
		$res = $json->encode($err);
		header("Content-Type: text/javascript; charset=utf-8");
		echo $res;
	}
}

// ログインしている顧客のデータ取得処理
if (isset($_REQUEST['getcustomer'])) {
	// ログイン状態のチェック
	$me = checkLogin();
	if (!$me) {
		$json = new Services_JSON();
		$res = $json->encode("");
		header("Content-Type: text/javascript; charset=utf-8");
		echo $res;
	} else {
		// 届き先を取得し、セッションに置く
		$conndb = new Conndb(_API_U);
		//お届け先情報を設定
		$deli = $conndb->getDeli($me['id']);
		$_SESSION['me']['delivery'] = $deli;
		$json = new Services_JSON();
		$res = $json->encode($_SESSION['me']);
		header("Content-Type: text/javascript; charset=utf-8");
		echo $res;
	}
}

?>