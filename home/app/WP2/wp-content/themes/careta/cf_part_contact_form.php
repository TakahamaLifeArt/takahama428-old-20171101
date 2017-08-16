<?php
//if(!session_id()){session_start();}
$mytoken = md5(date('Y-m-d H:i:s'));
$_SESSION['contactform']['mytoken'] = $mytoken;
?>
<script src="http://www.takahama428.com/app/WP2/wp-content/themes/careta/js/jquery.validate.min.js"></script>
<script src="http://www.takahama428.com/app/WP2/wp-content/themes/careta/js/detailContactForm.js"></script>
<div class="boxDetailContactForm">
<h2 id="Line01">お問い合わせフォーム</h2>
    <form name="detailContactForm" id="detailContactForm" action="#" method="post" style="width:91%;">
        <input type="hidden" name="cf_mytoken" value="<?php echo $mytoken;?>">
        テンプレート名：[<?php echo $post->ID;?>]<?php echo get_the_title();?><input type="hidden" name="cf_id_title" id="cf_id_title" value="[<?php echo $post->ID;?>]<?php echo get_the_title();?>"><br>
        名前<span class="red">※</span>：<input type="text" name="cf_name" id="cf_name" value="" placeholder="例)高濱 太郎"><br>
        メールアドレス<span class="red">※</span>：<input type="text" name="cf_email" id="cf_email" value="" placeholder="例 ) xxx@mail.co.jp"><br>
        電話番号<span class="red">※</span>：<input type="number" name="cf_tel" id="cf_tel" value="" placeholder="例 ) 08012345678" style="padding:8px;"><br>
        カテゴリ：
        <select name="cf_category" id="cf_category">
            <option value="0">選択してください</option>
            <option value="Tシャツ">Tシャツ</option>
            <option value="ポロシャツ">ポロシャツ</option>
            <option value="スウェット">スウェット</option>
            <option value="ポロシャツ">ポロシャツ</option>
            <option value="ブルゾン">ブルゾン</option>
            <option value="タオル">タオル</option>
            <option value="その他">その他</option>
        </select><br>
        都道府県：
        <select name="cf_destination" id="cf_destination">
							<option value="0">都道府県をお選びください。</option>  
							<optgroup label="北海道・東北">  
								<option value="北海道">北海道</option>  
								<option value="青森県">青森県</option>  
								<option value="秋田県">秋田県</option>  
								<option value="岩手県">岩手県</option>  
								<option value="山形県">山形県</option>  
								<option value="宮城県">宮城県</option>  
								<option value="福島県">福島県</option>  
							</optgroup>  
							<optgroup label="甲信越・北陸">  
								<option value="山梨県">山梨県</option>  
								<option value="長野県">長野県</option>  
								<option value="新潟県">新潟県</option>  
								<option value="富山県">富山県</option>  
								<option value="石川県">石川県</option>  
								<option value="福井県">福井県</option>  
							</optgroup>  
							<optgroup label="関東">  
								<option value="茨城県">茨城県</option>  
								<option value="栃木県">栃木県</option>  
								<option value="群馬県">群馬県</option>  
								<option value="埼玉県">埼玉県</option>  
								<option value="千葉県">千葉県</option>  
								<option value="東京都">東京都</option>  
								<option value="神奈川県">神奈川県</option>  
							</optgroup>  
							<optgroup label="東海">  
								<option value="愛知県">愛知県</option>  
								<option value="静岡県">静岡県</option>  
								<option value="岐阜県">岐阜県</option>  
								<option value="三重県">三重県</option>  
							</optgroup>  
							<optgroup label="関西">  
								<option value="大阪府">大阪府</option>  
								<option value="兵庫県">兵庫県</option>  
								<option value="京都府">京都府</option>  
								<option value="滋賀県">滋賀県</option>  
								<option value="奈良県">奈良県</option>  
								<option value="和歌山県">和歌山県</option>  
							</optgroup>  
							<optgroup label="中国">  
								<option value="岡山県">岡山県</option>  
								<option value="広島県">広島県</option>  
								<option value="鳥取県">鳥取県</option>  
								<option value="島根県">島根県</option>  
								<option value="山口県">山口県</option>  
							</optgroup>  
							<optgroup label="四国">  
								<option value="徳島県">徳島県</option>  
								<option value="香川県">香川県</option>  
								<option value="愛媛県">愛媛県</option>  
								<option value="高知県">高知県</option>  
							</optgroup>  
							<optgroup label="九州・沖縄">  
								<option value="福岡県">福岡県</option>  
								<option value="佐賀県">佐賀県</option>  
								<option value="長崎県">長崎県</option>  
								<option value="熊本県">熊本県</option>  
								<option value="大分県">大分県</option>  
								<option value="宮崎県">宮崎県</option>  
								<option value="鹿児島県">鹿児島県</option>  
								<option value="沖縄県">沖縄県</option>  
							</optgroup>  
						</select><br>
        希望納期：<input type="date" name="cf_delivery_date" id="cf_delivery_date" value=""><br>
        メッセージ：<textarea name="cf_message" id="cf_message" placeholder="ご要望などの詳細をご記入
ください" cols="40" rows="7"></textarea><br>
        <button id="btnSubmit">送信</button>
    </form>

</div>
<style>
    .cfInvalid{
        color:#f00;
    }
.boxDetailContactForm{
    width: 90%;
    max-width: 1140px;
    margin: 0 auto;
    text-indent:20px;
    text-align:center;
}

.boxDetailContactForm #Line01{
	background-color:#e1630d;
	color:#ffffff;
}

#detailContactForm{
    width: 100%;
    max-width: 1140px;
    margin: 0 auto;
    padding-left:10px;
    padding-top:30px;
    background-color: #fffdfa;
}

#detailContactForm input{
    margin-left:100px;
    margin:20px;

}

input#cf_name{
    margin-left:72px;
}

#btnSubmit{
    margin-bottom:50px;
}

.red{
	color:#ff0000;
}

input[type="text"], textarea, select, input[type="submit"], input[type="button"], .searchform input[type="text"], .searchform input[type="button"], .searchform input[type="submit"] {
    color: #494949;
    background-color: #fffdfa !important;
    border: 1px solid #ededed;
}

#btnSubmit {
    background-image: url(http://www.takahama428.com/order/express/img/next_btn.png);
    background-position: top;
    background-repeat: no-repeat;
    width: 192px;
    height: 43px;
    display: block;
    font-size: 20px;
    line-height: 40px;
    color: #fff;
    text-align: center;
    margin: 20px auto 10px;
}

#btnSubmit:hover{
	background-position: 0px -43px;
	 cursor: pointer;
}


@media only screen and (max-width: 1000px) 
{
.boxDetailContactForm {
    width: 98%;
    margin: 0 auto;
    text-align: center;
    text-indent:0px;
}

#detailContactForm {
    width: 100%;
    max-width: 1140px;
    margin: 0 auto;
    padding-left: 10px;
    padding-top: 10px;
    background-color: #fffdfa;
    font-size: 0.7rem;
}


}
</style>
