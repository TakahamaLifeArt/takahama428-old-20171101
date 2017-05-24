<?php
/*
*	HTTP Request
*	charset utf-8
*
*/
class HTTP {

	private $url;
	
	public function __construct($args){
		$this->url = $args;
	}
	
	
	/**
	 * TEST
	 */
	private function requestHTTP($params = array()) {
		$context = stream_context_create(array(
			"http" => array(
				'method'  => 'POST',
				'header'  => implode("\r\n", array(
					'Content-Type: application/x-www-form-urlencoded',
				)),
				'content' => http_build_query($params),
			)
		));
		$res = file_get_contents($this->url, false, $context);
		return $res;
	}
	
    public function request($method, $params = array()){
		
		//===== TEST =========
//		$res = $this->requestHTTP($params);
//		return $res;
		//====================
		
		
    	$url = $this->url;
	    $data = http_build_query($params);
	    if($method == 'GET') {
	        $url = ($data != '')?$url.'?'.$data:$url;
	    }
	 
	    $ch = curl_init($url);
		
	    if($method == 'POST'){
	        curl_setopt($ch,CURLOPT_POST,1);
	        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	    }
	 
	    curl_setopt($ch, CURLOPT_HEADER,false); //header情報も一緒に欲しい場合はtrue
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//	    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
	    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 60000);
	    $res = curl_exec($ch);
	    //ステータスをチェック
	    $respons = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    if(preg_match("/^(404|403|500)$/",$respons)){
			return 'HTTP error: '.$respons;
	    }
	 
	    return $res;
	}
	
	public function request2($method, $params = array()){
		$url = $this->url;
	    $data = http_build_query($params);
	    $header = Array("Content-Type: application/x-www-form-urlencoded");
	    $options = array('http' => Array(
	        'method' => $method,
	        'header'  => implode("\r\n", $header),
	    ));
	 
	    //ステータスをチェック / PHP5専用 get_headers()
	    $respons = get_headers($url);
	    if(preg_match("/(404|403|500)/",$respons['0'])){
	        return false;
	    }
	 
	    if($method == 'GET') {
	        $url = ($data != '')?$url.'?'.$data:$url;
	    }elseif($method == 'POST') {
	        $options['http']['content'] = $data;
	    }
	    $content = file_get_contents($url, false, stream_context_create($options));
	 
	    return $content;
	}
}
?>
