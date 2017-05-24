<?php
/*
*	Database connection
*	charset utf-8
*
*/
require_once dirname(__FILE__).'/http.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/JSON.php';

class Conndb extends HTTP {
	
	public function __construct($args=_API){
		parent::__construct($args);
	}
	
public function takahama_log($logContext) {
//	$now = date('Y/m/d H:i:s');
//	error_log($now.": ".$logContext."\n\r", 3, $_SERVER['DOCUMENT_ROOT'].'/debug.log.txt');
}

	/*
	*	商品名とカラーごとのコード一覧データ
	*	@itemid			アイテムID　default: 1
	*
	*	@return			['name':[アイテムコード:アイテム名], 'category':[カテゴリーキー:カテゴリー名], code:[code:カラー名, ...], size[...], ppid:プリントポジションID]
	*					codeのフォーマットは、「アイテムコード＿カラーコード」　ex) 085-cvt_001
	*/
	public function itemAttr($itemid=1){
		$res = parent::request('POST', array('act'=>'itemattr', 'itemid'=>$itemid));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	プリント代計算
	*	@orders_id		受注No.
	*	@curdate		注文確定日、または ''
	*
	*	@return			{'tot':プリント代合計, {プリント方法:金額}, {'item':{アイテムID:{'fee':1枚あたり, 'volume':枚数}}}}
	*
	public function getEstimation($orders_id, $curdate){
		$res = parent::request('POST', array('act'=>'getestimation', 'orders_id'=>$orders_id, 'curdate'=>$curdate));
		$data = unserialize($res);
		
		return $data;
	}
	*/
	
	
	/**
	*	注文履歴を取得（member）
	*
	*	@args		customer ID
	*
	*	return		[注文情報]
	*/
	public function getOroderHistory($args){
		$res = parent::request('POST', array('act'=>'getorderhistory', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/**
	*	製作進行状況の取得（member）
	*
	*	@args		[customer ID, order ID]
	*
	*	return		[進行状況]
	*/
	public function getProgress($args){
		$res = parent::request('POST', array('act'=>'getprogress', 'args'=>$args));
		$data = unserialize($res);
//$this->takahama_log("getProgress ".$data);
		
		return $data;
	}
	
	
	/*
	*	プリント情報（member）
	*	@args			受注No.
	*
	*	@return			[プリント情報]
	*/
	public function getDetailsPrint($args){
		$res = parent::request('POST', array('act'=>'getdetailsprint', 'args'=>$args));
		$data = unserialize($res);
		return $data;
	}
	
	
	/*
	*	請求書・領収書・納品書のデータ（member）
	*	@args			受注No.
	*
	*	@return			[出力情報]
	*/
	public function getPrintform($args){
		$res = parent::request('POST', array('act'=>'getprintform', 'args'=>$args));
		$data = unserialize($res);
		return $data;
	}
	
	
	/*
	*	フォローメールの配信停止処理
	*	
	*	@args			{'customer_id', 'cancel'(停止:1)'
	*
	*	@return			成功:true  失敗:false
	************************************************/
	public function unsubscribe($args){
		$res = parent::request('POST', array('act'=>'unsubscribe', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	
	
	/*====================================

		写真館サーバー（master）
		2017-2-13から
		takahamalifeartサーバー(User)に移動した

	=====================================*/
	
	/*
	*	ユーザー新規登録
	*	@args	['uname','email','pass','uicon','filename']
	*
	*	reutrn	true:OK　false:NG
	*/
	public function setUser($args) {
//$this->takahama_log("conndb setUser");
		$res = parent::request('POST', array('act'=>'setuser', 'args'=>$args));
		$data = unserialize($res);
//$this->takahama_log("conndb setUser");

		return $data;
	}

	/*
	*	ユーザーの存在確認
	*	@args	['email','pass']
	*
	*	reutrn	OK:{ユーザー情報}　NG:false
	*/
	public function getUser($args) {
		$res = parent::request('POST', array('act'=>'getuser', 'args'=>$args));
		$data = unserialize($res);

		return $data;
	}
	
	
	/*
	*	ユーザー情報の取得
	*	@args	ユーザーID　defult:null
	*
	*	reutrn	[ユーザー情報]
	*/
	public function getUserList($args=null) {
		$res = parent::request('POST', array('act'=>'getuserlist', 'args'=>$args));
		$data = unserialize($res);

		return $data;
	}

	/*
	*	ユーザーのお届け先の取得
	*	@args	ユーザーID　defult:null
	*
	*	reutrn	[お届け先情報]
	*/
	public function getDeli($args) {
//$this->takahama_log("conndb getDeli  333333 ".serialize($args));
		$res = parent::request('POST', array('act'=>'getdeli', 'args'=>$args));
//$this->takahama_log("conndb getDeli   ".$res);
		$data = unserialize($res);

		return $data;
	}

	/*
	*	ユーザーのお届け先更新
	*	@args	ユーザーID　defult:null
	*
	*	reutrn OK:{ユーザー情報}　NG:false
	*/
	public function updateDeli($args) {
		$res = parent::request('POST', array('act'=>'updatedeli', 'args'=>$args));
		$data = unserialize($res);

		return $data;
	}

	/*
	*	ユーザー情報の更新
	*	@args	['userid','uname','email','uicon','filename']
	*
	*	reutrn	true:OK　false:NG
	*/
	public function updateUser($args) {
		$res = parent::request('POST', array('act'=>'updateuser', 'args'=>$args));
		$data = unserialize($res);

		return $data;
	}
	
	
	/*
	*	パスワードの変更
	*	@args	['userid','pass']
	*
	*	reutrn	true:OK　false:NG
	*/
	public function updatePass($args) {
		$res = parent::request('POST', array('act'=>'updatepass', 'args'=>$args));
		$data = unserialize($res);

		return $data;
	}
	

	/*
	*	アドレスの変更
    * 2016-12-21
	*	@args	{'userid','zipcode','addr0','addr1','addr2'}
	*
	*	reutrn	true:OK　false:NG
	*/
	public function updateAddr($args) {
		$res = parent::request('POST', array('act'=>'updateaddr', 'args'=>$args));
		$data = unserialize($res);

		return $data;
	}


	/*
	*	メールアドレスの重複チェック
	*	@args	[メールアドレス]
	*	return	ユーザー情報:重複　false:新規
	*/
	public function checkExistEmail($args){
		$res = parent::request('POST', array('act'=>'checkexistemail', 'args'=>$args));
		$data = unserialize($res);

		return $data;
	}
	
	
	/*
	*	ユーザーネームの重複チェック
	*	@args	[ユーザーネーム, ユーザーID(default: null)]
	*	reutrn	true:重複　false:新規
	*/
	public function checkExistName($args) {
		$res = parent::request('POST', array('act'=>'checkexistname', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}

	/*
	*	イメージ画像表示
	*	return		イメージ画像
	*/
	public function getDesigned($order_id) {
		$res = parent::request('POST', array('act'=>'showDesignImg', 'order_id'=>$order_id, 'folder'=>'imgfile'));
		//$this->takahama_log("conndb getDesigned   ".$res);
		//$res = mb_convert_encoding($res,'euc-jp','utf-8');

		$json = new Services_JSON();
		$data = $json->decode($res);

//$data = $res;
//$this->takahama_log("conndb getDesigned   ".$data);

		return $data;

	}


}
?>
