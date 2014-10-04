<?php
require_once 'services/session_manager.php';
require_once 'services/user_service.php';
require_once 'services/order_service.php';
// Prevent caching.
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1976 00:00:00 GMT');

header('Content-type: text/plain; charset=utf-8');

$id = $_POST['id'];
$session = $_POST['session'];
$valid = checkSession($id, $session);
if (!$valid) {
	clearSessionCookies();
	die("Not authorized");
}
$action = $_POST['action'];

switch ($action) :
	case ("getorders"):
		$count = $_POST['count'];
		$from = $_POST['from'];
		if ($count && $from) {
			echo getOrders($count, $from);
		}
		else wrongParams();
		break;

	case ("createorder"):
		checkRights($id, 0);
		$summary = $_POST['summary'];
		$description = $_POST['description'];
		$cost = $_POST['cost'];
		if ($summary && $description && ($cost > 0)) {
			echo createOrder($id, $summary, $description, $cost);
		}
		else wrongParams();
		break;	
		
	case ("complete"):
		checkRights($id, 1);
		$orderid = $_POST['orderid'];
		if ($cardid){
			echo completeOrder($id, $orderid);
		}
		else wrongParams();
		break;
		
	default:	
		http_response_code(500);
		die("unknown action");
endswitch;

function wrongParams() {
	http_response_code(500);
	die("wrong params");
}
?>
