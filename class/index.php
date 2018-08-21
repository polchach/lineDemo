<?php
require_once('../class/class.lineAPI.php');
$line = new lineAPI();

$userId = $_REQUEST["userId"];
$role = $_REQUEST["role"];
if(($userId != '')&($role != '')){
	$line -> createAgentProfile($userId,$role);
}else{
	$line -> getUserProfile($userId);
}

 
?>

