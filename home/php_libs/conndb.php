<?php
/*
*	Database connection
*	charset     : utf-8
*
*/
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/JSON.php';
require_once dirname(__FILE__).'/http.php';
//require_once dirname(__FILE__).'/../../../dev_takahamalifeart.com/www/cgi-bin/review.php';
//require_once dirname(__FILE__).'/../../../dev_takahamalifeart.com/www/cgi-bin/master.php';

class Conndb extends HTTP {

public function takahama_log($logContext) {
//	$now = date('Y/m/d H:i:s'. '.' . substr(explode('.', (microtime(true) . ''))[1], 0, 3));
//	error_log($now.": ".$logContext."\n\r", 3, $_SERVER['DOCUMENT_ROOT'].'/debug.log.txt');
}
	
	public function __construct($args=_API){
		parent::__construct($args);
	}
	
	
	/*
	*	消費税率を取得
	*	@curdate		検索する日付（default:今日）
	*	@mode			general(default):2014-05-26より前は内税のため0を返す、industry:消費税率
	*
	*	@return			消費税率
	*/
	public function getSalesTax($curdate='', $mode='general'){
		if(empty($curdate)){
			$curdate = date('Y-m-d');
		}
		$res = parent::request('POST', array('act'=>'salestax', 'curdate'=>$curdate, 'ordertype'=>$mode));
		$data = unserialize($res);		
		return $data;
	}
	
	
	/*
	*	商品カテゴリー一覧
	*
	*	@return		[id:category_id, code:category_key, name:category_name][...]
	*/
	public function categoryList(){
//$this->takahama_log("categoryList");
		$res = parent::request('POST', array('act'=>'category'));
		$data = unserialize($res);
		
		return $data;
	}


	/*
	*	商品一覧
	*	@categoryid		カテゴリーID　default: 1
	*	@mode			NULL:default,　'item':第一引数でアイテムIDを渡す場合
	*
	*	@return			[id:item_id, code:item_code, name:item_name, posid:printposition_id][...]
	*/
	public function itemList($categoryid=1, $mode=NULL){
//$this->takahama_log("itemList");
		$res = parent::request('POST', array('act'=>'item', 'categoryid'=>$categoryid, 'mode'=>$mode, 'show_site'=>_SITE));
		$data = unserialize($res);
//$this->takahama_log("商品情報を返す3".$res);
		return $data;
	}
	
	
	/*
	*	サイズ一覧
	*	@id				アイテムID
	*	@colorcode		カラーコード
	*	@mode			id:アイテムID(default),  code:アイテムコード
	*
	*	@return			[id:size_from, name:size_name][...]
	*/
	public function itemSize($id, $colorcode=NULL, $mode='id'){
//$this->takahama_log("itemSize");
		$res = parent::request('POST', array('act'=>'size', 'itemid'=>$id, 'colorcode'=>$colorcode, 'mode'=>$mode, 'show_site'=>_SITE));
		$data = unserialize($res);
//$this->takahama_log("商品情報を返す4".$res);
		return $data;
	}
	
	
	/*
	*	商品価格
	*	@id				アイテムID
	*	@mode			id:アイテムID(default), code:アイテムコード
	*	@amount			量販単価の判別 0-149枚、150-299枚、300枚以上
	*
	*	@return			[sizeid:size_from, price_color:price_0, price_white:price_1, maker_color:price_0, maker_white:price_1]
	*/
	public function itemPrice($id, $mode='id', $amount=NULL){
//$this->takahama_log("itemPrice");
		$res = parent::request('POST', array('act'=>'price', 'itemid'=>$id, 'mode'=>$mode, 'show_site'=>_SITE));
		$data = unserialize($res);
//$this->takahama_log("商品情報を返す5".$res);
		return $data;
	}
	
	
	/*
	*	アイテムコードからIDを返す
	*	@itemcode		アイテムコード
	*
	*	@return			item_id
	*/
	public function itemID($itemcode){
//$this->takahama_log("itemID");
		$res = parent::request('POST', array('act'=>'itemid', 'itemcode'=>$itemcode, 'show_site'=>_SITE));
		$data = unserialize($res);
//$this->takahama_log("商品情報を返す6".$res);
		return $data;
	}
	
	
	/*
	*	アイテムごとに、シルクとデジタル転写で最安のプリント代を計算（プリント位置は1ヵ所）して最安の商品単価から見積り
	*	（商品詳細とシーン別ページ用）
	*	@itemid		アイテムコードの配列
	*	@amount		枚数の配列
	*	@ink		インク数の配列
	*	@pos		プリント位置の配列
	*	@sheetsize	転写のデザインサイズ　default:1
	*
	*	@return		{item_code:['price':見積金額, 'perone':1枚あたり],[...]}　引数に配列以外を設定した時はNULL
	*/
	public function estimateEach($itemcode, $amount, $ink, $pos, $sheetsize='1'){
//$this->takahama_log("estimateEach");
		$res = parent::request('POST', array('act'=>'estimateeach', 'sheetsize'=>$sheetsize, 'itemcode'=>$itemcode, 'amount'=>$amount, 'ink'=>$ink, 'pos'=>$pos));
		$data = unserialize($res);
//$this->takahama_log("商品情報を返す7".$res);		
		return $data;
	}
		
	
	/*
	*	プリント位置（絵型）の画像情報を返す
	*	@curitemid		ID
	*	@mode			id:アイテムID(default), code:アイテムコード, pos:プリントポジションID
	*
	*	@return			[プリント位置の絵型を配置するタグのテキストファイルへのパス, 位置名, position_id, ディレクトリ情報][...]
	*/
	public function positionFor($curitemid, $mode='id'){
//$this->takahama_log("positionFor");
		$res = parent::request('POST', array('act'=>'position', 'itemid'=>$curitemid, 'mode'=>$mode));
		$data = unserialize($res);
//$this->takahama_log("商品情報を返す8".$res);		
		/*---------------------------------------------------
		*	2013-10-24 
		*	下記2点のパーカーの絵型（フード前なし）の暫定的対応
		*	ID:348	241-cfh 裏起毛プルパーカー
		*	ID:349	242-cfz 裏起毛ジップパーカー
		*/
		
		if($curitemid=='348'){
			$data[0]['item']='parker-non-hood';
		}else if($curitemid=='349'){
			$data[0]['item']='zip-parker-non-hood';
		}
		
		$path = dirname(__FILE__).'/../common/txt/'.$data[0]['category'].'/'.$data[0]['item'].'/*.txt';
		$posid = $data[0]['id'];
		foreach (glob($path) as $filename) {
			$base = basename($filename, '.txt');
			if(strpos($base, 'front')!==false){
				$base_name = '前';
				$tmp[0] = array('filename'=>$filename, 'base_name'=>$base_name, 'posid'=>$posid, 'ppdata'=>$data[0]);
			}elseif(strpos($base, 'back')!==false){
				$base_name = '後';
				$tmp[1] = array('filename'=>$filename, 'base_name'=>$base_name, 'posid'=>$posid, 'ppdata'=>$data[0]);
			}elseif(strpos($base, 'side')!==false){
				$base_name = '横';
				$tmp[2] = array('filename'=>$filename, 'base_name'=>$base_name, 'posid'=>$posid, 'ppdata'=>$data[0]);
			}elseif(strpos($base, 'noprint')!==false){
				$base_name = 'プリントなし';
				$tmp[0] = array('filename'=>$filename, 'base_name'=>$base_name, 'posid'=>$posid, 'ppdata'=>$data[0]);
			}
		}
		
		// 添え字indexの付替え
		ksort($tmp, SORT_NUMERIC);
		foreach($tmp as $index=>$dat){
			$files[] = $dat;
		}
		
		return $files;
	}
	
	
	/*
	*	見積ページ
	************************************************/
	
	/*
	*	価格ごとのサイズ構成を取得してテーブルデータタグを生成
	*	@curitemid		アイテムID
	*	@colormode		白色が白色以外かの指定　white(default), color
	*	@mode			id:アイテムID(default), code:アイテムコード
	*
	*	@return			[[サイズ - サイズ　0,000円～, ...],白色が安い商品はture,[size_id:price,...]]
	*/
	public function priceFor($curitemid, $colormode='white', $mode='id'){
//$this->takahama_log("priceFor");
		$res = parent::request('POST', array('act'=>'price', 'itemid'=>$curitemid, 'mode'=>$mode));
		$data = unserialize($res);
		$isSwitch = false;
		if(empty($colormode)) $colormode = 'white';
		foreach($data as $key=>$val){
			$price[$val['sizeid']] = $val['price_'.$colormode];
			if($val['price_white']<$val['price_color']){
				$isSwitch = true;
			}
		}
		
		// 価格ごとにサイズ展開を設定して配列に代入
		$res = parent::request('POST', array('act'=>'size', 'itemid'=>$curitemid, 'mode'=>$mode));
		$size = unserialize($res);
		$rows = array();
		foreach($size as $key=>$val){
			if(empty($size_from)){
				$size_from = $val['name'];
				$size_to = '';
				$minprice = $price[$val['id']];
				$size_id = $val['id'];
			}else if($minprice==$price[$val['id']]){
				$size_to = $val['name'];
			}else{
				if($size_to==''){
					$rows[] = '<th class="sizefrom_'.$size_id.'">'.$size_from.'</th><td>'.number_format($minprice).'円&#65374;'.'</td>';
				}else{
					$rows[] = '<th class="sizefrom_'.$size_id.'">'.$size_from.' &minus; '.$size_to.'</th><td>'.number_format($minprice).'円&#65374;'.'</td>';
				}
				$size_from = $val['name'];
				$size_to = '';
				$minprice = $price[$val['id']];
				$size_id = $val['id'];
			}
		}
		if($size_to==''){
			$rows[] = '<th class="sizefrom_'.$size_id.'">'.$size_from.'</th><td>'.number_format($minprice).'円&#65374;'.'</td>';
		}else{
			$rows[] = '<th class="sizefrom_'.$size_id.'">'.$size_from.' &minus; '.$size_to.'</th><td>'.number_format($minprice).'円&#65374;'.'</td>';
		}
		
		$line ='';
		for($i=0; $i<count($rows); $i++){
			$line .= '<tr>'.$rows[$i].'<td><input type="number" value="0" min="0" class="forNum" /> '.'枚'.'</td></tr>';
		}
//$this->takahama_log("diaocha--------");
		return array($line, $isSwitch, $price);
	}

	
	/*
	*	注文フォーム、商品ページ
	************************************************/
	
	/*
	*	商品名とカラーごとのコード一覧データ
	*	@itemid			アイテムID　default: 1
	*
	*	@return			['name':[アイテムコード:アイテム名], 'category':[カテゴリーキー:カテゴリー名], code:[code:カラー名, ...], size[...], ppid:プリントポジションID]
	*					codeのフォーマットは、「アイテムコード＿カラーコード」　ex) 085-cvt_001
	*/
	public function itemAttr($itemid=1){
//$this->takahama_log("itemAttr");
		$res = parent::request('POST', array('act'=>'itemattr', 'itemid'=>$itemid, 'show_site'=>_SITE));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	アイテム一覧の商品情報、タグ情報あり
	*	@id				カテゴリID, タグID
	*	@tag			タグの配列
	*	@mode			idの種類 - category(default), tag
	*	@limit			検索レコード数
	*
	*	@return			[商品情報]
	*/
	public function itemOf($id, $tag=array(), $mode="category", $limit=0, $output=''){
//$this->takahama_log("itemOf");
		$res = parent::request('POST', array('act'=>'itemof', 'id'=>$id, 'tag'=>$tag, 'mode'=>$mode, 'limit'=>$limit, 'show_site'=>_SITE, 'output'=>$output));
		if ($output === 'json') {
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$data = $json->decode($res);
		} else {
			$data = unserialize($res);
		}
		
		return $data;
	}
	
	
	
	/**
	*	指定したタグ及びカテゴリのアイテムIDを返す
	*	@id			一覧ページの基底ID - カテゴリID, タグID
	*	@tag		タグID
	*	@mode		idの種類 - category(default), tag
	*	@target		検索対象のアイテムIDの配列
	*	@limit		検索レコード数
	*	@curdate	抽出条件に使用する日付(0000-00-00)。NULL:今日(default)
	*
	*	@return		[アイテムID, ...]
	*/
	public function itemIdOf($id, $tag=null, $mode='category', $target=null, $limit=null, $curdate=null){
//$this->takahama_log("itemIdOf");
		$res = parent::request('POST', array('act'=>'itemidof', 'id'=>$id, 'tag'=>$tag, 'mode'=>$mode, 'target'=>$target, 'limit'=>$limit, 'curdate'=>$curdate, 'show_site'=>_SITE));
		$data = unserialize($res);
		//------------------------
		return $data;
	}
	
	
	
	/*
	*	アイテムタグのマスター情報
	*	@id				タグID
	*
	*	@return			{tagid, tag_name, tag_type, tagtype_name, tagtype_key}
	*/
	public function tagInfo($id){
//$this->takahama_log("tagInfo");
		$res = parent::request('POST', array('act'=>'taginfo', 'id'=>$id));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	カテゴリーの商品情報
	*	@categoryid		カテゴリーID
	*	@mode			id:カテゴリーID、code:カテゴリーキー、list:カテゴリーIDで全サイズのリスト
	*
	*	@return			[category_key,item_id,item_name,item_code,size_id,size_from,size_to,colors,cost,pos_id][...]
	*/
	public function categories($categoryid, $mode='id'){
//$this->takahama_log("categories");
//----------------------------------------
		$res = parent::request('POST', array('act'=>'categories', 'id'=>$categoryid, 'mode'=>$mode ,'curdate'=>$curdate, 'show_site'=>_SITE ));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	商品ページの基本情報（サイズ数、カラー数、最安価格）
	*	@id				アイテムID
	*	@mode			id:アイテムID、code:アイテムコード
	*
	*	@return			{'item_name':item_name, 'sizes':size_count, 'colors':color_count, 'mincost':mincost}
	*/
	public function itemPageInfo($id, $mode='id'){
//$this->takahama_log("itemPageInfo");
		$res = parent::request('POST', array('act'=>'itempageinfo', 'id'=>$id, 'mode'=>$mode));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	注文フォーム
	************************************************/
	
	/*
	*	サイズと価格のデータ
	*	@curitemid		アイテムID　default: 1
	*	@colorcode		アイテムカラーコード　default: ''
	*
	*	@return			['id':サイズID, 'name':サイズ名, 'cost':販売価格, 'series':サイズシリーズID][...]
	*/
	public function sizePrice($itemid=1, $colorcode=''){
//$this->takahama_log("sizePrice");
		$res = parent::request('POST', array('act'=>'sizeprice', 'itemid'=>$itemid, 'colorcode'=>$colorcode));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	シルクとデジタル転写で最安のプリント代合計を返す
	*	@args		['itemid'=>itemid, 'amount'=>amount, 'ink'=>inkcount, 'pos'=>posname][][]
	*	@sheetsize	転写のデザインサイズ　default:1
	*
	*	@return		['printfee':プリント代, 'volume':枚数, 'tax':消費税率]　引数に配列以外を設定した時はNULL
	*/
	public function printfee($args, $sheetsize='1'){
		$res = parent::request('POST', array('act'=>'printfee', 'sheetsize'=>$sheetsize, 'args'=>$args, 'show_site'=>_SITE));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	
	/*
	*	注文ページ
	*	注文内容をデータベースに登録
	*	
	*	@args			[data1, data2, data3, ...]
	*
	*	@return			成功：ID　失敗：null
	************************************************/
	public function acceptingorder($args){
//$this->takahama_log("acceptingorder");
		$res = parent::request('POST', array('act'=>'acceptingorder', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	
	/*
	*	資料請求ページ
	*	フォームの内容をデータベースに登録
	*	
	*	@args			["requester", "subject", "message", "reqmail", "site_id"]
	*
	*	@return			成功：ID　失敗：null
	************************************************/
	public function requestmail($args){
//$this->takahama_log("requestmail");
		$res = parent::request('POST', array('act'=>'requestmail', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	
	/*
	*	商品到着確認後のアンケート結果を
	*	データベースに登録
	*	
	*	@args			["requester", "subject", "message", "reqmail", "site_id"]
	*
	*	@return			成功：ID　失敗：null
	************************************************/
	public function setEnquete($args){
//$this->takahama_log("setEnquete");
		$res = parent::request('POST', array('act'=>'enquete', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	
	
	/*
	*	カスタマーセンター
	************************************************/
	
	/*
	*	ユーザーの存在確認
	*	@args	['email','pass']
	*
	*	reutrn	OK:{ユーザー情報}　NG:false
	*/
	public function getUser($args) {
//$this->takahama_log("getUser");
		$res = parent::request('POST', array('act'=>'getuser', 'args'=>$args));
//$this->takahama_log('conndb get user res = '.$res);
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	ユーザー情報の取得
	*	
	*	@args			[customer.id(default: null)]
	*
	*	@return			[顧客情報]
	************************************************/
	public function getUserList($args=null) {
//$this->takahama_log("getUserList");
		$res = parent::request('POST', array('act'=>'getuserlist', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	
	/*
	*	お届け先情報の取得
	*	
	*	@args			[customer.id(default: null)]
	*
	*	@return			[お届け先情報]
	************************************************/
	public function getDeliveryList($args=null) {
//$this->takahama_log("getDeliveryList");
		$res = parent::request('POST', array('act'=>'getdeliverylist', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	
	/*
	*	メールアドレスの存在確認
	*	
	*	@args			[e-mail]
	*
	*	@return			[顧客情報]
	************************************************/
	public function checkExistEmail($args){
		$res = parent::request('POST', array('act'=>'checkexistemail', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	お知らせメールの配信停止処理
	*	
	*	@args			{'customer_id', 'cancel'(停止:1)'
	*
	*	@return			成功:true  失敗:false
	************************************************/
	public function unsubscribe($args){
//$this->takahama_log("unsubscribe");
		$res = parent::request('POST', array('act'=>'unsubscribe', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	
	/*
	*	レビュー
	************************************************/
	
	/*
	*	お客様レビュー取得
	*	
	*	@args			ソート項目（新着順：post　評価の高い順：high　評価の低い順：low）
	*
	*	@return			[]
	************************************************/
	public function getUserReview($args){
//$this->takahama_log("getUserReview");
		$res = parent::request('POST', array('act'=>'userreview', 'args'=>$args));
		$data = unserialize($res);
//		$rev = new Review();
//		$data=$rev->getUserReview($args);
//$this->takahama_log("getUserReview".$data);
		return $data;
	}
	
	/*
	*	アイテムレビュー取得
	*	
	*	@args			{sort: ソート項目（新着順：posted　評価の高い順：high　評価の低い順：low）,
	*					itemid:	アイテムID}
	*
	*	@return			[]
	************************************************/
	public function getItemReview($args){
//$this->takahama_log("getItemReview");
		$res = parent::request('POST', array('act'=>'itemreview', 'args'=>$args));
		$data = unserialize($res);
//		$rev = new Review();
//		$data=$rev->getItemReview($args);
		return $data;
	}
	
	/*
	*	アイテム詳細ページデータ取得
	*	
	*	@args			アイテムコード
	*
	*	@return			[]
	************************************************/
	public function getItemDetail($args){
//$this->takahama_log("getItemDetail");
		$res = parent::request('POST', array('act'=>'itemdetail', 'args'=>$args));
		$data = unserialize($res);
		
		return $data;
	}
	
	/*
	*	寸法データ取得
	*	
	*	@args			アイテムコード
	*
	*	@return			[]
	************************************************/
	public function getItemMeasure($args){
//$this->takahama_log("getItemMeasure");
		$res = parent::request('POST', array('act'=>'itemmeasure', 'args'=>$args));
		$data = unserialize($res);
//		$mst = new Master();
//		$data=$mst->getItemMeasure($args);
		return $data;
	}
	
	/*
	*	モデル写真
	*	
	*	@cat		categorykey	
	* 	@code		itemcode
	*
	*	@return			[]
	************************************************/
	public function getModelPhoto($cat, $code){
//$this->takahama_log("getModelPhoto");
		$res = parent::request('POST', array('act'=>'modelphoto', 'categorykey'=>$cat, 'itemcode'=>$code));
		$data = unserialize($res);
		
		return $data;
	}
	
	/*
	*	スタイル写真
	*	
	*	@cat		categorykey	
	* 	@code		itemcode
	*
	*	@return			[]
	************************************************/
	public function getStylePhoto($cat, $code){
//$this->takahama_log("getStylePhoto");
		$res = parent::request('POST', array('act'=>'stylephoto', 'categorykey'=>$cat, 'itemcode'=>$code));
		$data = unserialize($res);
		
		return $data;
	}


}
?>
