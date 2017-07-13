<?php
/*
*	お申し込みページ
*	１０秒見積もりページ
*	charset UTF-8
*/

require_once dirname(__FILE__).'/conndb.php';

class Items{

	private $_PAGE_CATEGORY_ID = 1;
	private $_PAGE_CATEGORY_KEY = 't-shirts';
	private $_CATEGORY_KEYS = array(
			"t-shirts"=>1,
			"sweat"=>2,
			"polo-shirts"=>3,
			"sportswear"=>4,
			"ladys"=>5,
			"outer"=>6,
			"cap"=>7,
			"towel"=>8,
			"tote-bag"=>9,
			"apron"=>10,
			"workwear"=>11,
			"goods"=>12,
			"long-shirts"=>13,
			"baby"=>14,
			"overall"=>16
		);
	/*
	*	sweat はSweatJackのみのアイテムコード
	*/
	private static $_SUB_CATEGORY = array(
		"sweat"=>array()
	);
	
	// カテゴリ毎のシルエットで使用する絵型IDをキーにしたタグIDと絵型名のハッシュ
	private $silhouetteId = array(
		array(),
		array(
			1=>array('tag'=>93,'label'=>'綿素材'),
			3=>array('tag'=>2,'label'=>'ドライ')
		),
		array(
			6=>array('tag'=>15,'label'=>'トレーナー'),
			7=>array('tag'=>13,'label'=>'プルパーカー'),
			8=>array('tag'=>16,'label'=>'パンツ'),
			10=>array('tag'=>14,'label'=>'ジップパーカー')
		),
		array(
			63=>array('tag'=>102,'label'=>'ポケット無し'),
			64=>array('tag'=>8,'label'=>'ポケット有り')
		),
		array(
			3=>array('tag'=>103,'label'=>'GAME'),
			16=>array('tag'=>104,'label'=>'TRAINING')
		),
		array(
			3=>array('tag'=>2,'label'=>'ドライ'),
			41=>array('tag'=>93,'label'=>'綿素材')
		),
		array(
			18=>array('tag'=>6,'label'=>'薄い生地'),
			23=>array('tag'=>5,'label'=>'厚い生地')
		),
		array(
			25=>array('tag'=>107,'label'=>'キャップ'),
			32=>array('tag'=>33,'label'=>'バンダナ')
		),
		array(
			29=>array('tag'=>78,'label'=>'タオル')
		),
		array(
			30=>array('tag'=>79,'label'=>'トートバッグ')
		),
		array(
			27=>array('tag'=>41,'label'=>'肩がけ'),
			34=>array('tag'=>42,'label'=>'腰巻き')
		),
		array(
			40=>array('tag'=>19,'label'=>'パンツ'),
			43=>array('tag'=>108,'label'=>'シャツ')
		),
		array(
			32=>array('tag'=>83,'label'=>'全アイテム')
		),
		array(
			2=>array('tag'=>12,'label'=>'長袖'),
			4=>array('tag'=>7,'label'=>'七部袖')
		),
		array(
			31=>array('tag'=>81,'label'=>'ベビー')
		),
		array(),
		array(
			49=>array('tag'=>12,'label'=>'長袖'),
			50=>array('tag'=>11,'label'=>'半袖')
		),
	);


	/*
	* プリントポジションIDをキーにした、たぐIDと絵型名のハッシュ返す
	* @id	category ID
	*/
	public function getSilhouetteId($id=null){
		if(empty($id)){
			return $this->silhouetteId;
		}else{
			return $this->silhouetteId[$id];
		}
	}


	public function __construct($category_key='t-shirts'){
		$this->_PAGE_CATEGORY_ID = $this->_CATEGORY_KEYS[$category_key];
		$this->_PAGE_CATEGORY_KEY = $category_key;
	}
	
	
	/*
	*	カテゴリーの設定
	*/
	public function setCategory($category_key){
		$this->_PAGE_CATEGORY_ID = $this->_CATEGORY_KEYS[$category_key];
		$this->_PAGE_CATEGORY_KEY = $category_key;
	}
	
	
	/*
	*	プロパティを返す
	*/
	public function getCategoryID(){
		return $this->_PAGE_CATEGORY_ID;
	}
	public function getCategoryKey(){
		return $this->_PAGE_CATEGORY_KEY;
	}
	public function getCategoryKEYS(){
		return $this->_CATEGORY_KEYS;
	}
	
	
	/*
	*	カテゴリーのサブグループを返す
	*/
	public static function getSubCategory(){
		return self::$_SUB_CATEGORY;
	}
	
	
	/*
	*	Step1 のアイテム一覧表示用のデータ取得
	*/
	public function getItemlist(){
		if(empty($this->_PAGE_CATEGORY_ID)) return;

		$conn = new Conndb();
		$mode = 'list';
		$data = $conn->categories($this->_PAGE_CATEGORY_ID, $mode);

		for($i=0; $i<count($data); $i++){
			if($cur!=$data[$i]['item_code']){
				$cur = $data[$i]['item_code'];
				if($i!=0){
					for($t=0; $t<3; $t++){
						if(!empty($size[$t])){
							if($size[$t][0]!=$size[$t][count($size[$t])-1]){
								$size[3][] = $size[$t][0].'-'.$size[$t][count($size[$t])-1];
							}else{
								$size[3][] = $size[$t][0];
							}
						}
					}
					$code = $data[$i-1]['item_code'];
					$res[$code]['maker_id'] = $data[$i-1]['maker_id'];
					$res[$code]['category_key'] = $data[$i-1]['category_key'];
					$res[$code]['item_row'] = $data[$i-1]['item_row'];
					$res[$code]['item_id'] = $data[$i-1]['item_id'];
					$res[$code]['item_name'] = $data[$i-1]['item_name'];
					$res[$code]['colors'] = $data[$i-1]['colors'];
					$res[$code]['pos_id'] = $data[$i-1]['pos_id'];
					$res[$code]['sizes'] = $size_count;
					$res[$code]['minprice'] = $minprice;

					$res[$code]['initcolor'] = $data[$i-1]['i_color_code'];	//$ic[$code];
					$res[$code]['features'] = $data[$i-1]['i_caption'];	//$f[$code];
				}
				$size = array();
				$size_count = 0;
				$minprice = number_format($data[$i]['cost']);
			}

			$size_count++;
			if($data[$i]['size_id']<11){									// 70-160
				$size[0][] = $data[$i]['size_from'];
			}else if($data[$i]['size_id']<17 || $data[$i]['size_id']>28){	// JS-JL, GS-GL, WS-WL
				$size[1][] = $data[$i]['size_from'];
			}else{															// XS-8L
				$size[2][] = $data[$i]['size_from'];
			}
		}
		for($t=0; $t<3; $t++){
			if(!empty($size[$t])){
				if($size[$t][0]!=$size[$t][count($size[$t])-1]){
					$size[3][] = $size[$t][0].'-'.$size[$t][count($size[$t])-1];
				}else{
					$size[3][] = $size[$t][0];
				}
			}
		}
		$code = $data[$i-1]['item_code'];
		$res[$code]['maker_id'] = $data[$i-1]['maker_id'];
		$res[$code]['category_key'] = $data[$i-1]['category_key'];
		$res[$code]['item_row'] = $data[$i-1]['item_row'];
		$res[$code]['item_id'] = $data[$i-1]['item_id'];
		$res[$code]['item_name'] = $data[$i-1]['item_name'];
		$res[$code]['colors'] = $data[$i-1]['colors'];
		$res[$code]['pos_id'] = $data[$i-1]['pos_id'];
		$res[$code]['sizes'] = $size_count;
		$res[$code]['minprice'] = $minprice;

		$res[$code]['initcolor'] = $data[$i-1]['i_color_code'];	//$ic[$code];
		$res[$code]['features'] = $data[$i-1]['i_caption'];	//$f[$code];

		// カテゴリーに関係のないアイテムの指定
		if(isset($_ITEM_DATA)){
			for($a=0; $a<count($_ITEM_DATA); $a++){
				$itemcode = $_ITEM_DATA[$a];
				$pageinfo = $conn->itemPageInfo($itemcode, 'code');

				$res[$itemcode]['item_name'] = $pageinfo['item_name'];
				$res[$itemcode]['colors'] = $pageinfo['colors'];
				$res[$itemcode]['sizes'] = $pageinfo['sizes']; $res[$itemcode]['minprice'] = number_format($pageinfo['mincost']); $res[$itemcode]['initcolor'] = $pageinfo['initcolor']; $res[$itemcode]['features'] = $pageinfo['caption'];
			}
		}
		return $res;
	}
}


$isJSON = false;
if (isset($_REQUEST['category_key'])) {
	$isJSON = true;
	$_items = new Items($_REQUEST['category_key']);
	$dat = $_items->getItemlist();
} else if (isset($_REQUEST['subcategory'])) {
	$isJSON = true;
	$dat = Items::getSubCategory();
} else if ($_REQUEST['act'] == 'itemtype') {
	$_items = new Items();
	$dat = $_items->getSilhouetteId($_REQUEST['category_id']);
	if(isset($_REQUEST['output']) && $_REQUEST['output']=='jsonp'){
		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$res = $json->encode($dat);
		header("Content-Type: text/javascript; charset=utf-8");
		echo $res;
	}
}
if ($isJSON) {
	$isJSON = false;
	$json = new Services_JSON();
	$res = $json->encode($dat);
	header("Content-Type: text/javascript; charset=utf-8");
	echo $res;
}
?>