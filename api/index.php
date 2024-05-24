<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once '../api/connect.php';
include_once '../api/functions.php';

$q = $_GET['q'];
$params = explode('/', $q);
$type = $params[0];
if($type === 'job'){
    job($connect, $_POST);
}
?>