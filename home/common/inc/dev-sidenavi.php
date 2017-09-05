<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/jd/myCalendar.php';
list($year, $month) = explode(' ', date("Y n"));
$myCalendar = new myCalendar($year, $month);
list($year2, $month2) = explode(' ', date('Y n', mktime(0, 0, 0, date('n')+1, 1, date('Y'))));
$myCalendar2 = new myCalendar($year2, $month2);
?>
<div class="snavi">
<?php
	$info = pathinfo($_SERVER['SCRIPT_NAME']);
	$fname = basename($_SERVER['SCRIPT_NAME'], '.'.$info['extension']);
	$root = DIRECTORY_SEPARATOR;
	if($info['dirname']!=DIRECTORY_SEPARATOR.$_SERVER['SERVER_NAME']){
		if($info['dirname']==$root.'design'){
			$html = <<<EOD
	<div class="box">
		<p class="heading">�ǥ�����</p>
		<ul>
			<li><a href="/design/designguide.html">�ǥ�����κ����</a></li>
			<li><a href="/design/printing.html">�ץ�����ˡ�μ���</a></li>
			<li><a href="/design/gallery.html">�ǥ��������</a></li>
			<li><a href="/design/designtemp.html">̵�����Ǻླྀ</a></li>
			<li><a href="/design/template_illust.html">����졦�ƥ�ץ졼��</a></li>
			<li><a href="/design/fontcolor.html">���󥯤ȥե����</a></li>
		</ul>
	</div>
EOD;
			echo $html;
		}else if($fname=='estimate'){
			$html = <<<EOD
	<div class="box">
		<p class="heading">����</p>
		<ul>
			<li><a href="/price/standard.html">������ܰ�</a></li>
			<li><a href="/guide/#discount">�����ʳ����˥塼</a></li>
			<li><a href="/guide/#carriage">����</a></li>
			<li><a href="/guide/#payment">����ʧ��ˡ</a></li>
		</ul>
	</div>
EOD;
			echo $html;
		}else if($info['dirname']==$root.'corporate' || $info['dirname']==$root.'corporate/profile'){
			$html = <<<EOD
	<div class="box">
		<p class="heading">��Ұ���</p>
		<ul>
			<li><a href="/corporate/profile/staff.html">�����åվҲ�</a></li>
			<li><a href="/corporate/profile/manager.html">��Ĺ���󥿥ӥ塼</a></li>
			<li><a href="/corporate/overview.html">��ҳ���</a></li>
			<li><a href="/corporate/commercial_trans.html">���꾦���ˡ</a></li>
			<li><a href="/corporate/commercial_trans.html#privacy">�ץ饤�Х����ݥꥷ��</a></li>
		</ul>
	</div>
EOD;
			echo $html;
		}else if($info['dirname']==$root.'archive'){
			$html = <<<EOD
	<p class="title">�ȥԥå���</p>
	<div class="box" id="toggle">
		<h4 class="toggler">���ꥸ�ʥ�ԥ���ĳ���ˡ</h4>
		<ul class="topics">
			<li><a href="/archive/repo-1.html">���󥹥�����ϥ��ꥸ�ʥ�ץ��Ȥ�</a></li>
			<li><a href="/archive/repo-2.html">ʸ���ס��ΰ�פ�����夲�롪���ꥸ�ʥ�ԥ����</a></li>
			<li><a href="/archive/repo-3.html">���٥�Ȥ�ɬ�ܡ���ʬ�����Ρȿ��ɤ����̤�</a></li>
			<li><a href="/archive/repo-4.html">���ݡ��ĤȤ����Х��ꥸ�ʥ륦����</a></li>
			<li><a href="/archive/repo-5.html">����夬��뺧���˥��ꥸ�ʥ륰�å�</a></li>
			<li><a href="/archive/repo-6.html">�繥����T����ĥ����ǥ��͡���</a></li>
			<li><a href="/archive/repo-7.html">Happy�ˤʤ륪�ꥸ�ʥ�ץ쥼���</a></li>
			<li><a href="/archive/repo-8.html">�����åե�˥ե�����ϡ����ꥸ�ʥ�ץ���</a></li>
			<li><a href="/archive/repo-9.html">�����ब�ҤȤĤˤʤ���ˡ</a></li>
			<li><a href="/archive/repo-10.html">��¥�ġ���˥��ꥸ�ʥ�ץ��Ȥγ��ѽ�</a></li>
		</ul>
		<h4 class="toggler">��Ū�̡������̡���������</h4>
		<ul class="topics">
			<li><a href="/archive/repo-11.html">��ư������ʤ饪�ꥸ�ʥ���</a></li>
			<li><a href="/archive/repo-12.html">���ʷ�ϵ�����ʤ餳��Ǥ��礦</a></li>
			<li><a href="/archive/repo-13.html">����������������������ʿͤ�</a></li>
			<li><a href="/archive/repo-14.html">�Ϻ����֤��פ��л���</a></li>
			<li><a href="/archive/repo-15.html">���ΤǤ�·������ʤ�</a></li>
			<li><a href="/archive/repo-16.html">���ԡ��뤹��ʤ饪�ꥸ�ʥ�ԥ����</a></li>
			<li><a href="/archive/repo-17.html">���ä�������˥��ꥸ�ʥ�</a></li>
		</ul>
		<h4 class="toggler">�ץ��ȤˤޤĤ�뤪�ä�</h4>
		<ul class="topics">
			<li><a href="/archive/repo-18.html">���ꥸ�ʥ�ץ��Ȥ���Τä��񤷤���</a></li>
			<li><a href="/archive/repo-19.html">����ץ��Ȥ��뤫Ǻ��Ǥ�ͤ�</a></li>
			<li><a href="/archive/repo-20.html">�ץ�����ˡ�Ϥ�¸����</a></li>
			<li><a href="/archive/repo-21.html">����⤳���ץ��Ȥ�������</a></li>
			<li><a href="/archive/repo-22.html">̣������T����Ĥˤʤꤿ��</a></li>
			<li><a href="/archive/repo-23.html">�ץ��Ȥμ�����</a></li>
		</ul>
		<h4 class="toggler">���ꥸ�ʥ�T����ĤΥǥ�����</h4>
		<ul class="topics">
			<li><a href="/archive/repo-24.html">How to �ǥ����� -1-</a></li>
			<li><a href="/archive/repo-25.html">How to �ǥ����� -2-</a></li>
			<li><a href="/archive/repo-26.html">���饹�ȡ��̿�������ʸ��</a></li>
			<li><a href="/archive/repo-27.html">�ǥ�����Υ쥤�����ȡ��Х�󥹡���</a></li>
			<li><a href="/archive/repo-28.html">�޴��Υ��᡼������ǥ�����</a></li>
			<li><a href="/archive/repo-29.html">ʷ�ϵ�����ǥ����� -1-</a></li>
			<li><a href="/archive/repo-30.html">ʷ�ϵ�����ǥ����� -2-</a></li>
			<li><a href="/archive/repo-31.html">���ꥸ�ʥ�ǥ�����κ��ʤȤ���</a></li>
		</ul>
		<h4 class="toggler">���ꥸ�ʥ�T����Ĳ��λ���</h4>
		<ul class="topics">
			<li><a href="/archive/repo-32.html">�ڥ���å��ˤĤ��Ƹ��</a></li>
			<li><a href="/archive/repo-33.html">T����Ĥμ�������ˡ</a></li>
			<li><a href="/archive/repo-34.html">��ᥤ���äƳڤ���</a></li>
			<li><a href="/archive/repo-35.html">���椤�Ȥ���˼꤬�Ϥ��ܵ�</a></li>
			<li><a href="/archive/repo-36.html">���֤ζ����������βᤴ����</a></li>
			<li><a href="/archive/repo-37.html">T����Ĥ����</a></li>
			<li><a href="/archive/repo-38.html">�Ҷ��Ȥ��ڤ����ᤴ����ˡ</a></li>
			<li><a href="/archive/repo-39.html">�Ķ���ͤ���������ϥ��饤��</a></li>
			<li><a href="/archive/repo-40.html">˹�Ҥ򰦤��Ƥ�ޤʤ���</a></li>
		</ul>
		<h4 class="toggler">���ꥸ�ʥ�T�������������</h4>
		<ul class="topics">
			<li><a href="/archive/repo-41.html">���å��������ꥸ�ʥ�T����Ĥ��ꤿ�������Ԥ��ʤ��ǥ�����Ȥϡ�</a></li>
			<li><a href="/archive/repo-42.html">���ꥸ�ʥ�T����ĤΥǥ������̥�Ϥ�����</a></li>
			<li><a href="/archive/repo-43.html">ʸ���פ����T����Ĥ����餳��������ꤿ����</a></li>
			<li><a href="/archive/repo-44.html">����ή�ˤ�����ꤿ���ʤ饪�ꥸ�ʥ�T����Ĥ��롪</a></li>
			<li><a href="/archive/repo-45.html">�������Τ��륪�ꥸ�ʥ������¤���륳��</a></li>
			<li><a href="/archive/repo-46.html">�¤��ƾ��ס����䤵�������礤�Υݥ����</a></li>
			<li><a href="/archive/repo-47.html">���٥�������θ����͵��Υ��å��Ⱥ��������</a></li>
			<li><a href="/archive/repo-48.html">���Ƥ��졪������Υޥ�����塪��</a></li>
			<li><a href="/archive/repo-49.html">��ǯ�����ߤ����롩�İ����ݥ����</a></li>
			<li><a href="/archive/repo-50.html">T����Ĥ���Ȥ������줿��ʸ���ϡ�</a></li>
			<li><a href="/archive/repo-51.html">���饹T����ġ��ץ��ȥ��顼�ϲ�����������</a></li>
			<li><a href="/archive/repo-52.html">�������ʤ��ͤǤ⥪�ꥸ�ʥ�T����ĤϺ��롪</a></li>
			<li><a href="/archive/repo-53.html">��������å���ˤߤ���T����Ĥ�</a></li>
		</ul>
		<h4 class="toggler">��������ΤäƤ�����������</h4>
		<ul class="topics">
			<li><a href="/archive/repo-54.html">T����Ĥ����ϡ������ɡ������ɡ�</a></li>
			<li><a href="/archive/repo-55.html">�ޤ���T����Ĥ��ꤿ�����˵���Ĥ���٤����Ȥϡ�</a></li>
			<li><a href="/archive/repo-56.html">�¿����ư�������ԥ���ĺ����ȼԤ˶��̤��Ƥ������Ȥϡ�</a></li>
			<li><a href="/archive/repo-57.html">�ΰ�פ��ķ��Ǥ�����٤˸���Ū�ʥ����ƥ�Ȥϡ�</a></li>
			<li><a href="/archive/repo-58.html">T����Ĥ����ϡ����ɡ��ɥ饤�ɡ�</a></li>
			<li><a href="/archive/repo-59.html">���٥�ȼ�ż�ɬ���������֤ΥΥ٥�ƥ��äơ�</a></li>
			<li><a href="/archive/repo-60.html">T����Ĥ���ݤˤ��������Ѥ����Ȥϡ�</a></li>
			<li><a href="/archive/repo-61.html">���ꥸ�ʥ�T����Ĥ��ä����䡪����T����Ĥ���ˤϡ�</a></li>
			<li><a href="/archive/repo-62.html">�Х������Τ˹礦���Ϥ�</a></li>
			<li><a href="/archive/repo-63.html">���򴥤����Ƥ����T����ĤȤ�</a></li>
			<li><a href="/archive/repo-64.html">���ꥸ�ʥ�ݥ���� �͵��ο���</a></li>
			<li><a href="/archive/repo-65.html">���ݡ��Ĥ˸����Ƥ���T����Ĥϡ�</a></li>
			<li><a href="/archive/repo-66.html">���ݡ��Ĥ˸����Ƥ���ݥ���Ĥϡ�</a></li>
			<li><a href="/archive/repo-67.html">���󥹥������ɬ�ܥ����ƥ�Ȥϡ�</a></li>
			<li><a href="/archive/repo-68.html">�襬�ΤȤ��夿��T����Ĥη�����</a></li>
			<li><a href="/archive/repo-69.html">ʸ����T����ġ��������͵���</a></li>
			<li><a href="/archive/repo-70.html">�η��˹�ä�T����Ĥ��������Ȥϡ�</a></li>
			<li><a href="/archive/repo-71.html">���饹T����ġ������餰�餤����졩</a></li>
			<li><a href="/archive/repo-72.html">T����ĤΥǥ�����Ϥɤ�ʤΤ�¿����</a></li>
			<li><a href="/archive/repo-73.html">��������T����Ĥ�Ŭ�����Ǻ�Ȥϡ�</a></li>
			<li><a href="/archive/repo-74.html">���ܤȥ���ꥫ�Υ��������äƤɤΤ��餤�㤦�Ρ�</a></li>
			<li><a href="/archive/repo-75.html">���פ�T����Ĥ��Ǻ�Ϥɤ�ʤ�Ρ�</a></li>
		</ul>
		<h4 class="toggler">���ꥸ�ʥ�T����ĳ�����</h4>
		<ul class="topics">
			<li><a href="/archive/repo-76.html">�������Ǻ�ä���ҤΥ��T�����������ס���</a></li>
			<li><a href="/archive/repo-77.html">ǰ��Τ�Ź��T����ġ����ǹ����</a></li>
			<li><a href="/archive/repo-78.html">���٥������������T����Ĥ�����Ĥʤ�������</a></li>
			<li><a href="/archive/repo-79.html">�եåȥ���������T����Ĥ�Ĥ���ޤ�������</a></li>
			<li><a href="/archive/repo-80.html">���ꥸ�ʥ륵�å�������Ĥ��äƥ�������ķ�򶯲���</a></li>
			<li><a href="/archive/repo-81.html">�Ŀ�̾�����ֹ椬���ä��椬�������T�����</a></li>
			<li><a href="/archive/repo-82.html">���˥󥰽��ҡ���������T�����</a></li>
			<li><a href="/archive/repo-83.html">������Υƥ󥷥���夲��Ƕ������ƥࡪ��</a></li>
			<li><a href="/archive/repo-84.html">�ߤΥ��ݡ��Ĵ�������ο����</a></li>
			<li><a href="/archive/repo-85.html">������ɽ�α��硢������T����Ĥ���롩</a></li>
		</ul>
	</div>
EOD;
			echo $html;
		}
		
		$html = <<<EOD
		<p class="title">�����ƥ����</p>
		<div class="box list">
			<ul>
				<li><a href="/items/t-shirts/" onclick="ga('send','event','tshirts','click','side');"><img alt="T�����" src="/common/img/thumbs/item/t-shirts.png" />�ԥ����</a></li>
				<li><a href="/items/sweat/"><img alt="�ѡ��������������å�" src="/common/img/thumbs/item/sweat.png" />�ѡ��������������å�</a></li>
				<li><a href="/items/outer/"><img alt="��������" src="/common/img/thumbs/item/outer.png" />�֥륾�󡦥����ѡ�</a></li>
				<li><a href="/items/polo-shirts/"><img alt="�ݥ����" src="/common/img/thumbs/item/polo.png" />�ݥ����</a></li>
				<li><a href="/items/sportswear/"><img alt="���ݡ��ĥ�����" src="/common/img/thumbs/item/sports.png" />���ݡ��ĥ�����</a></li>
				<li><a href="/items/long-shirts/"><img alt="���T�����" src="/common/img/thumbs/item/long-t.png" />��󥰣ԥ����</a></li>
				<li><a href="/items/towel/"><img alt="������" src="/common/img/thumbs/item/towel.png" />������</a></li>
				<li><a href="/items/tote-bag/"><img alt="�Хå�" src="/common/img/thumbs/item/tote-bag.png" />�Хå����ȡ��ȥХå�</a></li>
				<li><a href="/items/ladys/"><img alt="��ǥ�����" src="/common/img/thumbs/item/ladys.png" />��ǥ�����������</a></li>
				<li><a href="/items/apron/"><img alt="���ץ��" src="/common/img/thumbs/item/apron.png" />���ץ��</a></li>
				<li><a href="/items/baby/"><img alt="�٥ӡ�" src="/common/img/thumbs/item/baby.png" />�٥ӡ�������</a></li>
				<li><a href="/items/overall/"><img alt="�Ĥʤ�" src="/common/img/thumbs/item/overall.png" />�Ĥʤ�</a></li>
				<li><a href="/items/cap/"><img alt="����åס��Х����" src="/common/img/thumbs/item/cap.png" />����åס��Х����</a></li>
				<li><a href="/items/goods/"><img alt="�ץ쥼��ȡ����å�" src="/common/img/thumbs/item/goods.png" />��ǰ�ʡ��ץ쥼���</a></li>
			</ul>
		</div>
		
		<p class="title">�֥��ɰ���</p>
		<div class="box list">
			<ul>
				<li><a href="/items/printstar/"><img alt="Printstar" src="/common/img/thumbs/brand/printstar.png" />Printstar</a></li>
				<li><a href="/items/unitedathle/"><img alt="UnitedAthle" src="/common/img/thumbs/brand/unitedathle.png" />UnitedAthle</a></li>
				<li><a href="/items/crossandsttch/"><img alt="CROSS&STTCH" src="/common/img/thumbs/brand/crossandsttch.png" />CROSS&STTCH</a></li>
				<li><a href="/items/truss/"><img alt="TRUSS" src="/common/img/thumbs/brand/truss.png" />TRUSS</a></li>
				<li><a href="/items/wundou/"><img alt="wundou" src="/common/img/thumbs/brand/wundou.png" />wundou</a></li>
				<li><a href="/items/glimmer/"><img alt="glimmer" src="/common/img/thumbs/brand/glimmer.png" />glimmer</a></li>
				<li><a href="/items/rucca/"><img alt="rucca" src="/common/img/thumbs/brand/rucca.png" />rucca</a></li>
				<li><a href="/items/bees-beam/"><img alt="BEES BEAM" src="/common/img/thumbs/brand/beesbeam.png" />BEES BEAM</a></li>
				<li><a href="/items/seventeen-verglebee/"><img alt="Seventeen VergleBee" src="/common/img/thumbs/brand/seventeen-verglebee.png" /><p>Seventeen<br>VergleBee</p></a></li>
				<li><a href="/items/touchandgo/"><img alt="Touch&GO" src="/common/img/thumbs/brand/touchandgo.png" />Touch&GO</a></li>
				<li><a href="/items/cross/"><img alt="CROSS" src="/common/img/thumbs/brand/cross.png" />CROSS</a></li>
				<li><a href="/items/daluc/"><img alt="DALUC" src="/common/img/thumbs/brand/daluc.png" />DALUC</a></li>
				<li><a href="/items/sowa/"><img alt="SOWA" src="/common/img/thumbs/brand/sowa.png" />SOWA</a></li>
				<li><a href="/items/aimy/"><img alt="AIMY" src="/common/img/thumbs/brand/aimy.png" />AIMY</a></li>
				<li><a href="/items/gildan/"><img alt="GILDAN" src="/common/img/thumbs/brand/gildan.png" />GILDAN</a></li>
				<li><a href="/items/anvil/"><img alt="ANVIL" src="/common/img/thumbs/brand/anvil.png" />ANVIL</a></li>
				<li><a href="/items/comfortcolors/"><img alt="COMFORT COLORS" src="/common/img/thumbs/brand/comfortcolors.png" />ComfortColors</a></li>
			</ul>
		</div>
		
		<p class="title">������</p>
		<div class="box list">
			<ul>
				<li><a href="/scene/campus.html"><img alt="ʸ���ס��ΰ��" src="/common/img/thumbs/scene/festival.png" width="30" />ʸ���ס��ΰ��</a></li>
				<li><a href="/scene/sports.html"><img alt="���ݡ���" src="/common/img/thumbs/scene/sport.png" width="30" />���ݡ���</a></li>
				<li><a href="/scene/dance.html"><img alt="����" src="/common/img/thumbs/scene/dance.png" width="30" />����</a></li>
				<li><a href="/scene/team.html"><img alt="�����ࡦ���" src="/common/img/thumbs/scene/team.png" width="30" />�����ࡦ���</a></li>
				<li><a href="/scene/event.html"><img alt="���٥��" src="/common/img/thumbs/scene/events.png" width="30" />���٥��</a></li>
				<li><a href="/scene/uniform.html"><img alt="�����åե�˥ե�����" src="/common/img/thumbs/scene/uniform.png" width="30" />�����åե�˥ե�����</a></li>
				<li><a href="/scene/promotion.html"><img alt="��¥�ġ���" src="/common/img/thumbs/scene/sp.png" width="30" />��¥�ġ���</a></li>
				<li><a href="/scene/gift.html"><img alt="�ץ쥼���" src="/common/img/thumbs/scene/gift.png" width="30" />�ץ쥼���</a></li>
				<li><a href="/scene/wedding.html"><img alt="�뺧��" src="/common/img/thumbs/scene/marriage.png" width="30" />�뺧��</a></li>
			</ul>
		</div>

EOD;

	}else{
		$html = "";
	}

	echo $html;
?>
	
	<div class="workday_calendar_wrap">
		<table class="workday_calendar">
			<caption><?php echo "{$year} ǯ{$month} ��Ķ���"; ?></caption>
			<thead>
		    	<tr>
			        <td class="sun">��</td>
			        <td>��</td>
			        <td>��</td>
			        <td>��</td>
			        <td>��</td>
			        <td>��</td>
			        <td class="sat">��</td>
			    </tr>
		    </thead>
		    <tbody><?php echo $myCalendar->makeCalendar(); ?></tbody>
		</table>
		<table class="workday_calendar">
			<caption><?php echo "{$year2} ǯ{$month2} ��Ķ���"; ?></caption>
			<thead>
		    	<tr>
			        <td class="sun">��</td>
			        <td>��</td>
			        <td>��</td>
			        <td>��</td>
			        <td>��</td>
			        <td>��</td>
			        <td class="sat">��</td>
			    </tr>
		    </thead>
		    <tbody><?php echo $myCalendar2->makeCalendar(); ?></tbody>
		</table>
		<dl class="list">
			<dt>�ĶȻ��֡�</dt><dd>10:00&#65374;18:00</dd>
			<dt>�������</dt><dd>������</dd>
			<p>�����Į�ܳʥץ��ȱĶ��桪</p>
		</dl>
	</div>
	
	<div class="banner1">
		<a href="/contact/line/" class="sns01"></a><p>ͧã1000������!!<br>�����˴ؤ��뵿������!!</p>
		<a href="https://ja-jp.facebook.com/takahamalifeart" class="sns02"></a><p>���ꥸ�ʥ�T����Ĥ˴ؤ���˥塼����<br>�������ʤ򥢥åפ��Ƥ����ޤ�!!</p>
		<a href="https://twitter.com/takahamalifeart" class="sns03"></a><p>�ǥ������ᥤ��ˤ����ͤ�����<br>�줤�Ƥ����ޤ�!!</p>
		<a href="https://www.instagram.com/takahamalifeart/" class="sns04"></a><p>�ץ��Ȥ����ʤ䤪���ͤΥǥ������<br>���åפ��Ƥ����ޤ�!! </p>
	</div>
<!--
	<div class="box">
		<p class="heading">�̿���ƥ����ӥ�</p>
		<ul>
			<li><a href="/design/designpost/">�����ͼ̿���</a></li>
			<li><a href="/design/designpost/userpolicy.php">�����ѵ���</a></li>
			<li><a href="/design/designpost/register.php">�桼������Ͽ</a></li>
		</ul>
	</div>
-->	
	
	<div class="box">
		<p class="heading">Ǽ��</p>
		<ul>
			<li><a href="/delivery/">���������ǧ����</a></li>
			<li><a href="/guide/#express">2���������ž夲</a></li>
		</ul>
	</div>
	
<?php
	if($fname!='estimate'){
		$html = <<<EOD
	<div class="box">
		<p class="heading">����</p>
		<ul>
			<li><a href="/price/standard.html">������ܰ�</a></li>
			<li><a href="/guide/#discount">�����ʳ����˥塼</a></li>
			<li><a href="/guide/#carriage">����</a></li>
			<li><a href="/guide/#payment">����ʧ��ˡ</a></li>
		</ul>
	</div>
EOD;
		echo $html;
	}

	if($info['dirname']!=$root.'design'){
		$html = <<<EOD
	<div class="box">
		<p class="heading">�ץ��Ȥ���ǥ�����</p>
		<ul>
			<li><a href="/design/designguide.html">�ǥ�����κ����</a></li>
			<li><a href="/design/printing.html">�ץ�����ˡ�μ���</a></li>
			<li><a href="/design/gallery.html">�ǥ��������</a></li>
			<li><a href="/design/designtemp.html">̵�����Ǻླྀ</a></li>
			<li><a href="/design/template_illust.html">����졦�ƥ�ץ졼��</a></li>
			<li><a href="/design/fontcolor.html">���󥯤ȥե����</a></li>
		</ul>
	</div>
EOD;
		echo $html;
	}
?>
	
	<div class="banner1">
		<a href="/app/WP/?cat=4" class="staffblog"></a>
		<p>�����ϥޤ�����������åդ�����</p>
	</div>

	<div class="banner1">
		<a href="/blog/thanks_blog/" class="okyakusama"></a>
		<p>�����ͤξд�Ⱥ��ʽ�</p>
	</div>
	
	<div class="banner1">
		<a href="/glossary/a/" class="yougo2"></a>
		<p>T����ĺ�����Ľ���缭ŵ</p>
	</div>
	
	<div class="banner1">
		<a href="http://www.jota.or.jp/contest_2017.html" target="_blank" rel="nofollow"><img alt="����T�����ץ�" src="/img/banner/orit2.png" width="200" /></a>
		<p>Ǯ���ǳ�������祤�٥�ȡ�</p>
	</div>
	
	<div class="banner1">
		<a href="http://jota.or.jp/" target="_blank" rel="nofollow"><img alt="���ꥸ�ʥ�T����Ķ���" src="/img/banner/original-tee-japan.png" width="200" /></a>
		<p>�͡��ʳ�ư��ԤäƤ��ޤ���</p>
	</div>
	
	<div class="banner1">
		<a href="http://jota.or.jp/ippan/tsukuru/school.html" target="_blank" rel="nofollow" class="jota-oyako"></a>
		<p>�ƻҤǲ�²T���ޤ��󤫡�</p>
	</div>
	
	<div class="box banner1">
		<a href="/m3/"><img alt="���ޥ���" src="/img/banner/smartphone.png" width="198" /></a>
	</div>
	
<?php
	if($info['dirname']!=$root.'archive'){
		$html = <<<EOD
	<p class="title">�ȥԥå���</p>
	<div class="box" id="toggle">
		<h4 class="toggler">���ꥸ�ʥ�ԥ���ĳ���ˡ</h4>
		<ul class="topics">
			<li><a href="/archive/repo-1.html">���󥹥�����ϥ��ꥸ�ʥ�ץ��Ȥ�</a></li>
			<li><a href="/archive/repo-2.html">ʸ���ס��ΰ�פ�����夲�롪���ꥸ�ʥ�ԥ����</a></li>
			<li><a href="/archive/repo-3.html">���٥�Ȥ�ɬ�ܡ���ʬ�����Ρȿ��ɤ����̤�</a></li>
			<li><a href="/archive/repo-4.html">���ݡ��ĤȤ����Х��ꥸ�ʥ륦����</a></li>
			<li><a href="/archive/repo-5.html">����夬��뺧���˥��ꥸ�ʥ륰�å�</a></li>
			<li><a href="/archive/repo-6.html">�繥����T����ĥ����ǥ��͡���</a></li>
			<li><a href="/archive/repo-7.html">Happy�ˤʤ륪�ꥸ�ʥ�ץ쥼���</a></li>
			<li><a href="/archive/repo-8.html">�����åե�˥ե�����ϡ����ꥸ�ʥ�ץ���</a></li>
			<li><a href="/archive/repo-9.html">�����ब�ҤȤĤˤʤ���ˡ</a></li>
			<li><a href="/archive/repo-10.html">��¥�ġ���˥��ꥸ�ʥ�ץ��Ȥγ��ѽ�</a></li>
		</ul>
		<h4 class="toggler">��Ū�̡������̡���������</h4>
		<ul class="topics">
			<li><a href="/archive/repo-11.html">��ư������ʤ饪�ꥸ�ʥ���</a></li>
			<li><a href="/archive/repo-12.html">���ʷ�ϵ�����ʤ餳��Ǥ��礦</a></li>
			<li><a href="/archive/repo-13.html">����������������������ʿͤ�</a></li>
			<li><a href="/archive/repo-14.html">�Ϻ����֤��פ��л���</a></li>
			<li><a href="/archive/repo-15.html">���ΤǤ�·������ʤ�</a></li>
			<li><a href="/archive/repo-16.html">���ԡ��뤹��ʤ饪�ꥸ�ʥ�ԥ����</a></li>
			<li><a href="/archive/repo-17.html">���ä�������˥��ꥸ�ʥ�</a></li>
		</ul>
		<h4 class="toggler">�ץ��ȤˤޤĤ�뤪�ä�</h4>
		<ul class="topics">
			<li><a href="/archive/repo-18.html">���ꥸ�ʥ�ץ��Ȥ���Τä��񤷤���</a></li>
			<li><a href="/archive/repo-19.html">����ץ��Ȥ��뤫Ǻ��Ǥ�ͤ�</a></li>
			<li><a href="/archive/repo-20.html">�ץ�����ˡ�Ϥ�¸����</a></li>
			<li><a href="/archive/repo-21.html">����⤳���ץ��Ȥ�������</a></li>
			<li><a href="/archive/repo-22.html">̣������T����Ĥˤʤꤿ��</a></li>
			<li><a href="/archive/repo-23.html">�ץ��Ȥμ�����</a></li>
		</ul>
		<h4 class="toggler">���ꥸ�ʥ�T����ĤΥǥ�����</h4>
		<ul class="topics">
			<li><a href="/archive/repo-24.html">How to �ǥ����� -1-</a></li>
			<li><a href="/archive/repo-25.html">How to �ǥ����� -2-</a></li>
			<li><a href="/archive/repo-26.html">���饹�ȡ��̿�������ʸ��</a></li>
			<li><a href="/archive/repo-27.html">�ǥ�����Υ쥤�����ȡ��Х�󥹡���</a></li>
			<li><a href="/archive/repo-28.html">�޴��Υ��᡼������ǥ�����</a></li>
			<li><a href="/archive/repo-29.html">ʷ�ϵ�����ǥ����� -1-</a></li>
			<li><a href="/archive/repo-30.html">ʷ�ϵ�����ǥ����� -2-</a></li>
			<li><a href="/archive/repo-31.html">���ꥸ�ʥ�ǥ�����κ��ʤȤ���</a></li>
		</ul>
		<h4 class="toggler">���ꥸ�ʥ�T����Ĳ��λ���</h4>
		<ul class="topics">
			<li><a href="/archive/repo-32.html">�ڥ���å��ˤĤ��Ƹ��</a></li>
			<li><a href="/archive/repo-33.html">T����Ĥμ�������ˡ</a></li>
			<li><a href="/archive/repo-34.html">��ᥤ���äƳڤ���</a></li>
			<li><a href="/archive/repo-35.html">���椤�Ȥ���˼꤬�Ϥ��ܵ�</a></li>
			<li><a href="/archive/repo-36.html">���֤ζ����������βᤴ����</a></li>
			<li><a href="/archive/repo-37.html">T����Ĥ����</a></li>
			<li><a href="/archive/repo-38.html">�Ҷ��Ȥ��ڤ����ᤴ����ˡ</a></li>
			<li><a href="/archive/repo-39.html">�Ķ���ͤ���������ϥ��饤��</a></li>
			<li><a href="/archive/repo-40.html">˹�Ҥ򰦤��Ƥ�ޤʤ���</a></li>
		</ul>
		<h4 class="toggler">���ꥸ�ʥ�T�������������</h4>
		<ul class="topics">
			<li><a href="/archive/repo-41.html">���å��������ꥸ�ʥ�T����Ĥ��ꤿ�������Ԥ��ʤ��ǥ�����Ȥϡ�</a></li>
			<li><a href="/archive/repo-42.html">���ꥸ�ʥ�T����ĤΥǥ������̥�Ϥ�����</a></li>
			<li><a href="/archive/repo-43.html">ʸ���פ����T����Ĥ����餳��������ꤿ����</a></li>
			<li><a href="/archive/repo-44.html">����ή�ˤ�����ꤿ���ʤ饪�ꥸ�ʥ�T����Ĥ��롪</a></li>
			<li><a href="/archive/repo-45.html">�������Τ��륪�ꥸ�ʥ������¤���륳��</a></li>
			<li><a href="/archive/repo-46.html">�¤��ƾ��ס����䤵�������礤�Υݥ����</a></li>
			<li><a href="/archive/repo-47.html">���٥�������θ����͵��Υ��å��Ⱥ��������</a></li>
			<li><a href="/archive/repo-48.html">���Ƥ��졪������Υޥ�����塪��</a></li>
			<li><a href="/archive/repo-49.html">��ǯ�����ߤ����롩�İ����ݥ����</a></li>
			<li><a href="/archive/repo-50.html">T����Ĥ���Ȥ������줿��ʸ���ϡ�</a></li>
			<li><a href="/archive/repo-51.html">���饹T����ġ��ץ��ȥ��顼�ϲ�����������</a></li>
			<li><a href="/archive/repo-52.html">�������ʤ��ͤǤ⥪�ꥸ�ʥ�T����ĤϺ��롪</a></li>
			<li><a href="/archive/repo-53.html">��������å���ˤߤ���T����Ĥ�</a></li>
		</ul>
		<h4 class="toggler">��������ΤäƤ�����������</h4>
		<ul class="topics">
			<li><a href="/archive/repo-54.html">T����Ĥ����ϡ������ɡ������ɡ�</a></li>
			<li><a href="/archive/repo-55.html">�ޤ���T����Ĥ��ꤿ�����˵���Ĥ���٤����Ȥϡ�</a></li>
			<li><a href="/archive/repo-56.html">�¿����ư�������ԥ���ĺ����ȼԤ˶��̤��Ƥ������Ȥϡ�</a></li>
			<li><a href="/archive/repo-57.html">�ΰ�פ��ķ��Ǥ�����٤˸���Ū�ʥ����ƥ�Ȥϡ�</a></li>
			<li><a href="/archive/repo-58.html">T����Ĥ����ϡ����ɡ��ɥ饤�ɡ�</a></li>
			<li><a href="/archive/repo-59.html">���٥�ȼ�ż�ɬ���������֤ΥΥ٥�ƥ��äơ�</a></li>
			<li><a href="/archive/repo-60.html">T����Ĥ���ݤˤ��������Ѥ����Ȥϡ�</a></li>
			<li><a href="/archive/repo-61.html">���ꥸ�ʥ�T����Ĥ��ä����䡪����T����Ĥ���ˤϡ�</a></li>
			<li><a href="/archive/repo-62.html">�Х������Τ˹礦���Ϥ�</a></li>
			<li><a href="/archive/repo-63.html">���򴥤����Ƥ����T����ĤȤ�</a></li>
			<li><a href="/archive/repo-64.html">���ꥸ�ʥ�ݥ���� �͵��ο���</a></li>
			<li><a href="/archive/repo-65.html">���ݡ��Ĥ˸����Ƥ���T����Ĥϡ�</a></li>
			<li><a href="/archive/repo-66.html">���ݡ��Ĥ˸����Ƥ���ݥ���Ĥϡ�</a></li>
			<li><a href="/archive/repo-67.html">���󥹥������ɬ�ܥ����ƥ�Ȥϡ�</a></li>
			<li><a href="/archive/repo-68.html">�襬�ΤȤ��夿��T����Ĥη�����</a></li>
			<li><a href="/archive/repo-69.html">ʸ����T����ġ��������͵���</a></li>
			<li><a href="/archive/repo-70.html">�η��˹�ä�T����Ĥ��������Ȥϡ�</a></li>
			<li><a href="/archive/repo-71.html">���饹T����ġ������餰�餤����졩</a></li>
			<li><a href="/archive/repo-72.html">T����ĤΥǥ�����Ϥɤ�ʤΤ�¿����</a></li>
			<li><a href="/archive/repo-73.html">��������T����Ĥ�Ŭ�����Ǻ�Ȥϡ�</a></li>
			<li><a href="/archive/repo-74.html">���ܤȥ���ꥫ�Υ��������äƤɤΤ��餤�㤦�Ρ�</a></li>
			<li><a href="/archive/repo-75.html">���פ�T����Ĥ��Ǻ�Ϥɤ�ʤ�Ρ�</a></li>
		</ul>
		<h4 class="toggler">���ꥸ�ʥ�T����ĳ�����</h4>
		<ul class="topics">
			<li><a href="/archive/repo-76.html">�������Ǻ�ä���ҤΥ��T�����������ס���</a></li>
			<li><a href="/archive/repo-77.html">ǰ��Τ�Ź��T����ġ����ǹ����</a></li>
			<li><a href="/archive/repo-78.html">���٥������������T����Ĥ�����Ĥʤ�������</a></li>
			<li><a href="/archive/repo-79.html">�եåȥ���������T����Ĥ�Ĥ���ޤ�������</a></li>
			<li><a href="/archive/repo-80.html">���ꥸ�ʥ륵�å�������Ĥ��äƥ�������ķ�򶯲���</a></li>
			<li><a href="/archive/repo-81.html">�Ŀ�̾�����ֹ椬���ä��椬�������T�����</a></li>
			<li><a href="/archive/repo-82.html">���˥󥰽��ҡ���������T�����</a></li>
			<li><a href="/archive/repo-83.html">������Υƥ󥷥���夲��Ƕ������ƥࡪ��</a></li>
			<li><a href="/archive/repo-84.html">�ߤΥ��ݡ��Ĵ�������ο����</a></li>
			<li><a href="/archive/repo-85.html">������ɽ�α��硢������T����Ĥ���롩</a></li>
		</ul>
	</div>
EOD;
		echo $html;
	}
?>
	<div class="box">
		<p class="heading">����ʸ�����䤤��碌</p>
		<ul>
			<li><a href="/order/"><img alt="����������" src="/common/img/cart_black.png" width="23" />�������ߥե�����</a></li>
			<li><a href="/contact/faxorderform.pdf" target="_blank"><img alt="������FAX�ѻ�" src="/common/img/pdf_icon.png" width="23" />�ƣ�������ʸ�ѻ�</a></li>
			<li><a href="/guide/faq.html"><img alt="���ꥸ�ʥ�T�����Q&A" src="/common/img/help_icon.png" width="23" />�褯�������</a></li>
			<li><a href="/contact/"><img alt="���䤤��碌" src="/common/img/mail_icon_s.png" width="23" />���䤤��碌</a></li>
			<li><a href="/contact/request.html">̵������ץ��������</a></li>
			<li><a href="/sitemap/">�����ȥޥå�</a></li>
		</ul>
	</div>
</div>
