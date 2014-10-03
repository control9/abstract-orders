<?php
require_once 'services/session_manager.php';
// Prevent caching.
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1976 00:00:00 GMT');

header('Content-type: application/json; charset=utf-8');

$count = $_GET['count'];
$data = array();
for ($i = 0; $i < $count; $i = $i + 1) {
	$id = $i;
	$content = $id . '-content';
	$data[] = array( 'id' => $id, 'content' => $content);
}
echo json_encode($data);
?>
