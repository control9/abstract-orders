<?php
// THIS FILE SHOULD NOT BE EXECUTED DIRECTLY, require_once path is given from entry point.
require_once 'config/db_params.php';
require_once 'config/money_params.php';
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
		$data[] = array( 'id' => $id, 'summary' => $summary, 'description' => $description, 'cost' => $cost - ceil($cost * COMMISSION) );
	}
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	return json_encode($data);
}

function createOrder($id, $summary, $description, $cost) {
	if (getUserData($id)['money'] < $cost) return "Недостаточно средств";
	$order_id = prepareOrder($id, $summary, $description, $cost);
	if ($order_id == 0) {
		return "Ошибка при инициализации заказа";
	}
	$reserved_id = getReservedId();
	$transferResult = transferMoney($id, $reserved_id, $cost, $order_id);
	switch ($transferResult) :
		case 'ok':
			if (confirmOrder($order_id)) {
				return $order_id;
			}
			else return "Ошибка при подтверждении заказа";
			break;
		case 'transfer init failed':
		case 'transfer failed':
			return 'Ошибка при переводе оплаты';
		case 'transfer incomplete':
			return 'Ошибка при подтверждении оплаты';
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


function confirmOrder($order_id) {
	$link = mysqli_connect(ORDERS_DB_ADRESS, ORDERS_DB_LOGIN, ORDERS_DB_PASSWORD, ORDERS_DB_NAME);
	$stmt = mysqli_prepare($link, 'UPDATE ORDERS set paid = 1 where id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $order_id);
	if (!mysqli_stmt_execute($stmt)) {
		mysqli_stmt_close($stmt);
		mysqli_close($link);
		return 0;
	}
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	return true;
}

function completeOrder($id, $order_id) {
	if (reserveOrder($id, $order_id)) {
		return doCompleteOrder($id, $order_id);
	}
	else {
		return prepareErrorResponse($order_id, "Заказа не существует или заказ уже выполнен другим пользователем");
	}
}

function reserveOrder($id, $order_id) {
	$link = mysqli_connect(ORDERS_DB_ADRESS, ORDERS_DB_LOGIN, ORDERS_DB_PASSWORD, ORDERS_DB_NAME);
	$stmt = mysqli_prepare($link, 'UPDATE ORDERS set executor = ? where id = ? and isNull(executor)');
	mysqli_stmt_bind_param($stmt, 'ii', $id, $order_id);	
	$ok = mysqli_stmt_execute($stmt);
	$ok = $ok && (mysqli_stmt_affected_rows($stmt)> 0);
	mysqli_stmt_close($stmt);
	mysqli_close($link);
	return $ok;
}

function doCompleteOrder($id, $order_id) {
	$link = mysqli_connect(ORDERS_DB_ADRESS, ORDERS_DB_LOGIN, ORDERS_DB_PASSWORD, ORDERS_DB_NAME);
	$stmt = mysqli_prepare($link, 'select cost from ORDERS where id = ? and executor = ?');
	mysqli_stmt_bind_param($stmt, 'ii', $order_id, $id);	
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $cost);
	if (! mysqli_stmt_fetch($stmt)) {
		mysqli_stmt_close($stmt);
		mysqli_close($link);
		return prepareErrorResponse($order_id, "Неизвестная ошибка при обработке заказа");
	}
	mysqli_stmt_close($stmt);
	$reserved_id = getReservedId();
	$system_id = getSystemId();
	$commission = ceil($cost * COMMISSION);
	$real_cost = $cost - $commission;
	$commission_transfered = transferMoney($reserved_id, $system_id, $commission, $order_id);
	if ($commission_transfered == 'ok') {
		if ($real_cost > 0 && transferMoney($reserved_id, $id, $real_cost, $order_id)) {
			$stmt = mysqli_prepare($link, 'UPDATE ORDERS set completed = 1 where id = ?');
			mysqli_stmt_bind_param($stmt, 'i', $order_id);	
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			mysqli_close($link);
			return $order_id;
		}
	}
	mysqli_close($link);
	return prepareErrorResponse($order_id, "Ошибка при начислении оплаты, обратитесь к службе поддержки");
}

function prepareErrorResponse($order_id, $message) {
	return json_encode(array( 'id' => $order_id, 'message' => $message));
}

?>