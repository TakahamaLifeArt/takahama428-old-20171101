<?php
/**************************************
�����ޥ��ǥ�����
�����ʾ���μ���
��charset utf-8
***************************************/


require_once $_SERVER['DOCUMENT_ROOT'].'/php_libs/conndb.php';

class ItemInfo extends Conndb {

	public function __construct(){
		parent::__construct();
	}
	
	// ���ʥڡ���
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
				$tmp = parent::itemList(2, 'tag');	// ���ݡ��ĥ������˥ɥ饤�������ɲ�
				foreach($tmp as $key1=>$val1){
					if(!array_key_exists($val1["id"], $sub)){
						$data[] = $tmp[$key1];
					}
				}
			}
		}
		
		foreach($data as $key=>$val){
			// �����ƥ��ӥ塼
			$review = parent::getItemReview(array('sort'=>'post', 'itemid'=>$val['id']));
			$len = count($review);
			$r[$key]['len'] = $len;
			
			// ��ӥ塼���ɾ��
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
			$r[$key]['price'] = $val['cost'];	// �ǰ�ñ��
			
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
			
			// �����ƥ�ܺ٥ڡ���
			if(!is_null($itemid)){
				// ��ӥ塼�Υƥ����Ȥ�2��ޤ��֤�
				$reviewcount = 2;
				if($len<2){
					$reviewcount = $len;
				}
				for ($i=0; $i < $reviewcount; $i++) { 
					$r[$key]['review'][$i] = $review[$i];
				}
				
				// ���������ñ��
				$priceHash = parent::sizePrice($val['id'], $currentColorCode);
				for($i=0; $i<count($priceHash); $i++){
					$r[$key]['size_price'][$priceHash[$i]['id']] = $priceHash[$i]['cost'];
				}
				
				// ������Ÿ��
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
				
				// �����ƥ५�顼[{code:���顼̾},{},...]
				$r[$key]['thumbs'] = $attr['code'];
				
				// �����ƥ��������Ǻ�
				$r[$key]['explain'] = $val['i_description'];
				$r[$key]['material'] = $val['i_material'];
				
				// ��ˡ
				$r[$key]['measure'] = parent::getItemMeasure($val['code']);
			}
		}
		
		// ������
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
	
	
	// ���ƥ�����Υ��륨�åȤǻ��Ѥ��볨��ID�ȳ���̾�Υϥå���
	private $silhouetteId = array('',
						array(1=>'���Ǻ�',3=>'�ɥ饤'),
						array(6=>'�ȥ졼�ʡ�',7=>'�ץ�ѡ�����',8=>'�ѥ��',10=>'���åץѡ�����'),
						array(12=>'�ݥ��å�̵��',13=>'�ݥ��å�ͭ��'),
						array(3=>'GAME',16=>'TRAINING'),
						array(3=>'�ɥ饤',7=>'�������å�',41=>'���Ǻ�'),
						array(18=>'��������',23=>'��������'),
						array(25=>'����å�',32=>'�Х����'),
						array(29=>'������'),
						array(30=>'�ȡ��ȥХå�'),
						array(27=>'������',34=>'������'),
						array(18=>'�ɥꥺ�顼',40=>'�ѥ��'),
						array(32=>'�������ƥ�'),
						array(2=>'Ĺµ',4=>'����µ'),
						array(31=>'�٥ӡ�'),
						array(),
						array(49=>'Ĺµ',50=>'Ⱦµ'),
					);
	
	// �Ƴ����Υץ��Ȱ��֤�̾��
	private $positionName = array(
		"normal-tshirts_01"=>"�ޤ�",
		"normal-tshirts_02"=>"����",
		"normal-tshirts_03"=>"����",
		"normal-tshirts_04"=>"����",
		"normal-tshirts_05"=>"�ޤ���",
		"normal-tshirts_06"=>"����",
		"normal-tshirts_07"=>"���",
		"normal-tshirts_08"=>"����",
		"normal-tshirts_09"=>"�庸��",
		"normal-tshirts_10"=>"����",
		"normal-tshirts_11"=>"�屦��",
		"normal-tshirts_12"=>"��µ",
		"normal-tshirts_13"=>"����",
		"normal-tshirts_14"=>"��µ",
		"normal-tshirts_15"=>"����",
		"long-tshirts_01"=>"�ޤ�",
		"long-tshirts_02"=>"����",
		"long-tshirts_03"=>"����",
		"long-tshirts_04"=>"����",
		"long-tshirts_05"=>"�ޤ���",
		"long-tshirts_06"=>"����",
		"long-tshirts_07"=>"���",
		"long-tshirts_08"=>"����",
		"long-tshirts_09"=>"�庸��",
		"long-tshirts_10"=>"����",
		"long-tshirts_11"=>"�屦��",
		"long-tshirts_12"=>"����",
		"long-tshirts_13"=>"��µ��",
		"long-tshirts_14"=>"����",
		"long-tshirts_15"=>"����",
		"long-tshirts_16"=>"��µ��",
		"long-tshirts_17"=>"����",
		"raglan-tshirts_01"=>"�ޤ�",
		"raglan-tshirts_02"=>"����",
		"raglan-tshirts_03"=>"����",
		"raglan-tshirts_04"=>"����",
		"raglan-tshirts_05"=>"�ޤ���",
		"raglan-tshirts_06"=>"����",
		"raglan-tshirts_07"=>"���",
		"raglan-tshirts_08"=>"����",
		"raglan-tshirts_09"=>"�庸��",
		"raglan-tshirts_10"=>"����",
		"raglan-tshirts_11"=>"�屦��",
		"raglan-tshirts_12"=>"��µ",
		"raglan-tshirts_13"=>"����",
		"raglan-tshirts_14"=>"��µ",
		"raglan-tshirts_15"=>"����",
		"raglan-long-tshirts_01"=>"�ޤ�",
		"raglan-long-tshirts_02"=>"����",
		"raglan-long-tshirts_03"=>"����",
		"raglan-long-tshirts_04"=>"����",
		"raglan-long-tshirts_05"=>"�ޤ���",
		"raglan-long-tshirts_06"=>"����",
		"raglan-long-tshirts_07"=>"���",
		"raglan-long-tshirts_08"=>"����",
		"raglan-long-tshirts_09"=>"�庸��",
		"raglan-long-tshirts_10"=>"����",
		"raglan-long-tshirts_11"=>"�屦��",
		"raglan-long-tshirts_12"=>"����",
		"raglan-long-tshirts_13"=>"��µ��",
		"raglan-long-tshirts_14"=>"����",
		"raglan-long-tshirts_15"=>"����",
		"raglan-long-tshirts_16"=>"��µ��",
		"raglan-long-tshirts_17"=>"����",
		"tanktop_01"=>"�ޤ�",
		"tanktop_02"=>"����",
		"tanktop_03"=>"����",
		"tanktop_04"=>"����",
		"tanktop_05"=>"�ޤ���",
		"tanktop_06"=>"����",
		"tanktop_07"=>"���",
		"tanktop_08"=>"����",
		"tanktop_09"=>"����",
		"tanktop_10"=>"����",
		"tanktop_11"=>"����",
		"tanktop_12"=>"����",
		"tanktop_13"=>"����",
		"trainer_01"=>"�ޤ�",
		"trainer_02"=>"����",
		"trainer_03"=>"����",
		"trainer_04"=>"����",
		"trainer_05"=>"�ޤ���",
		"trainer_06"=>"����",
		"trainer_07"=>"���",
		"trainer_08"=>"����",
		"trainer_09"=>"�庸��",
		"trainer_10"=>"����",
		"trainer_11"=>"�屦��",
		"trainer_12"=>"����",
		"trainer_13"=>"��µ��",
		"trainer_14"=>"����",
		"trainer_15"=>"��µ��",
		"parker_01"=>"�ޤ�",
		"parker_02"=>"����",
		"parker_03"=>"����",
		"parker_04"=>"���ա���",
		"parker_05"=>"���",
		"parker_06"=>"�庸��",
		"parker_07"=>"����",
		"parker_08"=>"�屦��",
		"parker_09"=>"����",
		"parker_10"=>"���ա���",
		"parker_11"=>"����",
		"parker_12"=>"���ա���",
		"long-pants_01"=>"����",
		"long-pants_02"=>"�������",
		"long-pants_03"=>"��­��",
		"long-pants_04"=>"����",
		"long-pants_05"=>"�������",
		"long-pants_06"=>"��­��",
		"long-pants_07"=>"����",
		"long-pants_08"=>"������",
		"long-pants_09"=>"��­��",
		"long-pants_10"=>"����",
		"long-pants_11"=>"������",
		"long-pants_12"=>"��­��",
		"short-pants_01"=>"������",
		"short-pants_02"=>"������",
		"short-pants_03"=>"����",
		"short-pants_04"=>"����",
		"short-pants_05"=>"�����",
		"short-pants_06"=>"�����",
		"zip-parker_01"=>"����",
		"zip-parker_02"=>"����",
		"zip-parker_03"=>"���ա���",
		"zip-parker_04"=>"���",
		"zip-parker_05"=>"�庸��",
		"zip-parker_06"=>"����",
		"zip-parker_07"=>"�屦��",
		"zip-parker_08"=>"����",
		"zip-parker_09"=>"���ա���",
		"zip-parker_10"=>"����",
		"zip-parker_11"=>"���ա���",
		"zip-jacket_01"=>"����",
		"zip-jacket_02"=>"����",
		"zip-jacket_03"=>"���",
		"zip-jacket_04"=>"����",
		"zip-jacket_05"=>"�庸��",
		"zip-jacket_06"=>"����",
		"zip-jacket_07"=>"�屦��",
		"zip-jacket_08"=>"����",
		"zip-jacket_09"=>"��µ��",
		"zip-jacket_10"=>"����",
		"zip-jacket_11"=>"��µ��",
		"polo-non-pocket_01"=>"����",
		"polo-non-pocket_02"=>"����",
		"polo-non-pocket_03"=>"�ޤ�",
		"polo-non-pocket_04"=>"����",
		"polo-non-pocket_05"=>"�ޤ���",
		"polo-non-pocket_06"=>"����",
		"polo-non-pocket_07"=>"���",
		"polo-non-pocket_08"=>"����",
		"polo-non-pocket_09"=>"�庸��",
		"polo-non-pocket_10"=>"����",
		"polo-non-pocket_11"=>"�屦��",
		"polo-non-pocket_12"=>"��µ",
		"polo-non-pocket_13"=>"��µ",
		"polo-with-pocket_01"=>"�ݥ���",
		"polo-with-pocket_02"=>"�ݥ��å�",
		"polo-with-pocket_03"=>"����",
		"polo-with-pocket_04"=>"�ޤ�",
		"polo-with-pocket_05"=>"����",
		"polo-with-pocket_06"=>"�ޤ���",
		"polo-with-pocket_07"=>"����",
		"polo-with-pocket_08"=>"���",
		"polo-with-pocket_09"=>"����",
		"polo-with-pocket_10"=>"�庸��",
		"polo-with-pocket_11"=>"����",
		"polo-with-pocket_12"=>"�屦��",
		"polo-with-pocket_13"=>"��µ",
		"polo-with-pocket_14"=>"��µ",
		"longpolo-non-pocket_01"=>"����",
		"longpolo-non-pocket_02"=>"����",
		"longpolo-non-pocket_03"=>"�ޤ�",
		"longpolo-non-pocket_04"=>"����",
		"longpolo-non-pocket_05"=>"�ޤ���",
		"longpolo-non-pocket_06"=>"����",
		"longpolo-non-pocket_07"=>"���",
		"longpolo-non-pocket_08"=>"����",
		"longpolo-non-pocket_09"=>"�庸��",
		"longpolo-non-pocket_10"=>"����",
		"longpolo-non-pocket_11"=>"�屦��",
		"longpolo-non-pocket_12"=>"����",
		"longpolo-non-pocket_13"=>"��µ��",
		"longpolo-non-pocket_14"=>"����",
		"longpolo-non-pocket_15"=>"��µ��",
		"longpolo-with-pocket_01"=>"�ݥ���",
		"longpolo-with-pocket_02"=>"�ݥ��å�",
		"longpolo-with-pocket_03"=>"����",
		"longpolo-with-pocket_04"=>"�ޤ�",
		"longpolo-with-pocket_05"=>"����",
		"longpolo-with-pocket_06"=>"�ޤ���",
		"longpolo-with-pocket_07"=>"����",
		"longpolo-with-pocket_08"=>"���",
		"longpolo-with-pocket_09"=>"����",
		"longpolo-with-pocket_10"=>"�庸��",
		"longpolo-with-pocket_11"=>"����",
		"longpolo-with-pocket_12"=>"�屦��",
		"longpolo-with-pocket_13"=>"����",
		"longpolo-with-pocket_14"=>"��µ��",
		"longpolo-with-pocket_15"=>"����",
		"longpolo-with-pocket_16"=>"��µ��",
		"jacket',2_01"=>"����",
		"jacket',2_02"=>"����",
		"jacket',2_03"=>"���",
		"long-pants',2_01"=>"����",
		"long-pants',2_02"=>"�������",
		"long-pants',2_03"=>"��­��",
		"long-pants',2_04"=>"����",
		"long-pants',2_05"=>"�������",
		"long-pants',2_06"=>"��­��",
		"long-pants',2_07"=>"����",
		"long-pants',2_08"=>"������",
		"long-pants',2_09"=>"��­��",
		"long-pants',2_10"=>"����",
		"long-pants',2_11"=>"������",
		"long-pants',2_12"=>"��­��",
		"blouson_01"=>"����",
		"blouson_02"=>"����",
		"blouson_03"=>"���",
		"blouson_04"=>"����",
		"blouson_05"=>"����",
		"coat_01"=>"����",
		"coat_02"=>"����",
		"coat_03"=>"���",
		"coat_04"=>"����",
		"coat_05"=>"����",
		"bench-coat_01"=>"����",
		"bench-coat_02"=>"����",
		"bench-coat_03"=>"���",
		"bench-coat_04"=>"����",
		"bench-coat_05"=>"����",
		"best',2_01"=>"����",
		"best',2_02"=>"����",
		"best',2_03"=>"���",
		"outdoor-jacket_01"=>"����",
		"outdoor-jacket_02"=>"����",
		"outdoor-jacket_03"=>"���",
		"outdoor-jacket_04"=>"����",
		"outdoor-jacket_05"=>"����",
		"sports-jacket_01"=>"����",
		"sports-jacket_02"=>"����",
		"sports-jacket_03"=>"���",
		"sports-jacket_04"=>"����",
		"sports-jacket_05"=>"����",
		"windbreaker_01"=>"����",
		"windbreaker_02"=>"����",
		"windbreaker_03"=>"���",
		"windbreaker_04"=>"����",
		"windbreaker_05"=>"����",
		"mesh-cap_01"=>"�ޤ�",
		"twill-cap_01"=>"���ޤ�",
		"twill-cap_02"=>"���ޤ�",
		"apron_01"=>"�ޤ�",
		"apron_02"=>"�ݥ���",
		"apron_03"=>"����",
		"happi_01"=>"��µ",
		"happi_02"=>"����",
		"happi_03"=>"�����Ʊ�",
		"happi_04"=>"��µ",
		"happi_05"=>"����",
		"happi_06"=>"�����ƺ�",
		"happi_07"=>"���",
		"towel_01"=>"���",
		"towel_02"=>"������",
		"bag_01"=>"����",
		"bag_02"=>"����",
		"rompers_01"=>"�ޤ�",
		"rompers_02"=>"����",
		"rompers_03"=>"����",
		"rompers_04"=>"���",
		"rompers_05"=>"����",
		"rompers_06"=>"��µ",
		"rompers_07"=>"��µ",
		"visor_01"=>"���",
		"visor_01"=>"���",
		"short-apron_01"=>"�ޤ�",
		"short-apron_02"=>"�ݥ���",
		"short-apron_03"=>"����",
		"mascot-tshirts_01"=>"�ޤ�",
		"mascot-tshirts_02"=>"���",
		"pocket-tshirts_01"=>"�ݥ���",
		"pocket-tshirts_02"=>"�ݥ���",
		"pocket-tshirts_03"=>"����",
		"pocket-tshirts_04"=>"�ޤ�",
		"pocket-tshirts_05"=>"����",
		"pocket-tshirts_06"=>"�ޤ���",
		"pocket-tshirts_07"=>"����",
		"pocket-tshirts_08"=>"���",
		"pocket-tshirts_09"=>"����",
		"pocket-tshirts_10"=>"�庸��",
		"pocket-tshirts_11"=>"����",
		"pocket-tshirts_12"=>"�屦��",
		"pocket-tshirts_13"=>"��µ",
		"pocket-tshirts_14"=>"����",
		"pocket-tshirts_15"=>"��µ",
		"pocket-tshirts_16"=>"����",
		"boxerpants_01"=>"���ޤ�",
		"boxerpants_02"=>"���ޤ�",
		"boxerpants_03"=>"���",
		"army-work-cap_01"=>"�ޤ�",
		"active-dry-cap_01"=>"�ޤ�",
		"chino-pants_01"=>"����",
		"chino-pants_02"=>"�������",
		"chino-pants_03"=>"��­��",
		"chino-pants_04"=>"����",
		"chino-pants_05"=>"�������",
		"chino-pants_06"=>"��­��",
		"chino-pants_07"=>"����",
		"chino-pants_08"=>"������",
		"chino-pants_09"=>"��­��",
		"chino-pants_10"=>"����",
		"chino-pants_11"=>"������",
		"chino-pants_12"=>"��­��",
		"fraise-t_01"=>"�ޤ�",
		"fraise-t_02"=>"����",
		"fraise-t_03"=>"����",
		"fraise-t_04"=>"����",
		"fraise-t_05"=>"�ޤ���",
		"fraise-t_06"=>"����",
		"fraise-t_07"=>"���",
		"fraise-t_08"=>"����",
		"fraise-t_09"=>"�庸��",
		"fraise-t_10"=>"����",
		"fraise-t_11"=>"�屦��",
		"fraise-t_12"=>"��µ",
		"fraise-t_13"=>"����",
		"fraise-t_14"=>"��µ",
		"fraise-t_15"=>"����",
		"henry-neck-t_01"=>"����",
		"henry-neck-t_02"=>"����",
		"henry-neck-t_03"=>"�ޤ�",
		"henry-neck-t_04"=>"����",
		"henry-neck-t_05"=>"�ޤ���",
		"henry-neck-t_06"=>"����",
		"henry-neck-t_07"=>"���",
		"henry-neck-t_08"=>"����",
		"henry-neck-t_09"=>"�庸��",
		"henry-neck-t_10"=>"����",
		"henry-neck-t_11"=>"�屦��",
		"henry-neck-t_12"=>"��µ",
		"henry-neck-t_13"=>"����",
		"henry-neck-t_14"=>"��µ",
		"henry-neck-t_15"=>"����",
		"button-down-shirt-short_01"=>"�ݥ���",
		"button-down-shirt-short_02"=>"�ݥ��å�",
		"button-down-shirt-short_03"=>"����",
		"button-down-shirt-short_04"=>"����",
		"button-down-shirt-short_05"=>"����",
		"button-down-shirt-short_06"=>"���",
		"button-down-shirt-short_07"=>"����",
		"button-down-shirt-short_08"=>"�庸��",
		"button-down-shirt-short_09"=>"����",
		"button-down-shirt-short_10"=>"�屦��",
		"button-down-shirt-short_11"=>"����",
		"button-down-shirt-short_12"=>"����",
		"button-down-shirt-short_01"=>"�ݥ���",
		"button-down-shirt-short_02"=>"�ݥ��å�",
		"button-down-shirt-short_03"=>"����",
		"button-down-shirt-short_04"=>"����",
		"button-down-shirt-short_05"=>"����",
		"button-down-shirt-short_06"=>"���",
		"button-down-shirt-short_07"=>"����",
		"button-down-shirt-short_08"=>"�庸��",
		"button-down-shirt-short_09"=>"����",
		"button-down-shirt-short_10"=>"�屦��",
		"button-down-shirt-short_11"=>"��µ",
		"button-down-shirt-short_12"=>"��µ",
		"polyester-pants_01"=>"����",
		"polyester-pants_02"=>"�������",
		"polyester-pants_03"=>"��­��",
		"polyester-pants_04"=>"����",
		"polyester-pants_05"=>"�������",
		"polyester-pants_06"=>"��­��",
		"polyester-pants_07"=>"������",
		"polyester-pants_08"=>"��­��",
		"polyester-pants_09"=>"����",
		"polyester-pants_10"=>"������",
		"polyester-pants_11"=>"��­��",
		"noprint_01"=>"�ʤ�",
		"parker-non-hood_01"=>"�ޤ�",
		"parker-non-hood_02"=>"����",
		"parker-non-hood_03"=>"����",
		"parker-non-hood_04"=>"���",
		"parker-non-hood_05"=>"�庸��",
		"parker-non-hood_06"=>"����",
		"parker-non-hood_07"=>"�屦��",
		"parker-non-hood_08"=>"����",
		"parker-non-hood_09"=>"���ա���",
		"parker-non-hood_10"=>"����",
		"parker-non-hood_11"=>"���ա���",
		"zip-parker-non-hood_01"=>"����",
		"zip-parker-non-hood_02"=>"����",
		"zip-parker-non-hood_03"=>"���",
		"zip-parker-non-hood_04"=>"�庸��",
		"zip-parker-non-hood_05"=>"����",
		"zip-parker-non-hood_06"=>"�屦��",
		"zip-parker-non-hood_07"=>"����",
		"zip-parker-non-hood_08"=>"���ա���",
		"zip-parker-non-hood_09"=>"����",
		"zip-parker-non-hood_10"=>"���ա���",
		"tsunagi_01"=>"����",
		"tsunagi_02"=>"����",
		"tsunagi_03"=>"���",
		"tsunagi-short_01"=>"����",
		"tsunagi-short_02"=>"����",
		"tsunagi-short_03"=>"���",
		"tsunagi-back_01"=>"���",
		"basket-shirt_01"=>"�ޤ�",
		"basket-shirt_02"=>"���",
		"game-pants_01"=>"���ޤ�",
		"game-pants_02"=>"���ޤ�",
		"game-pants_03"=>"�����",
		"game-pants_04"=>"�����",
	);
	
	
	// ɾ����0.5ñ�̤��Ѵ��������ѥ����֤�
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
	* �ץ��ȥݥ������ID���֤�
	* @id	category ID
	*/
	public function getPrintPositionID($id){
		return $this->silhouetteId[$id];
	}
	
	
	/*
	* ���ѥڡ����Υ��륨�åȤΥ������֤�
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
	* ���ѥڡ����Υץ��Ȱ��ֻ���Υ������֤�
	* @id		printposition ID
	* @offset	����ǥå����γ����ֹ�
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
	*	������ɽ���򥽡���(public)
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