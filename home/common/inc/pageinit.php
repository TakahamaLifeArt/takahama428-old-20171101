<?php
/*
*	カートの状態取得とセッション開始
*	charset utf-8
*/
	require_once $_SERVER['DOCUMENT_ROOT'].'/php_libs/orders.php';
	$order = new Orders();
	$tax = $order->salestax();
	$tax /= 100;
	$cartdata = $order->reqDetails();
	$total = $cartdata['total']*(1+$tax);
	if($cartdata['options']['payment']==3) $total = $total*(1+_CREDIT_RATE);
	$cart_amount = $cartdata['amount'];
	//$perone = floor($total/$cart_amount);
	$cart_total = floor($total);
?>