<?php
/**************************************
°°•π•ﬁ•€»«•µ•§•»
°°æ¶… æ Û§ŒºË∆¿
°°charset utf-8
***************************************/


require_once $_SERVER['DOCUMENT_ROOT'].'/php_libs/conndb.php';

class ItemInfo extends Conndb {

	public function __construct(){
		parent::__construct();
	}
	
	// æ¶… •⁄°º•∏
	public function getData($category, $itemid=null, $priceorder='index'){
		$data = array();
		if(!is_null($itemid)){
			$data = parent::itemList($itemid, "item");
		}else{
			$cat = parent::categoryList();
			for($i=0; $i<count($cat); $i++){
				if($cat[$i]['code']==$category){
					$data = parent::itemList($cat[$i]['id']);
					break;
				}
			}
			if($category=='sportswear'){
				foreach($data as $key=>$val){
					$sub[$val["id"]] = true;
				}
				$tmp = parent::itemList(2, 'tag');	// •π•›°º•ƒ•¶•ß•¢§À•…•È•§•ø•∞§Úƒ…≤√
				foreach($tmp as $key1=>$val1){
					if(!array_key_exists($val1["id"], $sub)){
						$data[] = $tmp[$key1];
					}
				}
			}
		}
		
		foreach($data as $key=>$val){
			// •¢•§•∆•‡•Ï•”•Â°º
			$review = parent::getItemReview(array('sort'=>'post', 'itemid'=>$val['id']));
			$len = count($review);
			$r[$key]['len'] = $len;
			
			// •Ï•”•Â°º¡ÌπÁ…æ≤¡
			$v = 0;
			for($i=0; $i<$len; $i++){
				$v += $review[$i]['vote'];
			}
			if($v==0){
				$r[$key]['ratio'] = 0;
			}else{
				$r[$key]['ratio'] = round($v/$len, 1);
			}
			$r[$key]['img'] = $this->getStar($r[$key]['ratio']);

			$r[$key]['itemid'] = $val['id'];
			$r[$key]['itemcode'] = $val['code'];
			$r[$key]['itemname'] = $val['name'];
			$r[$key]['row'] = $val['item_row'];
			$r[$key]['posid'] = $val['posid'];
			$r[$key]['price'] = $val['cost'];	// ∫«∞¬√±≤¡
			
			$attr = parent::itemAttr($val['id']);
			$r[$key]['color'] = count($attr['code']);	// color count
			list($itemCode_colorCode, $sizehash) = each($attr['size']);
			$r[$key]['sizecount'] = count($sizehash);	// size count
			$part = explode('_', $itemCode_colorCode);
			$currentColorCode = $part[1];
			
			if( preg_match('/^p-/',$val['code']) && $val['i_color_code']==""){
				$suffix = '_style_0'; 
			}else{ 
				$suffix = '_'.$val['i_color_code']; 
			}
			$r[$key]['imgname'] = $val['code'].$suffix;

			list($categorykey, $categoryname) = each($attr['category']);
			$r[$key]['categorykey'] = $categorykey;
			$r[$key]['categoryname'] = $categoryname;
			
			// •¢•§•∆•‡æ‹∫Ÿ•⁄°º•∏
			if(!is_null($itemid)){
				// •Ï•”•Â°º§Œ•∆•≠•π•»§Ú2∑Ô§ﬁ§« ÷§π
				$reviewcount = 2;
				if($len<2){
					$reviewcount = $len;
				}
				for ($i=0; $i < $reviewcount; $i++) { 
					$r[$key]['review'][$i] = $review[$i];
				}
				
				// •µ•§•∫ÀË§Œ√±≤¡
				$priceHash = parent::sizePrice($val['id'], $currentColorCode);
				for($i=0; $i<count($priceHash); $i++){
					$r[$key]['size_price'][$priceHash[$i]['id']] = $priceHash[$i]['cost'];
				}
				
				// •µ•§•∫≈∏≥´
				$r[$key]['size'] = $sizehash;
				foreach($sizehash as $sizeid=>$sizename){
					if($sizeid<11){								// 70-160
						$s[0][] = array($sizeid,$sizename);
					}else if($sizeid<17 || $sizeid>28){			// JS-JL, GS-GL, WS-WL
						$s[1][] = array($sizeid,$sizename);
					}else{										// XS-8L
						$s[2][] = array($sizeid,$sizename);
					}
				}
				for($i=0; $i<3; $i++){
					if(!empty($s[$i])){
						if($s[$i][0]!=$s[$i][count($s[$i])-1]){
							if($s[$i][0][0]+1==$s[$i][1][0]){
								$s[3][] = $s[$i][0][1].'-'.$s[$i][count($s[$i])-1][1];
							}else{
								for($t=0; $t<count($s[$i]); $t++){
									$s[3][] = $s[$i][$t][1];
								}
							}
						}else{
							$s[3][] = $s[$i][0][1];
						}
					}
				}
				$r[$key]['sizeseries'] = implode(', ', $s[3]);
				
				// •¢•§•∆•‡•´•È°º[{code:•´•È°ºÃæ},{},...]
				$r[$key]['thumbs'] = $attr['code'];
				
				// •¢•§•∆•‡¿‚Ã¿§»¡«∫‡
				$r[$key]['explain'] = $val['i_description'];
				$r[$key]['material'] = $val['i_material'];
				
				// ¿£À°
				$r[$key]['measure'] = parent::getItemMeasure($val['code']);
			}
		}
		
		// •Ω°º•»
		if($priceorder!='index'){
			$sort = 'price';
		}else{
			$sort = 'row';
		}
		foreach($r as $key=>$row){
			$sortkey[$key] = $row[$sort];
		}
		if($priceorder!='high'){
			array_multisort($sortkey,SORT_ASC,SORT_NUMERIC,$r);
			//usort($data, 'sort_asc');
		}else{
			array_multisort($sortkey,SORT_DESC,SORT_NUMERIC,$r);
			//usort($data, 'sort_desc');
		}
		
		return $r;
	}
	
	
	// •´•∆•¥•ÍÀË§Œ•∑•Î•®•√•»§«ª»Õ—§π§Î≥®∑øID§»≥®∑øÃæ§Œ•œ•√•∑•Â
	private $silhouetteId = array('',
						array(1=>'Ã ¡«∫‡',3=>'•…•È•§'),
						array(6=>'•»•Ï°º• °º',7=>'•◊•Î•—°º•´°º',8=>'•—•Û•ƒ',10=>'•∏•√•◊•—°º•´°º'),
						array(12=>'•›•±•√•»Ãµ§∑',13=>'•›•±•√•»Õ≠§Í'),
						array(3=>'GAME',16=>'TRAINING'),
						array(3=>'•…•È•§',7=>'•π•¶•ß•√•»',41=>'Ã ¡«∫‡'),
						array(18=>'«ˆ§§¿∏√œ',23=>'∏¸§§¿∏√œ'),
						array(25=>'•≠•„•√•◊',32=>'•–•Û•¿• '),
						array(29=>'•ø•™•Î'),
						array(30=>'•»°º•»•–•√•∞'),
						array(27=>'∏™§¨§±',34=>'π¯¥¨§≠'),
						array(18=>'•…•Í•∫•È°º',40=>'•—•Û•ƒ'),
						array(32=>'¡¥•¢•§•∆•‡'),
						array(2=>'ƒπ¬µ',4=>'º∑…Ù¬µ'),
						array(31=>'•Ÿ•”°º'),
						array(),
						array(49=>'ƒπ¬µ',50=>'»æ¬µ'),
					);
	
	// ≥∆≥®∑ø§Œ•◊•Í•Û•»∞Ã√÷§ŒÃææŒ
	private $positionName = array(
		"normal-tshirts_01"=>"§ﬁ§®",
		"normal-tshirts_02"=>"∫∏∂ª",
		"normal-tshirts_03"=>"±¶∂ª",
		"normal-tshirts_04"=>"∫∏ø˛",
		"normal-tshirts_05"=>"§ﬁ§®ø˛",
		"normal-tshirts_06"=>"±¶ø˛",
		"normal-tshirts_07"=>"∏Â§Ì",
		"normal-tshirts_08"=>"ºÛ∏Â§Ì",
		"normal-tshirts_09"=>"∏Â∫∏ø˛",
		"normal-tshirts_10"=>"∏Â§Ìø˛",
		"normal-tshirts_11"=>"∏Â±¶ø˛",
		"normal-tshirts_12"=>"∫∏¬µ",
		"normal-tshirts_13"=>"∫∏œ∆",
		"normal-tshirts_14"=>"±¶¬µ",
		"normal-tshirts_15"=>"±¶œ∆",
		"long-tshirts_01"=>"§ﬁ§®",
		"long-tshirts_02"=>"∫∏∂ª",
		"long-tshirts_03"=>"±¶∂ª",
		"long-tshirts_04"=>"∫∏ø˛",
		"long-tshirts_05"=>"§ﬁ§®ø˛",
		"long-tshirts_06"=>"±¶ø˛",
		"long-tshirts_07"=>"∏Â§Ì",
		"long-tshirts_08"=>"ºÛ∏Â§Ì",
		"long-tshirts_09"=>"∏Â∫∏ø˛",
		"long-tshirts_10"=>"∏Â§Ìø˛",
		"long-tshirts_11"=>"∏Â±¶ø˛",
		"long-tshirts_12"=>"∫∏œ”",
		"long-tshirts_13"=>"∫∏¬µ∏˝",
		"long-tshirts_14"=>"∫∏œ∆",
		"long-tshirts_15"=>"±¶œ”",
		"long-tshirts_16"=>"±¶¬µ∏˝",
		"long-tshirts_17"=>"±¶œ∆",
		"raglan-tshirts_01"=>"§ﬁ§®",
		"raglan-tshirts_02"=>"∫∏∂ª",
		"raglan-tshirts_03"=>"±¶∂ª",
		"raglan-tshirts_04"=>"∫∏ø˛",
		"raglan-tshirts_05"=>"§ﬁ§®ø˛",
		"raglan-tshirts_06"=>"±¶ø˛",
		"raglan-tshirts_07"=>"∏Â§Ì",
		"raglan-tshirts_08"=>"ºÛ∏Â§Ì",
		"raglan-tshirts_09"=>"∏Â∫∏ø˛",
		"raglan-tshirts_10"=>"∏Â§Ìø˛",
		"raglan-tshirts_11"=>"∏Â±¶ø˛",
		"raglan-tshirts_12"=>"∫∏¬µ",
		"raglan-tshirts_13"=>"∫∏œ∆",
		"raglan-tshirts_14"=>"±¶¬µ",
		"raglan-tshirts_15"=>"±¶œ∆",
		"raglan-long-tshirts_01"=>"§ﬁ§®",
		"raglan-long-tshirts_02"=>"∫∏∂ª",
		"raglan-long-tshirts_03"=>"±¶∂ª",
		"raglan-long-tshirts_04"=>"∫∏ø˛",
		"raglan-long-tshirts_05"=>"§ﬁ§®ø˛",
		"raglan-long-tshirts_06"=>"±¶ø˛",
		"raglan-long-tshirts_07"=>"∏Â§Ì",
		"raglan-long-tshirts_08"=>"ºÛ∏Â§Ì",
		"raglan-long-tshirts_09"=>"∏Â∫∏ø˛",
		"raglan-long-tshirts_10"=>"∏Â§Ìø˛",
		"raglan-long-tshirts_11"=>"∏Â±¶ø˛",
		"raglan-long-tshirts_12"=>"∫∏œ”",
		"raglan-long-tshirts_13"=>"∫∏¬µ∏˝",
		"raglan-long-tshirts_14"=>"∫∏œ∆",
		"raglan-long-tshirts_15"=>"±¶œ”",
		"raglan-long-tshirts_16"=>"±¶¬µ∏˝",
		"raglan-long-tshirts_17"=>"±¶œ∆",
		"tanktop_01"=>"§ﬁ§®",
		"tanktop_02"=>"∫∏∂ª",
		"tanktop_03"=>"±¶∂ª",
		"tanktop_04"=>"∫∏ø˛",
		"tanktop_05"=>"§ﬁ§®ø˛",
		"tanktop_06"=>"±¶ø˛",
		"tanktop_07"=>"∏Â§Ì",
		"tanktop_08"=>"ºÛ∏Â§Ì",
		"tanktop_09"=>"∫∏ø˛",
		"tanktop_10"=>"∏Â§Ìø˛",
		"tanktop_11"=>"±¶ø˛",
		"tanktop_12"=>"∫∏œ∆",
		"tanktop_13"=>"±¶œ∆",
		"trainer_01"=>"§ﬁ§®",
		"trainer_02"=>"∫∏∂ª",
		"trainer_03"=>"±¶∂ª",
		"trainer_04"=>"∫∏ø˛",
		"trainer_05"=>"§ﬁ§®ø˛",
		"trainer_06"=>"±¶ø˛",
		"trainer_07"=>"∏Â§Ì",
		"trainer_08"=>"ºÛ∏Â§Ì",
		"trainer_09"=>"∏Â∫∏ø˛",
		"trainer_10"=>"∏Â§Ìø˛",
		"trainer_11"=>"∏Â±¶ø˛",
		"trainer_12"=>"∫∏œ”",
		"trainer_13"=>"∫∏¬µ∏˝",
		"trainer_14"=>"±¶œ”",
		"trainer_15"=>"±¶¬µ∏˝",
		"parker_01"=>"§ﬁ§®",
		"parker_02"=>"∫∏∂ª",
		"parker_03"=>"±¶∂ª",
		"parker_04"=>"¡∞•’°º•…",
		"parker_05"=>"∏Â§Ì",
		"parker_06"=>"∏Â∫∏ø˛",
		"parker_07"=>"∏Â§Ìø˛",
		"parker_08"=>"∏Â±¶ø˛",
		"parker_09"=>"∫∏œ”",
		"parker_10"=>"∫∏•’°º•…",
		"parker_11"=>"±¶œ”",
		"parker_12"=>"±¶•’°º•…",
		"long-pants_01"=>"∫∏¡∞",
		"long-pants_02"=>"∫∏§‚§‚¡∞",
		"long-pants_03"=>"∫∏¬≠¡∞",
		"long-pants_04"=>"±¶¡∞",
		"long-pants_05"=>"±¶§‚§‚¡∞",
		"long-pants_06"=>"±¶¬≠¡∞",
		"long-pants_07"=>"∫∏∏Â",
		"long-pants_08"=>"∫∏§‚§‚∏Â",
		"long-pants_09"=>"∫∏¬≠∏Â",
		"long-pants_10"=>"±¶∏Â",
		"long-pants_11"=>"±¶§‚§‚∏Â",
		"long-pants_12"=>"±¶¬≠∏Â",
		"short-pants_01"=>"∫∏¡∞ø˛",
		"short-pants_02"=>"±¶¡∞ø˛",
		"short-pants_03"=>"∫∏∏Â",
		"short-pants_04"=>"±¶∏Â",
		"short-pants_05"=>"∫∏∏Âø˛",
		"short-pants_06"=>"±¶∏Âø˛",
		"zip-parker_01"=>"∫∏∂ª",
		"zip-parker_02"=>"±¶∂ª",
		"zip-parker_03"=>"¡∞•’°º•…",
		"zip-parker_04"=>"∏Â§Ì",
		"zip-parker_05"=>"∏Â∫∏ø˛",
		"zip-parker_06"=>"∏Â§Ìø˛",
		"zip-parker_07"=>"∏Â±¶ø˛",
		"zip-parker_08"=>"∫∏œ”",
		"zip-parker_09"=>"∫∏•’°º•…",
		"zip-parker_10"=>"±¶œ”",
		"zip-parker_11"=>"±¶•’°º•…",
		"zip-jacket_01"=>"∫∏∂ª",
		"zip-jacket_02"=>"±¶∂ª",
		"zip-jacket_03"=>"∏Â§Ì",
		"zip-jacket_04"=>"ºÛ∏Â§Ì",
		"zip-jacket_05"=>"∏Â∫∏ø˛",
		"zip-jacket_06"=>"∏Â§Ìø˛",
		"zip-jacket_07"=>"∏Â±¶ø˛",
		"zip-jacket_08"=>"∫∏œ”",
		"zip-jacket_09"=>"∫∏¬µ∏˝",
		"zip-jacket_10"=>"±¶œ”",
		"zip-jacket_11"=>"±¶¬µ∏˝",
		"polo-non-pocket_01"=>"∫∏∂ª",
		"polo-non-pocket_02"=>"±¶∂ª",
		"polo-non-pocket_03"=>"§ﬁ§®",
		"polo-non-pocket_04"=>"∫∏ø˛",
		"polo-non-pocket_05"=>"§ﬁ§®ø˛",
		"polo-non-pocket_06"=>"±¶ø˛",
		"polo-non-pocket_07"=>"∏Â§Ì",
		"polo-non-pocket_08"=>"ºÛ∏Â§Ì",
		"polo-non-pocket_09"=>"∏Â∫∏ø˛",
		"polo-non-pocket_10"=>"∏Â§Ìø˛",
		"polo-non-pocket_11"=>"∏Â±¶ø˛",
		"polo-non-pocket_12"=>"∫∏¬µ",
		"polo-non-pocket_13"=>"±¶¬µ",
		"polo-with-pocket_01"=>"•›•±æÂ",
		"polo-with-pocket_02"=>"•›•±•√•»",
		"polo-with-pocket_03"=>"±¶∂ª",
		"polo-with-pocket_04"=>"§ﬁ§®",
		"polo-with-pocket_05"=>"∫∏ø˛",
		"polo-with-pocket_06"=>"§ﬁ§®ø˛",
		"polo-with-pocket_07"=>"±¶ø˛",
		"polo-with-pocket_08"=>"∏Â§Ì",
		"polo-with-pocket_09"=>"ºÛ∏Â§Ì",
		"polo-with-pocket_10"=>"∏Â∫∏ø˛",
		"polo-with-pocket_11"=>"∏Â§Ìø˛",
		"polo-with-pocket_12"=>"∏Â±¶ø˛",
		"polo-with-pocket_13"=>"∫∏¬µ",
		"polo-with-pocket_14"=>"±¶¬µ",
		"longpolo-non-pocket_01"=>"∫∏∂ª",
		"longpolo-non-pocket_02"=>"±¶∂ª",
		"longpolo-non-pocket_03"=>"§ﬁ§®",
		"longpolo-non-pocket_04"=>"∫∏ø˛",
		"longpolo-non-pocket_05"=>"§ﬁ§®ø˛",
		"longpolo-non-pocket_06"=>"±¶ø˛",
		"longpolo-non-pocket_07"=>"∏Â§Ì",
		"longpolo-non-pocket_08"=>"ºÛ∏Â§Ì",
		"longpolo-non-pocket_09"=>"∏Â∫∏ø˛",
		"longpolo-non-pocket_10"=>"∏Â§Ìø˛",
		"longpolo-non-pocket_11"=>"∏Â±¶ø˛",
		"longpolo-non-pocket_12"=>"∫∏œ”",
		"longpolo-non-pocket_13"=>"∫∏¬µ∏˝",
		"longpolo-non-pocket_14"=>"±¶œ”",
		"longpolo-non-pocket_15"=>"±¶¬µ∏˝",
		"longpolo-with-pocket_01"=>"•›•±æÂ",
		"longpolo-with-pocket_02"=>"•›•±•√•»",
		"longpolo-with-pocket_03"=>"±¶∂ª",
		"longpolo-with-pocket_04"=>"§ﬁ§®",
		"longpolo-with-pocket_05"=>"∫∏ø˛",
		"longpolo-with-pocket_06"=>"§ﬁ§®ø˛",
		"longpolo-with-pocket_07"=>"±¶ø˛",
		"longpolo-with-pocket_08"=>"∏Â§Ì",
		"longpolo-with-pocket_09"=>"ºÛ∏Â§Ì",
		"longpolo-with-pocket_10"=>"∏Â∫∏ø˛",
		"longpolo-with-pocket_11"=>"∏Â§Ìø˛",
		"longpolo-with-pocket_12"=>"∏Â±¶ø˛",
		"longpolo-with-pocket_13"=>"∫∏œ”",
		"longpolo-with-pocket_14"=>"∫∏¬µ∏˝",
		"longpolo-with-pocket_15"=>"±¶œ”",
		"longpolo-with-pocket_16"=>"±¶¬µ∏˝",
		"jacket',2_01"=>"∫∏∂ª",
		"jacket',2_02"=>"±¶∂ª",
		"jacket',2_03"=>"∏Â§Ì",
		"long-pants',2_01"=>"∫∏¡∞",
		"long-pants',2_02"=>"∫∏§‚§‚¡∞",
		"long-pants',2_03"=>"∫∏¬≠¡∞",
		"long-pants',2_04"=>"±¶¡∞",
		"long-pants',2_05"=>"±¶§‚§‚¡∞",
		"long-pants',2_06"=>"±¶¬≠¡∞",
		"long-pants',2_07"=>"∫∏∏Â",
		"long-pants',2_08"=>"∫∏§‚§‚∏Â",
		"long-pants',2_09"=>"∫∏¬≠∏Â",
		"long-pants',2_10"=>"±¶∏Â",
		"long-pants',2_11"=>"±¶§‚§‚∏Â",
		"long-pants',2_12"=>"±¶¬≠∏Â",
		"blouson_01"=>"∫∏∂ª",
		"blouson_02"=>"±¶∂ª",
		"blouson_03"=>"∏Â§Ì",
		"blouson_04"=>"∫∏œ”",
		"blouson_05"=>"±¶œ”",
		"coat_01"=>"∫∏∂ª",
		"coat_02"=>"±¶∂ª",
		"coat_03"=>"∏Â§Ì",
		"coat_04"=>"∫∏œ”",
		"coat_05"=>"±¶œ”",
		"bench-coat_01"=>"∫∏∂ª",
		"bench-coat_02"=>"±¶∂ª",
		"bench-coat_03"=>"∏Â§Ì",
		"bench-coat_04"=>"∫∏œ”",
		"bench-coat_05"=>"±¶œ”",
		"best',2_01"=>"∫∏∂ª",
		"best',2_02"=>"±¶∂ª",
		"best',2_03"=>"∏Â§Ì",
		"outdoor-jacket_01"=>"∫∏∂ª",
		"outdoor-jacket_02"=>"±¶∂ª",
		"outdoor-jacket_03"=>"∏Â§Ì",
		"outdoor-jacket_04"=>"∫∏œ”",
		"outdoor-jacket_05"=>"±¶œ”",
		"sports-jacket_01"=>"∫∏∂ª",
		"sports-jacket_02"=>"±¶∂ª",
		"sports-jacket_03"=>"∏Â§Ì",
		"sports-jacket_04"=>"∫∏œ”",
		"sports-jacket_05"=>"±¶œ”",
		"windbreaker_01"=>"∫∏∂ª",
		"windbreaker_02"=>"±¶∂ª",
		"windbreaker_03"=>"∏Â§Ì",
		"windbreaker_04"=>"∫∏œ”",
		"windbreaker_05"=>"±¶œ”",
		"mesh-cap_01"=>"§ﬁ§®",
		"twill-cap_01"=>"∫∏§ﬁ§®",
		"twill-cap_02"=>"±¶§ﬁ§®",
		"apron_01"=>"§ﬁ§®",
		"apron_02"=>"•›•±√Ê",
		"apron_03"=>"∫∏ø˛",
		"happi_01"=>"±¶¬µ",
		"happi_02"=>"±¶∂ª",
		"happi_03"=>"¡∞§ø§∆±¶",
		"happi_04"=>"∫∏¬µ",
		"happi_05"=>"∫∏∂ª",
		"happi_06"=>"¡∞§ø§∆∫∏",
		"happi_07"=>"∏Â§Ì",
		"towel_01"=>"√Ê±˚",
		"towel_02"=>"•µ•§•…",
		"bag_01"=>"¡∞ÃÃ",
		"bag_02"=>"∏ÂÃÃ",
		"rompers_01"=>"§ﬁ§®",
		"rompers_02"=>"∫∏∂ª",
		"rompers_03"=>"±¶∂ª",
		"rompers_04"=>"∏Â§Ì",
		"rompers_05"=>"ºÛ∏Â§Ì",
		"rompers_06"=>"∫∏¬µ",
		"rompers_07"=>"±¶¬µ",
		"visor_01"=>"√Ê±˚",
		"visor_01"=>"√Ê±˚",
		"short-apron_01"=>"§ﬁ§®",
		"short-apron_02"=>"•›•±√Ê",
		"short-apron_03"=>"∫∏ø˛",
		"mascot-tshirts_01"=>"§ﬁ§®",
		"mascot-tshirts_02"=>"∏Â§Ì",
		"pocket-tshirts_01"=>"•›•±æÂ",
		"pocket-tshirts_02"=>"•›•±√Ê",
		"pocket-tshirts_03"=>"±¶∂ª",
		"pocket-tshirts_04"=>"§ﬁ§®",
		"pocket-tshirts_05"=>"∫∏ø˛",
		"pocket-tshirts_06"=>"§ﬁ§®ø˛",
		"pocket-tshirts_07"=>"±¶ø˛",
		"pocket-tshirts_08"=>"∏Â§Ì",
		"pocket-tshirts_09"=>"ºÛ∏Â§Ì",
		"pocket-tshirts_10"=>"∏Â∫∏ø˛",
		"pocket-tshirts_11"=>"∏Â§Ìø˛",
		"pocket-tshirts_12"=>"∏Â±¶ø˛",
		"pocket-tshirts_13"=>"∫∏¬µ",
		"pocket-tshirts_14"=>"∫∏œ∆",
		"pocket-tshirts_15"=>"±¶¬µ",
		"pocket-tshirts_16"=>"±¶œ∆",
		"boxerpants_01"=>"±¶§ﬁ§®",
		"boxerpants_02"=>"∫∏§ﬁ§®",
		"boxerpants_03"=>"∏Â§Ì",
		"army-work-cap_01"=>"§ﬁ§®",
		"active-dry-cap_01"=>"§ﬁ§®",
		"chino-pants_01"=>"∫∏¡∞",
		"chino-pants_02"=>"∫∏§‚§‚¡∞",
		"chino-pants_03"=>"∫∏¬≠¡∞",
		"chino-pants_04"=>"±¶¡∞",
		"chino-pants_05"=>"±¶§‚§‚¡∞",
		"chino-pants_06"=>"±¶¬≠¡∞",
		"chino-pants_07"=>"∫∏∏Â",
		"chino-pants_08"=>"∫∏§‚§‚∏Â",
		"chino-pants_09"=>"∫∏¬≠∏Â",
		"chino-pants_10"=>"±¶∏Â",
		"chino-pants_11"=>"±¶§‚§‚∏Â",
		"chino-pants_12"=>"±¶¬≠∏Â",
		"fraise-t_01"=>"§ﬁ§®",
		"fraise-t_02"=>"∫∏∂ª",
		"fraise-t_03"=>"±¶∂ª",
		"fraise-t_04"=>"∫∏ø˛",
		"fraise-t_05"=>"§ﬁ§®ø˛",
		"fraise-t_06"=>"±¶ø˛",
		"fraise-t_07"=>"∏Â§Ì",
		"fraise-t_08"=>"ºÛ∏Â§Ì",
		"fraise-t_09"=>"∏Â∫∏ø˛",
		"fraise-t_10"=>"∏Â§Ìø˛",
		"fraise-t_11"=>"∏Â±¶ø˛",
		"fraise-t_12"=>"∫∏¬µ",
		"fraise-t_13"=>"∫∏œ∆",
		"fraise-t_14"=>"±¶¬µ",
		"fraise-t_15"=>"±¶œ∆",
		"henry-neck-t_01"=>"∫∏∂ª",
		"henry-neck-t_02"=>"±¶∂ª",
		"henry-neck-t_03"=>"§ﬁ§®",
		"henry-neck-t_04"=>"∫∏ø˛",
		"henry-neck-t_05"=>"§ﬁ§®ø˛",
		"henry-neck-t_06"=>"±¶ø˛",
		"henry-neck-t_07"=>"∏Â§Ì",
		"henry-neck-t_08"=>"ºÛ∏Â§Ì",
		"henry-neck-t_09"=>"∏Â∫∏ø˛",
		"henry-neck-t_10"=>"∏Â§Ìø˛",
		"henry-neck-t_11"=>"∏Â±¶ø˛",
		"henry-neck-t_12"=>"∫∏¬µ",
		"henry-neck-t_13"=>"∫∏œ∆",
		"henry-neck-t_14"=>"±¶¬µ",
		"henry-neck-t_15"=>"±¶œ∆",
		"button-down-shirt-short_01"=>"•›•±æÂ",
		"button-down-shirt-short_02"=>"•›•±•√•»",
		"button-down-shirt-short_03"=>"±¶∂ª",
		"button-down-shirt-short_04"=>"∫∏ø˛",
		"button-down-shirt-short_05"=>"±¶ø˛",
		"button-down-shirt-short_06"=>"∏Â§Ì",
		"button-down-shirt-short_07"=>"ºÛ∏Â§Ì",
		"button-down-shirt-short_08"=>"∏Â∫∏ø˛",
		"button-down-shirt-short_09"=>"∏Â§Ìø˛",
		"button-down-shirt-short_10"=>"∏Â±¶ø˛",
		"button-down-shirt-short_11"=>"∫∏œ”",
		"button-down-shirt-short_12"=>"±¶œ”",
		"button-down-shirt-short_01"=>"•›•±æÂ",
		"button-down-shirt-short_02"=>"•›•±•√•»",
		"button-down-shirt-short_03"=>"±¶∂ª",
		"button-down-shirt-short_04"=>"∫∏ø˛",
		"button-down-shirt-short_05"=>"±¶ø˛",
		"button-down-shirt-short_06"=>"∏Â§Ì",
		"button-down-shirt-short_07"=>"ºÛ∏Â§Ì",
		"button-down-shirt-short_08"=>"∏Â∫∏ø˛",
		"button-down-shirt-short_09"=>"∏Â§Ìø˛",
		"button-down-shirt-short_10"=>"∏Â±¶ø˛",
		"button-down-shirt-short_11"=>"∫∏¬µ",
		"button-down-shirt-short_12"=>"±¶¬µ",
		"polyester-pants_01"=>"∫∏¡∞",
		"polyester-pants_02"=>"∫∏§‚§‚¡∞",
		"polyester-pants_03"=>"∫∏¬≠¡∞",
		"polyester-pants_04"=>"±¶¡∞",
		"polyester-pants_05"=>"±¶§‚§‚¡∞",
		"polyester-pants_06"=>"±¶¬≠¡∞",
		"polyester-pants_07"=>"∫∏§‚§‚∏Â",
		"polyester-pants_08"=>"∫∏¬≠∏Â",
		"polyester-pants_09"=>"±¶∏Â",
		"polyester-pants_10"=>"±¶§‚§‚∏Â",
		"polyester-pants_11"=>"±¶¬≠∏Â",
		"noprint_01"=>"§ §∑",
		"parker-non-hood_01"=>"§ﬁ§®",
		"parker-non-hood_02"=>"∫∏∂ª",
		"parker-non-hood_03"=>"±¶∂ª",
		"parker-non-hood_04"=>"∏Â§Ì",
		"parker-non-hood_05"=>"∏Â∫∏ø˛",
		"parker-non-hood_06"=>"∏Â§Ìø˛",
		"parker-non-hood_07"=>"∏Â±¶ø˛",
		"parker-non-hood_08"=>"∫∏œ”",
		"parker-non-hood_09"=>"∫∏•’°º•…",
		"parker-non-hood_10"=>"±¶œ”",
		"parker-non-hood_11"=>"±¶•’°º•…",
		"zip-parker-non-hood_01"=>"∫∏∂ª",
		"zip-parker-non-hood_02"=>"±¶∂ª",
		"zip-parker-non-hood_03"=>"∏Â§Ì",
		"zip-parker-non-hood_04"=>"∏Â∫∏ø˛",
		"zip-parker-non-hood_05"=>"∏Â§Ìø˛",
		"zip-parker-non-hood_06"=>"∏Â±¶ø˛",
		"zip-parker-non-hood_07"=>"∫∏œ”",
		"zip-parker-non-hood_08"=>"∫∏•’°º•…",
		"zip-parker-non-hood_09"=>"±¶œ”",
		"zip-parker-non-hood_10"=>"±¶•’°º•…",
		"tsunagi_01"=>"∫∏∂ª",
		"tsunagi_02"=>"±¶∂ª",
		"tsunagi_03"=>"∏Â§Ì",
		"tsunagi-short_01"=>"∫∏∂ª",
		"tsunagi-short_02"=>"±¶∂ª",
		"tsunagi-short_03"=>"∏Â§Ì",
		"tsunagi-back_01"=>"∏Â§Ì",
		"basket-shirt_01"=>"§ﬁ§®",
		"basket-shirt_02"=>"∏Â§Ì",
		"game-pants_01"=>"∫∏§ﬁ§®",
		"game-pants_02"=>"±¶§ﬁ§®",
		"game-pants_03"=>"∫∏∏Â§Ì",
		"game-pants_04"=>"±¶∏Â§Ì",
	);
	
	
	// …æ≤¡§Ú0.5√±∞Ã§À —¥π§∑≤Ë¡¸•—•π§Ú ÷§π
	private function getStar($args){
		if($args<0.5){
			$r = 'star00';
		}else if($args>=0.5 && $args<1){
			$r = 'star05';
		}else if($args>=1 && $args<1.5){
			$r = 'star10';
		}else if($args>=1.5 && $args<2){
			$r = 'star15';
		}else if($args>=2 && $args<2.5){
			$r = 'star20';
		}else if($args>=2.5 && $args<3){
			$r = 'star25';
		}else if($args>=3 && $args<3.5){
			$r = 'star30';
		}else if($args>=3.5 && $args<4){
			$r = 'star35';
		}else if($args>=4 && $args<4.5){
			$r = 'star40';
		}else if($args>=4.5 && $args<5){
			$r = 'star45';
		}else{
			$r = 'star50';
		}
		return $r;
	}
	
	
	/*
	* •◊•Í•Û•»•›•∏•∑•Á•ÛID§Ú ÷§π
	* @id	category ID
	*/
	public function getPrintPositionID($id){
		return $this->silhouetteId[$id];
	}
	
	
	/*
	* ∏´¿—•⁄°º•∏§Œ•∑•Î•®•√•»§Œ•ø•∞§Ú ÷§π
	* @id		category ID
	*/
	public function getSilhouette($id){
		$idx = 1;
		foreach($this->silhouetteId[$id] as $ppid=>$lbl){
			$files = parent::positionFor($ppid, 'pos');
			$imgfile = file_get_contents($files[0]['filename']);
			$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
			preg_match('/<img (.*?)>/', $f, $match);
			//$f = mb_convert_encoding($match[1], 'euc-jp', 'utf-8');
			$box .= '<li>';
				$box .= '<div class="back">';
					$box .= '<span class="heightLine-1"><img '.$match[1].'>'.$lbl.'</span>';
					$box .= '<input type="radio" value="'.$ppid.'" name="body_type" class="check_body" id="check'.$idx.'"';
					if($idx==1) $box .= ' checked="checked"';
					$box .= '><label for="check'.$idx.'">&nbsp;</label>';
				$box .= '</div>';
			$box .= '</li>';
			
			$idx++;
		}
		
		return $box;
	}
	
	
	/*
	* ∏´¿—•⁄°º•∏§Œ•◊•Í•Û•»∞Ã√÷ªÿƒÍ§Œ•ø•∞§Ú ÷§π
	* @id		printposition ID
	* @offset	•§•Û•«•√•Ø•π§Œ≥´ªœ»÷πÊ
	*/
	public function getPrintPosition($id, $offset=0){
		if(preg_match('/\A[1-9][0-9]*\z/', $id)){
			$files = parent::positionFor($id, 'pos');
		}else{
			return;
		}
		
		//$files = parent::positionFor($args, 'pos');
		$filedir = "/m3/img/position/".$files[0]['ppdata']['category']."/".$files[0]['ppdata']['item']."/";
		$path = dirname(__FILE__)."/../..".$filedir."*.png";
		foreach (glob($path) as $filename) {
			$base = basename($filename, '.png');
			$posName = $this->positionName[$base];
			$tmp = explode("_", $base);
			$num = $tmp[1]."-".$offset;
			$pos .= '<li class="swiper-slide">';
				$pos .= '<span><img src="'.$filedir.$base.'.png" width="85" height="74" alt="">'.$posName.'</span>';
				$pos .= '<input type="checkbox" name="check'.$num.'" value="'.$posName.'" class="check_pos" id="check'.$num.'">';
				$pos .= '<label for="check'.$num.'">&nbsp;</label>';
			$pos .= '</li>';
		}
		
		return $pos;
	}
	
	
	/* 
	*	≥®∑ø§Œ…Ωº®§Ú•Ω°º•»(public)
	*	order by printposition_id, selective_key
	*/
	public function sortSelectivekey($args){
		$tmp = array(
			"mae"=>1,
			"mae_mini"=>1,
			"jacket_mae_mini"=>1,
			"mae_mini_2"=>1,
			"parker_mae_mini_2"=>1,
			"parker_mae_mini_zip "=>1,
			"apron_mae"=>1,
			"tote_mae"=>1,
			"short_apron_mae"=>1,
			"cap_mae"=>1,
			"visor_mae "=>1,
			"active_mae"=>1,
			"army_mae"=>1,
			
			"mae_hood"=>2,
			"short_apron_ue"=>2,
			
			"mune_right"=>3,
			"parker_mune_right"=>3,
			"active_mune_right"=>3,
			"cap_mae_right"=>3,
			"boxerpants_right"=>3,
			"shirt_mune_right"=>3,
			"game_pants_suso_right"=>3,
			
			"pocket"=>4,
			"parker_mae_pocket"=>4,
			"apron_pocket"=>4,
			"short_apron_pocket"=>4,
			
			"mune_left"=>5,
			"parker_mune_left"=>5,
			"active_mune_left"=>5,
			"polo_mune_left"=>5,
			"cap_mae_left"=>5,
			"boxerpants_left"=>5,
			"game_pants_suso_left"=>5,
			
			"suso_left"=>6,
			"apron_suso_left"=>6,
			"shirt_suso_left"=>6,
			
			"suso_mae"=>7,
			
			"suso_right"=>8,
			"shirt_suso_right"=>8,
			
			
			"mae_right"=>9,
			"workwear_mae_right"=>9,
			
			"mae_suso_right"=>10,
			"boxerpants_suso_right"=>10,
			
			"mae_momo_right"=>11,
			"workwear_mae_momo_right"=>11,
			
			"mae_hiza_right"=>12,
			"workwear_mae_hiza_right"=>12,
			
			"mae_asi_right"=>13,
			"workwear_mae_asi_right"=>13,
			
			
			"mae_left"=>14,
			"workwear_mae_left"=>14,
			
			"mae_suso_left"=>15,
			"boxerpants_suso_left"=>15,
			
			"mae_momo_left"=>16,
			"workwear_mae_momo_left"=>16,
			
			"mae_hiza_left"=>17,
			"workwear_mae_hiza_left"=>17,
			
			"mae_asi_left"=>18,
			"workwear_mae_asi_left"=>18,
			
			"happi_sode_left"=>19,
			"happi_mune_left"=>19,
			"happi_maetate_left"=>19,
			"happi_sode_right"=>19,
			"happi_mune_right"=>19,
			"happi_maetate_right"=>19,
			
			"towel_center"=>20,
			"towel_left"=>20,
			"towel_right"=>20,
			
			
			
			"usiro"=>21,
			"usiro_mini"=>21,
			"parker_usiro"=>21,
			"bench_usiro"=>21,
			"best_usiro"=>21,
			"tote_usiro"=>21,
			"cap_usiro"=>21,
			"active_cap_usiro"=>21,
			
			"eri"=>22,
			"kubi_usiro"=>22,
			"shirt_long_kubi_usiro"=>22,
			"shirt_short_kubi_usiro"=>22,
			
			"usiro_suso_left"=>23,
			"shirt_usiro_suso_left"=>23,
			
			"usiro_suso"=>24,
			
			"usiro_suso_right"=>25,
			"shirt_usiro_suso_right"=>25,
			
			"osiri"=>26,
			"pants_osiri"=>26,
			"boxerpants_osiri"=>26,
			
			
			"usiro_left"=>27,
			"pants_usiro_left"=>27,
			"workwear_usiro_left"=>27,
			
			"pants_usiro_suso_left"=>28,
			"boxerpants_usiro_suso_left"=>28,
			"game_pants_usiro_suso_left"=>28,
			
			"usiro_momo_left"=>29,
			"workwear_usiro_momo_left"=>29,
			
			"usiro_hiza_left"=>30,
			"workwear_usiro_hiza_left"=>30,
			
			"usiro_asi_left"=>31,
			"workwear_usiro_asi_left"=>31,
			
			"usiro_right"=>32,
			"pants_usiro_right"=>32,
			"workwear_usiro_right"=>32,
			
			"pants_usiro_suso_right"=>33,
			"boxerpants_usiro_suso_right"=>33,
			"game_pants_usiro_suso_right"=>33,
			
			"usiro_momo_right"=>34,
			"workwear_usiro_momo_right"=>34,
			
			"usiro_hiza_right"=>35,
			"workwear_usiro_hiza_right"=>35,
			
			"usiro_asi_right"=>36,
			"workwear_usiro_asi_right"=>36,
			
			
			
			"sode_right"=>37,
			"sode_right2"=>37,
			
			"hood_right"=>38,
			
			"long_sode_right"=>39,
			"trainer_sode_right"=>39,
			"parker_sode_right"=>39,
			"blouson_sode_right"=>39,
			"coat_sode_right"=>39,
			"boxerpants_side_right"=>39,
			"shirt_sode_right"=>39,
			"shirt_long_sode_right"=>39,
			
			"long_ude_right"=>40,
			"trainer_ude_right"=>40,
			"parker_ude_right"=>40,
			"blouson_ude_right"=>40,
			"coat_ude_right"=>40,
			"shirt_long_ude_right"=>40,
			
			"long_sodeguti_right"=>41,
			"trainer_sodeguti_right"=>41,
			
			"long_waki_right"=>42,
			"waki_right"=>42,
			"waki_right2"=>42,
			
			"sode_left"=>43,
			"sode_left2"=>43,
			
			"hood_left"=>44,
			
			"long_sode_left"=>45,
			"trainer_sode_left"=>45,
			"parker_sode_left"=>45,
			"blouson_sode_left"=>45,
			"coat_sode_left"=>45,
			"boxerpants_side_left"=>45,
			"shirt_sode_left"=>45,
			"shirt_long_sode_left"=>45,
			
			"long_ude_left"=>46,
			"trainer_ude_left"=>46,
			"parker_ude_left"=>46,
			"blouson_ude_left"=>46,
			"coat_ude_left"=>46,
			"shirt_long_ude_left"=>46,
			
			"long_sodeguti_left"=>47,
			"trainer_sodeguti_left"=>47,
			
			"long_waki_left"=>48,
			"waki_left"=>48,
			"waki_left2"=>48,
			
			"cap_side_right"=>49,
			"active_cap_side_right"=>49,
			
			"cap_side_left"=>50,
			"active_cap_side_left"=>50
		);
		
		foreach($args as $key=>$val){
			$a[$key] = $tmp[$val['key']];
		}
		array_multisort($a, $args);
		
		return $args;
	}
}


if(isset($_REQUEST['act'])){
	$iteminfo = new ItemInfo();
	switch($_REQUEST['act']){
	case 'body':
		// item silhouette
		$res = $iteminfo->getSilhouette($_REQUEST['category_id']);
		break;
		
	case 'position':
		// print position
		$res = $iteminfo->getPrintPosition($_REQUEST['pos_id']);
		break;
	}
	
	echo $res;
}
?>