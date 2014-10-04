<?php
// THIS FILE SHOULD NOT BE EXECUTED DIRECTLY, require_once path is given from entry point.
require_once 'config/db_params.php';
require_once 'services/user_service.php';
require_once 'services/money_service.php';

function getOrders($count, $from) {
	$data = array();
	for ($i = $from; $i > $from - $count; $i = $i - 1) {
		$id = $i;
		$summary = $id . '-summary';
		$description = $id . '-description';
		$data[] = array( 'id' => $id, 'summary' => $summary, 'description' => $description);
	}
	return json_encode($data);
}

function createOrder($id, $summary, $description, $cost) {
	$order_id = prepareOrder($id, $summary, $description, $cost);
	if ($order_id == 0) {
		return "fail-prepare";
	}
	$reserved_id = getReservedId();
	$transferResult = transferMoney($id, $reserved_id, $cost, $order_id);
	switch ($transferResult) :
		case 'ok':
			if (completeOrder($order_id)) {
				return $order_id;
			}
			else return "fail-complete";
			break;
		case 'transfer init failed':
		case 'transfer failed':
			return 'fail';
		case 'transfer incomplete':
			return 'critical failure';
	endswitch;
}

function prepareOrder($id, $summary, $description, $cost){
	$link = mysqli_connect(ORDERS_DB_ADRESS, ORDERS_DB_LOGIN, ORDERS_DB_PASSWORD, ORDERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'INSERT INTO ORDERS (creator, summary, description, cost, paid, completed) VALUES (?, ?, ?, ?, 0, 0)');
	mysqli_stmt_bind_param($stmt, 'issi', $id, $summary, $description, $cost);
	if (!mysqli_stmt_execute($stmt)) {
		return 0;
	}
	mysqli_stmt_close($stmt);
	$order_id = mysqli_insert_id($link);
	mysqli_close($link);
	return $order_id;
}


function completeOrder($order_id) {
	$link = mysqli_connect(ORDERS_DB_ADRESS, ORDERS_DB_LOGIN, ORDERS_DB_PASSWORD, ORDERS_DB_NAME);
	$stmt = mysqli_prepare($link, 'UPDATE ORDERS set paid = 1 where id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $order_id);
	if (!mysqli_stmt_execute($stmt)) {
		return 0;
	}
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	return true;
}
?>