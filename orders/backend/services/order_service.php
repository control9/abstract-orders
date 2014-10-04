<?php
// THIS FILE SHOULD NOT BE EXECUTED DIRECTLY, require_once path is given from entry point.
require_once 'config/db_params.php';
require_once 'services/user_service.php';
require_once 'services/money_service.php';

function getOrders($count, $from) {
	$link = mysqli_connect(ORDERS_DB_ADRESS, ORDERS_DB_LOGIN, ORDERS_DB_PASSWORD, ORDERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id, summary, description, cost from ORDERS where id < ? and paid = 1 and isNull(executor) order by id desc limit ?');
	mysqli_stmt_bind_param($stmt, 'ii', $from, $count);
	return doGetOrders($link, $stmt);
}

function getLastOrders($count){
	$link = mysqli_connect(ORDERS_DB_ADRESS, ORDERS_DB_LOGIN, ORDERS_DB_PASSWORD, ORDERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id, summary, description, cost from ORDERS where paid = 1 and isNull(executor) order by id desc limit ?');
	mysqli_stmt_bind_param($stmt, 'i', $count);
	return doGetOrders($link, $stmt);
}

function getNewOrders($newest) {
	$link = mysqli_connect(ORDERS_DB_ADRESS, ORDERS_DB_LOGIN, ORDERS_DB_PASSWORD, ORDERS_DB_NAME)
	 or die('cannot connect: ' . mysqli_error($link));
	$stmt = mysqli_prepare($link, 'SELECT id, summary, description, cost from ORDERS where id > ? and paid = 1 and isNull(executor) order by id desc');
	mysqli_stmt_bind_param($stmt, 'i', $newest);
	return doGetOrders($link, $stmt);
}	
function doGetOrders($link, $stmt) {
	if (!mysqli_stmt_execute($stmt)) {
		return "fail";
	}
	mysqli_stmt_bind_result($stmt, $id, $summary, $description, $cost);
	$data = array();
	while (mysqli_stmt_fetch($stmt)) {
		$data[] = array( 'id' => $id, 'summary' => $summary, 'description' => $description, 'cost' => $cost);
	}
	mysqli_stmt_close($stmt);
	mysqli_close($link);
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
			return 'critical fail';
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