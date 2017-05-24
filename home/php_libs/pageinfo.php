<?php
/*
*	page information
*	charset utf-8
*
*	見積、料金の目安、商品詳細、シーン別
*/
include $_SERVER['DOCUMENT_ROOT']."/common/inc/pageinit.php";
require_once dirname(__FILE__).'/categorypage.php';

$conn = new Conndb();



/*
*	商品一覧ページ
*	@id				カテゴリID
*	@tag			タグの配列
*	@mode			@idの種類'  category', 'tag'
*	@sort			並び順
*	@limit			検索レコード数
*
*	return	商品情報の配列
*/
function getCategoryInfo($id, $tag=null, $mode='category', $sort='index', $limit=null){

	// 商品情報を取得
	$conn = new Conndb();
	$data = $conn->itemOf($id, $tag, $mode, $limit);
	
	$tmp = array();
	$res = array();
	$cnt = count($data);

	if($cnt==0) return $res;
	
	// タグを生成
	$idx = 0;
	for($i=0; $i<$cnt; $i++){
		$code = $data[$i]['item_code'];
		$tmp[$idx]['category_key'] = $data[$i]['category_key'];
		$tmp[$idx]['category_name'] = mb_convert_encoding($data[$i]['category_name'], 'euc-jp', 'utf-8');
        $tmp[$idx]['item_name'] = mb_convert_encoding($data[$i]['item_name'], 'euc-jp', 'utf-8');
        $tmp[$idx]['colors'] = $data[$i]['colors'];
    	$tmp[$idx]['sizes'] = $data[$i]['sizecount'];
		$tmp[$idx]['sizerange'] = array($data[$i]['sizename_from'],$data[$i]['sizename_to']);
		$tmp[$idx]['makerprice'] = number_format($data[$i]['makercost']);	//$maker price;
        $tmp[$idx]['minprice'] = number_format($data[$i]['cost']);	//$minprice;
        $tmp[$idx]['initcolor'] = $data[$i]['i_color_code'];	//$ic[$code];
        $tmp[$idx]['features'] = mb_convert_encoding($data[$i]['i_caption'],'euc-jp','utf-8');
        
        $tmp[$idx]['sort'] = $data[$i]['cost'];	//$sortprice;
        $tmp[$idx]['row'] = $data[$i]['item_row'];
        $tmp[$idx]['oz'] = $data[$i]['oz'];
        $tmp[$idx]['item_code'] = $code;
        
        // タグ
        $tmp[$idx]['tag'] = $data[$i]['tag'];
		
		// ブランドタグ
		$tmp[$idx]['brand_tag'] = $data[$i]['brandtag_id'];
        
        // アイテムレビュー
		$review_data = $conn->getItemReview(array('sort'=>'post', 'itemid'=>$data[$i]['item_id'], 'nodata'=>'1'));
		$review_len = count($review_data);
		$tmp[$idx]['review_count'] = $review_len;
		if($review_len>0){
			$tmp[$idx]['review_path'] = '<p><a href="/itemreviews/index.php?item='.$data[$i]['item_id'].'">レビューを見る（'.$review_len.'件）</a></p>';
		}else{
			$tmp[$idx]['review_path'] = '<p>レビューを見る（0件）</p>';
		}
		
        $idx++;
	
	}
	
	// ソート
	switch($sort){
		case 'none':	break;
		case 'low':		$order_type = 'sort';
						$sort = 'asc';
					break;
		case 'high':	$order_type = 'sort';
						$sort = 'desc';
					break;
		case 'asc':		$order_type = 'review_count';
						$sort = 'asc';
					break;
		case 'desc':	$order_type = 'review_count';
						$sort = 'desc';
					break;
		case 'heavy':	$order_type = 'oz';
						$sort = 'desc';
					break;
		case 'light':	$order_type = 'oz';
						$sort = 'asc';
					break;
		default:		$order_type = 'row';
						$sort = 'asc';
					break;
	}
	
	if($sort!="none"){
		foreach($tmp as $key=>$row){
			$sortkey[$key] = $row[$order_type];
		}
		if($sort=='asc'){
			array_multisort($sortkey,SORT_ASC,SORT_NUMERIC,$tmp);
		}else{
			array_multisort($sortkey,SORT_DESC,SORT_NUMERIC,$tmp);
		}
	
		// キーをアイテムコードに変換
		for($i=0; $i<count($tmp); $i++){
			if($order_type=='oz' && $tmp[$i]['oz']==0) continue;
			$res[$tmp[$i]['item_code']] = $tmp[$i];
		}
	}else{
		$res = $tmp;
	}

    return $res;
}


/*
*	ソート（廃止）
*/
function sort_asc($a,$b){
	return ($a["cost"] - $b["cost"]);
}
function sort_desc($a,$b){
	return ($b["cost"] - $a["cost"]);
}


/*
*	レビューの星マーク
*	評価を0.5単位に変換し画像パスを返す
*/
function getStar($args){
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


if(isset($_REQUEST['act'])){
	switch($_REQUEST['act']){
	case 'price';
	/* 見積ページ */
		list($rows, $isSwitch) = $conn->priceFor($_REQUEST['itemid'], $_REQUEST['colormode']);
		$res = $rows.'|'.$isSwitch;
		break;
		
	case 'position':
	/* 見積ページ */
		if(isset($_REQUEST['id_type'])){
			$files = $conn->positionFor($_REQUEST['itemid'], $_REQUEST['id_type']);
		}else{
			$files = $conn->positionFor($_REQUEST['itemid']);
		}
		if(isset($_REQUEST['mode'])){
			$res = '<tr class="posid_'.$files[0]['posid'].'"><td colspan="3" class="pos_step ps1">(1)プリントする位置を選択してください。</td></tr>';
			$res .= '<tr>';
			for($i=0; $i<count($files); $i++){
				$imgfile = file_get_contents($files[$i]['filename']);
				$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
				$res .= '<td><div class="pos_'.$i.'">'.$f.'</div></td>';
				$ink .= '<td id="inktarget'.$i.'" class="'.$files[$i]['base_name'].'">';
				$ink .= '</td>';
			}
			$res .= '</tr>';
			
			$res .= '<tr>';
			$res .= '<td colspan="3" class="pos_step">(2)選択した位置のプリントに使用する、インクの色数を選択してください。';
			$res .= '<span class="questions"><a class="info_icon" target="_new" href="/design/fontcolor.html#navi2">使用インク色？</a></span>';
			$res .= '</td>';
		    $res .= '</tr>';
			$res .= '<tr>'.$ink.'</tr>';
		}else{

$posdiv = "";
	for($i=0; $i<count($files); $i++){
		$imgfile = file_get_contents($files[$i]['filename']);
		$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
		$posname = '<div class="posname_'.$i.'"></div>';
		$ink = '<div><select class="ink_'.$i.'"><option value="0" selected="selected">選択してください</option>';
		$ink .= '<option value="1">1色</option><option value="2">2色</option><option value="3">3色</option>';
		$ink .= '<option value="9">4色以上</option></select></div>';
			$posdiv .= '<li class="pntposition">';
				$posdiv .= '<div class="psnv">';
					$posdiv .= '<div class="pos_'.$i.'">'.$f.'</div>';
					$posdiv .= $posname;
					$posdiv .= $ink;
					$posdiv .= '</div>';
			$posdiv .= '</li>';
	}
//$posdiv = mb_convert_encoding($posdiv,'euc-jp','utf-8');
$res = '<figure><div></div><ul>' .$posdiv .'</ul></figure>';

/*			
			$res = '<tr>';
			$res .= '<th>プリント位置</th>';
			for($i=0; $i<count($files); $i++){
				$imgfile = file_get_contents($files[$i]['filename']);
				$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
				$res .= '<td><div class="pos_'.$i.'">'.$f.'</div></td>';
				$posname .= '<td><p class="posname_'.$i.'"></p></td>';
				if($files[$i]['posid']!=46){
					$ink .= '<td><p><select class="ink_'.$i.'"><option value="0" selected="selected">選択してください</option>';
					$ink .= '<option value="1">1色</option><option value="2">2色</option><option value="3">3色</option>';
					$ink .= '<option value="9">4色以上</option></select></p></td>';
				}
			}
			$res .= '</tr>';
			$res .= '<tr><th>　</th>'.$posname.'</tr>';
			if($files[0]['posid']!=46){
				$res .= '<tr><th>インクの色数</th>'.$ink.'</tr>';
			}
*/
		}
		break;
		
	case 'body':
	/* 見積ページのシルエット*/
		$ids = array('',
			array(1=>'綿素材',3=>'ドライ'),
			array(6=>'トレーナー',7=>'プルパーカー',8=>'パンツ',10=>'ジップパーカー'),
			array(63=>'ポケット無し',64=>'ポケット有り'),
			array(3=>'GAME',16=>'TRAINING'),
			array(41=>'綿素材',3=>'ドライ'),
			array(18=>'薄い生地',23=>'厚い生地'),
			array(25=>'キャップ',32=>'バンダナ'),
			array(29=>'タオル'),
			array(30=>'トートバッグ'),
			array(27=>'肩がけ',34=>'腰巻き'),
			array(44=>'シャツ',40=>'パンツ'),
			array(32=>'全アイテム'),
			array(2=>'長袖',4=>'七部袖'),
			array(31=>'ベビー'),
			array(),
			array(49=>'長袖',50=>'半袖'),
			);
		$isFirst = true;
		foreach($ids[$_POST['category_id']] as $id=>$lbl){
			$files = $conn->positionFor($id, 'pos');
			$imgfile = file_get_contents($files[0]['filename']);
			$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
			//$f = preg_replace('/\/base/', '/layout', $f);
			preg_match('/<img (.*?)>/', $f, $match);
			//$f = mb_convert_encoding($match[1], 'euc-jp', 'utf-8');
			$box .= '<div class="box">';
				$box .= '<div class="body_type"><img '.$match[1].'></div>';
				$box .= '<div class="desc">';
					$box .= '<p><label><input type="radio" value="'.$id.'" name="body_type" class="check_body"';
					if($isFirst) $box .= ' checked="checked"';
					$box .= '> '.$lbl.'</label></p>';
				$box .= '</div>';
			$box .= '</div>';
			
			$isFirst = false;
		}
		$res = $box;
		break;
		
	case 'eachprice':
	/* 料金の目安ページ */
		$area = array('f');
		for($i=0; $i<count($_REQUEST['itemcode']); $i++){
			$p1 = array($_REQUEST['itemcode'][$i]);
			$p2 = array($_REQUEST['amount'][$i]);
			$p3 = array($_REQUEST['ink'][$i]);
			$price = $conn->estimateEach($p1, $p2, $p3, $area, $sheetsize='1');
			$dat[$i] = $price[$_REQUEST['itemcode'][$i]];
		}
		
		$json = new Services_JSON();
		$res = $json->encode($dat);
		header("Content-Type: text/javascript; charset=utf-8");
		
		break;
	
	case 'itemtag':
	/* 商品一覧ページの条件（タグ）で探す */
		$dat = getCategoryInfo($_REQUEST['catid'], $_REQUEST['tagid'], $_REQUEST['mode'], $_REQUEST['priceorder']);
		
		$ls = array();
		$i = 0;
		foreach($dat as $code=>$v){
			$folder=$v['category_key'];
			if($i%4==0){
				$firstlist = ' firstlist';
			}else{
				$firstlist = '';
			}
			if( (preg_match('/^p-/',$code) && $v['initcolor']=="") || $code=='ss-9999'){
				$suffix = '_style_0';
			}else{
				$suffix = '_'.$v['initcolor'];
			}
			$itemlist_data = '
			<li class="listitems_ex'.$firstlist.'">
				<a href="/items/'.$folder.'/'.$code.'.html">
					<ul>
						<li class="point_s">'.mb_convert_encoding($v['features'], 'utf-8', 'EUC-JP').'</li>
						<li class="item_name_s">
							<ul>
								<li class="item_name_kata">'.strtoupper($code).'</li>
								<li class="item_name_name">'.mb_convert_encoding($v['item_name'], 'utf-8', 'EUC-JP').'</li>
							</ul>
						</li>
						<li class="item_image_s">';
			
			if($i<3){
				$itemlist_data .= '<img class="rankno" src="../img/index/no'.($i+1).'.png" width="60" height="55" alt="No'.($i+1).'">';
			}
			
			$itemlist_data .= '
							<img src="'._IMG_PSS.'items/list/'.$folder.'/'.$code.'/'.$code.$suffix.'.jpg" width="90%" height="auto" alt="'.strtoupper($code).'">
						</li>
						<li class="item_info_s clearfix">
							<ul>
								<li class="cs_s">
									<ul>
										<li class="colors">'.$v['colors'].'</li>
										<li class="sizes">'.$v['sizes'].'</li>
									</ul>
								</li>
								<li class="price_s">TAKAHAMA価格
									<br> <span><span>'.$v['minprice'].'</span>円～</span>
								</li>
							</ul>
						</li>
					</ul>
				</a>
				<div class="review_anchor">'.$v['review_path'].'</div>
			</li>';
			
			$ls[] = $itemlist_data;
			$i++; 
		} 
		$res = implode("", $ls).'|'.mb_convert_kana(count($dat),'A', 'utf-8');
		
		break;
	}
	echo $res;

}else if(isset($_PAGE_ESTIMATION_1)){
	/*
	*	カンタン見積ページ
	*/
	$data = $conn->categoryList();
	$category_selector = '<select id="category_selector" onchange="$.changeCategory(this)">';
	for($i=0; $i<count($data); $i++){
		$category_selector .= '<option value="'.$data[$i]['id'].'" rel="'.$data[$i]['code'].'"';
		if($i==0) $category_selector .= ' selected="selected"';
		$category_selector .= '>'.mb_convert_encoding($data[$i]['name'],'euc-jp','utf-8').'</option>';
	}
	$category_selector .= '</select>';
	
	$data = $conn->itemList();
	$curitemid = $data[0]['id'];	
	
	$files = $conn->positionFor($curitemid);
	// 見積り計算フォームのプリント位置指定

$posdiv = "";
	for($i=0; $i<count($files); $i++){
		$imgfile = file_get_contents($files[$i]['filename']);
		$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
//			$ink = '<div id="inktarget'.$i.'" class="'.$files[$i]['base_name'].'">';
//			$ink .= '</div>';
		$posname = '<div class="posname_'.$i.'"></div>';
		$ink = '<div><select class="ink_'.$i.'"><option value="0" selected="selected">選択してください</option>';
		$ink .= '<option value="1">1色</option><option value="2">2色</option><option value="3">3色</option>';
		$ink .= '<option value="9">4色以上</option></select></div>';
			$posdiv .= '<li class="pntposition">';
				$posdiv .= '<div class="psnv">';
//				$posdiv .= '<div class="pnttxt"><p class="posname_'.$i.'"></p></div>';
					$posdiv .= '<div class="pos_'.$i.'">'.$f.'</div>';
					//$posdiv .= '<div><p>デザインの色数</p><p><select class="ink_'.$i.'"><option value="0" selected="selected">選択してください</option><option value="1">1色</option><option value="2">2色</option><option value="3">3色</option><option value="9">4色以上</option></select></p></div>';
					$posdiv .= $posname;
					$posdiv .= $ink;
					$posdiv .= '</div>';
			$posdiv .= '</li>';
	}
$posdiv = mb_convert_encoding($posdiv,'euc-jp','utf-8');

	$pos = '<tr>';
	$pos .= '<th>プリント位置</th>';
	for($i=0; $i<count($files); $i++){
		$imgfile = file_get_contents($files[$i]['filename']);
		$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
		$pos .= '<td><div class="pos_'.$i.'">'.$f.'</div></td>';
		$posname .= '<td><p class="posname_'.$i.'"></p></td>';
		$ink .= '<td><p><select class="ink_'.$i.'"><option value="0" selected="selected">選択してください</option>';
		$ink .= '<option value="1">1色</option><option value="2">2色</option><option value="3">3色</option>';
		$ink .= '<option value="9">4色以上</option></select></p></td>';
	}
	$pos .= '</tr>';
	$pos .= '<tr><th>　</th>'.$posname.'</tr>';
	$pos .= '<tr><th>デザインの<br>色数</th>'.$ink.'</tr>';

	$pos = mb_convert_encoding($pos,'euc-jp','utf-8');

	// 個別のアイテム指定がある場合
	if(isset($param['itemcode'], $param['amount'], $param['ink'])){
		for($t=0; $t<count($param['itemcode']); $t++){
			$area[]='f';
			
			// size
			$data = $conn->itemSize($param['itemcode'][$t],null,'code');
			$s = array();
			for($i=0; $i<count($data); $i++){
				if($data[$i]['id']<11){								// 70-160
					$s[0][] = $data[$i]['name'];
				}else if($data[$i]['id']<17 || $data[$i]['id']>28){	// JS-JL, GS-GL, WS-WL
					$s[1][] = $data[$i]['name'];
				}else{												// XS-8L
					$s[2][] = $data[$i]['name'];
				}
			}
			for($i=0; $i<3; $i++){
				if(!empty($s[$i])){
					if($s[$i][0]!=$s[$i][count($s[$i])-1]){
						$s[3][] = $s[$i][0].'-'.$s[$i][count($s[$i])-1];
					}else{
						$s[3][] = $s[$i][0];
					}
				}
			}
			$size[$param['itemcode'][$t]] = implode(', ', $s[3]);
		
		}
		$res = $conn->estimateEach($param['itemcode'], $param['amount'], $param['ink'], $area, $sheetsize='1');
	}

}else if(isset($_PAGE_STANDARD)){
	/* 
	*	料金の目安
	*	一枚あたりの料金の目安を取得する
	*	@itemcode	アイテムコード
	*	@amount		枚数
	*	@int		インク色数
	*
	*	@return		[['price':見積金額, 'perone':1枚あたり],[...]]
	*				引数の配列インデックスに対応した商品の料金を返す
	*/
	$area = array('f');
	for($i=0; $i<count($param['itemcode']); $i++){
		$p1 = array($param['itemcode'][$i]);
		$p2 = array($param['amount'][$i]);
		$p3 = array($param['ink'][$i]);
		$price = $conn->estimateEach($p1, $p2, $p3, $area, $sheetsize='1');
		$res[$i] = $price[$param['itemcode'][$i]];
	}
	
	$data = $conn->categoryList();
	$category_selector = '<select id="category_selector" onchange="$.changeCategory(this)">';
	foreach($data as $key=>$val){
		$category_selector .= '<option value="'.$val['id'].'"';
		if($key==0) $category_selector .= ' selected="selected"';
		$category_selector .= '>'.mb_convert_encoding($val['name'],'euc-jp','utf-8').'</option>';
	}
	$category_selector .= '</select>';
	
}else if(isset($_PAGE_ESTIMATION)){
	/*
	*	シーン別ページ
	*/

	$data = $conn->categoryList();
	$category_selector = '<select id="category_selector">';
	foreach($data as $key=>$val){
		$category_selector .= '<option value="'.$val['code'].'" rel="'.$val['id'].'"';
		if($key==0) $category_selector .= ' selected="selected"';
		$category_selector .= '>'.mb_convert_encoding($val['name'],'euc-jp','utf-8').'</option>';
	}
	$category_selector .= '</select>';
	
	$data = $conn->itemList();
	$item_selector = '<select id="item_selector" onchange="$.changeItem()">';
	foreach($data as $key=>$val){
		//if($subcat['long-shirts'][$val['code']] || $subcat['baby'][$val['code']]) continue;
		if($val['code']=='ss-9999-96') continue;
		
		$item_selector .= '<option value="'.$val['id'].'" rel="'.$val['code'].'"';
		if($key==0) $item_selector .= ' selected="selected"';
		$item_selector .= '>'.mb_convert_encoding($val['name'],'euc-jp','utf-8').'</option>';
	}
	$item_selector .= '</select>';
	$curitemid = $data[0]['id'];
	
	$itemattr = $conn->itemAttr($curitemid);
	list($categorykey, $categoryname) = each($itemattr['category']);
	list($itemcode, $itemname) = each($itemattr['name']);
	list($code, $colorname) = each($itemattr['code']);
	
	$color_count = 0;
	foreach($itemattr['code'] as $code=>$colorname){
		$color_count++;
		$c = explode('_', $code);
		$color_name = mb_convert_encoding($colorname,'euc-jp','utf-8');
		$thumbs .= '<li';
		if($color_count==1){
			$thumbs .= ' class="nowimg"';
			$curcolor = $color_name;
		}
		$thumbs .= '><img alt="'.$c[1].'" title="'.$color_name.'" src="'._IMG_PSS.'items/'.$categorykey.'/'.$itemcode.'/'.$code.'_s.jpg" /></li>';
	}
	
	$files = $conn->positionFor($curitemid);
	$pos = '<tr class="posid_'.$files[0]['posid'].'"><td colspan="3" class="pos_step ps1">(1)プリントする位置を選択してください。</td></tr>';
	$pos .= '<tr>';
	for($i=0; $i<count($files); $i++){
		$imgfile = file_get_contents($files[$i]['filename']);
		$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
		$pos .= '<td><div class="pos_'.$i.'">'.$f.'</div></td>';
		$ink .= '<td id="inktarget'.$i.'" class="'.$files[$i]['base_name'].'">';
		$ink .= '</td>';
	}
	$pos .= '</tr>';
	
	$pos .= '<tr>';
	$pos .= '<td colspan="3" class="pos_step">(2)選択した位置のプリントに使用する、インクの色数を選択してください。';
	$pos .= '<span class="questions"><a class="info_icon" target="_new" href="/design/fontcolor.html#navi2">使用インク色？</a></span>';
	$pos .= '</td>';
    $pos .= '</tr>';
    
	$pos .= '<tr>'.$ink.'</tr>';
	$pos = mb_convert_encoding($pos,'euc-jp','utf-8');
		
	if(isset($param['itemcode'], $param['amount'], $param['ink'])){
		for($t=0; $t<count($param['itemcode']); $t++){
			$area[]='f';
		}
		$res = $conn->estimateEach($param['itemcode'], $param['amount'], $param['ink'], $area, $sheetsize='1');
		
		for($t=0; $t<count($param['itemcode']); $t++){			
			// サイズ展開
			$data = $conn->itemSize($param['itemcode'][$t],null,'code');
			$s = array();
			for($i=0; $i<count($data); $i++){
				if($data[$i]['id']<11){								// 70-160
					$s[0][] = $data[$i]['name'];
				}else if($data[$i]['id']<17 || $data[$i]['id']>28){	// JS-JL, GS-GL, WS-WL
					$s[1][] = $data[$i]['name'];
				}else{												// XS-8L
					$s[2][] = $data[$i]['name'];
				}
			}
			for($i=0; $i<3; $i++){
				if(!empty($s[$i])){
					if($s[$i][0]!=$s[$i][count($s[$i])-1]){
						$s[3][] = $s[$i][0].'-'.$s[$i][count($s[$i])-1];
					}else{
						$s[3][] = $s[$i][0];
					}
				}
			}
			$size[$param['itemcode'][$t]] = implode(', ', $s[3]);
			
			// カラー数、最低価格
			$itemcode = $param['itemcode'][$t];
			$pageinfo = $conn->itemPageInfo($itemcode, 'code');
			
			$res[$itemcode]['item_name'] = mb_convert_encoding($pageinfo['item_name'], 'euc-jp', 'utf-8');
			$res[$itemcode]['colors'] = $pageinfo['colors'];
			$tes[$itemcode]['sizes'] = $pageinfo['sizes'];
			$res[$itemcode]['minprice'] = number_format($pageinfo['mincost']);
			$res[$itemcode]['initcolor'] = $pageinfo['initcolor'];
			$tmpes[$itemcode]['features'] = mb_convert_encoding($pageinfo['caption'],'euc-jp','utf-8');
		}
		
	}
	
}else if(isset($_PAGE_CATEGORIES)){
	/*
	*	商品一覧ページ
	*/
	$catpage = new CategoryPage();
	$id = $_PAGE_CATEGORIES;
	$tag = array();
	$mode = "category";
	if(isset($_IS_TAG)){
		// ブランド、スポーツウェア
		$mode = "tag";
	}
	
	// タグ条件チェックボックスの指定状況
	$_IS_SCROLL = "";
	parse_str(urldecode($_SERVER['QUERY_STRING']), $prm);
	if(!empty($prm)){
		$_TAG = array();
		$tag_data = "";
		foreach($prm as $key=>$val){
			if(empty($val)) continue;
			if(is_array($prm[$key])){
				for($i=0; $i<count($prm[$key]); $i++){
					$tag_data .= '<input type="hidden" name="'.$key.'[]" value="'.$val[$i].'">';
					$_TAG[] = $val[$i];
				}
			}else{
				$addtag = explode("_", $val);
				$tag_data .= '<input type="hidden" name="'.$addtag[1].'[]" value="'.$addtag[0].'">';
				$_TAG[] = $addtag[0];
			}
		}
		$_IS_SCROLL = "tag_wrap";
	}
	
	
	if(isset($_TAG)){
		// 検索条件で指定されているタグを設定
		$tag = array_merge($tag, $_TAG);
		$tag = array_unique($tag);
		$tag = array_values($tag);
		
		// カテゴリ説明
		$writing = $catpage->getTagText(end($_TAG));
	} 
	
	// カテゴリ一覧情報を取得
	$res = getCategoryInfo($id, $tag, $mode);
	
	// アイテムデータ一覧のタグを生成
	$category_name = '';
	$folder = '';
	$itemlist_data='' ; 
	$item_count = 0;
	foreach($res as $code=>$v){
		$category_name = $v['category_name'];
		$folder = $v['category_key'];
		if($item_count%4==0){ 
			$firstlist = ' firstlist'; 
		}else{ 
			$firstlist = ''; 
		} 
		if( (preg_match('/^p-/',$code) && $v['initcolor']=="") ){
			$suffix = '_style_0'; 
		} elseif (_IS_THUMB_FOR_EXPRESS=='1' && ($code=='522-ft' || $code=='085-cvt' )) {
			$suffix = '_for-express';	// 当日特急用のサムネイル 
		}else{ 
			$suffix = '_'.$v['initcolor']; 
		}
		$itemlist_data .= '
		<li class="listitems_ex'.$firstlist.'">
			<a href="/items/'.$folder.'/item.html?id='.$code.'">
				<ul>
					<li class="point_s">'.$v['features'].'</li>
					<li class="item_name_s">
						<ul>
							<li class="item_name_kata">'.strtoupper($code).'</li>
							<li class="item_name_name">'.$v['item_name'].'</li>
						</ul>
					</li>
					<li class="item_image_s">';
		
		if($item_count<3){
			$itemlist_data .= '<img class="rankno" src="../img/index/no'.($item_count+1).'.png" width="60" height="55" alt="No'.($item_count+1).'">';
			$itemname_data .= '<img class="today" src="../img/index/today'.($item_count+1).'.png" width="60" height="55" alt="No'.($item_count+1).'">';
			$itemname_data .= '<img class="today" src="../img/index/today'.($item_count+1).'.png" width="60" height="55" alt="No'.($item_count+1).'">';
		}
		
		$itemlist_data .= '
						<img src="'._IMG_PSS.'items/list/'.$folder.'/'.$code.'/'.$code.$suffix.'.jpg" width="90%" height="auto" alt="'.strtoupper($code).'">
					</li>
					<li class="item_info_s clearfix">
						<ul>
							<li class="cs_s">
								<ul>
									<li class="colors">'.$v['colors'].'</li>
									<li class="sizes">'.$v['sizes'].'</li>
								</ul>
							</li>
							<li class="price_s">TAKAHAMA'.mb_convert_encoding('価格', 'euc-jp','utf-8').'
								<br> <span><span>'.$v['minprice'].'</span>'.mb_convert_encoding('円～', 'euc-jp','utf-8').'</span>
							</li>
						</ul>
					</li>
				</ul>
			</a>
			<div class="review_anchor">'.mb_convert_encoding($v['review_path'], 'euc-jp','utf-8').'</div>
		</li>';
		
		// 条件検索のタグ情報
		foreach((array)$v['tag'] as $tag_id=>$tag_val){
			$taginfo[$tag_id] = $tag_val;
		}
		
		$item_count++;
	}
	
	// 除外するタグ
	switch($id){
		case 1:		unset($taginfo[71], $taginfo[11]);
					break;
		case 2:		unset($taginfo[72]);
					break;
		case 3:		unset($taginfo[17]);
					break;
		case 73:	unset($taginfo[73], $taginfo[2]);
					break;
		case 5:		unset($taginfo[75]);
					break;
		case 6:		unset($taginfo[76]);
					break;
		case 7:		unset($taginfo[77]);
					break;
		case 8:		unset($taginfo[78]);
					break;
		case 9:		unset($taginfo[79]);
					break;
		case 10:	unset($taginfo[80]);
					break;
		case 11:	unset($taginfo[84]);
					break;
		case 12:	unset($taginfo[83]);
					break;
		case 13:	unset($taginfo[74], $taginfo[12]);
					break;
		case 14:	unset($taginfo[81]);
					break;
		case 16:	unset($taginfo[82]);
					break;
	}
	
	// 各カテゴリの商品一覧ページで全ての商品を指定した時のライティング
	if(empty($writing)) $writing = $catpage->getCatText($_PAGE_CATEGORIES);
	
	// 人気商品から探すで表示するアイテム
	if(isset($popular)){
		for($a=0; $a<count($popular); $a++){
			$itemcode = $popular[$a];
			$pageinfo = $conn->itemPageInfo($itemcode, 'code');
			$pop[$itemcode]['item_name'] = mb_convert_encoding($pageinfo['item_name'], 'euc-jp', 'utf-8');
			$pop[$itemcode]['colors'] = $pageinfo['colors'];
			$pop[$itemcode]['sizes'] = $pageinfo['sizes'];
			$pop[$itemcode]['minprice'] = number_format($pageinfo['mincost']);
			$pop[$itemcode]['initcolor'] = mb_convert_encoding($pageinfo['initcolor'], 'euc-jp', 'utf-8');	//$ic[$itemcode];
		}
	}
	
	// タグの表示順で並び替え
	$tmp = array();
	foreach((array)$taginfo as $tag_id=>$tag_val){
		$tmp[$tag_val['tagorder']] = $tag_val;
	}
	unset($tag_val);
	$tagSorted = $tmp;
	ksort($tagSorted);
	
	// サイドナビの条件検索タグを生成
	$catNumber = 0;
	$category_tag = "";
	$scene_tag = "";
	$silhouette_tag = "";
	$material_tag = "";
	$cloth_tag = "";
	$size_tag = "";
	foreach($tagSorted as $tag_order=>$tag_val){
		$isChecked = '';
		if(in_array($tag_val['tagid'], $tag)) $isChecked = 'checked="checked"';
		switch($tag_val["tagtype"]){
/*
		case 1:	$category_tag .= '<li><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></li>';
				$catNumber++;
				break;
		case 2: $scene_tag .= '<li><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></li>';
				break;
		case 3: $silhouette_tag .= '<li><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></li>';
				break;
		case 4: $material_tag .= '<li><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></li>';
				break;
		case 5: $cloth_tag .= '<li><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></li>';
				break;
		case 6: $size_tag .= '<li><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></li>';
				break;
*/
		case 1:	$category_tag .= '<div class="tag_list_item"><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></div>';
				$catNumber++;
				break;
		case 2: $scene_tag .= '<div class="tag_list_item"><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></div>';
				break;
		case 3: $silhouette_tag .= '<div class="tag_list_item"><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></div>';
				break;
		case 4: $material_tag .= '<div class="tag_list_item"><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></div>';
				break;
		case 5: $cloth_tag .= '<div class="tag_list_item"><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></div>';
				break;
		case 6: $size_tag .= '<div class="tag_list_item"><label><input type="checkbox" '.$isChecked.' value="'.$tag_val['tagid'].'_'.$tag_val["tagtype_key"].'">'.mb_convert_encoding($tag_val["tagname"], 'euc-jp', 'utf-8').'</label></div>';
				break;
		}
	}

	// ブランド一覧の条件タグの初期表示でカテゴリが複数種類の場合はカテゴリのみを表示する、ただしスポーツウェア、チャンピオン、プーマ、ウンドウはシーンカテゴリも表示
	if(isset($_IS_TAG) && empty($_TAG) && $catNumber>1){
		$silhouette_tag = "";
		$material_tag = "";
		$cloth_tag = "";
		$size_tag = "";
	}
/*
	$category_tag = empty($category_tag)? "": '<ul><li class="index">'.mb_convert_encoding("カテゴリー", 'euc-jp', 'utf-8').'</li>'.$category_tag."</ul>";
	$scene_tag = empty($scene_tag)? "": '<ul><li class="index">'.mb_convert_encoding("シーン", 'euc-jp', 'utf-8').'</li>'.$scene_tag."</ul>";
	$silhouette_tag = empty($silhouette_tag)? "": '<ul><li class="index">'.mb_convert_encoding("シルエット", 'euc-jp', 'utf-8').'</li>'.$silhouette_tag."</ul>";
	$material_tag = empty($material_tag)? "": '<ul><li class="index">'.mb_convert_encoding("素材", 'euc-jp', 'utf-8').'</li>'.$material_tag."</ul>";
	$cloth_tag = empty($cloth_tag)? "": '<ul><li class="index">'.mb_convert_encoding("生地", 'euc-jp', 'utf-8').'</li>'.$cloth_tag."</ul>";
	$size_tag = empty($size_tag)? "": '<ul><li class="index">'.mb_convert_encoding("サイズ", 'euc-jp', 'utf-8').'</li>'.$size_tag."</ul>";
*/
	$category_tag = empty($category_tag)? "": '<div class="tag_list"><div class="tag_list_title">'.mb_convert_encoding("カテゴリー", 'euc-jp', 'utf-8').'</div>'.$category_tag."</div><hr>";
	$scene_tag = empty($scene_tag)? "": '<div class="tag_list"><div class="tag_list_title">'.mb_convert_encoding("シーン", 'euc-jp', 'utf-8').'</div>'.$scene_tag."</div><hr>";
	$silhouette_tag = empty($silhouette_tag)? "": '<div class="tag_list"><div class="tag_list_title">'.mb_convert_encoding("シルエット", 'euc-jp', 'utf-8').'</div>'.$silhouette_tag."</div><hr>";
	$material_tag = empty($material_tag)? "": '<div class="tag_list"><div class="tag_list_title">'.mb_convert_encoding("素材", 'euc-jp', 'utf-8').'</div>'.$material_tag."</div><hr>";
	$cloth_tag = empty($cloth_tag)? "": '<div class="tag_list"><div class="tag_list_title">'.mb_convert_encoding("生地", 'euc-jp', 'utf-8').'</div>'.$cloth_tag."</div><hr>";
	$size_tag = empty($size_tag)? "": '<div class="tag_list"><div class="tag_list_title">'.mb_convert_encoding("サイズ", 'euc-jp', 'utf-8').'</div>'.$size_tag."</div><hr>";
	
	// パンナビを生成
	if(isset($_IS_TAG)){
		$tag_master = $conn->tagInfo($_PAGE_CATEGORIES);
		$label_pannavi = mb_convert_encoding($tag_master[0]['tag_name'], 'euc-jp', 'utf-8');
	}else{
		$label_pannavi = mb_convert_encoding('オリジナル', 'euc-jp', 'utf-8').$category_name;
	}
	$pan_navi = "";
	$navi_querystring = array();
	if(count($tag)>0){
		$pan_navi = '<li><a href="/items/'.$folder.'">'.$label_pannavi.'</a></li>';
		for($i=0; $i<count($tag)-1; $i++){
			$navi_querystring[] = urlencode($taginfo[$tag[$i]]['tagtype_key']."[]")."=".$tag[$i];
			$pan_navi .= '<li><a href="'.$_SERVER["SCRIPT_NAME"].'?'.implode("&", $navi_querystring).'">'.mb_convert_encoding($taginfo[$tag[$i]]['tagname'], 'euc-jp', 'utf-8').'</a></li>';
		}
		$pan_navi .= '<li>'.mb_convert_encoding($taginfo[$tag[$i]]['tagname'], 'euc-jp', 'utf-8').'</li>';
	}else{
		$pan_navi = '<li>'.$label_pannavi.'</li>';
	}
	
	
}else if(isset($_PAGE_ITEMDETAIL)){
	/*
	*	商品詳細ページ
	*/
	
	if($_PAGE_ITEMDETAIL===true) $_PAGE_ITEMDETAIL=1;
	
	$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
	/* PHP 5.2.0 以降
	$data['itemcode'] = $path_parts['filename'];
	*/

//パラメーターからitemcodeを取得
$_ITEM_CODE = $_GET['id'];

	if(isset($_ITEM_CODE)){
		$data['itemcode'] = $_ITEM_CODE;
	}else{
		$data['itemcode'] = basename($_SERVER['SCRIPT_NAME'],'.'.$path_parts['extension']);
	}
	$data['itemid'] = $conn->itemID($data['itemcode']);
	

	// アイテム情報取得
	$itemattr = $conn->itemAttr($data['itemid']);
	list($categorykey, $categoryname) = each($itemattr['category']);
	list($itemcode, $itemname) = each($itemattr['name']);
	list($code, $colorname) = each($itemattr['code']);
	$categoryname = mb_convert_encoding($categoryname,'euc-jp','utf-8');
	$itemname = mb_convert_encoding($itemname,'euc-jp','utf-8');
	
	// サイズテーブル
	$itemMeasure = $conn->getItemMeasure($itemcode);
	$columns = array("KIDS&#39"=>1,"LADIES&#39"=>1,"UNISEX"=>1);
	$tblHash = array("KIDS&#39"=>"","LADIES&#39"=>"","UNISEX"=>"");
	$tblHead = array("KIDS&#39"=>"<tr><td>SIZE</td>","LADIES&#39"=>"<tr><td>SIZE</td>","UNISEX"=>"<tr><td>SIZE</td>");
	$tblType = array();
	$thumb = "";
	$len = count($itemMeasure);
	$curMeasure = $itemMeasure[0]["measure_id"];
	for($i=0; $i<$len; $i++){
		if($itemcode=='085-cvt'){
			$sizeTitle = mb_convert_encoding("サイズ： ".$itemMeasure[$i]["size_name"], 'euc-jp', 'utf-8');
			$thumb = '<div class="thumb">';
			$thumb .= '<a href="../img/size/pop/085-cvt_'.$itemMeasure[$i]["size_name"].'-size.jpg" rel="prettyPhoto[size]">';
			$thumb .= '<img src="../img/size/pop/085-cvt_'.$itemMeasure[$i]["size_name"].'-size.jpg" width="25" alt="'.$sizeTitle.'">';
			$thumb .= '</a></div>';
		}
		if($itemMeasure[$i]["size_name"]=='F' || $itemMeasure[$i]["size_name"]=='S' || $itemMeasure[$i]["size_name"]=='M' || $itemMeasure[$i]["size_name"]=='L' || $itemMeasure[$i]["size_name"]=='XL' || $itemMeasure[$i]["size_name"]=='O' || $itemMeasure[$i]["size_name"]=='XO' || $itemMeasure[$i]["size_name"]=='YO' || $itemMeasure[$i]["size_name"]=='2YO' || (substr($itemMeasure[$i]["size_name"],0,1)>=3 && substr($itemMeasure[$i]["size_name"],1,1)>="L")){
			$tblType["UNISEX"][] = $itemMeasure[$i];
			if($itemMeasure[$i]["measure_id"]==$curMeasure){
				$tblHead["UNISEX"] .= "<td>".$thumb.$itemMeasure[$i]["size_name"]."</td>";
				$columns["UNISEX"]++;
			}
		}else if(substr($itemMeasure[$i]["size_name"],0,1)=='W' || substr($itemMeasure[$i]["size_name"],0,1)=='G'){
			$tblType["LADIES&#39"][] = $itemMeasure[$i];
			if($itemMeasure[$i]["measure_id"]==$curMeasure){
				$tblHead["LADIES&#39"] .= "<td>".$thumb.$itemMeasure[$i]["size_name"]."</td>";
				$columns["LADIES&#39"]++;
			}
		}else{
			$tblType["KIDS&#39"][] = $itemMeasure[$i];
			if($itemMeasure[$i]["measure_id"]==$curMeasure){
				$tblHead["KIDS&#39"] .= "<td>".$thumb.$itemMeasure[$i]["size_name"]."</td>";
				$columns["KIDS&#39"]++;
			}
		}
		$col++;
	}
	$itemsize_table = "";
	foreach ($tblType as $key => $value) {
		$curMeasure = 0;
		$preDimension = "";
		$col = 0;
		$len = count($value);
		for($i=0; $i<$len; $i++){
			if(empty($tblHash[$key])){
				if($categorykey=='tote-bag' || $categorykey=='towel'){
					$tblHash[$key] .= '<table>';
				}else{
					$tblHash[$key] .= '<table><caption>'.$key.'</caption>';
				}
				$tblHash[$key] .= '<tfoot><tr><td colspan="'.$columns[$key].'">(cm)</td></tr></tfoot><tbody>';
				$tblHash[$key] .= $tblHead[$key].="</tr>";
			}
			if($value[$i]["measure_id"]!=$curMeasure){
				if($curMeasure!=0){
					if($col==1){
						$tblHash[$key] .= '<td>';
					}else{
						$tblHash[$key] .= '<td colspan="'.$col.'">';
					}
					$tblHash[$key] .= mb_convert_encoding($preDimension, 'euc-jp', 'utf-8').'</td>';
					$col = 0;
					$preDimension = "";
					$tblHash[$key] .= "</tr>";
				}
				$tblHash[$key] .= "<tr><td>".mb_convert_encoding($value[$i]["measure_name"], 'euc-jp', 'utf-8')."</td>";
				$curMeasure = $value[$i]["measure_id"];
			}
			if($preDimension!="" && $preDimension!=$value[$i]["dimension"]){
				if($col==1){
					$tblHash[$key] .= '<td>';
				}else{
					$tblHash[$key] .= '<td colspan="'.$col.'">';
				}
				$tblHash[$key] .= mb_convert_encoding($preDimension, 'euc-jp', 'utf-8').'</td>';
				$col = 1;
				$preDimension = $value[$i]["dimension"];
			}else{
				$col++;
				$preDimension = $value[$i]["dimension"];
			}
		}
		if($col==1){
			$tblHash[$key] .= '<td>';
		}else{
			$tblHash[$key] .= '<td colspan="'.$col.'">';
		}
		$tblHash[$key] .= mb_convert_encoding($preDimension, 'euc-jp', 'utf-8').'</td>';
		$itemsize_table .= $tblHash[$key].'</tr></tbody></table>';
	}
	$ite = new Items($categorykey);
	$_PAGE_CATEGORYID = $ite->getCategoryID();
	$posid = $itemattr['ppid'];
	
	$curthumb = '<img id="item_image_l" src="'._IMG_PSS.'items/'.$categorykey.'/'.$itemcode.'/'.$code.'.jpg" width="300" height="300">';
	$color_count = 0;
	
	foreach($itemattr['code'] as $code=>$colorname){
		$size = array();
		foreach($itemattr['size'][$code] as $sizeid=>$sizename){
			if($sizeid<11){						// 70-160
				$size[0][] = $sizename;
			}else if($sizeid<17 || $sizeid>28){	// JS-JL, GS-GL, WS-WL
				$size[1][] = $sizename;
			}else{								// XS-8L
				$size[2][] = $sizename;
			}
		}
		for($i=0; $i<3; $i++){
			if(!empty($size[$i])){
				if(count($size[$i])==1){
					$size[3][] = $size[$i][0];
				}else{
					$size[3][] = $size[$i][0].'-'.$size[$i][count($size[$i])-1];
				}
			}
		}
		$s = implode(', ', $size[3]);
		
		$color_count++;
		$c = explode('_', $code);
		$thumbs .= '<li';
		if($color_count==$_PAGE_ITEMDETAIL){
			$thumbs .= ' class="nowimg"';
			$curcolor = mb_convert_encoding($colorname,'euc-jp','utf-8');
			$curthumbcolor = $colorname;
			$cursize = $s;
			
			$curthumb = '<img id="item_image_l" src="'._IMG_PSS.'items/'.$categorykey.'/'.$itemcode.'/'.$code.'.jpg" width="300" height="300">';
		}
		$thumbs .= '><img alt="'.$c[1].'" title="'.$colorname.'" src="'._IMG_PSS.'items/'.$categorykey.'/'.$itemcode.'/'.$code.'_s.jpg" /></li>';
	}
	
	$res['itemid'] = $data['itemid'];
	list($rows, $isSwitch, $cost) = $conn->priceFor($data['itemid']);
	list($sizeid, $mincost) = each($cost);
	$res['mincost'] = $mincost;
	$amount = 30;
	$args[] = array('itemid'=>$data['itemid'], 'amount'=>$amount, 'ink'=>1, 'pos'=>'f');
	$price = $conn->printfee($args);
	$base = $price['printfee'] + $mincost*$amount;
	$tax = 1+($price['tax']/100);
	$res['perone'] = ceil(floor($base*$tax)/$amount);
	
	// モデル着用写真のポップアップ
	$filename = $conn->getModelPhoto($categorykey, $itemcode);
	for ($i=0; $i < count($filename); $i++) { 
		$base = explode('.', $filename[$i]);
		$tmp = explode('_', $base[0]);
		$alt = mb_convert_encoding($tmp[2].'cm・'.$tmp[1].'サイズ着用', 'euc-jp', 'utf-8');
		$pop_title = mb_convert_encoding('モデル着用写真 (','euc-jp','utf-8').$itemname.')';
		
		$modelimage = _IMG_PSS."items/".$categorykey."/model/".$itemcode.'/'.$filename[$i];
		$model_gallery .= '<li><a href="'.$modelimage.'" rel="prettyPhoto[model]" title="'.$pop_title.'"><img src="'.$modelimage.'" height="70" alt="'.$alt.'" /></a></li>';
	}
	if(!empty($model_gallery)){
		$model_gallery = '<p class="thumb_h">Model</p><ul class="clearfix">'.$model_gallery.'</ul>';
	}
	
	// スタイル写真
	$filename = $conn->getStylePhoto($categorykey, $itemcode);
	for ($i=0; $i < count($filename); $i++) {
		$style_gallery .= '<li><img src="'._IMG_PSS.'items/'.$categorykey.'/'.$itemcode.'/'.$filename[$i].'" height="70" alt="'.$itemcode.'" /></li>';
	}
	if(!empty($style_gallery)){
		$style_gallery = '<p class="thumb_h">Style</p><ul id="style_thumb">'.$style_gallery.'</ul>';
	}
	
	// アンカーリンク
	$path = str_replace(DIRECTORY_SEPARATOR.$_SERVER['SERVER_NAME'], '', dirname($_SERVER['SCRIPT_NAME'])).DIRECTORY_SEPARATOR;
	$items = $conn->itemList($_PAGE_CATEGORYID);
	$extension = '<div class="ext_wrap"><ul>';
	foreach((array)$items as $key=>$val){
		$extension .= '<li><a href="'.$path.'item.html?id='.$val['code'].'">'.mb_convert_encoding($val['name'],'euc-jp','utf-8').'</a></li>';
	}
	$extension .= '</ul></div>';
	
	// アイテムレビュー
	$review = '';
	$itemreview = '';
	$review_data = $conn->getItemReview(array('sort'=>'post', 'itemid'=>$data['itemid']));
	$review_len = count($review_data);
	if($review_len>0){
		if($review_len>2){
			$end = 2;	// レビューを2件まで表示
		}else{
			$end = $review_len;
		}
		$review_list = '';
		for($i=0; $i<$end; $i++){
			$amount = number_format($review_data[$i]['amount']);
			if(mb_strlen($review_data[$i]['review'], 'utf-8')>32){
				$review_text = mb_substr($review_data[$i]['review'], 0, 32, 'utf-8');
				$review_text .= ' ...';
			}else{
				$review_text = $review_data[$i]['review'];
			}
			$review_text = nl2br(mb_convert_encoding($review_text, 'euc-jp', 'utf-8'));
			
			$review_list .= '<div class="unit_body clearfix">';
				$review_list .= '<div class="unit_body_left">';
					$review_list .= '<p>Vote: <ins>'.$review_data[$i]['vote'].'</ins></p>';
				$review_list .= '</div>';
				$review_list .= '<div class="unit_body_right">';
					$review_list .= '<ul class="unit_body_right_inner">';
						$review_list .= '<li>'.$review_text.'</li>';
					$review_list .= '</ul>';
				$review_list .= '</div>';
			$review_list .= '</div>';
		}
		$review = '<div id="item_review"><p class="thumb_h">Review</p>'.$review_list;
		$review .= mb_convert_encoding('<p class="tor"><a href="/itemreviews/index.php?item='.$data['itemid'].'">もっと見る（'.$review_len.'件）</a></p>','euc-jp','utf-8');
		$review .= '</div>';
		
		$review_list = '';
		for($i=0; $i<$end; $i++){
			$star = getStar($review_data[$i]['vote']);
			$amount = number_format($review_data[$i]['amount']);
			if(mb_strlen($review_data[$i]['review'], 'utf-8')>32){
				$review_text = mb_substr($review_data[$i]['review'], 0, 32, 'utf-8');
				$review_text .= ' ...';
			}else{
				$review_text = $review_data[$i]['review'];
			}
			$review_text = nl2br(mb_convert_encoding($review_text, 'euc-jp', 'utf-8'));
			
			$review_list .= '<div class="unit_body">';
				$review_list .= '<p><img src="/itemreviews/img/'.$star.'.png" width="114" height="21" alt=""><ins>'.$review_data[$i]['vote'].'</ins></p>';
				$review_list .= '<p>'.$review_text.'</p>';
			$review_list .= '</div>';
		}
		$itemreview = '<h2 id="review_side">'.mb_convert_encoding('アイテムレビュー', 'euc-jp','utf-8').'</h2>';
		$itemreview .= $review_list;
		$itemreview .= mb_convert_encoding('<p class="tor"><a href="/itemreviews/index.php?item='.$data['itemid'].'">もっと見る（'.$review_len.'件）</a></p>','euc-jp','utf-8');
	}
	
	// 右側列
	$itemDetail = $conn->getItemDetail($itemcode);
	$right_column = '<div id="item_title">';
		$right_column .= '<h2><span id="item_code">'.strtoupper($itemcode).'</span></h2>';
		$right_column .= '<h1>'.mb_convert_encoding($itemname, "utf-8", "euc-jp").'</h1>';
		$right_column .= '<div id="price">Takahama価格：<span id="price_detail">'.number_format($res['mincost']).'円&#65374;/１枚</span></div>';
		$right_column .= '<div id="priceex">';
			$right_column .= '例えば、<br>注文枚数<span>30</span>枚&nbsp;プリント位置<span>1</span>ヶ所&nbsp;インク<span>1</span>色で、<br>';
			$right_column .= '<div id="priceexper"><span>'.number_format($res['perone']).'</span>円/1枚</div>';
		$right_column .= '</div>';
		$right_column .= '<div id="orderbtn_wrap_up">';
			$right_column .= '<form name="f1" action="/order/" method="post">';
				$right_column .= '<input type="hidden" name="item_id" value="'.$data['itemid'].'">';
				$right_column .= '<input type="hidden" name="update" id="update" value="1">';
				$right_column .= '<div id="btnOrder_up" onclick="ga([\'send\',\'event\',\'order\',\'click\',\''.$itemcode.'\']);">お申込みフォームへ</div>';
	    	$right_column .= '</form>';
	    $right_column .= '</div>';
		$right_column .= '<ul id="blue_btns"><li id="calbtn"><a href="#howmuch" onclick="ga([\'send\',\'event\',\'howmuch\',\'click\',\''.$itemcode.'\']);"></a></li></ul>';
		$right_column .= '</div>';
	$right_column .= '<div class="contents-lv3">'.mb_convert_encoding($itemreview, "utf-8", "euc-jp").'</div>';
	$right_column .= '<div class="contents-lv3">';
		$right_column .= '<h2 id="info_side">アイテム説明</h2>';
		$right_column .= '<div id="info_txt">';
			$right_column .= '<p>'.nl2br($itemDetail["i_description"]).'</p>';
			$right_column .= '<p>■素材<br>'.nl2br($itemDetail["i_material"]).'</p>';
		$right_column .= '</div>';
	$right_column .= '</div>';
	$right_column = mb_convert_encoding($right_column, "euc-jp", "utf-8");
	
	// 対応するプリント方法
	$printMethod = $itemDetail['i_silk']? "<span>シルクスクリーン</span>": "<span class='none'></span>";
	$printMethod .= $itemDetail['i_digit']? "<span>デジタル転写</span>": "<span class='none'></span>";
	$printMethod .= $itemDetail['i_inkjet']? "<span>インクジェット</span>": "<span class='none'></span>";
	$printMethod .= $itemDetail['i_cutting']? "<span>カッティング</span>": "<span class='none'></span>";
	$printMethod = mb_convert_encoding($printMethod, "euc-jp", "utf-8");
	
	// 脚注
	$footNote .= $itemDetail["i_note_label"]? "<h3>".$itemDetail["i_note_label"]."</h3>": "";
	$footNote .= $itemDetail["i_note"]? "<p>".$itemDetail["i_note"]."</p>": "";
	$footNote = mb_convert_encoding($footNote, "euc-jp", "utf-8");
	
	// プリント可能範囲のサイズ
	$printSizeTable = '';
	$row = 0;
	$isFirstRow = TRUE;
	$preValue = "";
	$sizeName = array();
	$posArea = array();
	$sizePrice = $conn->sizePrice($data['itemid']);
	$len = count($sizePrice);
	for($i=0; $i<$len; $i++){
		$posArea[0][$sizePrice[$i]["name"]] = $sizePrice[$i]["printarea_1"];
		$posArea[1][$sizePrice[$i]["name"]] = $sizePrice[$i]["printarea_2"];
		$posArea[2][$sizePrice[$i]["name"]] = $sizePrice[$i]["printarea_3"];
		$posArea[3][$sizePrice[$i]["name"]] = $sizePrice[$i]["printarea_4"];
		$posArea[4][$sizePrice[$i]["name"]] = $sizePrice[$i]["printarea_5"];
		$posArea[5][$sizePrice[$i]["name"]] = $sizePrice[$i]["printarea_6"];
		$posArea[6][$sizePrice[$i]["name"]] = $sizePrice[$i]["printarea_7"];
		$row++;
	}
	for($i=0; $i<7; $i++){
		$tbl = "";
		$preValue = "";
		foreach ($posArea[$i] as $key => $value) {
			if(!$value) break;
			if(empty($tbl)){
				$tbl .= '<table>
							<colgroup>
								<col span="1" class="col01" />
								<col span="2" />
							</colgroup>
							<thead>
								<tr>
									<th>プリント箇所</th>
									<th>サイズ</th>
									<th>プリントサイズ(cm)</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th rowspan="'.$row.'">'.($i+1).'</th>';
			}
			
			if($preValue==""){
				$preValue = $value;
				$sizeName = array($key);
				$isFirstRow = TRUE;
			}else if($preValue!=$value){
				if(!$isFirstRow) $tbl .= '<tr>';
				if(empty($sizeName[1])){
					$tbl .= '<td>'.$sizeName[0].'</td>';
				}else{
					$tbl .= '<td>'.$sizeName[0].' - '.$sizeName[1].'</td>';
				}
				$tbl .= '<td>'.$preValue.'</td></tr>';
				$sizeName = array($key);
				$preValue = $value;
				$isFirstRow = FALSE;
			}else{
				$sizeName[1] = $key;
			}
			
		}
		if(!empty($tbl)){
			if(!$isFirstRow) $tbl .= '<tr>';
			if(empty($sizeName[1])){
				$tbl .= '<td>'.$sizeName[0].'</td>';
			}else{
				$tbl .= '<td>'.$sizeName[0].' - '.$sizeName[1].'</td>';
			}
			$tbl .= '<td>'.$preValue.'</td></tr>';
			$tbl .= '</tbody></table>';
			$printSizeTable .= $tbl;
		}
	}
	$printSizeTable = mb_convert_encoding($printSizeTable, "euc-jp", "utf-8");
	
	// プリント可能範囲の絵型
	$files = $conn->positionFor($data['itemid']);
	$position_type = trim($files[0]["ppdata"]["pos"]);
	$baseName = array(
		"前"=>"front",
		"後"=>"back",
		"横"=>"side",
		"プリントなし"=>"front"
	);
	$baseNameText = array(
		"front"=>"前",
		"back"=>"後",
		"side"=>"横"
	);

	$printAreaImage = "";
	for($i=0; $i<count($files); $i++){
		$printAreaImage .= '<div id="printarea_pic"><img src="'._IMG_PSS.'printarea/'.$position_type.'/base_'.$baseName[$files[$i]['base_name']].'.png" alt=""><div id="printAreaImageText_one">'.mb_convert_encoding($baseNameText[$baseName[$files[$i]['base_name']]],"euc-jp", "utf-8").'</div></div>';
	}
/*
	if(count($files)>0) {
		$printAreaImage .= '<div id="printAreaImageText">';
		for($i=0; $i<count($files); $i++){
			$printAreaImage .= '<div id="printAreaImageText_one">'.mb_convert_encoding($baseNameText[$baseName[$files[$i]['base_name']]],"euc-jp", "utf-8").'</div>';
		}
		$printAreaImage .= '</div>';
	}
*/

	// 見積り計算フォームのプリント位置指定
$posdiv = "";
	for($i=0; $i<count($files); $i++){
		$imgfile = file_get_contents($files[$i]['filename']);
		$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
//		$ink .= '<td id="inktarget'.$i.'" class="'.$files[$i]['base_name'].'">';
//		$ink .= '</td>';
			$ink = '<div id="inktarget'.$i.'" class="'.$files[$i]['base_name'].'">';
			$ink .= '</div>';
			$posdiv .= '<li class="pntposition">';
				$posdiv .= '<div class="psnv">';
//				$posdiv .= '<div class="pnttxt"><p class="posname_'.$i.'"></p></div>';
					$posdiv .= '<div class="pos_'.$i.'">'.$f.'</div>';
					//$posdiv .= '<div><p>デザインの色数</p><p><select class="ink_'.$i.'"><option value="0" selected="selected">選択してください</option><option value="1">1色</option><option value="2">2色</option><option value="3">3色</option><option value="9">4色以上</option></select></p></div>';
					$posdiv .= $ink;
					$posdiv .= '</div>';
			$posdiv .= '</li>';
	}


    $pos = '<tr class="posid_'.$files[0]['posid'].'"><td colspan="3" class="pos_step ps1">(1)プリントする位置を選択してください。</td></tr>';
	$pos .= '<tr>';
	for($i=0; $i<count($files); $i++){
		$imgfile = file_get_contents($files[$i]['filename']);
		$f = preg_replace('/.\/img\//', _IMG_PSS, $imgfile);
		$pos .= '<td><div class="pos_'.$i.'">'.$f.'</div></td>';
		$ink .= '<td id="inktarget'.$i.'" class="'.$files[$i]['base_name'].'">';
		$ink .= '</td>';
	}
	$pos .= '</tr>';
	$pos .= '<tr>';
	$pos .= '<td colspan="3" class="pos_step">(2)選択した位置のプリントに使用する、インクの色数を選択してください。';
	$pos .= '<span class="questions"><a class="info_icon" target="_new" href="/design/fontcolor.html#navi2">使用インク色？</a></span>';
	$pos .= '</td>';
    $pos .= '</tr>';
    
	$pos .= '<tr>'.$ink.'</tr>';
	
	// 見積り計算フォーム
	$isPopup = $itemattr['maker']==10? 'class="popup"': '';
$DOC = <<<EOD
	<div id="cul">
		<div id="price_wrap">
			<h3 class="stepone">カラーとサイズごとの枚数をご指定ください。<a class="info_icon" href="#size">サイズ目安</a></h3>
			<div class="item_colors">
				<p class="thumb_h">(1) Color<span>全{$color_count}色</span><span class="notes_color">{$curthumbcolor}</span></p>
				<ul class="color_thumb clearfix">{$thumbs}</ul>
			</div>
			<table class="curitemid_{$data['itemid']}">
				<caption>(2) サイズと枚数</caption>
				<tbody></tbody>
			</table>
		</div>
		<div id="pos_wrap">
			<div class="content-lv3">
				<h3 class="steptwo">プリント位置と、使用するインクの色数を指定してください。<span class="questions"><a class="info_icon" target="_new" href="/design/fontcolor.html#navi2">使用インク色？</a></span></h3>
				<div>
					<figure>
						<div>
							<p class="pos_step ps1">(1)プリントする位置を選択してください。</p>
						</div>
						<ul>
							{$posdiv}
						</ul>
					</figure>
				</div>
			</div>
		</div>
		<div id="printfee_wrap">
			<h3 class="stepthree">計算結果を確認してください。</h3> 
			<table>
				<tbody>
					<tr>
						<td class="lbl02"><p>計</p></td>
						<td><p id="baseprice"><span>0</span> 円</p></td>
					</tr>
					<tr>
						<td class="lbl02"><p>消費税</p></td>
						<td><p id="salestax"><span>0</span> 円</p></td>
					</tr>
					<tr>
						<td class="lbl01"><p>合　　計</p></td>
						<td><p id="result"><span>0</span> 円</p></td>
					</tr>
					<tr>
						<td class="lbl02"><p>1枚あたり</p></td>
						<td><p id="perone"><span>0</span> 円</p></td>
					</tr>
					<tr>
						<td class="lbl02"><p>合計枚数</p></td>
						<td><p id="totamount"><span>0</span> 枚</p></td>
						<!--<td><input type="button" value="結果をみんなにメール" id="mass-email" /></td>-->
					</tr>
				</tbody>
			</table>
		</div>
		<div id="order_wrap">
			<p id="orderguide"><span>お見積もり金額について</span><br>デザイン、ボディのカラー・サイズ・素材により、表示されているお見積もり金額と別のプリント方法でご提案させていただくこともございますので、お見積もり金額がお打ち合わせ後変わることがございます。</p>
			<div id="orderbtn_wrap">
				<form name="f1" action="/order/" method="post" >
				    <input type="hidden" name="item_id" value="{$res['itemid']}" />
					<input type="hidden" name="update" value="1" />
				    <div id="btnOrder" {$isPopup}>お申込みフォームへ</div>
			    </form>
			</div>
		</div>
	</div>
EOD;
$DOC = mb_convert_encoding($DOC, 'euc-jp', 'utf-8');
$thumbs = mb_convert_encoding($thumbs,'euc-jp','utf-8');
}
?>
